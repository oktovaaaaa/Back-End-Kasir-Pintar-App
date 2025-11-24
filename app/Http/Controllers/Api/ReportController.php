<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan keuntungan harian/mingguan/bulanan/tahunan (overall).
     * GET /api/reports/profit?period=daily|weekly|monthly|yearly
     */
    public function profitSummary(Request $request)
    {
        $period = $request->query('period', 'daily'); // default harian

        // Tentukan grouping berdasarkan period
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

        $rows = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->selectRaw("
                {$groupExpr} as period_label,
                SUM((sale_items.price - sale_items.cost_price) * sale_items.qty) as total_profit,
                SUM(sale_items.subtotal) as total_sales,
                COUNT(DISTINCT sales.id) as transaksi
            ")
            ->groupBy('period_label')
            ->orderBy('period_label', 'desc')
            ->limit(30)
            ->get();

        return response()->json($rows);
    }

    /**
     * Keuntungan per produk.
     * GET /api/reports/profit-by-product?period=daily (optional)
     * Untuk tugas besar: cukup harian/all-time juga sudah oke.
     */
    public function profitByProduct(Request $request)
    {
        $rows = DB::table('sale_items')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw("
                products.id as product_id,
                products.name as product_name,
                SUM(sale_items.qty) as total_qty,
                SUM(sale_items.subtotal) as total_sales,
                SUM((sale_items.price - sale_items.cost_price) * sale_items.qty) as total_profit
            ")
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_profit', 'desc')
            ->limit(50)
            ->get();

        return response()->json($rows);
    }

    /**
     * Keuntungan per kategori.
     * GET /api/reports/profit-by-category
     */
    public function profitByCategory()
    {
        $rows = DB::table('sale_items')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw("
                categories.id as category_id,
                categories.name as category_name,
                SUM(sale_items.qty) as total_qty,
                SUM(sale_items.subtotal) as total_sales,
                SUM((sale_items.price - sale_items.cost_price) * sale_items.qty) as total_profit
            ")
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_profit', 'desc')
            ->get();

        return response()->json($rows);
    }

    /**
     * Daftar kasbon (belum lunas) + info total & sisa hutang per transaksi.
     * GET /api/reports/kasbon
     */
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
