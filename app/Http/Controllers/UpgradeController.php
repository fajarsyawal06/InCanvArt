<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpgradeController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();

        if ($user->role !== 'pengunjung') {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Akun anda sudah bukan pengunjung');
        }

        return view('upgrade', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Cek role dulu
        if ($user->role !== 'pengunjung') {
            abort(403, 'Anda tidak dapat melakukan upgrade ini.');
        }

        // Validasi input
        $request->validate([
            'nama_seniman' => 'required|string|max:120',
            'bio'          => 'required|string|max:2000',
            'instagram'    => 'nullable|string|max:255',
            'facebook'     => 'nullable|string|max:255',
            'twitter'      => 'nullable|string|max:255',
        ]);

        // Ambil atau buat profile user (mirip getOrCreateProfile di ProfileController)
        $profile = Profile::firstOrCreate([
            'user_id' => $user->user_id,   // perhatikan: pakai user_id, bukan id
        ]);

        // Susun kontak seperti di ProfileController
        $kontak = array_filter([
            'instagram' => $request->instagram,
            'twitter'   => $request->twitter,
            'facebook'  => $request->facebook,
        ], fn($v) => filled($v));

        // Update profile
        $profile->update([
            'nama_lengkap' => $request->nama_seniman,
            'bio'          => $request->bio,
            'kontak'       => $kontak ?: null,
        ]);

        // Upgrade role user
        $user->role = 'seniman';
        $user->save();

        return redirect()
            ->route('profiles.index')
            ->with('success', 'Selamat, akun Anda berhasil diupgrade menjadi Seniman.');
    }
}
