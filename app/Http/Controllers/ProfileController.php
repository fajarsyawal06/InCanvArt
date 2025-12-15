<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Follow;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Halaman profil milik user yang login + daftar karya user
     */
    public function index()
    {
        $user = Auth::user();

        $artworks = Artwork::visibleFor(Auth::user())
            ->where('user_id', $user->user_id)
            ->orderBy('tanggal_upload', 'desc')
            ->paginate(24);

        $this->truncateArtworkCollection($artworks);

        $profile = $this->getOrCreateProfile($user);

        $followersCount = Follow::where('following_id', $user->user_id)->count();
        $followingCount = Follow::where('follower_id', $user->user_id)->count();

        return view('profiles.index', compact(
            'artworks',
            'user',
            'profile',
            'followersCount',
            'followingCount'
        ));
    }

    /**
     * Form edit profil
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $this->getOrCreateProfile($user);

        $kontak    = is_array($profile->kontak) ? $profile->kontak : [];
        $instagram = $kontak['instagram'] ?? '';
        $twitter   = $kontak['twitter'] ?? '';
        $facebook  = $kontak['facebook'] ?? '';

        // Karena DB kita simpan sebagai URL publik (/storage/avatars/..),
        // maka cukup pakai asset().
        $profileUrl = $profile->foto_profil
            ? asset(ltrim($profile->foto_profil, '/'))
            : null;

        $coverUrl = $profile->foto_cover
            ? asset(ltrim($profile->foto_cover, '/'))
            : null;

        return view('profiles.edit', compact(
            'user',
            'profile',
            'instagram',
            'twitter',
            'facebook',
            'profileUrl',
            'coverUrl'
        ));
    }

    /**
     * Update profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username'            => 'required|string|max:60|unique:users,username,' . $user->user_id . ',user_id',
            'nama_lengkap'        => 'nullable|string|max:120',
            'bio'                 => 'nullable|string|max:2000',
            'cropped_foto_profil' => 'nullable|string', // base64 dari cropper
            'cropped_foto_cover'  => 'nullable|string', // base64 dari cropper
            'instagram'           => 'nullable|string',
            'twitter'             => 'nullable|string',
            'facebook'            => 'nullable|string',
        ]);

        $profile = $this->getOrCreateProfile($user);

        $fotoProfilUrl = $profile->foto_profil; // contoh: /storage/avatars/xxx.jpg
        $fotoCoverUrl  = $profile->foto_cover;  // contoh: /storage/covers/xxx.jpg

        /**
         * FOTO PROFIL (400x400)
         */
        if ($request->filled('cropped_foto_profil')) {
            // hapus lama (fisik file)
            $this->deleteByPublicUrl($fotoProfilUrl);

            $savedUrl = $this->saveBase64ImageToPublicRoot(
                $request->input('cropped_foto_profil'),
                'avatars',
                'ava_',
                400,
                400
            );

            if ($savedUrl) {
                $fotoProfilUrl = $savedUrl;
            }
        }

        /**
         * FOTO COVER (1600x400)
         */
        if ($request->filled('cropped_foto_cover')) {
            $this->deleteByPublicUrl($fotoCoverUrl);

            $savedUrl = $this->saveBase64ImageToPublicRoot(
                $request->input('cropped_foto_cover'),
                'covers',
                'cover_',
                1600,
                400
            );

            if ($savedUrl) {
                $fotoCoverUrl = $savedUrl;
            }
        }

        /**
         * KONTAK
         */
        $kontak = array_filter([
            'instagram' => $request->instagram,
            'twitter'   => $request->twitter,
            'facebook'  => $request->facebook,
        ], fn($v) => filled($v));

        // Update PROFILE
        $profile->update([
            'nama_lengkap' => $request->nama_lengkap,
            'bio'          => $request->bio,
            'foto_profil'  => $fotoProfilUrl,
            'foto_cover'   => $fotoCoverUrl,
            'kontak'       => $kontak ?: null,
        ]);

        // Update USERNAME di tabel users
        if ($user->username !== $request->username) {
            $user->username = $request->username;
            $user->save();
        }

        return redirect()
            ->route('profiles.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Simpan base64 image ke folder publik ROOT (public_html/storage/{folder})
     * dan return URL publik: /storage/{folder}/file.jpg
     */
    protected function saveBase64ImageToPublicRoot(
        ?string $base64,
        string $folder,
        string $prefix,
        int $width,
        int $height
    ): ?string {
        if (!$base64) {
            return null;
        }

        // format: data:image/jpeg;base64,xxxx
        if (strpos($base64, ',') !== false) {
            [, $base64] = explode(',', $base64, 2);
        }

        $data = base64_decode($base64);
        if ($data === false) {
            return null;
        }

        $image = Image::make($data)
            ->fit($width, $height, function ($c) {
                $c->upsize();
            })
            ->encode('jpg', 90);

        // Pastikan folder ada: public_html/storage/{folder}
        $dir = base_path('storage/' . $folder);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = uniqid($prefix, true) . '.jpg';

        // Simpan file fisik
        File::put($dir . '/' . $filename, (string) $image);

        // Return URL publik untuk disimpan ke DB
        return '/storage/' . $folder . '/' . $filename;
    }

    /**
     * Hapus file fisik berdasarkan URL publik (/storage/...)
     */
    protected function deleteByPublicUrl(?string $publicUrl): void
    {
        if (!$publicUrl) return;

        // contoh: /storage/avatars/xxx.jpg -> public_html/storage/avatars/xxx.jpg
        $fullPath = base_path(ltrim($publicUrl, '/'));
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    /**
     * PROFIL PUBLIK CREATOR (user lain, dipanggil dari /profile/{user})
     */
    public function show($userId)
    {
        $creator = User::where('user_id', $userId)->firstOrFail();
        $creatorProfile = $this->getOrCreateProfile($creator);

        $viewer = Auth::user();

        $creatorArtworks = Artwork::with('kategori')
            ->visibleFor($viewer)
            ->where('user_id', $creator->user_id)
            ->orderByDesc('tanggal_upload')
            ->paginate(24);

        $this->truncateArtworkCollection($creatorArtworks);

        $creatorFollowersCount = Follow::where('following_id', $creator->user_id)->count();
        $creatorFollowingCount = Follow::where('follower_id', $creator->user_id)->count();

        $isFollowing = $viewer
            ? $viewer->isFollowing($creator)
            : false;

        return view('profiles.show', [
            'creator'               => $creator,
            'creatorProfile'        => $creatorProfile,
            'creatorArtworks'       => $creatorArtworks,
            'creatorFollowersCount' => $creatorFollowersCount,
            'creatorFollowingCount' => $creatorFollowingCount,
            'isFollowing'           => $isFollowing,
        ]);
    }

    /**
     * Helper: ambil atau buat Profile untuk user tertentu.
     */
    protected function getOrCreateProfile(User $user): Profile
    {
        return Profile::firstOrCreate([
            'user_id' => $user->user_id,
        ]);
    }

    /**
     * Helper: potong deskripsi artwork di dalam Paginator.
     */
    protected function truncateArtworkCollection($paginator): void
    {
        $paginator->getCollection()->transform(function ($art) {
            if (!empty($art->deskripsi)) {
                $art->deskripsi = Str::words($art->deskripsi, 15, '...');
            }
            return $art;
        });
    }
}
