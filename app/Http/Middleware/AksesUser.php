<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AksesUser
{
    public function handle(Request $request, Closure $next)
    {
        // Jika belum login → ke login
        if (!Auth::check()) {
            return redirect()->route('login.lihat');
        }

        $role = Auth::user()->role;

        // IZINKAN: pengunjung dan seniman mengakses /dashboard
        if (in_array($role, ['pengunjung', 'seniman'])) {
            return $next($request);
        }

        // Jika admin (atau role lain) mencoba ke /dashboard → arahkan ke admin dashboard
        if ($role === 'admin') {
            return redirect()->route('admin');
        }

        // Role di luar itu, amankan (bisa 403 atau redirect lain sesuai kebutuhan)
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
