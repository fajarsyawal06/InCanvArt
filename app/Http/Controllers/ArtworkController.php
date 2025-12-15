<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Artwork;
use App\Models\Favorite;
use App\Models\Like;
use App\Models\Statistic;
use App\Models\Moderation;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtworkController extends Controller
{
    // Menampilkan semua data
    public function index()
    {
        $loginUser = Auth::user();

        // Query utama: daftar artwork untuk halaman /artworks
        $artworks = Artwork::with(['kategori', 'user.profile'])
            ->visibleFor($loginUser)               // Hormati status moderasi
            ->inRandomOrder()                      // Tetap random seperti keinginan awal
            ->orderByDesc('tanggal_upload')        // Random + tertata fallback
            ->paginate(24);

        // Potong deskripsi menjadi 15 kata
        $artworks->getCollection()->transform(function ($art) {
            if (!empty($art->deskripsi)) {
                $art->deskripsi = \Illuminate\Support\Str::words($art->deskripsi, 15, '...');
            }
            return $art;
        });

        return view('artworks.index', compact('artworks'));
    }


    // Form tambah data
    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('artworks.create', compact('categories'));
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'file'         => 'required|image|mimes:jpg,jpeg,png',
            'kategori_id'  => 'required|exists:categories,kategori_id',
        ]);

        $path    = $request->file('file')->store('artworks', 'public');
        $fileUrl = Storage::url($path);

        $artwork = new Artwork();
        $artwork->user_id        = Auth::id();
        $artwork->kategori_id    = $validated['kategori_id'];
        $artwork->judul          = $validated['judul'];
        $artwork->deskripsi      = $validated['deskripsi'] ?? null;
        $artwork->file_url       = $fileUrl;
        $artwork->status         = 'aktif';
        $artwork->tanggal_upload = now();
        $artwork->save();

        return redirect()
            ->route('profiles.index')
            ->with('success', 'Artwork berhasil diupload.');
    }

    // Form edit
    public function edit(Artwork $artwork)
    {
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit artwork ini.');
        }

        $categories = Category::orderBy('nama_kategori')->get();
        return view('artworks.edit', compact('artwork', 'categories'));
    }

    // Update data
    public function update(Request $request, Artwork $artwork)
    {
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah artwork ini.');
        }

        $validated = $request->validate([
            'judul'        => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'file'         => 'nullable|image|mimes:jpg,jpeg,png',
            'kategori_id'  => 'required|exists:categories,kategori_id',
            'status'       => 'nullable|in:aktif,nonaktif',
        ]);

        $data = [
            'judul'        => $validated['judul'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'kategori_id'  => $validated['kategori_id'],
            'status'       => $validated['status'] ?? $artwork->status,
        ];

        if ($request->hasFile('file')) {
            if ($artwork->file_url) {
                $oldPath = str_replace('/storage/', '', $artwork->file_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('file')->store('artworks', 'public');
            $data['file_url'] = Storage::url($path);
        }

        $artwork->update($data);

        return redirect()
            ->route('artworks.show', $artwork)
            ->with('success', 'Artwork berhasil diperbarui.');
    }

    // Hapus data
    public function destroy(Artwork $artwork)
    {
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus artwork ini.');
        }

        if ($artwork->file_url) {
            $oldPath = str_replace('/storage/', '', $artwork->file_url);
            Storage::disk('public')->delete($oldPath);
        }

        $artwork->delete();

        return redirect()
            ->route('profiles.index')
            ->with('success', 'Artwork berhasil dihapus.');
    }

    // Detail artwork + data untuk follow / like / bookmark
    public function show(Artwork $artwork)
    {
        // 1. Increment jumlah view
        $stat = Statistic::firstOrCreate(
            ['artwork_id' => $artwork->artwork_id],
            [
                'jumlah_like'     => 0,
                'jumlah_share'    => 0,
                'jumlah_komentar' => 0,
                'jumlah_favorit'  => 0,
                'jumlah_view'     => 0,
            ]
        );
        $stat->increment('jumlah_view');

        // 2. Eager load relasi
        $artwork->load([
            'user.profile',
            'kategori',
            'comments.user.profile',
            'stat'
        ])->loadCount('likes');

        // 3. Ambil user login
        $loginUser = Auth::user();
        $isOwner   = $loginUser && $loginUser->user_id === $artwork->user_id;
        $isAdmin   = $loginUser && $loginUser->role === 'admin';

        // 4. Proteksi akses berdasarkan status artwork
        // User lain hanya boleh lihat artwork yang status = 'aktif'
        if (!in_array($artwork->status, ['aktif']) && !$isOwner && !$isAdmin) {
            abort(404);
        }

        // 5. Data creator
        $creator     = $artwork->user;
        $creatorProf = $creator?->profile;

        // Foto creator
        if ($creatorProf?->foto_profil) {
            $fotoCreator = asset('storage/' . $creatorProf->foto_profil);
        } elseif ($creator?->avatar) {
            $fotoCreator = $creator->avatar;
        } else {
            $fotoCreator = asset('images/avatar-sample.jpg');
        }

        // Nama creator
        $creatorName     = $creatorProf?->nama_lengkap ?? $creator?->username ?? 'Pengguna';
        $creatorUsername = '@' . ($creator?->username ?? Str::slug($creatorName));

        // 6. Like & Bookmark state
        $userHasLiked = auth()->check()
            ? Like::where('user_id', auth()->id())
            ->where('artwork_id', $artwork->artwork_id)
            ->exists()
            : false;

        $userHasBookmarked = auth()->check()
            ? Favorite::where('user_id', auth()->id())
            ->where('artwork_id', $artwork->artwork_id)
            ->exists()
            : false;

        // 7. Follow status & count
        $isFollowing     = auth()->check() ? auth()->user()->isFollowing($creator) : false;
        $followersCount  = $creator ? $creator->followers()->count() : 0;
        $followingsCount = $creator ? $creator->followings()->count() : 0;

        // 8. Ambil moderasi terbaru untuk artwork ini
        $moderation = Moderation::where('target_type', 'artwork')
            ->where('target_id', $artwork->artwork_id)
            ->latest('tanggal')
            ->first();

        // 9. Ambil komentar top-level dengan logika moderasi
        $commentsQuery = Comment::where('artwork_id', $artwork->artwork_id)
            ->whereNull('parent_comment_id')
            ->orderByDesc('tanggal');

        if ($isAdmin) {
            // Admin boleh melihat semua komentar (aktif / ditandai / ditolak)
            // tidak ada filter tambahan
        } else {
            if ($loginUser) {
                $viewerId = $loginUser->user_id;

                $commentsQuery->where(function ($q) use ($viewerId) {
                    $q->where('status', 'aktif')
                        ->orWhere(function ($qq) use ($viewerId) {
                            // pemilik komentar tetap melihat komentarnya yang "ditandai"
                            $qq->where('status', 'ditandai')
                                ->where('user_id', $viewerId);
                        });
                });
            } else {
                // Guest: hanya komentar aktif
                $commentsQuery->where('status', 'aktif');
            }
        }

        $comments = $commentsQuery->get();

        return view('artworks.show', [
            'artwork'            => $artwork,
            'comments'           => $comments,
            'creatorBg'          => $fotoCreator,
            'creatorName'        => $creatorName,
            'creatorUsername'    => $creatorUsername,
            'userHasLiked'       => $userHasLiked,
            'userHasBookmarked'  => $userHasBookmarked,
            'user'               => $creator,
            'isFollowingCreator' => $isFollowing,
            'followersCount'     => $followersCount,
            'followingsCount'    => $followingsCount,
            'moderation'         => $moderation,
            'loginUser'          => $loginUser,
            'isOwner'            => $isOwner,
            'isAdmin'            => $isAdmin,
        ]);
    }


    // Halaman statistik / insight untuk pemilik artwork
    public function statistic(Artwork $artwork)
    {
        // Hanya pemilik yang boleh akses
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke statistik artwork ini.');
        }

        // Load kategori + statistik + hitung relasi
        $artwork->load([
            'kategori',
            'stat',
        ])->loadCount([
            'likes',
            'comments',
            'favorites',
        ]);

        // Statistik view (dari tabel statistics)
        $stat = $artwork->stat ?? new Statistic([
            'artwork_id'       => $artwork->artwork_id,
            'jumlah_like'      => 0,
            'jumlah_share'     => 0,
            'jumlah_komentar'  => 0,
            'jumlah_favorit'   => 0,
            'jumlah_view'      => 0,
        ]);

        // Hitungan interaksi dari relasi
        $likesCount     = $artwork->likes_count ?? 0;
        $commentsCount  = $artwork->comments_count ?? 0;
        $favoritesCount = $artwork->favorites_count ?? 0;

        return view('artworks.statistic', [
            'artwork'        => $artwork,
            'stat'           => $stat,
            'likesCount'     => $likesCount,
            'commentsCount'  => $commentsCount,
            'favoritesCount' => $favoritesCount,
        ]);
    }
}
