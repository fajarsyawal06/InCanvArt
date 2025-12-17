<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use App\Models\User;
use App\Models\Like;
use App\Models\Share;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Statistic;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StatisticController extends Controller
{
    public function index()
    {
        /* --------------------------------------------------------------
         * 1. DATA UTAMA (TABEL PER ARTWORK)
         * - load user + profile agar nama seniman tampil
         * - withCount supaya likes/comments/favorites/shares akurat
         * -------------------------------------------------------------- */
        $artworks = Artwork::query()
            ->with(['user.profile', 'kategori', 'stat'])
            ->withCount(['likes', 'shares', 'comments', 'favorites'])
            ->orderByDesc('tanggal_upload')
            ->paginate(7);

        /* --------------------------------------------------------------
         * 2. RINGKASAN GLOBAL
         * -------------------------------------------------------------- */
        $totalArtworks  = Artwork::count();
        $totalViews     = Statistic::sum('jumlah_view');
        $totalLikes     = Like::count();
        $totalComments  = Comment::count();
        $totalFavorites = Favorite::count();
        $totalShares    = Share::count();

        $totalArtists   = User::where('role', 'seniman')->count();
        $totalUsers     = User::count();
        $totalVisitors  = User::where('role', 'pengunjung')->count();

        $totalInteraksi = $totalLikes + $totalComments + $totalFavorites + $totalShares;

        $globalEngagementRate = $totalViews > 0
            ? ($totalInteraksi / $totalViews) * 100
            : 0;

        /* --------------------------------------------------------------
         * 3. TOP ARTWORK: LIKES, FAVORITE, VIEWS
         * -------------------------------------------------------------- */
        $topByLikes = Artwork::query()
            ->withCount('likes')
            ->orderByDesc('likes_count')
            ->take(5)
            ->get();

        $topByFavorites = Artwork::query()
            ->withCount('favorites')
            ->orderByDesc('favorites_count')
            ->take(5)
            ->get();

        // Top Views (aman pakai join + COALESCE)
        $topByViews = Artwork::query()
            ->with(['stat'])
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->select('artworks.*')
            ->orderByDesc(DB::raw('COALESCE(statistics.jumlah_view, 0)'))
            ->take(5)
            ->get();

        /* --------------------------------------------------------------
         * 4. TOP CATEGORY
         * -------------------------------------------------------------- */
        $topCategories = Category::query()
            ->withCount('artworks')
            ->orderByDesc('artworks_count')
            ->take(5)
            ->get();

        /* --------------------------------------------------------------
         * 5. TOP SENIMAN (load profile supaya nama lengkap bisa dipakai)
         * -------------------------------------------------------------- */
        $topArtists = User::query()
            ->where('role', 'seniman')
            ->with(['profile'])
            ->withCount('artworks')
            ->orderByDesc('artworks_count')
            ->take(5)
            ->get();

        /* --------------------------------------------------------------
         * 6. TREND BULANAN
         * -------------------------------------------------------------- */
        $monthlyStats = DB::table('artworks')
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->selectRaw("
                DATE_FORMAT(artworks.tanggal_upload, '%Y-%m') AS month_key,
                DATE_FORMAT(artworks.tanggal_upload, '%M %Y') AS month_label,
                COUNT(artworks.artwork_id) AS uploads,
                COALESCE(SUM(statistics.jumlah_view), 0) AS views
            ")
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key', 'desc')
            ->limit(12)
            ->get();

        return view('statistics.index', compact(
            'artworks',
            'totalArtworks',
            'totalViews',
            'totalLikes',
            'totalComments',
            'totalFavorites',
            'totalShares',
            'totalArtists',
            'totalUsers',
            'totalVisitors',
            'globalEngagementRate',
            'monthlyStats',
            'topByLikes',
            'topByFavorites',
            'topByViews',
            'topCategories',
            'topArtists'
        ));
    }

    public function exportPdf()
    {
        // Ambil artwork + user(profile) + kategori + view(stat)
        // Hitung like/comment/favorite/share dari tabel asli pakai withCount
        $stats = \App\Models\Artwork::query()
            ->with(['user.profile', 'kategori', 'stat'])
            ->withCount(['likes', 'comments', 'favorites', 'shares'])
            ->orderByDesc('tanggal_upload')
            ->get();

        // Ringkasan global
        $global = [
            'total_artwork'  => $stats->count(),
            'total_view'     => $stats->sum(fn($a) => (int) ($a->stat->jumlah_view ?? 0)),
            'total_like'     => $stats->sum(fn($a) => (int) ($a->likes_count ?? 0)),
            'total_comment'  => $stats->sum(fn($a) => (int) ($a->comments_count ?? 0)),
            'total_favorite' => $stats->sum(fn($a) => (int) ($a->favorites_count ?? 0)),
            'total_share'    => $stats->sum(fn($a) => (int) ($a->shares_count ?? 0)),
        ];

        // Insight cepat
        $insight = [
            'most_viewed'  => $stats->sortByDesc(fn($a) => (int) ($a->stat->jumlah_view ?? 0))->first(),
            'most_liked'   => $stats->sortByDesc(fn($a) => (int) ($a->likes_count ?? 0))->first(),
            'most_comment' => $stats->sortByDesc(fn($a) => (int) ($a->comments_count ?? 0))->first(),
            'most_share'   => $stats->sortByDesc(fn($a) => (int) ($a->shares_count ?? 0))->first(),
        ];

        // Tren bulanan 12 bulan terakhir (pakai collection biar tidak dobel hitung karena join)
        $monthly = $stats
            ->groupBy(fn($a) => \Carbon\Carbon::parse($a->tanggal_upload)->format('Y-m'))
            ->map(function ($items, $monthKey) {
                $label = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('F Y');

                return (object) [
                    'month_key'   => $monthKey,
                    'month_label' => $label,
                    'uploads'     => $items->count(),
                    'views'       => $items->sum(fn($a) => (int) ($a->stat->jumlah_view ?? 0)),
                    'likes'       => $items->sum(fn($a) => (int) ($a->likes_count ?? 0)),
                    'komentar'    => $items->sum(fn($a) => (int) ($a->comments_count ?? 0)),
                    'favorit'     => $items->sum(fn($a) => (int) ($a->favorites_count ?? 0)),
                    'share'       => $items->sum(fn($a) => (int) ($a->shares_count ?? 0)),
                ];
            })
            ->sortBy('month_key')
            ->take(-12)
            ->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.statistic', [
            'stats'        => $stats,      // ini ARTWORK collection
            'global'       => $global,
            'insight'      => $insight,
            'monthly'      => $monthly,
            'generated_at' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Statistik-InCanvArt-' . now()->format('Y-m-d') . '.pdf');
    }
}
