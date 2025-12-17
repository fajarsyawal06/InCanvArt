<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik InCanvArt</title>
    <style>
        body{ font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        h1{ font-size: 16px; margin: 0 0 6px; }
        h2{ font-size: 13px; margin: 16px 0 8px; }
        .muted{ color:#555; font-size: 11px; }
        table{ width:100%; border-collapse: collapse; margin-top:8px; }
        th, td{ border:1px solid #333; padding:6px 8px; vertical-align: top; }
        th{ background:#eee; }
        .right{ text-align:right; }
        .center{ text-align:center; }
    </style>
</head>
<body>

    <h1>Laporan Statistik InCanvArt</h1>
    <div class="muted">
        Total data statistik: {{ is_countable($stats) ? count($stats) : 0 }} baris<br>
        Dibuat: {{ isset($generated_at) ? $generated_at->format('d-m-Y H:i') : now()->format('d-m-Y H:i') }}
    </div>

    {{-- ===================== 1. RINGKASAN GLOBAL ===================== --}}
    <h2>1. Ringkasan Global</h2>
    <table>
        <thead>
            <tr>
                <th>Indikator</th>
                <th class="right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Total Artwork</td><td class="right">{{ $global['total_artwork'] ?? 0 }}</td></tr>
            <tr><td>Total View</td><td class="right">{{ $global['total_view'] ?? 0 }}</td></tr>
            <tr><td>Total Like</td><td class="right">{{ $global['total_like'] ?? 0 }}</td></tr>
            <tr><td>Total Komentar</td><td class="right">{{ $global['total_comment'] ?? 0 }}</td></tr>
            <tr><td>Total Favorit</td><td class="right">{{ $global['total_favorite'] ?? 0 }}</td></tr>
            <tr><td>Total Share</td><td class="right">{{ $global['total_share'] ?? 0 }}</td></tr>
        </tbody>
    </table>

    {{-- ===================== 2. INSIGHT CEPAT ===================== --}}
    <h2>2. Insight Cepat</h2>
    <table>
        <thead>
            <tr>
                <th>Jenis Insight</th>
                <th>Judul Artwork</th>
                <th>Seniman</th>
                <th class="right">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php
                $iv = $insight['most_viewed'] ?? null;
                $il = $insight['most_liked'] ?? null;
                $ic = $insight['most_comment'] ?? null;
                $is = $insight['most_share'] ?? null;

                $title = fn($a) => $a->judul ?? '-';
                $artist = fn($a) => $a->user->profile->nama_lengkap
                                ?? $a->user->username
                                ?? '-';
                $views = fn($a) => (int) ($a->stat->jumlah_view ?? 0);
                $likes = fn($a) => (int) ($a->likes_count ?? 0);
                $comments = fn($a) => (int) ($a->comments_count ?? 0);
                $shares = fn($a) => (int) ($a->shares_count ?? 0);
            @endphp

            <tr>
                <td>View Tertinggi</td>
                <td>{{ $iv ? $title($iv) : '-' }}</td>
                <td>{{ $iv ? $artist($iv) : '-' }}</td>
                <td class="right">{{ $iv ? $views($iv) : 0 }}</td>
            </tr>
            <tr>
                <td>Like Tertinggi</td>
                <td>{{ $il ? $title($il) : '-' }}</td>
                <td>{{ $il ? $artist($il) : '-' }}</td>
                <td class="right">{{ $il ? $likes($il) : 0 }}</td>
            </tr>
            <tr>
                <td>Komentar Terbanyak</td>
                <td>{{ $ic ? $title($ic) : '-' }}</td>
                <td>{{ $ic ? $artist($ic) : '-' }}</td>
                <td class="right">{{ $ic ? $comments($ic) : 0 }}</td>
            </tr>
            <tr>
                <td>Share Terbanyak</td>
                <td>{{ $is ? $title($is) : '-' }}</td>
                <td>{{ $is ? $artist($is) : '-' }}</td>
                <td class="right">{{ $is ? $shares($is) : 0 }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ===================== 3. TREN BULANAN ===================== --}}
    <h2>3. Tren Bulanan (12 Bulan Terakhir)</h2>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="right">Upload</th>
                <th class="right">View</th>
                <th class="right">Like</th>
                <th class="right">Komentar</th>
                <th class="right">Favorit</th>
                <th class="right">Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($monthly as $row)
                <tr>
                    <td>{{ $row->month_label ?? '-' }}</td>
                    <td class="right">{{ $row->uploads ?? 0 }}</td>
                    <td class="right">{{ $row->views ?? 0 }}</td>
                    <td class="right">{{ $row->likes ?? 0 }}</td>
                    <td class="right">{{ $row->komentar ?? 0 }}</td>
                    <td class="right">{{ $row->favorit ?? 0 }}</td>
                    <td class="right">{{ $row->share ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada data tren bulanan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===================== 4. DETAIL STATISTIK PER ARTWORK ===================== --}}
    <h2>4. Detail Statistik per Artwork</h2>
    <table>
        <thead>
            <tr>
                <th class="center" style="width:40px;">No</th>
                <th>Judul</th>
                <th>Seniman</th>
                <th class="right">View</th>
                <th class="right">Like</th>
                <th class="right">Komentar</th>
                <th class="right">Favorit</th>
                <th class="right">Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stats as $i => $a)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $a->judul ?? '-' }}</td>
                    <td>{{ $a->user->profile->nama_lengkap ?? $a->user->username ?? '-' }}</td>
                    <td class="right">{{ (int) ($a->stat->jumlah_view ?? 0) }}</td>
                    <td class="right">{{ (int) ($a->likes_count ?? 0) }}</td>
                    <td class="right">{{ (int) ($a->comments_count ?? 0) }}</td>
                    <td class="right">{{ (int) ($a->favorites_count ?? 0) }}</td>
                    <td class="right">{{ (int) ($a->shares_count ?? 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
