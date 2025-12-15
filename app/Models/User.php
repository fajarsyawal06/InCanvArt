<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'users';

    // Primary key
    protected $primaryKey = 'user_id';

    // Kolom yang bisa diisi secara massal (fillable)
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'status',
        'tanggal_registrasi',
        'token'
    ];

    // Menonaktifkan timestamps default Laravel (karena tabel kamu tidak punya created_at & updated_at)
    public $timestamps = false;
    public function artworks()
    {
        return $this->hasMany(Artwork::class, 'user_id', 'user_id');
    }

    // app/Models/User.php
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'user_id');
    }

    // app/Models/Artwork.php
    public function comments()
    {
        return $this->hasMany(Comment::class, 'artwork_id', 'artwork_id'); // sesuaikan PK artwork
    }

    public function followers()
    {
        // orang yang mengikuti saya
        return $this->hasMany(Follow::class, 'following_id', 'user_id');
    }

    public function followings()
    {
        // orang yang saya ikuti
        return $this->hasMany(Follow::class, 'follower_id', 'user_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->followings()
            ->where('following_id', $user->getKey())
            ->exists();
    }
}
