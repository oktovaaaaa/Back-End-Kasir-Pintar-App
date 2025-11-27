<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * /admin/dashboard
     */
    public function index()
    {
        $adminName = optional(auth('admin')->user())->name ?? 'Admin';

        /*
        |--------------------------------------------------------------------------
        | PRODUK
        |--------------------------------------------------------------------------
        */
        $totalProducts   = Product::count();
        $totalStock      = (int) Product::sum('stock');
        $lowStockCount   = Product::where('stock', '>', 0)
                                  ->where('stock', '<=', 5)
                                  ->count();
        $outStockCount   = Product::where('stock', '<=', 0)->count();

        /*
        |--------------------------------------------------------------------------
        | KASIR
        |--------------------------------------------------------------------------
        */
        $pendingCashiers  = User::where('role', 'cashier')->where('status', 'pending')->get();
        $approvedCashiers = User::where('role', 'cashier')->where('status', 'approved')->get();
        $rejectedCashiers = User::where('role', 'cashier')->where('status', 'rejected')->get();

        $totalCashiersPending  = $pendingCashiers->count();
        $totalCashiersApproved = $approvedCashiers->count();
        $totalCashiersRejected = $rejectedCashiers->count();

        /*
        |--------------------------------------------------------------------------
        | PELANGGAN & KASBON
        |--------------------------------------------------------------------------
        */
        $totalCustomers = Customer::count();

        // Semua transaksi kasbon
        $kasbonSalesQuery = Sale::where('status', 'kasbon');

        // Total piutang (sisa kasbon)
        $totalPiutang = (clone $kasbonSalesQuery)->get()->sum(function ($s) {
            $remain = (float) $s->total_amount - (float) $s->paid_amount;
            return $remain > 0 ? $remain : 0;
        });

        // Total nominal kasbon (total amount transaksi kasbon)
        $totalKasbonAmount = (clone $kasbonSalesQuery)->sum('total_amount');

        // Jumlah pelanggan yang masih punya utang (distinct customer_id)
        $debtCustomers = DB::table('sales')
            ->where('status', 'kasbon')
            ->whereColumn('total_amount', '>', 'paid_amount')
            ->whereNotNull('customer_id')
            ->distinct('customer_id')
            ->count('customer_id');

        $noDebtCustomers = max(0, $totalCustomers - $debtCustomers);

        /*
        |--------------------------------------------------------------------------
        | PENJUALAN / LAPORAN
        |--------------------------------------------------------------------------
        */
        $totalSalesCount  = Sale::count();
        $totalSalesAmount = Sale::sum('total_amount');

        // Estimasi total profit dari sale_items * (harga jual - modal)
        $totalProfit = (float) (DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(DB::raw('SUM( (sale_items.price - products.cost_price) * sale_items.qty ) as profit'))
            ->value('profit') ?? 0);

        /*
        |--------------------------------------------------------------------------
        | DATA CHART: OMZET & PROFIT 7 HARI TERAKHIR
        |--------------------------------------------------------------------------
        */
        $startDate = now()->subDays(6)->startOfDay();

        // Omzet per hari
        $dailySales = Sale::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Profit per hari (join sale_items + products)
        $profitPerDay = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(sales.created_at)'))
            ->select(
                DB::raw('DATE(sales.created_at) as date'),
                DB::raw('SUM( (sale_items.price - products.cost_price) * sale_items.qty ) as profit')
            )
            ->pluck('profit', 'date');

        $chartLabels      = [];
        $chartSalesData   = [];
        $chartProfitData  = [];

        foreach ($dailySales as $row) {
            $dateKey = $row->date;
            $chartLabels[]     = Carbon::parse($dateKey)->translatedFormat('d M');
            $chartSalesData[]  = (float) $row->total_sales;
            $chartProfitData[] = (float) ($profitPerDay[$dateKey] ?? 0);
        }

        $chartLabelsJson     = json_encode($chartLabels);
        $chartSalesJson      = json_encode($chartSalesData);
        $chartProfitJson     = json_encode($chartProfitData);

        /*
        |--------------------------------------------------------------------------
        | DATA DONUT CHART: STATUS TRANSAKSI (LUNAS vs KASBON)
        |--------------------------------------------------------------------------
        */
        $paidCount   = Sale::where('status', 'paid')->count();
        $kasbonCount = Sale::where('status', 'kasbon')->count();

        $donutStatusLabelsJson = json_encode(['Lunas', 'Kasbon']);
        $donutStatusDataJson   = json_encode([$paidCount, $kasbonCount]);

        return view('admin.dashboard', compact(
            'adminName',

            // Produk
            'totalProducts',
            'totalStock',
            'lowStockCount',
            'outStockCount',

            // Kasir
            'totalCashiersPending',
            'totalCashiersApproved',
            'totalCashiersRejected',

            // Pelanggan & kasbon
            'totalCustomers',
            'debtCustomers',
            'noDebtCustomers',
            'totalPiutang',
            'totalKasbonAmount',

            // Sales / laporan
            'totalSalesCount',
            'totalSalesAmount',
            'totalProfit',

            // Chart line
            'chartLabelsJson',
            'chartSalesJson',
            'chartProfitJson',

            // Donut status
            'donutStatusLabelsJson',
            'donutStatusDataJson'
        ));
    }

    /**
     * /admin/kelola-kasir
     */
    public function kelolaKasir()
    {
        $pendingCashiers  = User::where('role', 'cashier')->where('status', 'pending')->get();
        $approvedCashiers = User::where('role', 'cashier')->where('status', 'approved')->get();
        $rejectedCashiers = User::where('role', 'cashier')->where('status', 'rejected')->get();

        return view('admin.kelola_kasir.index', compact(
            'pendingCashiers',
            'approvedCashiers',
            'rejectedCashiers'
        ));
    }

    public function approve($id)
    {
        $cashier = User::where('role', 'cashier')->findOrFail($id);
        $cashier->status = 'approved';
        $cashier->save();

        return redirect()
            ->route('admin.kelola-kasir')
            ->with('success', 'Kasir berhasil di-approve.');
    }

    public function reject($id)
    {
        $cashier = User::where('role', 'cashier')->findOrFail($id);
        $cashier->status = 'rejected';
        $cashier->save();

        return redirect()
            ->route('admin.kelola-kasir')
            ->with('success', 'Kasir berhasil ditolak.');
    }
}
