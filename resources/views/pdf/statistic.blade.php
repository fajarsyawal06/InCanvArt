<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik InCanvArt</title>
    <style>
        * { box-sizing: border-box; }

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color:#111;
        }

        h1,h2,h3{ margin:0 0 6px 0; }
        h1{ font-size: 18px; }
        h2{ font-size: 14px; margin-top: 16px; }
        h3{ font-size: 12px; margin-top: 12px; }

        .small{ font-size: 9px; color:#555; }
        .mb-12{ margin-bottom: 12px; }

        .text-right{ text-align:right; }
        .text-center{ text-align:center; }

        table{
            width:100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,td{
            border:1px solid #444;
            padding:4px 6px;
            vertical-align: top;
        }

        th{
            background:#f0f0f0;
            font-weight:700;
        }

        /* kecilkan tabel tren biar muat */
        .table-small th,
        .table-small td{
            padding:3px 5px;
            font-size:10px;
        }

        /* page break helper */
        .page-break{
            page-break-before: always;
        }
    </style>
</head>

<body>

    <h1>Laporan Statistik InCanvArt</h1>
    <p class="small mb-12">
        Tanggal generate: {{ $generated_at->format('d/m/Y H:i') }}<br>
        Total data statistik: {{ $stats->count() }} baris
    </p>

    {{-- Helper untuk ambil nama seniman --}}
    @php
        $artistName = function ($obj) {
            return data_get($obj, 'artwork.user.profile.nama_lengkap')
                ?: data_get($obj, 'artwork.user.nama')
                ?: data_get($obj, 'artwork.user.name')
                ?: data_get($obj, 'artwork.user.username')
                ?: '-';
        };
    @endphp

    <h2>1. Ringkasan Global</h2>
    <table>
        <tr>
            <th>Indikator</th>
            <th class="text-right">Jumlah</th>
        </tr>
        <tr>
            <td>Total Artwork</td>
            <td class="text-right">{{ number_format($global['total_artwork']) }}</td>
        </tr>
        <tr>
            <td>Total View</td>
            <td class="text-right">{{ number_format($global['total_view']) }}</td>
        </tr>
        <tr>
            <td>Total Like</td>
            <td class="text-right">{{ number_format($global['total_like']) }}</td>
        </tr>
        <tr>
            <td>Total Komentar</td>
            <td class="text-right">{{ number_format($global['total_comment']) }}</td>
        </tr>
        <tr>
            <td>Total Favorit</td>
            <td class="text-right">{{ number_format($global['total_favorite']) }}</td>
        </tr>
        <tr>
            <td>Total Share</td>
            <td class="text-right">{{ number_format($global['total_share']) }}</td>
        </tr>
    </table>

    <h2>2. Insight Cepat</h2>
    <table>
        <tr>
            <th>Jenis Insight</th>
            <th>Judul Artwork</th>
            <th>Seniman</th>
            <th class="text-right">Nilai</th>
        </tr>

        @if($insight['most_viewed'])
        <tr>
            <td>View Tertinggi</td>
            <td>{{ data_get($insight['most_viewed'], 'artwork.judul') ?? '-' }}</td>
            <td>{{ $artistName($insight['most_viewed']) }}</td>
            <td class="text-right">{{ number_format($insight['most_viewed']->jumlah_view) }}</td>
        </tr>
        @endif

        @if($insight['most_liked'])
        <tr>
            <td>Like Tertinggi</td>
            <td>{{ data_get($insight['most_liked'], 'artwork.judul') ?? '-' }}</td>
            <td>{{ $artistName($insight['most_liked']) }}</td>
            <td class="text-right">{{ number_format($insight['most_liked']->jumlah_like) }}</td>
        </tr>
        @endif

        @if($insight['most_comment'])
        <tr>
            <td>Komentar Terbanyak</td>
            <td>{{ data_get($insight['most_comment'], 'artwork.judul') ?? '-' }}</td>
            <td>{{ $artistName($insight['most_comment']) }}</td>
            <td class="text-right">{{ number_format($insight['most_comment']->jumlah_komentar) }}</td>
        </tr>
        @endif

        @if($insight['most_share'])
        <tr>
            <td>Share Terbanyak</td>
            <td>{{ data_get($insight['most_share'], 'artwork.judul') ?? '-' }}</td>
            <td>{{ $artistName($insight['most_share']) }}</td>
            <td class="text-right">{{ number_format($insight['most_share']->jumlah_share) }}</td>
        </tr>
        @endif

        @if(!$insight['most_viewed'] && !$insight['most_liked'] && !$insight['most_comment'] && !$insight['most_share'])
        <tr>
            <td colspan="4" class="text-center">Belum ada insight yang dapat ditampilkan.</td>
        </tr>
        @endif
    </table>

    <h2>3. Tren Tahunan</h2>
    <table class="table-small">
        <tr>
            <th>Tahun</th>
            <th class="text-right">Upload</th>
            <th class="text-right">View</th>
            <th class="text-right">Like</th>
            <th class="text-right">Komentar</th>
            <th class="text-right">Favorit</th>
            <th class="text-right">Share</th>
        </tr>
        @forelse($yearly as $row)
        <tr>
            <td>{{ $row->year_label }}</td>
            <td class="text-right">{{ number_format($row->uploads) }}</td>
            <td class="text-right">{{ number_format($row->views) }}</td>
            <td class="text-right">{{ number_format($row->likes) }}</td>
            <td class="text-right">{{ number_format($row->komentar) }}</td>
            <td class="text-right">{{ number_format($row->favorit) }}</td>
            <td class="text-right">{{ number_format($row->share) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Belum ada data tren tahunan.</td>
        </tr>
        @endforelse
    </table>

    <h2>4. Tren Bulanan (12 Bulan Terakhir)</h2>
    <table class="table-small">
        <tr>
            <th>Bulan</th>
            <th class="text-right">Upload</th>
            <th class="text-right">View</th>
            <th class="text-right">Like</th>
            <th class="text-right">Komentar</th>
            <th class="text-right">Favorit</th>
            <th class="text-right">Share</th>
        </tr>
        @forelse($monthly as $row)
        <tr>
            <td>{{ $row->month_label }}</td>
            <td class="text-right">{{ number_format($row->uploads) }}</td>
            <td class="text-right">{{ number_format($row->views) }}</td>
            <td class="text-right">{{ number_format($row->likes) }}</td>
            <td class="text-right">{{ number_format($row->komentar) }}</td>
            <td class="text-right">{{ number_format($row->favorit) }}</td>
            <td class="text-right">{{ number_format($row->share) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Belum ada data tren bulanan.</td>
        </tr>
        @endforelse
    </table>

    <h2>5. Tren Harian (30 Hari Terakhir)</h2>
    <table class="table-small">
        <tr>
            <th>Tanggal</th>
            <th class="text-right">Upload</th>
            <th class="text-right">View</th>
            <th class="text-right">Like</th>
            <th class="text-right">Komentar</th>
            <th class="text-right">Favorit</th>
            <th class="text-right">Share</th>
        </tr>
        @forelse($daily as $row)
        <tr>
            <td>{{ $row->day_label }}</td>
            <td class="text-right">{{ number_format($row->uploads) }}</td>
            <td class="text-right">{{ number_format($row->views) }}</td>
            <td class="text-right">{{ number_format($row->likes) }}</td>
            <td class="text-right">{{ number_format($row->komentar) }}</td>
            <td class="text-right">{{ number_format($row->favorit) }}</td>
            <td class="text-right">{{ number_format($row->share) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Belum ada data tren harian.</td>
        </tr>
        @endforelse
    </table>

    <div class="page-break"></div>

    <h2>6. Detail Statistik per Artwork</h2>
    <table class="table-small">
        <tr>
            <th style="width:28px;">No</th>
            <th>Judul</th>
            <th>Seniman</th>
            <th class="text-right">View</th>
            <th class="text-right">Like</th>
            <th class="text-right">Komentar</th>
            <th class="text-right">Favorit</th>
            <th class="text-right">Share</th>
        </tr>

        @forelse($stats as $i => $s)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ data_get($s, 'artwork.judul') ?? '-' }}</td>
            <td>{{ $artistName($s) }}</td>
            <td class="text-right">{{ number_format($s->jumlah_view) }}</td>
            <td class="text-right">{{ number_format($s->jumlah_like) }}</td>
            <td class="text-right">{{ number_format($s->jumlah_komentar) }}</td>
            <td class="text-right">{{ number_format($s->jumlah_favorit) }}</td>
            <td class="text-right">{{ number_format($s->jumlah_share) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Belum ada data statistik artwork.</td>
        </tr>
        @endforelse
    </table>

    <p class="small">
        Laporan ini dihasilkan otomatis dari sistem InCanvArt dan dapat digunakan sebagai bahan monitoring performa galeri digital.
    </p>

</body>
</html>
