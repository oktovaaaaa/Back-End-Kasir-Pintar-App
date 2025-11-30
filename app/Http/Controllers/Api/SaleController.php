<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    // =========================================
    // GET /api/sales → riwayat transaksi
    // =========================================
    public function index()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $sales = Sale::with(['customer', 'items.product'])
            ->where('user_id', $userId)     // filter per kasir yg login
            // ->whereNotNull('customer_id') // JANGAN pakai filter ini kalau mau semua transaksi
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($sales);
    }

    // =========================================
    // GET /api/sales/{id} → detail 1 transaksi
    // =========================================
    public function show($id)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $sale = Sale::with(['customer', 'items.product'])
            ->where('user_id', $userId)
            ->findOrFail($id);

        return response()->json($sale);
    }

    // =========================================
    // POST /api/sales → buat transaksi baru
    // =========================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'   => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',

            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.qty'         => 'required|integer|min:1',

            'paid_amount'    => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'note'           => 'nullable|string',
        ]);

        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return DB::transaction(function () use ($data, $userId) {
            $total = 0;
            $saleItemsData = [];

            // hitung total & update stok
            foreach ($data['items'] as $item) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $qty = (int) $item['qty'];

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => ["Stok produk {$product->name} tidak mencukupi"],
                    ]);
                }

                $price     = $product->price;
                $costPrice = $product->cost_price;
                $subtotal  = $price * $qty;
                $total    += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product->id,
                    'qty'        => $qty,
                    'price'      => $price,
                    'cost_price' => $costPrice,
                    'subtotal'   => $subtotal,
                ];

                // kurangi stok
                $product->decrement('stock', $qty);
            }

            $paidAmount   = (float) $data['paid_amount'];
            $changeAmount = max(0, $paidAmount - $total);

            $status = $paidAmount >= $total ? 'paid' : 'kasbon';

            // aturan bisnis: kalau KASBON wajib punya pelanggan
            $customerId   = $data['customer_id'] ?? null;
            $customerName = $data['customer_name'] ?? null;

            if ($status === 'kasbon') {
                if (!$customerId && !$customerName) {
                    throw ValidationException::withMessages([
                        'customer_id' => ['Pelanggan wajib diisi untuk kasbon.'],
                    ]);
                }

                // kalau belum punya id tapi ada nama → buat customer baru otomatis
                if (!$customerId && $customerName) {
                    $customer = Customer::create([
                        'name' => $customerName,
                    ]);
                    $customerId = $customer->id;
                }
            }

            // snapshot nama pelanggan (buat history)
            $customerNameSnapshot = null;
            if ($customerId) {
                $customer = Customer::find($customerId);
                $customerNameSnapshot = $customer?->name;
            } elseif ($customerName) {
                // lunas tanpa customer_id tapi tetap simpan nama (kalau dikirim)
                $customerNameSnapshot = $customerName;
            }

            // simpan header sale
            $sale = Sale::create([
                'user_id'                => $userId,
                'customer_id'            => $customerId,
                'total_amount'           => $total,
                'paid_amount'            => $paidAmount,
                'change_amount'          => $changeAmount,
                'status'                 => $status,
                'payment_method'         => $data['payment_method'] ?? null,
                'customer_name_snapshot' => $customerNameSnapshot,
                'note'                   => $data['note'] ?? null,
            ]);

            // simpan detail items
            foreach ($saleItemsData as $itemData) {
                $itemData['sale_id'] = $sale->id;
                SaleItem::create($itemData);
            }

            $sale->load(['customer', 'items.product']);

            return response()->json($sale, 201);
        });
    }

    // =========================================
    // POST /api/sales/{id}/pay-kasbon → cicil / lunasi kasbon
    // =========================================
    public function payKasbon(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return DB::transaction(function () use ($id, $data) {
            /** @var Sale $sale */
            $sale = Sale::lockForUpdate()->findOrFail($id);

            if ($sale->status !== 'kasbon') {
                throw ValidationException::withMessages([
                    'sale' => ['Transaksi ini sudah lunas, tidak bisa dibayar lagi.'],
                ]);
            }

            $remaining = $sale->total_amount - $sale->paid_amount;
            if ($remaining <= 0) {
                throw ValidationException::withMessages([
                    'amount' => ['Kasbon sudah lunas.'],
                ]);
            }

            $amount = (float) $data['amount'];
            if ($amount > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => ['Nominal tidak boleh melebihi sisa kasbon (' . $remaining . ').'],
                ]);
            }

            $sale->paid_amount += $amount;
            $sale->change_amount = 0;

            if ($sale->paid_amount >= $sale->total_amount) {
                $sale->status = 'paid';
            }

            $sale->save();
            $sale->refresh();

            $newRemaining = max(0, $sale->total_amount - $sale->paid_amount);

            return response()->json([
                'message'   => 'Pembayaran kasbon berhasil disimpan.',
                'sale'      => $sale->load(['customer', 'items.product']),
                'remaining' => $newRemaining,
            ]);
        });
    }
}
