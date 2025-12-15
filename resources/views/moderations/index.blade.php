<!doctype html>
<html lang="id">

<head>
    <x-header></x-header>
    <link rel="stylesheet" href="{{ asset('css/showModeration.css') }}">
</head>

<body class="moderation-page">
    <x-navbar></x-navbar>

    <div class="sm:mt-16">
        <div class="mod-wrap">

            {{-- HEADER --}}
            <div class="mod-header">
                <h1 class="mod-title">
                    Moderasi Konten
                </h1>
                <p class="mod-subtitle">
                    Daftar laporan konten yang ditandai, disetujui, atau ditolak oleh admin.
                </p>
                <div class="mod-topline"></div>
            </div>

            {{-- TOPBAR: FILTER STATUS --}}
            <div class="mod-topbar">
                <div class="mod-filter-group">
                    {{-- Semua --}}
                    <a href="{{ route('moderations.index') }}"
                        class="mod-filter-chip {{ $status === null ? 'mod-filter-chip--active' : '' }}">
                        Semua
                    </a>

                    {{-- Filter per status --}}
                    @foreach (['ditandai','disetujui','ditolak'] as $s)
                    <a href="{{ route('moderations.index', ['status' => $s]) }}"
                        class="mod-filter-chip {{ $status === $s ? 'mod-filter-chip--active' : '' }}">
                        {{ ucfirst($s) }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- TABEL LIST MODERASI --}}
            <div class="mod-table-wrap">
                <div class="mod-table no-card">
                    <table class="mod-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Target</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Admin</th>
                                <th>Tanggal</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($moderasi as $m)
                            <tr>
                                {{-- ID --}}
                                <td class="mod-col-id">
                                    #{{ $m->moderation_id }}
                                </td>

                                {{-- TARGET --}}
                                <td class="mod-col-target">
                                    @if($m->target_type === 'artwork')
                                    Artwork #{{ $m->target_id }}
                                    @else
                                    Komentar #{{ $m->target_id }}
                                    @endif
                                </td>

                                {{-- JENIS TARGET --}}
                                <td>
                                    {{ ucfirst($m->target_type) }}
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    <span class="mod-status-pill
                                        @if($m->status === 'disetujui')
                                            mod-status--disetujui
                                        @elseif($m->status === 'ditolak')
                                            mod-status--ditolak
                                        @else
                                            mod-status--ditandai
                                        @endif">
                                        {{ ucfirst($m->status) }}
                                    </span>
                                </td>

                                {{-- ADMIN --}}
                                <td class="mod-col-admin">
                                    {{ $m->admin
                                        ? (optional($m->admin->profile)->nama_lengkap ?? $m->admin->username)
                                        : 'Belum diproses' }}
                                </td>

                                {{-- TANGGAL --}}
                                <td>
                                    {{ $m->tanggal }}
                                </td>

                                {{-- AKSI --}}
                                <td class="mod-col-action">
                                    <a href="{{ route('moderations.show', $m->moderation_id) }}"
                                        class="mod-action-link">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding:14px 18px; font-size:13px; color:var(--text-sub);">
                                    Belum ada data moderasi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="mod-pagination">
                {{ $moderasi->links() }}
            </div>
        </div>
    </div>
</body>

</html>