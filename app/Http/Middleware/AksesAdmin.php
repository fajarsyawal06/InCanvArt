<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AksesAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.lihat');
        }

        if (Auth::user()->role === 'admin') {
            // Admin valid → lanjut
            return $next($request);
        }

        // Bukan admin → lempar ke dashboard umum
        return redirect()->route('dashboard');
        // atau: abort(403, 'Anda tidak memiliki akses ke halaman admin.');
    }
}
