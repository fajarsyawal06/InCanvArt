<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    // Nama tabel (jika tidak mengikuti konvensi plural)
    protected $table = 'profiles';

    // Primary key
    protected $primaryKey = 'profile_id';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'bio',
        'foto_profil',
        'foto_cover',
        'kontak',
    ];

    public $timestamps = false;
    protected $casts = [
        'kontak' => 'array', // penting agar otomatis array <-> json
    ];
    // Relasi opsional (kalau nanti kamu ingin hubungan dengan user dan kategori)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
