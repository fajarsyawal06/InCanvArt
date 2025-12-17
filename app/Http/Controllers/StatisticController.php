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
        // STAT PDF: ambil statistik + artwork + user + profile
        $stats = Statistic::query()
            ->with(['artwork.user.profile', 'artwork.kategori'])
            ->whereHas('artwork')
            ->get();

        $global = [
            'total_artwork'   => Artwork::count(),
            'total_view'      => $stats->sum('jumlah_view'),
            'total_like'      => $stats->sum('jumlah_like'),
            'total_comment'   => $stats->sum('jumlah_komentar'),
            'total_favorite'  => $stats->sum('jumlah_favorit'),
            'total_share'     => $stats->sum('jumlah_share'),
        ];

        $insight = [
            'most_viewed'  => $stats->sortByDesc('jumlah_view')->first(),
            'most_liked'   => $stats->sortByDesc('jumlah_like')->first(),
            'most_comment' => $stats->sortByDesc('jumlah_komentar')->first(),
            'most_share'   => $stats->sortByDesc('jumlah_share')->first(),
        ];

        $monthly = DB::table('artworks')
            ->leftJoin('statistics', 'artworks.artwork_id', '=', 'statistics.artwork_id')
            ->selectRaw("
                DATE_FORMAT(artworks.tanggal_upload, '%Y-%m')  AS month_key,
                DATE_FORMAT(artworks.tanggal_upload, '%M %Y')  AS month_label,
                COUNT(artworks.artwork_id)                     AS uploads,
                COALESCE(SUM(statistics.jumlah_view), 0)       AS views,
                COALESCE(SUM(statistics.jumlah_like), 0)       AS likes,
                COALESCE(SUM(statistics.jumlah_komentar), 0)   AS komentar,
                COALESCE(SUM(statistics.jumlah_favorit), 0)    AS favorit,
                COALESCE(SUM(statistics.jumlah_share), 0)      AS share
            ")
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key', 'asc')
            ->limit(12)
            ->get();

        $pdf = Pdf::loadView('pdf.statistic', [
            'stats'        => $stats,
            'global'       => $global,
            'insight'      => $insight,
            'monthly'      => $monthly,
            'generated_at' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Statistik-InCanvArt-' . now()->format('Y-m-d') . '.pdf');
    }
}
