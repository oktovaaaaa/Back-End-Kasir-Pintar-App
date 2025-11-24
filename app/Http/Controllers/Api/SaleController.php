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
    // GET /api/sales → riwayat transaksi sederhana
    public function index()
    {
        $sales = Sale::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($sales);
    }

    // GET /api/sales/{id} → detail 1 transaksi (untuk kasbon / riwayat)
    public function show($id)
    {
        $sale = Sale::with(['customer', 'items.product'])
            ->findOrFail($id);

        return response()->json($sale);
    }

    // POST /api/sales → buat transaksi baru (lunas / kasbon)
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'   => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255', // dari Flutter kalau belum terdaftar

            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.qty'         => 'required|integer|min:1',

            'paid_amount'   => 'required|numeric|min:0',
            'payment_method'=> 'nullable|string|max:50',
            'note'          => 'nullable|string',
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

                $price     = $product->price;        // harga jual
                $costPrice = $product->cost_price;   // harga modal
                $subtotal  = $price * $qty;          // total jual
                $total    += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product->id,
                    'qty'        => $qty,
                    'price'      => $price,
                    'cost_price' => $costPrice,  // simpan modal per unit saat transaksi
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

            // simpan snapshot nama pelanggan (biar history tetap kebaca kalau nama berubah)
            $customerNameSnapshot = null;
            if ($customerId) {
                $customer = Customer::find($customerId);
                $customerNameSnapshot = $customer?->name;
            } elseif ($customerName) {
                // kalau lunas tapi tetap mau simpan nama di snapshot (opsional)
                $customerNameSnapshot = $customerName;
            }

            // simpan header sale
            $sale = Sale::create([
                'user_id'               => $userId,
                'customer_id'           => $customerId,
                'total_amount'          => $total,
                'paid_amount'           => $paidAmount,
                'change_amount'         => $changeAmount,
                'status'                => $status,
                'payment_method'        => $data['payment_method'] ?? null,
                'customer_name_snapshot'=> $customerNameSnapshot,
                'note'                  => $data['note'] ?? null,
            ]);

            // simpan detail items
            foreach ($saleItemsData as $itemData) {
                $itemData['sale_id'] = $sale->id;
                SaleItem::create($itemData);
            }

            // load relasi untuk response ke Flutter
            $sale->load(['customer', 'items.product']);

            return response()->json($sale, 201);
        });
    }
}
