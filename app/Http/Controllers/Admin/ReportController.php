<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /** Helper: sama seperti Api\ReportController */
    protected function getRangeStart(string $period): Carbon
    {
        $now = Carbon::now();

        switch ($period) {
            case 'weekly':
                return $now->copy()->subWeeks(12)->startOfWeek();
            case 'monthly':
                return $now->copy()->subMonths(12)->startOfMonth();
            case 'yearly':
                return $now->copy()->subYears(5)->startOfYear();
            case 'daily':
            default:
                return $now->copy()->subDays(7)->startOfDay();
        }
    }

    /** Halaman utama view */
    public function index()
    {
        return view('admin.reports.index');
    }

    /** JSON: ringkasan profit (untuk chart + tabel) */
    public function profitSummary(Request $request)
    {
        $period = $request->query('period', 'daily');

        switch ($period) {
            case 'weekly':
                $groupExpr = "date_trunc('week', sales.created_at)";
                break;
            case 'monthly':
                $groupExpr = "date_trunc('month', sales.created_at)";
                break;
            case 'yearly':
                $groupExpr = "date_trunc('year', sales.created_at)";
                break;
            case 'daily':
            default:
                $groupExpr = "date_trunc('day', sales.created_at)";
                break;
        }

        $startDate = $this->getRangeStart($period);

        $rows = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->selectRaw("
                {$groupExpr} as period_label,
                SUM((sale_items.price - sale_items.cost_price) * sale_items.qty) as total_profit,
                SUM(sale_items.subtotal) as total_sales,
                SUM(sale_items.qty) as total_qty,
                COUNT(DISTINCT sales.id) as transaksi
            ")
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy('period_label')
            ->orderBy('period_label', 'asc')
            ->get();

        return response()->json($rows);
    }

    /** JSON: profit per produk */
    public function profitByProduct(Request $request)
    {
        $period = $request->query('period', 'daily');
        $startDate = $this->getRangeStart($period);

        $rows = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw("
                products.id as product_id,
                products.name as product_name,
                SUM(sale_items.qty) as total_qty,
                SUM(sale_items.subtotal) as total_sales,
                SUM((sale_items.price - sale_items.cost_price) * sale_items.qty) as total_profit
            ")
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_profit', 'desc')
            ->limit(50)
            ->get();

        return response()->json($rows);
    }

    /** JSON: profit per kategori */
    public function profitByCategory(Request $request)
    {
        $period = $request->query('period', 'daily');
        $startDate = $this->getRangeStart($period);

        $rows = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw("
                categories.id as category_id,
                categories.name as category_name,
                SUM(sale_items.qty) as total_qty,
                SUM(sale_items.subtotal) as total_sales,
                SUM((sale_items.price - sale_items.cost_price) * sale_items.qty) as total_profit
            ")
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_profit', 'desc')
            ->get();

        return response()->json($rows);
    }

    /** JSON: daftar kasbon belum lunas */
    public function kasbonList()
    {
        $rows = DB::table('sales')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->select(
                'sales.id',
                'customers.name as customer_name',
                'sales.total_amount',
                'sales.paid_amount',
                'sales.change_amount',
                'sales.status',
                'sales.created_at'
            )
            ->where('sales.status', 'kasbon')
            ->orderBy('sales.created_at', 'desc')
            ->get()
            ->map(function ($row) {
                $row->remaining = $row->total_amount - $row->paid_amount;
                return $row;
            });

        return response()->json($rows);
    }
}
