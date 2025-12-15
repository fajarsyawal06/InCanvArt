<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Artwork extends Model
{
    use HasFactory;

    // Nama tabel (jika tidak mengikuti konvensi plural)
    protected $table = 'artworks';

    // Primary key
    protected $primaryKey = 'artwork_id';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'user_id',
        'kategori_id',
        'slug',
        'judul',
        'deskripsi',
        'file_url',
        'tanggal_upload',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artwork) {
            $artwork->slug = Str::slug($artwork->judul) . '-' . Str::random(6);
        });

        static::updating(function ($artwork) {
            if ($artwork->isDirty('judul')) {
                $artwork->slug = Str::slug($artwork->judul) . '-' . Str::random(6);
            }
        });
    }

    // Jika tabel tidak pakai timestamps (created_at, updated_at)
    public $timestamps = false;

    // Relasi opsional (kalau nanti kamu ingin hubungan dengan user dan kategori)
    public function user()
    {
        // FK di artworks = user_id, PK di users = user_id
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Category::class, 'kategori_id', 'kategori_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'artwork_id', 'artwork_id')
            ->whereNull('parent_comment_id')
            ->orderBy('tanggal', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'artwork_id', 'artwork_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'artwork_id', 'artwork_id');
    }

    public function shares()
    {
        return $this->hasMany(Share::class, 'artwork_id', 'artwork_id');
    }

    public function scopeVisibleFor(Builder $query, $user = null)
    {
        $user = $user ?: Auth::user();

        // Admin → lihat semua
        if ($user && $user->role === 'admin') {
            return $query;
        }

        $userId = $user ? $user->user_id : null;

        return $query->where(function ($q) use ($userId) {
            // Publik → hanya status 'aktif'
            $q->where('status', 'aktif');

            // Pemilik → boleh lihat semua karyanya sendiri
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        });
    }
    public function stat()
    {
        return $this->hasOne(Statistic::class, 'artwork_id', 'artwork_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
