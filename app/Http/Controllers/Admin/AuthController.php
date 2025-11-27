<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Form login admin
     * Route: GET /admin/login  (name: admin.login)
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Proses login admin
     * Route: POST /admin/login  (name: admin.login.post)
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Pakai guard 'admin' dan pastikan role = 'admin'
        if (Auth::guard('admin')->attempt(
            array_merge($credentials, ['role' => 'admin']),
            $request->boolean('remember') // kalau ada checkbox remember
        )) {
            // regenerate session untuk keamanan
            $request->session()->regenerate();

            // setelah login -> ke dashboard admin
            return redirect()->route('admin.dashboard');
        }

        // Gagal login
        return back()
            ->withErrors([
                'email' => 'Email atau password salah, atau Anda bukan admin.',
            ])
            ->onlyInput('email');
    }

    /**
     * Logout admin
     * Route: POST /admin/logout  (name: admin.logout)
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
