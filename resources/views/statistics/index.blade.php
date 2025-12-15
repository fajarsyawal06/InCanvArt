<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>
    <title>Statistik Artwork</title>
    <link rel="stylesheet" href="{{ asset('css/showStatistic.css') }}">
</head>

<body>
    <x-navbar></x-navbar>

    <div class="sm:mt-16">
        <div class="stats-wrap">

            <h1 class="stats-title"">Statistik Artwork</h1>

            {{-- ===================== DETAIL STATISTIK PER ARTWORK ===================== --}}
            <section class=" stats-section">
                <h2 class="stats-section-title">Detail Statistik per Artwork</h2>
                {{-- Tabel tanpa scroll internal, full + pagination Laravel --}}
                <div class="stats-main-table">
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Artwork</th>
                                <th>Likes</th>
                                <th>Share</th>
                                <th>Komentar</th>
                                <th>Favorite</th>
                                <th>Views</th>
                                <th>Engagement (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($artworks as $index => $art)
                            <tr>
                                <td>{{ $artworks->firstItem() + $index }}</td>
                                <td class="art-info">
                                    <div style="display:flex; gap:.75rem; align-items:center;">
                                        <img src="{{ $art->file_url }}" class="thumb-img">
                                        <div>
                                            <span class="art-title">
                                                <a href="{{ route('artworks.show', $art->slug) }}">
                                                    {{ $art->judul }}
                                                </a>
                                            </span>
                                            <span class="art-meta">
                                                {{ $art->kategori->nama_kategori ?? 'Tanpa kategori' }} ·
                                                oleh {{ $art->user->name ?? $art->user->username ?? 'User' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $art->likes_count }}</td>
                                <td>{{ $art->shares_count }}</td>
                                <td>{{ $art->comments_count }}</td>
                                <td>{{ $art->favorites_count }}</td>
                                <td>{{ $art->stat->jumlah_view ?? 0 }}</td>
                                <td>
                                    @php
                                    $views = $art->stat->jumlah_view ?? 0;
                                    $interaksi = $art->likes_count
                                    + $art->comments_count
                                    + $art->favorites_count
                                    + $art->shares_count;
                                    echo number_format($views ? ($interaksi / $views) * 100 : 0, 2);
                                    @endphp
                                    %
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">Belum ada data artwork.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination bawaan Laravel --}}
                <div class="stats-main-pagination">
                    {{ $artworks->onEachSide(1)->links('components.pagination') }}
                </div>

                </section>
                {{-- ===================== RINGKASAN GLOBAL ===================== --}}
                <section class="stats-section stats-overview">
                    <h2 class="stats-section-title">Ringkasan Global</h2>

                    <div class="stats-grid">
                        <div class="stats-card">
                            <span class="stats-card-label">Total Artwork</span>
                            <span class="stats-card-value">{{ $totalArtworks }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Views</span>
                            <span class="stats-card-value">{{ $totalViews }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Likes</span>
                            <span class="stats-card-value">{{ $totalLikes }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Komentar</span>
                            <span class="stats-card-value">{{ $totalComments }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Favorite</span>
                            <span class="stats-card-value">{{ $totalFavorites }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Share</span>
                            <span class="stats-card-value">{{ $totalShares }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Seniman</span>
                            <span class="stats-card-value">{{ $totalArtists }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Pengguna</span>
                            <span class="stats-card-value">{{ $totalUsers }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Total Pengunjung</span>
                            <span class="stats-card-value">{{ $totalVisitors }}</span>
                        </div>
                        <div class="stats-card">
                            <span class="stats-card-label">Engagement Rate Global</span>
                            <span class="stats-card-value">{{ number_format($globalEngagementRate, 2) }}%</span>
                        </div>
                    </div>
                </section>

                {{-- ===================== INSIGHT CEPAT (CARD VERSION) ===================== --}}
                <section class="stats-section">
                    <h2 class="stats-section-title">Insight Cepat</h2>

                    <div class="insight-grid">

                        {{-- TOP LIKES --}}
                        <div class="insight-card">
                            <h3 class="insight-title">Artwork Terpopuler (Likes)</h3>
                            <ul>
                                @foreach ($topByLikes as $art)
                                <li>{{ $art->judul }} — {{ $art->likes_count }} likes</li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- TOP FAVORITE --}}
                        <div class="insight-card">
                            <h3 class="insight-title">Paling Difavoritkan</h3>
                            <ul>
                                @foreach ($topByFavorites as $art)
                                <li>{{ $art->judul }} — {{ $art->favorites_count }} favorit</li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- TOP VIEWS --}}
                        <div class="insight-card">
                            <h3 class="insight-title">Paling Banyak Dilihat</h3>
                            <ul>
                                @foreach ($topByViews as $art)
                                <li>{{ $art->judul }} — {{ $art->stat->jumlah_view }} views</li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- TOP KATEGORI --}}
                        <div class="insight-card">
                            <h3 class="insight-title">Kategori Terbanyak</h3>
                            <ul>
                                @foreach ($topCategories as $cat)
                                <li>{{ $cat->nama_kategori }} — {{ $cat->artworks_count }} artwork</li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- TOP ARTISTS --}}
                        <div class="insight-card">
                            <h3 class="insight-title">Seniman Paling Aktif</h3>
                            <ul>
                                @foreach ($topArtists as $artist)
                                <li>{{ $artist->name ?? $artist->username }} — {{ $artist->artworks_count }} karya</li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </section>



                {{-- ===================== TREND BULANAN ===================== --}}
                <section class="stats-section stats-monthly">
                    <h2 class="stats-section-title">Tren Bulanan</h2>

                    <table class="stats-table stats-table-small">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Artwork Diunggah</th>
                                <th>Total Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthlyStats as $row)
                            <tr>
                                <td>{{ $row->month_label }}</td>
                                <td>{{ $row->uploads }}</td>
                                <td>{{ $row->views }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">Belum ada data tren bulanan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    
                </section>
        </div>
        <a href="{{ route('statistic.export') }}"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Export PDF
                    </a>

    </div>

</body>

</html>