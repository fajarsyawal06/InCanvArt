<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Artwork;
use App\Models\Statistic; // ← TAMBAHKAN INI
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Artwork $artwork)
    {
        $userId     = Auth::id();
        $bookmarked = false;
        $count      = 0;

        DB::transaction(function () use ($artwork, $userId, &$bookmarked, &$count) {
            // Cek apakah user sudah pernah bookmark artwork ini
            $existing = Favorite::where('user_id', $userId)
                ->where('artwork_id', $artwork->artwork_id)
                ->first();

            if ($existing) {
                // Jika sudah ada, berarti ini unbookmark
                $existing->delete();
                $bookmarked = false;
            } else {
                // Jika belum, buat record baru sebagai bookmark
                Favorite::create([
                    'user_id'           => $userId,
                    'artwork_id'        => $artwork->artwork_id,
                    'tanggal_favorite'  => now(),
                ]);
                $bookmarked = true;
            }

            // Hitung ulang total bookmark untuk artwork ini
            $count = Favorite::where('artwork_id', $artwork->artwork_id)->count();

            // Update / buat record di tabel statistics
            $stat = Statistic::firstOrCreate(
                ['artwork_id' => $artwork->artwork_id],
                [
                    'jumlah_like'      => 0,
                    'jumlah_share'     => 0,
                    'jumlah_komentar'  => 0,
                    'jumlah_favorit'   => 0,
                    'jumlah_view'      => 0,
                ]
            );

            $stat->jumlah_favorit = $count;
            $stat->save();
        });

        // Kalau mau hitung total bookmark per artwork (sudah dihitung di atas)
        // $count = $artwork->favorites()->count(); // tidak wajib lagi, tapi boleh saja

        return response()->json([
            'bookmarked'      => $bookmarked,
            'favorites_count' => $count,
        ]);
    }

    public function index()
    {
        $userId = Auth::id();

        $favorites = Favorite::where('user_id', $userId)
            ->with('artwork')
            ->orderBy('tanggal_favorite', 'desc')
            ->get();

        return view('favorite', compact('favorites'));
    }
}
