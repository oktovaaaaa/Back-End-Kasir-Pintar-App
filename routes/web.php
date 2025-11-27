<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SaleController as AdminSaleController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// AUTH ADMIN
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {

    // DASHBOARD ADMIN
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // KELOLA KASIR -> index.blade.php di folder admin/kelola_kasir
    Route::get('/kelola-kasir', [DashboardController::class, 'kelolaKasir'])->name('kelola-kasir');
    Route::post('/cashiers/{id}/approve', [DashboardController::class, 'approve'])->name('cashiers.approve');
    Route::post('/cashiers/{id}/reject', [DashboardController::class, 'reject'])->name('cashiers.reject');

    // LAPORAN
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/summary', [AdminReportController::class, 'profitSummary'])->name('reports.summary');
    Route::get('/reports/profit-by-product', [AdminReportController::class, 'profitByProduct'])->name('reports.profitByProduct');
    Route::get('/reports/profit-by-category', [AdminReportController::class, 'profitByCategory'])->name('reports.profitByCategory');
    Route::get('/reports/kasbon', [AdminReportController::class, 'kasbonList'])->name('reports.kasbon');

    // STOK PRODUK
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // RIWAYAT TRANSAKSI
    Route::get('/sales-history', [AdminSaleController::class, 'index'])->name('sales.index');

    // PELANGGAN & KASBON
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [AdminCustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [AdminCustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/kasbon', [AdminCustomerController::class, 'kasbon'])->name('customers.kasbon');

    // BAYAR KASBON
    Route::post('/kasbon/{sale}/pay', [AdminCustomerController::class, 'payKasbon'])->name('kasbon.pay');
});
