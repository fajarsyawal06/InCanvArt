<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Artwork;

class CommentController extends Controller
{
    // 🔹 Simpan komentar
    public function store(Request $request, Artwork $artwork)
    {
        $validated = $request->validate([
            'isi_komentar' => 'required|string|max:5000',
            'parent_comment_id' => 'nullable|integer|exists:comments,comment_id',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'artwork_id' => $artwork->artwork_id, // atau $artwork->id jika PK-nya 'id'
            'isi_komentar' => $validated['isi_komentar'],
            'parent_comment_id' => $validated['parent_comment_id'] ?? null,
        ]);

        return back();
    }

    // 🔹 Hapus komentar
    public function destroy(Comment $comment)
    {
        // Pastikan hanya pemilik komentar atau admin yang boleh hapus
        if (Auth::id() !== $comment->user_id && !(Auth::user() && Auth::user()->role === 'admin')) {
            abort(403, 'Tidak diizinkan menghapus komentar ini.');
        }

        // Hapus komentar (dan anak-anaknya jika perlu)
        // Jika belum pakai foreign key cascade:
        // foreach ($comment->children as $child) $child->delete();

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
