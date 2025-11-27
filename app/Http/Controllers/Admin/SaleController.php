<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        // Ambil 200 transaksi terakhir + relasi
        $sales = Sale::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        return view('admin.sales.index', compact('sales'));
    }
}
