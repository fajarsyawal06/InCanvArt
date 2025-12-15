<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{

    public function toggle(User $user)
    {
        $authUser = Auth::user();

        // Pakai primary key sebenarnya (user_id), bukan ->id
        if ($authUser->getKey() === $user->getKey()) {
            abort(403, 'Anda tidak dapat mengikuti diri sendiri.');
        }

        $followerId  = $authUser->getKey(); // biasanya user_id
        $followingId = $user->getKey();

        // Cek apakah sudah follow
        $existing = Follow::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->first();

        if ($existing) {
            // Unfollow
            $existing->delete();
            $status = 'unfollow';
        } else {
            // Follow baru
            Follow::create([
                'follower_id'    => $followerId,
                'following_id'   => $followingId,
                'tanggal_follow' => now(),
            ]);
            $status = 'follow';
        }

        if (request()->wantsJson()) {
            return response()->json(['status' => $status]);
        }

        return back();
    }
}
