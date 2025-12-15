<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Artwork;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * Halaman hasil pencarian (user + artwork).
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        // =======================
        // 1. Cari user
        // =======================
        $users = User::query()
            ->where(function ($query) use ($q) {
                $query->where('username', 'LIKE', '%' . $q . '%')
                      ->orWhere('email', 'LIKE', '%' . $q . '%');
            })
            ->take(10)
            ->get();

        // =======================
        // 2. Cari artwork
        // =======================
        $artworks = Artwork::with('kategori')
            ->visibleFor(Auth::user()) // scope di model Artwork
            ->where(function ($query) use ($q) {
                $query->where('judul', 'LIKE', "%{$q}%")
                      ->orWhere('deskripsi', 'LIKE', "%{$q}%");
            })
            ->orderByDesc('tanggal_upload')
            ->paginate(24);

        // Potong deskripsi menjadi 15 kata pada collection dari paginator
        $artworks->getCollection()->transform(function ($art) {
            if (!empty($art->deskripsi)) {
                $art->deskripsi = Str::words($art->deskripsi, 15, '...');
            }
            return $art;
        });

        return view('search.index', compact('q', 'users', 'artworks'));
    }

    /**
     * Live search user (untuk AJAX suggestion).
     */
    public function liveUsers(Request $request)
    {
        $q = trim($request->input('q', ''));

        if ($q === '') {
            return response()->json(['users' => []]);
        }

        $users = User::with('profile') // sesuaikan jika nama relasi berbeda
            ->where(function ($query) use ($q) {
                $query->where('username', 'LIKE', '%' . $q . '%')
                      ->orWhere('email', 'LIKE', '%' . $q . '%');
            })
            ->orderBy('username')
            ->take(10)
            ->get();

        $results = $users->map(function ($user) {
            $profile = $user->profile ?? null;

            $avatar = $profile && $profile->foto_profil
                ? asset('storage/' . $profile->foto_profil)
                : asset('images/avatar-sample.jpg');

            return [
                'id'          => $user->user_id ?? $user->id,
                'username'    => $user->username,
                'email'       => $user->email,
                'avatar'      => $avatar,
                'profile_url' => route('profiles.show', $user->user_id ?? $user->id),
                'bio'         => optional($profile)->bio ?? '',
            ];
        });

        return response()->json([
            'users' => $results,
        ]);
    }
}
