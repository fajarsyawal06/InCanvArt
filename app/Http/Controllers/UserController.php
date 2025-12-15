<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('profile');

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian nama / username / email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($qp) use ($search) {
                        $qp->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->orderBy('tanggal_registrasi', 'desc')->paginate(15);

        // TAMBAHAN: user yang sedang login (untuk <x-navbar>)
        $user = auth()->user();

        return view('users.index', compact('users', 'user'));
    }


    public function show(User $user)
    {
        $user->load('profile');
        return view('users.show', compact('user'));
    }

    public function deactivate(User $user)
    {
        $user->update(['status' => 'nonaktif']);
        return back()->with('success', 'Akun berhasil dinonaktifkan.');
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'aktif']);
        return back()->with('success', 'Akun berhasil diaktifkan.');
    }
}
