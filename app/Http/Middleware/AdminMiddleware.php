<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        // Belum login sebagai admin
        if (! $user) {
            return redirect()->route('admin.login');
        }

        // Tambahan pengecekan role (kalau mau ekstra aman)
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
