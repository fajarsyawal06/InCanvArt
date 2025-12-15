<!doctype html>
<html lang="id">

<head>
    <x-header></x-header>
    <link rel="stylesheet" href="{{ asset('css/detailModerasi.css') }}">
</head>

<body class="moderation-detail-page">
    <x-navbar></x-navbar>

    <div class="sm:mt-16">
        <div class="mdd-wrap">

            {{-- LINK KEMBALI --}}
            <a href="{{ route('moderations.index') }}" class="mdd-back-link">
                &larr; Kembali ke daftar moderasi
            </a>

            {{-- CARD: INFORMASI MODERASI --}}
            <div class="mdd-card">
                <h1 class="mdd-title">Detail Moderasi #{{ $data->moderation_id }}</h1>
                <p class="mdd-subtext">Informasi lengkap tentang proses moderasi konten ini</p>

                <dl class="mdd-info-grid">
                    {{-- Jenis Target --}}
                    <div class="mdd-info-item">
                        <dt>Jenis Target</dt>
                        <dd>{{ ucfirst($data->target_type) }}</dd>
                    </div>

                    {{-- ID Target --}}
                    <div class="mdd-info-item">
                        <dt>ID Target</dt>
                        <dd>{{ $data->target_id }}</dd>
                    </div>

                    {{-- Status --}}
                    <div class="mdd-info-item">
                        <dt>Status</dt>
                        <dd>
                            <span class="mdd-status-pill
                                @if($data->status === 'disetujui') mdd-status--disetujui
                                @elseif($data->status === 'ditolak') mdd-status--ditolak
                                @else mdd-status--ditandai @endif">
                                {{ ucfirst($data->status) }}
                            </span>
                        </dd>
                    </div>

                    {{-- Admin --}}
                    <div class="mdd-info-item">
                        <dt>Diproses Oleh</dt>
                        <dd>
                            {{ $data->admin
                                ? (optional($data->admin->profile)->nama_lengkap ?? $data->admin->username)
                                : 'Belum diproses' }}
                        </dd>
                    </div>

                    {{-- Alasan --}}
                    <div class="mdd-info-item mdd-info-full">
                        <dt>Alasan</dt>
                        <dd>{{ $data->alasan ?? '-' }}</dd>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mdd-info-item mdd-info-full">
                        <dt>Tanggal Moderasi</dt>
                        <dd>{{ $data->tanggal }}</dd>
                    </div>
                </dl>
            </div>

            {{-- CARD: DETAIL TARGET (GAMBAR + INFO + AKSI) --}}
            <div class="mdd-card">
                <h2 class="mdd-title">Detail Target</h2>
                <p class="mdd-subtext">Informasi tentang konten yang sedang dimoderasi</p>

                {{-- JIKA TARGETNYA ARTWORK --}}
                @if ($data->target_type === 'artwork' && $target)
                    <div class="mdd-target-row">

                        {{-- KIRI: PANEL GAMBAR --}}
                        <div class="mdd-target-art-panel">
                            <div class="mdd-target-art-frame">
                                <div class="mdd-target-art-media">
                                    <img src="{{ $target->file_url }}" alt="Artwork">
                                </div>
                            </div>
                        </div>

                        {{-- KANAN: INFO ARTWORK + AKSI --}}
                        <div class="mdd-target-infoCol">
                            <div class="mdd-target-header">
                                <h3 class="mdd-target-title-main">
                                    {{ $target->judul }}
                                </h3>

                                <span class="mdd-target-badge">
                                    Artwork ID: {{ $target->artwork_id ?? $data->target_id }}
                                </span>
                            </div>

                            <p class="mdd-target-desc">
                                {{ $target->deskripsi ?? 'Tidak ada deskripsi.' }}
                            </p>

                            <div class="mdd-target-meta-grid">
                                <div class="mdd-target-meta-item">
                                    <span class="mdd-target-meta-label">Pemilik</span>
                                    <span class="mdd-target-meta-value">
                                        {{ optional($target->user->profile)->nama_lengkap
                                            ?? $target->user->username ?? 'Unknown' }}
                                    </span>
                                </div>

                                <div class="mdd-target-meta-item">
                                    <span class="mdd-target-meta-label">Tanggal Upload</span>
                                    <span class="mdd-target-meta-value">
                                        {{ $target->tanggal_upload ?? '-' }}
                                    </span>
                                </div>

                                <div class="mdd-target-meta-item">
                                    <span class="mdd-target-meta-label">Jenis Target</span>
                                    <span class="mdd-target-meta-value">Artwork</span>
                                </div>

                                <div class="mdd-target-meta-item">
                                    <span class="mdd-target-meta-label">ID Moderasi</span>
                                    <span class="mdd-target-meta-value">
                                        #{{ $data->moderation_id }}
                                    </span>
                                </div>
                            </div>

                            {{-- AKSI: UBAH STATUS & HAPUS LOG --}}
                            <div class="mdd-target-actions">

                                {{-- FORM UBAH STATUS --}}
                                <form action="{{ route('moderations.update', $data->moderation_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mdd-form-group">
                                        <label class="mdd-label">Status Baru</label>
                                        <select name="status" class="mdd-select">
                                            <option value="disetujui" {{ $data->status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="ditolak" {{ $data->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                            <option value="ditandai" {{ $data->status === 'ditandai' ? 'selected' : '' }}>Ditandai</option>
                                        </select>
                                    </div>

                                    <div class="mdd-form-group">
                                        <label class="mdd-label">Alasan (opsional)</label>
                                        <textarea name="alasan" class="mdd-textarea"
                                            rows="3">{{ $data->alasan }}</textarea>
                                    </div>

                                    <div class="mdd-btn-row">
                                        <button type="submit" class="mdd-btn mdd-btn-primary">
                                            Perbarui Status
                                        </button>
                                    </div>
                                </form>

                                {{-- FORM HAPUS LOG --}}
                                <form action="{{ route('moderations.destroy', $data->moderation_id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus log moderasi ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <div class="mdd-btn-row">
                                        <button class="mdd-btn mdd-btn-danger">
                                            Hapus Log Moderasi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                {{-- JIKA TARGETNYA KOMENTAR --}}
                @elseif ($data->target_type === 'comment' && $target)
                    <div class="mdd-comment-box">
                        <p class="mdd-comment-text">
                            "{{ $target->isi_komentar }}"
                        </p>

                        <p class="mdd-comment-meta">
                            Oleh: {{ $target->user->username ?? 'Unknown' }}
                            • {{ $target->tanggal ?? $target->created_at }}
                        </p>

                        @if ($target->artwork)
                            <p class="mdd-comment-artwork">
                                Pada artwork: "{{ $target->artwork->judul }}"
                                (ID: {{ $target->artwork->artwork_id }})
                            </p>
                        @endif
                    </div>

                    {{-- AKSI UNTUK KOMENTAR (DI BAWAH BOX, MASIH DALAM CARD YANG SAMA) --}}
                    <div class="mdd-target-actions">

                        {{-- FORM UBAH STATUS --}}
                        <form action="{{ route('moderations.update', $data->moderation_id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mdd-form-group">
                                <label class="mdd-label">Status Baru</label>
                                <select name="status" class="mdd-select">
                                    <option value="disetujui" {{ $data->status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ $data->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="ditandai" {{ $data->status === 'ditandai' ? 'selected' : '' }}>Ditandai</option>
                                </select>
                            </div>

                            <div class="mdd-form-group">
                                <label class="mdd-label">Alasan (opsional)</label>
                                <textarea name="alasan" class="mdd-textarea"
                                    rows="3">{{ $data->alasan }}</textarea>
                            </div>

                            <div class="mdd-btn-row">
                                <button type="submit" class="mdd-btn mdd-btn-primary">
                                    Perbarui Status
                                </button>
                            </div>
                        </form>

                        {{-- FORM HAPUS LOG --}}
                        <form action="{{ route('moderations.destroy', $data->moderation_id) }}"
                            method="POST"
                            onsubmit="return confirm('Hapus log moderasi ini?')">
                            @csrf
                            @method('DELETE')

                            <div class="mdd-btn-row">
                                <button class="mdd-btn mdd-btn-danger">
                                    Hapus Log Moderasi
                                </button>
                            </div>
                        </form>
                    </div>

                {{-- TARGET HILANG --}}
                @else
                    <p class="mdd-comment-meta">Target sudah dihapus atau tidak ditemukan.</p>
                @endif
            </div>

        </div>
    </div>
</body>

</html>
