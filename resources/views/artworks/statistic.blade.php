<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>
    <link rel="stylesheet" href="{{ asset('css/statArtwork.css') }}">
</head>

<body>
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="sm:ml-64 mt-16 pt-12">
        <div class="">
            <div class="detail-layout">

                {{-- PANEL GAMBAR KIRI --}}
                <section class="art-panel">
                    <div class="art-frame">
                        <div class="art-media"
                            style="background-image:url('{{ $artwork->file_url }}');">
                        </div>
                    </div>
                </section>

                {{-- KOLOM KANAN: INFO + STATISTIK --}}
                <aside class="info-col">

                    @php
                    $views = $stat->jumlah_view ?? 0;
                    $likes = $likesCount ?? 0;
                    $comments = $commentsCount ?? 0;
                    $favorites = $favoritesCount ?? 0;

                    $ratioLike = $views > 0 ? round(($likes / $views) * 100, 1) : 0;
                    $ratioComment = $views > 0 ? round(($comments / $views) * 100, 1) : 0;
                    $ratioFav = $views > 0 ? round(($favorites / $views) * 100, 1) : 0;
                    @endphp

                    <div class="info-card">
                        {{-- HEADER: tombol kembali + judul insight --}}
                        <div class="info-header">
                            <a href="{{ route('artworks.show', $artwork) }}" class="btn-back">
                                <span>←</span>
                                <span>Kembali ke detail</span>
                            </a>

                            <div class="title-wrap">
                                <div class="title-main">Insight Artwork</div>
                                <div class="title-sub">
                                    {{ $artwork->judul }}
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>

                        {{-- META ARTWORK (kategori, tanggal upload) --}}
                        <div class="stat-meta">
                            @if($artwork->kategori)
                            <div class="meta-chip">
                                <span class="meta-label">Kategori</span>
                                <span class="meta-value">{{ $artwork->kategori->nama_kategori }}</span>
                            </div>
                            @endif

                            <div class="meta-chip">
                                <span class="meta-label">Diunggah</span>
                                <span class="meta-value">
                                    @if($artwork->tanggal_upload)
                                    {{ \Carbon\Carbon::parse($artwork->tanggal_upload)->format('d M Y H:i') }}
                                    @else
                                    -
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="desc-box">
                            @if($artwork->deskripsi)
                            {{ $artwork->deskripsi }}
                            @else
                            <span class="desc-empty">Tidak ada deskripsi untuk artwork ini.</span>
                            @endif
                        </div>

                        <div class="divider"></div>

                        {{-- STATISTIK UTAMA (4 kartu) --}}
                        <div class="stat-grid">
                            {{-- Views --}}
                            <div class="stat-card">
                                <p class="stat-label">View</p>
                                <p class="stat-value">{{ $views }}</p>
                                <p class="stat-caption">Total kunjungan</p>
                            </div>

                            {{-- Likes --}}
                            <div class="stat-card">
                                <p class="stat-label">Like</p>
                                <p class="stat-value">{{ $likes }}</p>
                                <p class="stat-caption">Jumlah pengguna menyukai</p>
                            </div>

                            {{-- Comments --}}
                            <div class="stat-card">
                                <p class="stat-label">Komentar</p>
                                <p class="stat-value">{{ $comments }}</p>
                                <p class="stat-caption">Jumlah komentar masuk</p>
                            </div>

                            {{-- Favorites --}}
                            <div class="stat-card">
                                <p class="stat-label">Favorit</p>
                                <p class="stat-value">{{ $favorites }}</p>
                                <p class="stat-caption">Berapa kali disimpan</p>
                            </div>
                        </div>

                        <div class="divider divider-soft"></div>

                        {{-- RASIO + CATATAN KREATOR --}}
                        <div class="stat-split">
                            <div class="mini-card">
                                <h3 class="mini-title">Interaksi Pengguna</h3>
                                <ul class="mini-list">
                                    <li>• Rasio like per view:
                                        <span class="mini-highlight">{{ $ratioLike }}%</span>
                                    </li>
                                    <li>• Rasio komentar per view:
                                        <span class="mini-highlight">{{ $ratioComment }}%</span>
                                    </li>
                                    <li>• Rasio favorit per view:
                                        <span class="mini-highlight">{{ $ratioFav }}%</span>
                                    </li>
                                </ul>
                                <p class="mini-note">
                                    Semakin tinggi persentase, semakin kuat daya tarik artwork terhadap audiens.
                                </p>
                            </div>

                            <div class="mini-card">
                                <h3 class="mini-title">Catatan Kreator</h3>
                                <p class="mini-text">
                                    Gunakan insight ini untuk mengevaluasi karya:
                                </p>
                                <ul class="mini-list">
                                    <li>• Tinjau apakah thumbnail dan komposisi visual sudah cukup menarik.</li>
                                    <li>• Uji variasi judul serta deskripsi untuk memancing klik dan interaksi.</li>
                                    <li>• Bandingkan performa artwork ini dengan karya Anda yang lain di Galeri Online.</li>
                                </ul>
                            </div>
                        </div>

                    </div> {{-- .info-card --}}
                </aside>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>