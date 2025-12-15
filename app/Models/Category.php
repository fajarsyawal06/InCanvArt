<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'categories';

    // Primary key
    protected $primaryKey = 'kategori_id';

    // Kolom yang bisa diisi secara massal (fillable)
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    // Menonaktifkan timestamps default Laravel (karena tabel kamu tidak punya created_at & updated_at)
    public $timestamps = false;

    public function artworks()
    {
        // FK di artworks = kategori_id, PK di categories = kategori_id
        return $this->hasMany(Artwork::class, 'kategori_id', 'kategori_id');
    }
}
