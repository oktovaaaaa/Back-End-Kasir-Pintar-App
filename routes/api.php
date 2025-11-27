<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'registerCashier']);
    Route::post('/login', [ApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/me', [ApiAuthController::class, 'me']);
    });
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // kategori
    Route::apiResource('categories', CategoryController::class)->except(['show']);

    // produk
    Route::apiResource('products', ProductController::class);
    // (yang dua ini sebenarnya double, tapi kalau mau biarin dulu juga nggak apa2)
    Route::post('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    // pelanggan
    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customers', [CustomerController::class, 'store']);
    Route::get('customers/{id}', [CustomerController::class, 'show']);
    Route::put('customers/{id}', [CustomerController::class, 'update']);
    Route::delete('customers/{id}', [CustomerController::class, 'destroy']);

    // transaksi penjualan
    Route::get('sales', [SaleController::class, 'index']);   // riwayat
    Route::post('sales', [SaleController::class, 'store']);  // buat transaksi baru
    Route::get('sales/{id}', [SaleController::class, 'show']);

    // 🔵 INI YANG PENTING: route pelunasan kasbon
    Route::post('sales/{id}/pay-kasbon', [SaleController::class, 'payKasbon']);

    // laporan keuntungan & utang
    Route::get('/reports/profit', [ReportController::class, 'profitSummary']);
    Route::get('/reports/profit-by-product', [ReportController::class, 'profitByProduct']);
    Route::get('/reports/profit-by-category', [ReportController::class, 'profitByCategory']);
    Route::get('/reports/product/{product}/timeline', [ReportController::class, 'productTimeline']);
    Route::get('/reports/category/{category}/timeline', [ReportController::class, 'categoryTimeline']);
    Route::get('/reports/kasbon', [ReportController::class, 'kasbonList']);
});
