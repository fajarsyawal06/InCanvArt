<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AksesSeniman
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.lihat');
        }

        if (Auth::user()->role !== 'seniman') {
            // Bisa diarahkan ke dashboard biasa + pesan error
            return redirect()->route('dashboard')
                ->with('error', 'Akses khusus untuk akun seniman.');
        }

        return $next($request);
    }
}
