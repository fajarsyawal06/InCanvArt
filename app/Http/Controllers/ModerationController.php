<?php

namespace App\Http\Controllers;

use App\Models\Moderation;
use App\Models\Artwork;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModerationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $moderasi = Moderation::with(['admin'])
            ->status($status)              // scope status(optional) → filter berdasarkan query ?status=
            ->latest('tanggal')
            ->paginate(20)
            ->appends(['status' => $status]);

        return view('moderations.index', [
            'moderasi' => $moderasi,
            'status'   => $status,
        ]);
    }

    public function show(Moderation $moderation)
    {
        // eager-load admin supaya di blade bisa pakai $data->admin
        $moderation->load('admin');

        $target = null;

        if ($moderation->target_type === 'artwork') {
            // ikutkan user + profile agar bisa tampil di detail
            $target = Artwork::with(['user.profile'])
                ->find($moderation->target_id);
        } elseif ($moderation->target_type === 'comment') {
            // ikutkan user & artwork
            $target = Comment::with(['user', 'artwork'])
                ->find($moderation->target_id);
        }

        return view('moderations.show', [
            'data'   => $moderation,
            'target' => $target,
        ]);
    }

    // Simpan keputusan moderasi (jika admin membuat log langsung)
    public function store(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:artwork,comment',
            'target_id'   => 'required|integer',
            'status'      => 'required|in:disetujui,ditolak,ditandai',
            'alasan'      => 'nullable|string',
        ]);

        // Buat log moderasi oleh admin
        $moderation = Moderation::create([
            'admin_id'    => Auth::id(),      // dibuat oleh admin
            'target_type' => $request->target_type,
            'target_id'   => $request->target_id,
            'status'      => $request->status,
            'alasan'      => $request->alasan,
            'tanggal'     => now(),
        ]);

        // Sinkron ke target
        if ($request->target_type === 'artwork') {
            $artwork = Artwork::find($request->target_id);

            if ($artwork) {
                if ($request->status === 'disetujui') {
                    // laporan disetujui → artwork melanggar → tandai 'ditolak'
                    $artwork->update(['status' => 'ditolak']);
                } elseif ($request->status === 'ditolak') {
                    // laporan ditolak → artwork aman → aktifkan kembali
                    $artwork->update(['status' => 'aktif']);
                }
                // status 'ditandai' → biarkan status artwork apa adanya
            }
        } elseif ($request->target_type === 'comment') {
            $comment = Comment::find($request->target_id);

            if ($comment) {
                if ($request->status === 'disetujui') {
                    // laporan disetujui → komentar dinyatakan melanggar → sembunyikan
                    $comment->update(['status' => 'ditolak']);
                } elseif ($request->status === 'ditolak') {
                    // laporan ditolak → komentar dinyatakan aman → aktifkan kembali
                    $comment->update(['status' => 'aktif']);
                }
                // 'ditandai' → tidak mengubah status, hanya log
            }
        }

        return back()->with('success', 'Keputusan moderasi berhasil disimpan.');
    }


    // Ubah status moderasi (revisi keputusan admin)
    public function update(Request $request, Moderation $moderation)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak,ditandai',
            'alasan' => 'nullable|string',
        ]);

        // SET JUGA admin_id = admin yang sedang login
        $moderation->update([
            'status'   => $request->status,
            'alasan'   => $request->alasan,
            'admin_id' => Auth::id(),
        ]);

        // Sinkronkan ke artwork jika targetnya artwork
        if ($moderation->target_type === 'artwork') {
            $artwork = Artwork::find($moderation->target_id);

            if ($artwork) {
                if ($request->status === 'disetujui') {
                    // laporan disetujui → artwork disembunyikan
                    $artwork->update(['status' => 'ditolak']);
                } elseif ($request->status === 'ditolak') {
                    // laporan ditolak → tampilkan lagi
                    $artwork->update(['status' => 'aktif']);
                }
                // 'ditandai' tidak mengubah status artwork
            }
        }

        if ($moderation->target_type === 'comment') {
            $comment = Comment::find($moderation->target_id);

            if ($comment) {
                if ($request->status === 'disetujui') {
                    // laporan disetujui → komentar melanggar → nonaktifkan
                    $comment->update(['status' => 'ditolak']);
                } elseif ($request->status === 'ditolak') {
                    // laporan ditolak → komentar aman → aktifkan kembali
                    $comment->update(['status' => 'aktif']);
                } elseif ($request->status === 'ditandai') {
                    // tetap dalam proses peninjauan
                    $comment->update(['status' => 'ditandai']);
                }
            }
        }

        return redirect()
            ->route('moderations.show', $moderation->moderation_id)
            ->with('success', 'Status moderasi berhasil diperbarui.');
    }


    public function destroy(Moderation $moderation)
    {
        $moderation->delete();

        return redirect()
            ->route('moderations.index')
            ->with('success', 'Log moderasi berhasil dihapus.');
    }

    // Dipanggil ketika user menekan tombol Report di halaman artwork
    public function reportArtwork(Request $request, Artwork $artwork)
    {
        // Hanya admin yang boleh menggunakan fitur ini
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403); // atau redirect()->back()->with('error', 'Tidak punya akses.');
        }

        $request->validate([
            'alasan' => 'nullable|string|max:500',
        ]);

        Moderation::create([
            'admin_id'    => Auth::id(),           // ADMIN YANG MENANDAI
            'target_type' => 'artwork',
            'target_id'   => $artwork->artwork_id,
            'status'      => 'ditandai',          // status awal: ditandai untuk ditinjau
            'alasan'      => $request->alasan,
            'tanggal'     => now(),
        ]);

        return back()->with('success', 'Artwork berhasil ditandai untuk moderasi.');
    }

    // jangan lupa di atas sudah ada:
    // use App\Models\Comment;

    public function reportComment(Request $request, Comment $comment)
    {
        // pastikan hanya admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'alasan' => 'nullable|string|max:500',
        ]);

        // buat log moderasi
        Moderation::create([
            'admin_id'    => Auth::id(),
            'target_type' => 'comment',
            'target_id'   => $comment->comment_id,
            'status'      => 'ditandai',
            'alasan'      => $request->alasan,
            'tanggal'     => now(),
        ]);

        // tandai komentar sebagai "sedang ditinjau"
        $comment->update([
            'status' => 'ditandai',
        ]);

        return back()->with('success', 'Komentar berhasil ditandai untuk moderasi.');
    }
}
