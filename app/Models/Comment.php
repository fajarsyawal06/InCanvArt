<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'comments';

    // Primary key
    protected $primaryKey = 'comment_id';

    // Kolom yang bisa diisi secara massal (fillable)
    protected $fillable = [
        'user_id',
        'artwork_id',
        'isi_komentar',
        'parent_comment_id',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];
    // Menonaktifkan timestamps default Laravel (karena tabel kamu tidak punya created_at & updated_at)
    public $timestamps = false;

    // Relasi

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class, 'artwork_id', 'artwork_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id', 'comment_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_comment_id', 'comment_id')
            ->orderBy('tanggal', 'asc')
            ->with(['user', 'children']);
    }
}
