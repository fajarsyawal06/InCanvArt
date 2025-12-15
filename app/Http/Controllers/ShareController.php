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
        // 1. Simpan log share
        Share::create([
            'artwork_id'   => $artwork->artwork_id,          // atau $artwork->artwork_id
            'user_id'      => Auth::id(),           // boleh null kalau guest
            'tanggal_share'=> now(),
        ]);

        // 2. Buat URL artwork
        $url   = route('artworks.show', $artwork->artwork_id);
        $judul = $artwork->judul ?? 'Artwork';

        // 3. Redirect ke WhatsApp share
        $waUrl = 'https://wa.me/?text=' . urlencode($judul . ' - ' . $url);

        return redirect()->away($waUrl);
    }
}
