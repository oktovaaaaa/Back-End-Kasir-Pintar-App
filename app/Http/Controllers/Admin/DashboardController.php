<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingCashiers  = User::where('role', 'cashier')->where('status', 'pending')->get();
        $approvedCashiers = User::where('role', 'cashier')->where('status', 'approved')->get();
        $rejectedCashiers = User::where('role', 'cashier')->where('status', 'rejected')->get();

        return view('admin.dashboard', compact('pendingCashiers', 'approvedCashiers', 'rejectedCashiers'));
    }

    public function approve($id)
    {
        $cashier = User::where('role', 'cashier')->findOrFail($id);
        $cashier->status = 'approved';
        $cashier->save();

        return redirect()->back()->with('success', 'Kasir berhasil di-approve.');
    }

    public function reject($id)
    {
        $cashier = User::where('role', 'cashier')->findOrFail($id);
        $cashier->status = 'rejected';
        $cashier->save();

        return redirect()->back()->with('success', 'Kasir berhasil di-reject.');
    }
}
