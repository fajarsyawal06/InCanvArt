<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Follow;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Halaman profil milik user yang login + daftar karya user
     */
    public function index()
    {
        $user = Auth::user();

        // Artwork milik user login
        $artworks = Artwork::visibleFor(Auth::user())
            ->where('user_id', $user->user_id)
            ->orderBy('tanggal_upload', 'desc')
            ->paginate(24);

        // Potong deskripsi seperti di index artwork
        $this->truncateArtworkCollection($artworks);

        // Profil user (auto buat jika belum ada)
        $profile = $this->getOrCreateProfile($user);

        // orang yang mengikuti saya
        $followersCount = Follow::where('following_id', $user->user_id)->count();
        // orang yang saya ikuti
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

        // Pastikan kontak berupa array
        $kontak    = is_array($profile->kontak) ? $profile->kontak : [];
        $instagram = $kontak['instagram'] ?? '';
        $twitter   = $kontak['twitter'] ?? '';
        $facebook  = $kontak['facebook'] ?? '';

        // URL foto profil & cover (butuh storage:link)
        $profileUrl = $profile->foto_profil
            ? Storage::url($profile->foto_profil)
            : null;

        $coverUrl = $profile->foto_cover
            ? Storage::url($profile->foto_cover)
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

        $fotoProfilPath = $profile->foto_profil;
        $fotoCoverPath  = $profile->foto_cover;

        /**
         * FOTO PROFIL (400x400)
         */
        if ($request->filled('cropped_foto_profil')) {
            if ($fotoProfilPath && Storage::disk('public')->exists($fotoProfilPath)) {
                Storage::disk('public')->delete($fotoProfilPath);
            }

            $saved = $this->saveBase64Image(
                $request->input('cropped_foto_profil'),
                'avatars',
                'ava_',
                400,
                400
            );

            if ($saved) {
                $fotoProfilPath = $saved;
            }
        }

        /**
         * FOTO COVER (1600x400)
         */
        if ($request->filled('cropped_foto_cover')) {
            if ($fotoCoverPath && Storage::disk('public')->exists($fotoCoverPath)) {
                Storage::disk('public')->delete($fotoCoverPath);
            }

            $saved = $this->saveBase64Image(
                $request->input('cropped_foto_cover'),
                'covers',
                'cover_',
                1600,
                400
            );

            if ($saved) {
                $fotoCoverPath = $saved;
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
            'foto_profil'  => $fotoProfilPath,
            'foto_cover'   => $fotoCoverPath,
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
     * Simpan base64 image ke storage/public dengan ukuran fix.
     */
    protected function saveBase64Image(?string $base64, string $folder, string $prefix, int $width, int $height): ?string
    {
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

        $filename = $folder . '/' . uniqid($prefix, true) . '.jpg';
        Storage::disk('public')->put($filename, (string) $image);

        return $filename;
    }

    /**
     * PROFIL PUBLIK CREATOR (user lain, dipanggil dari /profile/{user})
     */
    public function show($userId)
    {
        // 1. Ambil user pemilik profil
        $creator = User::where('user_id', $userId)->firstOrFail();

        // 2. Profil pemilik
        $creatorProfile = $this->getOrCreateProfile($creator);

        // 3. User yang sedang melihat profil
        $viewer = Auth::user();

        // 4. Semua artwork milik creator, tetapi difilter sesuai moderasi
        $creatorArtworks = Artwork::with('kategori')
            ->visibleFor($viewer)                      // Hormati moderasi
            ->where('user_id', $creator->user_id)      // Pemilik profil
            ->orderByDesc('tanggal_upload')
            ->paginate(24);

        // potong deskripsi
        $this->truncateArtworkCollection($creatorArtworks);

        // 5. Hitung follower & following pemilik profil
        $creatorFollowersCount = Follow::where('following_id', $creator->user_id)->count();
        $creatorFollowingCount = Follow::where('follower_id', $creator->user_id)->count();

        // 6. Viewer follow creator atau tidak
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
