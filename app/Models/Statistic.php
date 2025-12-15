<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Statistic extends Model
{
    use HasFactory;

    // Nama tabel (jika tidak mengikuti konvensi plural)
    protected $table = 'statistics';

    // Primary key
    protected $primaryKey = 'stat_id';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'artwork_id',
        'jumlah_like',
        'jumlah_share',
        'jumlah_komentar',
        'jumlah_favorit',
        'jumlah_view',
        'terakhir_update',
    ];

    public $timestamps = false;
    
    public function artwork(){
        return $this->belongsTo(Artwork::class, 'artwork_id', 'artwork_id');
    }
}
