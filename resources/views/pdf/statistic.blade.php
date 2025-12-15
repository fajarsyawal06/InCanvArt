<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik InCanvArt</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        h1, h2, h3 {
            margin: 0 0 6px 0;
        }
        h1 { font-size: 18px; }
        h2 { font-size: 14px; margin-top: 16px; }
        h3 { font-size: 12px; margin-top: 12px; }

        .small { font-size: 9px; color: #555; }
        .mb-8 { margin-bottom: 8px; }
        .mb-12 { margin-bottom: 12px; }
        .mb-16 { margin-bottom: 16px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #444;
            padding: 4px 6px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .no-border { border: none; }
        .no-border td, .no-border th {
            border: none;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <h1>Laporan Statistik InCanvArt</h1>
    <p class="small mb-12">
        Tanggal generate: {{ $generated_at->format('d/m/Y H:i') }}<br>
        Total data statistik: {{ $stats->count() }} baris
    </p>

    {{-- 1. RINGKASAN GLOBAL --}}
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

    {{-- 2. INSIGHT CEPAT --}}
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
                <td>{{ optional($insight['most_viewed']->artwork)->judul ?? '-' }}</td>
                <td>{{ optional(optional($insight['most_viewed']->artwork)->user)->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($insight['most_viewed']->jumlah_view) }}</td>
            </tr>
        @endif

        @if($insight['most_liked'])
            <tr>
                <td>Like Tertinggi</td>
                <td>{{ optional($insight['most_liked']->artwork)->judul ?? '-' }}</td>
                <td>{{ optional(optional($insight['most_liked']->artwork)->user)->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($insight['most_liked']->jumlah_like) }}</td>
            </tr>
        @endif

        @if($insight['most_comment'])
            <tr>
                <td>Komentar Terbanyak</td>
                <td>{{ optional($insight['most_comment']->artwork)->judul ?? '-' }}</td>
                <td>{{ optional(optional($insight['most_comment']->artwork)->user)->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($insight['most_comment']->jumlah_komentar) }}</td>
            </tr>
        @endif

        @if($insight['most_share'])
            <tr>
                <td>Share Terbanyak</td>
                <td>{{ optional($insight['most_share']->artwork)->judul ?? '-' }}</td>
                <td>{{ optional(optional($insight['most_share']->artwork)->user)->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($insight['most_share']->jumlah_share) }}</td>
            </tr>
        @endif
    </table>

    {{-- 3. TREN BULANAN --}}
    <h2>3. Tren Bulanan (12 Bulan Terakhir)</h2>
    <table>
        <tr>
            <th>Bulan</th>
            <th class="text-right">Upload</th>
            <th class="text-right">View</th>
            <th class="text-right">Like</th>
            <th class="text-right">Komentar</th>
            <th class="text-right">Favorit</th>
            <th class="text-right">Share</th>
        </tr>
        @foreach($monthly as $row)
            <tr>
                <td>{{ $row->month_label }}</td>
                <td class="text-right">{{ number_format($row->uploads) }}</td>
                <td class="text-right">{{ number_format($row->views) }}</td>
                <td class="text-right">{{ number_format($row->likes) }}</td>
                <td class="text-right">{{ number_format($row->komentar) }}</td>
                <td class="text-right">{{ number_format($row->favorit) }}</td>
                <td class="text-right">{{ number_format($row->share) }}</td>
            </tr>
        @endforeach
    </table>

    {{-- 4. DETAIL PER ARTWORK --}}
    <h2>4. Detail Statistik per Artwork</h2>
    <table>
        <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Seniman</th>
            <th class="text-right">View</th>
            <th class="text-right">Like</th>
            <th class="text-right">Komentar</th>
            <th class="text-right">Favorit</th>
            <th class="text-right">Share</th>
        </tr>
        @foreach($stats as $i => $s)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ optional($s->artwork)->judul ?? '-' }}</td>
                <td>{{ optional(optional($s->artwork)->user)->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($s->jumlah_view) }}</td>
                <td class="text-right">{{ number_format($s->jumlah_like) }}</td>
                <td class="text-right">{{ number_format($s->jumlah_komentar) }}</td>
                <td class="text-right">{{ number_format($s->jumlah_favorit) }}</td>
                <td class="text-right">{{ number_format($s->jumlah_share) }}</td>
            </tr>
        @endforeach
    </table>

    <p class="small mt-12">
        Laporan ini dihasilkan otomatis dari sistem InCanvArt dan dapat digunakan sebagai
        bahan monitoring performa galeri digital.
    </p>

</body>
</html>
