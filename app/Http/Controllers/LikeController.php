<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function toggle(Request $request, Artwork $artwork)
    {
        $userId = Auth::id();

        // Pastikan atomic & anti race condition
        $liked = false;
        DB::transaction(function () use ($artwork, $userId, &$liked) {
            $existing = Like::where('user_id', $userId)
                ->where('artwork_id', $artwork->artwork_id)
                ->first();

            if ($existing) {
                $existing->delete();
                $liked = false;
            } else {
                Like::create([
                    'user_id' => $userId,
                    'artwork_id' => $artwork->artwork_id,
                    'tanggal_like' => now(),
                ]);
                $liked = true;
            }
        });

        $count = $artwork->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $count,
        ]);
    }
}
