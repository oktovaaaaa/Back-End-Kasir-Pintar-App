<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index()
    {
        // ambil semua customer + total utang (sum total_amount - paid_amount untuk status kasbon)
        $customers = Customer::select('customers.*')
            ->leftJoin('sales', 'sales.customer_id', '=', 'customers.id')
            ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN sales.status = 'kasbon' THEN (sales.total_amount - sales.paid_amount)
                        ELSE 0
                    END
                ), 0) AS total_debt
            ")
            ->groupBy('customers.id')
            ->orderBy('customers.name')
            ->get();

        $totalCustomers     = $customers->count();
        $totalDebt          = $customers->sum('total_debt');
        $debtCustomers      = $customers->where('total_debt', '>', 0.0001)->count();
        $noDebtCustomers    = $totalCustomers - $debtCustomers;

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'totalDebt',
            'debtCustomers',
            'noDebtCustomers'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'note'    => 'nullable|string',
        ]);

        Customer::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'note'    => 'nullable|string',
        ]);

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    // DETAIL + HISTORY
    public function show(Customer $customer)
    {
        $customer->load(['sales' => function ($q) {
            $q->orderBy('created_at', 'desc')
              ->with('items.product');
        }]);

        // hitung total utang aktif
        $totalDebt = $customer->sales
            ->where('status', 'kasbon')
            ->sum(function ($s) {
                return max(0, $s->total_amount - $s->paid_amount);
            });

        return view('admin.customers.show', compact('customer', 'totalDebt'));
    }

    // LIST KASBON AKTIF (dipakai AJAX di modal)
    public function kasbon(Customer $customer)
    {
        $kasbon = $customer->sales()
            ->where('status', 'kasbon')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Sale $sale) {
                $remaining = max(0, $sale->total_amount - $sale->paid_amount);
                return [
                    'id'           => $sale->id,
                    'total_amount' => $sale->total_amount,
                    'paid_amount'  => $sale->paid_amount,
                    'remaining'    => $remaining,
                    'created_at'   => $sale->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json($kasbon);
    }

    // BAYAR KASBON (AJAX)
    public function payKasbon(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($sale, $data) {
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

            $newRemaining = max(0, $sale->total_amount - $sale->paid_amount);

            return response()->json([
                'message'   => 'Pembayaran kasbon berhasil disimpan.',
                'remaining' => $newRemaining,
            ]);
        });
    }
}
