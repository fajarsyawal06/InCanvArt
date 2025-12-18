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
         * -------------------------------------------------------------- */
        $artworks = Artwork::with(['user', 'kategori', 'stat'])
            ->withCount(['likes', 'shares', 'comments', 'favorites'])
            ->orderBy('tanggal_upload', 'desc')
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

        // Top 5 Likes
        $topByLikes = Artwork::withCount('likes')
            ->orderByDesc('likes_count')
            ->take(5)
            ->get();

        // Top 5 Favorites
        $topByFavorites = Artwork::withCount('favorites')
            ->orderByDesc('favorites_count')
            ->take(5)
            ->get();

        // Top 5 Views — FIX (subquery orderByDesc)
        $topByViews = Artwork::with('stat')
            ->whereHas('stat')
            ->orderByDesc(
                Statistic::select('jumlah_view')
                    ->whereColumn('statistics.artwork_id', 'artworks.artwork_id')
                    ->limit(1)
            )
            ->take(5)
            ->get();

        /* --------------------------------------------------------------
         * 4. TOP CATEGORY
         * -------------------------------------------------------------- */
        $topCategories = Category::withCount('artworks')
            ->orderByDesc('artworks_count')
            ->take(5)
            ->get();

        /* --------------------------------------------------------------
         * 5. TOP SENIMAN
         * -------------------------------------------------------------- */
        $topArtists = User::where('role', 'seniman')
            ->withCount('artworks')
            ->orderByDesc('artworks_count')
            ->take(5)
            ->get();

        /* --------------------------------------------------------------
         * 6. TREND HARIAN (30 hari terakhir)
         * -------------------------------------------------------------- */
        $dailyStats = DB::table('artworks')
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->selectRaw("
                DATE(artworks.tanggal_upload) AS day_key,
                DATE_FORMAT(artworks.tanggal_upload, '%d %M %Y') AS day_label,
                COUNT(artworks.artwork_id) AS uploads,
                COALESCE(SUM(statistics.jumlah_view), 0) AS views
            ")
            ->groupBy('day_key', 'day_label')
            ->orderBy('day_key', 'desc')
            ->limit(30)
            ->get();

        /* --------------------------------------------------------------
         * 7. TREND BULANAN (12 bulan terakhir)
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

        /* --------------------------------------------------------------
         * 8. TREND TAHUNAN (5 tahun terakhir)
         * -------------------------------------------------------------- */
        $yearlyStats = DB::table('artworks')
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->selectRaw("
                YEAR(artworks.tanggal_upload) AS year_key,
                CONCAT(YEAR(artworks.tanggal_upload)) AS year_label,
                COUNT(artworks.artwork_id) AS uploads,
                COALESCE(SUM(statistics.jumlah_view), 0) AS views
            ")
            ->groupBy('year_key', 'year_label')
            ->orderBy('year_key', 'desc')
            ->limit(5)
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
            'dailyStats',
            'monthlyStats',
            'yearlyStats',
            'topByLikes',
            'topByFavorites',
            'topByViews',
            'topCategories',
            'topArtists'
        ));
    }

    public function exportPdf()
    {
        /**
         * DATA PER ARTWORK
         * - Seniman: dari user (username / profile)
         * - Interaksi: dari relasi counts (likes/comments/favorites/shares)
         * - View: dari tabel statistics via relasi stat
         */
        $stats = Artwork::with(['user.profile', 'stat'])
            ->withCount(['likes', 'comments', 'favorites', 'shares'])
            ->orderBy('tanggal_upload', 'desc')
            ->get()
            ->map(function ($a) {
                return (object) [
                    'artwork'         => $a,
                    'jumlah_view'     => (int) (optional($a->stat)->jumlah_view ?? 0),
                    'jumlah_like'     => (int) $a->likes_count,
                    'jumlah_komentar' => (int) $a->comments_count,
                    'jumlah_favorit'  => (int) $a->favorites_count,
                    'jumlah_share'    => (int) $a->shares_count,
                ];
            });

        // RINGKASAN GLOBAL
        $global = [
            'total_artwork'  => $stats->count(),
            'total_view'     => $stats->sum('jumlah_view'),
            'total_like'     => $stats->sum('jumlah_like'),
            'total_comment'  => $stats->sum('jumlah_komentar'),
            'total_favorite' => $stats->sum('jumlah_favorit'),
            'total_share'    => $stats->sum('jumlah_share'),
        ];

        // INSIGHT CEPAT
        $insight = [
            'most_viewed'  => $stats->sortByDesc('jumlah_view')->first(),
            'most_liked'   => $stats->sortByDesc('jumlah_like')->first(),
            'most_comment' => $stats->sortByDesc('jumlah_komentar')->first(),
            'most_share'   => $stats->sortByDesc('jumlah_share')->first(),
        ];

        /**
         * TREND BULANAN (12 bulan terakhir) - ASC biar enak dibaca
         * (likes/komentar/favorit/share diset 0 dulu seperti versi kamu)
         */
        $monthly = DB::table('artworks')
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->selectRaw("
                DATE_FORMAT(artworks.tanggal_upload, '%Y-%m') AS month_key,
                DATE_FORMAT(artworks.tanggal_upload, '%M %Y') AS month_label,
                COUNT(artworks.artwork_id) AS uploads,
                COALESCE(SUM(statistics.jumlah_view), 0) AS views,
                0 AS likes,
                0 AS komentar,
                0 AS favorit,
                0 AS share
            ")
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key', 'asc')
            ->limit(12)
            ->get();

        /**
         * TREND HARIAN (30 hari terakhir) - ASC biar enak dibaca
         */
        $daily = DB::table('artworks')
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->selectRaw("
                DATE(artworks.tanggal_upload) AS day_key,
                DATE_FORMAT(artworks.tanggal_upload, '%d %M %Y') AS day_label,
                COUNT(artworks.artwork_id) AS uploads,
                COALESCE(SUM(statistics.jumlah_view), 0) AS views,
                0 AS likes,
                0 AS komentar,
                0 AS favorit,
                0 AS share
            ")
            ->groupBy('day_key', 'day_label')
            ->orderBy('day_key', 'asc')
            ->limit(30)
            ->get();

        /**
         * TREND TAHUNAN (5 tahun terakhir) - ASC biar enak dibaca
         */
        $yearly = DB::table('artworks')
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->selectRaw("
                YEAR(artworks.tanggal_upload) AS year_key,
                CONCAT(YEAR(artworks.tanggal_upload)) AS year_label,
                COUNT(artworks.artwork_id) AS uploads,
                COALESCE(SUM(statistics.jumlah_view), 0) AS views,
                0 AS likes,
                0 AS komentar,
                0 AS favorit,
                0 AS share
            ")
            ->groupBy('year_key', 'year_label')
            ->orderBy('year_key', 'asc')
            ->limit(5)
            ->get();

        $pdf = Pdf::loadView('pdf.statistic', [
            'stats'        => $stats,
            'global'       => $global,
            'insight'      => $insight,
            'monthly'      => $monthly,
            'daily'        => $daily,
            'yearly'       => $yearly,
            'generated_at' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Statistik-InCanvArt-' . now()->format('Y-m-d') . '.pdf');
    }
}
