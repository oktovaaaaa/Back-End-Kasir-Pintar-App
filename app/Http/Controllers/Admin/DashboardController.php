<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin
     * Route: GET /admin/dashboard  (name: admin.dashboard)
     */
    public function index()
    {
        // Nanti bisa diisi data statistik untuk dashboard
        return view('admin.dashboard');
    }

    /**
     * Tampilkan halaman kelola kasir
     * Route: GET /admin/kelola-kasir  (name: admin.kelola-kasir)
     */
    public function kelolaKasir()
    {
        $pendingCashiers  = User::where('role', 'cashier')
            ->where('status', 'pending')
            ->get();

        $approvedCashiers = User::where('role', 'cashier')
            ->where('status', 'approved')
            ->get();

        $rejectedCashiers = User::where('role', 'cashier')
            ->where('status', 'rejected')
            ->get();

        // View: resources/views/admin/kelola_kasir/index.blade.php
        return view('admin.kelola_kasir.index', compact(
            'pendingCashiers',
            'approvedCashiers',
            'rejectedCashiers'
        ));
    }

    /**
     * Approve kasir
     * Route: POST /admin/cashiers/{id}/approve  (name: admin.cashiers.approve)
     */
    public function approve($id)
    {
        $cashier = User::where('role', 'cashier')->findOrFail($id);
        $cashier->status = 'approved';
        $cashier->save();

        return redirect()
            ->back()
            ->with('success', 'Kasir berhasil di-approve.');
    }

    /**
     * Reject kasir
     * Route: POST /admin/cashiers/{id}/reject  (name: admin.cashiers.reject)
     */
    public function reject($id)
    {
        $cashier = User::where('role', 'cashier')->findOrFail($id);
        $cashier->status = 'rejected';
        $cashier->save();

        return redirect()
            ->back()
            ->with('success', 'Kasir berhasil di-reject.');
    }
}
