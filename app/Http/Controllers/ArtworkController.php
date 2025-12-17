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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ArtworkController extends Controller
{
    // =========================
    // Helper: folder & file ops
    // =========================
    private function artworksPublicDir(): string
    {
        // Karena webroot kamu public_html, folder publik untuk URL /storage/... adalah:
        // public_html/storage/artworks
        return base_path('storage/artworks');
    }

    private function ensureArtworksPublicDirExists(): void
    {
        $dir = $this->artworksPublicDir();
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
    }

    private function makeArtworkFilename(\Illuminate\Http\UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        return (string) Str::uuid() . '.' . $ext;
    }

    private function publicUrlForArtwork(string $filename): string
    {
        // URL publik yang dipakai browser
        return '/storage/artworks/' . $filename;
    }

    private function deleteArtworkFileByUrl(?string $fileUrl): void
    {
        if (!$fileUrl) return;

        // file_url format: /storage/artworks/xxxx.jpg
        // Karena webroot public_html, path fisik file adalah base_path('storage/artworks/...')
        $fullPath = base_path(ltrim($fileUrl, '/')); // => public_html/storage/artworks/xxxx.jpg
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    // =====================
    // Menampilkan semua data
    // =====================
    public function index()
    {
        $loginUser = Auth::user();

        $artworks = Artwork::with(['kategori', 'user.profile'])
            ->visibleFor($loginUser)
            ->inRandomOrder()
            ->orderByDesc('tanggal_upload')
            ->paginate(24);

        $artworks->getCollection()->transform(function ($art) {
            if (!empty($art->deskripsi)) {
                $art->deskripsi = \Illuminate\Support\Str::words($art->deskripsi, 15, '...');
            }
            return $art;
        });

        return view('artworks.index', compact('artworks'));
    }

    // ==========
    // Form tambah
    // ==========
    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('artworks.create', compact('categories'));
    }

    // =================
    // Simpan data baru
    // =================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'file'         => 'required|image|mimes:jpg,jpeg,png',
            'kategori_id'  => 'required|exists:categories,kategori_id',
        ]);

        // Pastikan folder tujuan ada: public_html/storage/artworks
        $this->ensureArtworksPublicDirExists();

        // Simpan file langsung ke public_html/storage/artworks
        $file     = $request->file('file');
        $filename = $this->makeArtworkFilename($file);
        $file->move($this->artworksPublicDir(), $filename);

        // Simpan URL publik ke DB
        $fileUrl = $this->publicUrlForArtwork($filename);

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

    // =========
    // Form edit
    // =========
    public function edit(Artwork $artwork)
    {
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit artwork ini.');
        }

        $categories = Category::orderBy('nama_kategori')->get();
        return view('artworks.edit', compact('artwork', 'categories'));
    }

    // ===========
    // Update data
    // ===========
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
            // Hapus file lama (public_html/storage/artworks/...)
            $this->deleteArtworkFileByUrl($artwork->file_url);

            // Pastikan folder tujuan ada
            $this->ensureArtworksPublicDirExists();

            // Simpan file baru
            $file     = $request->file('file');
            $filename = $this->makeArtworkFilename($file);
            $file->move($this->artworksPublicDir(), $filename);

            // Update URL publik
            $data['file_url'] = $this->publicUrlForArtwork($filename);
        }

        $artwork->update($data);

        return redirect()
            ->route('artworks.show', $artwork)
            ->with('success', 'Artwork berhasil diperbarui.');
    }

    // ==========
    // Hapus data
    // ==========
    public function destroy(Artwork $artwork)
    {
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus artwork ini.');
        }

        // Hapus file fisik (kalau ada)
        $this->deleteArtworkFileByUrl($artwork->file_url);

        $artwork->delete();

        return redirect()
            ->route('profiles.index')
            ->with('success', 'Artwork berhasil dihapus.');
    }

    // ================================
    // Detail artwork + follow/like/bookmark
    // ================================
    public function show(Artwork $artwork)
    {
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

        $artwork->load([
            'user.profile',
            'kategori',
            'comments.user.profile',
            'stat'
        ])->loadCount('likes');

        $loginUser = Auth::user();
        $isOwner   = $loginUser && $loginUser->user_id === $artwork->user_id;
        $isAdmin   = $loginUser && $loginUser->role === 'admin';

        if (!in_array($artwork->status, ['aktif']) && !$isOwner && !$isAdmin) {
            abort(404);
        }

        $creator     = $artwork->user;
        $creatorProf = $creator?->profile;

        // Catatan: ini masih pakai asset('storage/...'), pastikan foto profil juga disimpan di folder publik yg sama
        if ($creatorProf?->foto_profil) {
            // support: "/storage/..", "storage/..", "profiles/..", "/profiles/.."
            $path = ltrim($creatorProf->foto_profil, '/');

            if (str_starts_with($path, 'storage/')) {
                // sudah ada "storage/"
                $fotoCreator = asset($path);
            } elseif (str_starts_with($path, 'public/')) {
                // kalau tersimpan "public/xxx", jadikan "storage/xxx"
                $fotoCreator = asset('storage/' . ltrim(substr($path, 7), '/'));
            } else {
                // anggap relative di bawah storage
                $fotoCreator = asset('storage/' . $path);
            }
        } elseif ($creator?->avatar) {
            $fotoCreator = $creator->avatar;
        } else {
            $fotoCreator = asset('images/avatar-sample.jpg');
        }


        $creatorName     = $creatorProf?->nama_lengkap ?? $creator?->username ?? 'Pengguna';
        $creatorUsername = '@' . ($creator?->username ?? Str::slug($creatorName));

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

        $isFollowing     = auth()->check() ? auth()->user()->isFollowing($creator) : false;
        $followersCount  = $creator ? $creator->followers()->count() : 0;
        $followingsCount = $creator ? $creator->followings()->count() : 0;

        $moderation = Moderation::where('target_type', 'artwork')
            ->where('target_id', $artwork->artwork_id)
            ->latest('tanggal')
            ->first();

        $commentsQuery = Comment::where('artwork_id', $artwork->artwork_id)
            ->whereNull('parent_comment_id')
            ->orderByDesc('tanggal');

        if (!$isAdmin) {
            if ($loginUser) {
                $viewerId = $loginUser->user_id;

                $commentsQuery->where(function ($q) use ($viewerId) {
                    $q->where('status', 'aktif')
                        ->orWhere(function ($qq) use ($viewerId) {
                            $qq->where('status', 'ditandai')
                                ->where('user_id', $viewerId);
                        });
                });
            } else {
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

    // ===========================
    // Halaman statistik / insight
    // ===========================
    public function statistic(Artwork $artwork)
    {
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke statistik artwork ini.');
        }

        $artwork->load([
            'kategori',
            'stat',
        ])->loadCount([
            'likes',
            'comments',
            'favorites',
        ]);

        $stat = $artwork->stat ?? new Statistic([
            'artwork_id'       => $artwork->artwork_id,
            'jumlah_like'      => 0,
            'jumlah_share'     => 0,
            'jumlah_komentar'  => 0,
            'jumlah_favorit'   => 0,
            'jumlah_view'      => 0,
        ]);

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
