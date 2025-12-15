<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'shares';

    // Primary key
    protected $primaryKey = 'share_id';

    // Kolom yang bisa diisi secara massal (fillable)
    protected $fillable = [
        'artwork_id',
        'user_id',
        'tanggal_share',
    ];

    // Menonaktifkan timestamps default Laravel (karena tabel kamu tidak punya created_at & updated_at)
    public $timestamps = false;

    public function artwork()
    {
        return $this->belongsTo(Artwork::class, 'artwork_id', 'artwork_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
