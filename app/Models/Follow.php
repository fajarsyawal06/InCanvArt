<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'follows';

    // Primary key
    protected $primaryKey = 'follow_id';

    // Kolom yang bisa diisi secara massal (fillable)
    protected $fillable = [
        'follower_id',
        'following_id',
        'tanggal_follow',
    ];

    // Menonaktifkan timestamps default Laravel (karena tabel kamu tidak punya created_at & updated_at)
    public $timestamps = false;

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
