<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moderation extends Model
{
    protected $table = 'moderations';
    protected $primaryKey = 'moderation_id';
    protected $fillable = [
        'admin_id',
        'target_type',
        'target_id',
        'status',
        'alasan',
        'tanggal',
    ];

    public $timestamps = false;

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class, 'target_id')->where('target_type', 'artwork');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'target_id')->where('target_type', 'comment');
    }

    public function scopeStatus($query, $status = null)
    {
        if ($status && in_array($status, ['disetujui', 'ditolak', 'ditandai'])) {
            return $query->where('status', $status);
        }

        return $query;
    }
}
