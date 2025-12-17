<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    public function store(Request $request, Artwork $artwork)
    {
        Share::create([
            'artwork_id'    => $artwork->artwork_id,
            'user_id'       => Auth::id(),
            'tanggal_share' => now(),
        ]);

        $url   = route('artworks.show', ['artwork' => $artwork->slug]);
        $judul = $artwork->judul ?? 'Artwork';

        return redirect()->away('https://wa.me/?text=' . urlencode($judul . ' - ' . $url));
    }
}
