<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/showArtwork.css') }}">
    {{-- dipakai oleh showArtwork.js untuk tombol Back --}}
    <meta name="artworks-index-url" content="{{ route('artworks.index') }}">
</head>

<body>
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="sm:ml-64 mt-16 px-0">
        <br><br>

        <div class="detail-layout">
            {{-- LEFT: FOTO ARTWORK --}}
            <div class="art-panel">
                <div class="art-frame">
                    <div class="art-media" style="background-image:url('{{ $artwork->file_url }}');"></div>
                </div>
            </div>

            {{-- RIGHT: INFO --}}
            <div class="info-col">
                {{-- Creator bar --}}
                <div class="creator-bar">
                    <a href="{{ route('profiles.show', ['user' => $artwork->user_id]) }}"
                        class="creator-link flex items-center gap-3">

                        {{-- Avatar --}}
                        <div class="creator-avatar" style="background-image:url('{{ $creatorBg }}');"></div>


                        {{-- Nama Ditukar --}}
                        <div class="creator-name-wrap">

                            {{-- Sekarang creator-name = username --}}
                            <span class="creator-name">
                                {{ '@' . $artwork->user->username }}
                            </span>

                            {{-- Sekarang creator-sub = nama lengkap / fallback username --}}
                            <span class="creator-sub">
                                {{ optional($artwork->user->profile)->nama_lengkap ?? $artwork->user->username }}
                            </span>

                        </div>

                    </a>


                    <div class="creator-divider"></div>

                    <div class="creator-actions">
                        @auth

                        {{-- 1. JIKA PEMILIK ARTWORK --}}
                        @if (auth()->id() === $artwork->user_id)
                        <a href="{{ route('artworks.statistic', $artwork->slug) }}" class="btn-follow">
                            Statistik
                        </a>

                        {{-- 2. JIKA ADMIN (bukan pemilik artwork) --}}
                        @elseif (auth()->user()->role === 'admin')
                        {{-- TOMBOL REPORT KHUSUS ADMIN --}}
                        <form id="report-artwork-form"
                            action="{{ route('artworks.report', $artwork->slug) }}"
                            method="POST" class="mr-2">
                            @csrf
                            <input type="hidden" name="alasan" value="">

                            <button type="button"
                                id="btn-report-artwork"
                                class="btn-follow flex items-center gap-1"
                                title="Tandai untuk moderasi">
                                <svg class="w-4 h-4 text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24"
                                    fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>Tandai</span>
                            </button>
                        </form>

                        {{-- 3. JIKA USER BIASA (BUKAN PEMILIK & BUKAN ADMIN) --}}
                        @else
                        <form action="{{ route('users.follow', $artwork->user) }}"
                            method="POST" class="mr-2">
                            @csrf
                            <button type="submit" class="btn-follow">
                                {{ auth()->user()->isFollowing($artwork->user) ? 'Unfollow' : 'Follow' }}
                            </button>
                        </form>
                        @endif

                        @endauth
                    </div>

                </div>

                {{-- Banner status moderasi: hanya tampil untuk PEMILIK ARTWORK --}}
                @auth
                @if(auth()->id() === $artwork->user_id && $moderation)
                <div class="moderation-banner">
                    @if ($moderation->status === 'ditandai')
                    <strong>Artwork Anda sedang ditinjau.</strong><br>
                    Telah ada laporan terkait konten ini dan saat ini sedang dalam proses peninjauan admin.
                    @elseif ($moderation->status === 'ditolak')
                    <strong>Artwork Anda melanggar kebijakan.</strong><br>
                    Konten ini telah ditandai melanggar aturan dan mungkin disembunyikan dari pengunjung.
                    @elseif ($moderation->status === 'disetujui')
                    <strong>Laporan terhadap artwork ini telah ditolak.</strong><br>
                    Admin telah meninjau laporan dan menyatakan konten ini tetap dapat ditampilkan.
                    @endif

                    @if ($moderation->alasan)
                    <div class="moderation-reason">
                        Catatan admin: “{{ $moderation->alasan }}”
                    </div>
                    @endif

                    <div class="moderation-meta">
                        Terakhir diperbarui: {{ $moderation->tanggal }}
                    </div>
                </div>
                @endif
                @endauth

                {{-- Card utama --}}
                <div class="info-card">
                    <div class="info-header">
                        {{-- Baris atas: tombol kembali & edit --}}
                        <div class="info-header-top">
                            {{-- Tombol kembali kiri --}}
                            <button type="button" id="btn-back" class="btn-back">
                                ← Kembali
                            </button>

                            {{-- Tombol edit kanan, sama style dengan back --}}
                            @auth
                            @if(auth()->id() === $artwork->user_id)
                            <a href="{{ route('artworks.edit', $artwork) }}"
                                class="btn-back btn-edit">
                                Edit
                            </a>
                            @endif
                            @endauth
                        </div>

                        {{-- Baris bawah: judul & kategori (penuh, tidak ketimpa tombol) --}}
                        <div class="title-wrap">
                            <div class="title-main">{{ $artwork->judul }}</div>
                            <div class="title-sub">
                                {{ $artwork->kategori->nama_kategori ?? 'Kategori' }}
                            </div>
                        </div>
                    </div>

                    <div class="desc-box">
                        {{ $artwork->deskripsi ?: 'Belum ada deskripsi untuk artwork ini.' }}
                    </div>

                    <div class="divider"></div>

                    <div>
                        <div class="comment-label">Komentar</div>
                        <div class="comments-box">
                            @if ($comments->count())
                            @foreach ($comments as $comment)
                            @include('artworks.partials.comment', ['comment' => $comment])
                            @endforeach
                            @else
                            <div class="comment-item">
                                Belum ada komentar. Jadilah yang pertama memberikan apresiasi.
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="actions-wrap">
                        <div class="actions-left">
                            @auth
                            <button
                                id="btn-like"
                                class="circle-btn"
                                type="button"
                                aria-label="Like"
                                data-liked="{{ $userHasLiked ? '1' : '0' }}"
                                data-url="{{ route('artworks.like', $artwork) }}">

                                {{-- Hati terisi (liked) --}}
                                <svg id="icon-like-filled"
                                    class="w-6 h-6 text-red-500 transition-all duration-200 {{ $userHasLiked ? '' : 'hidden' }}"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24"
                                    fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z" />
                                </svg>

                                {{-- Hati kosong (belum like) --}}
                                <svg id="icon-like-outline"
                                    class="w-6 h-6 text-gray-300 dark:text-white transition-all duration-200 {{ $userHasLiked ? 'hidden' : '' }}"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24"
                                    fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                                </svg>
                            </button>
                            @else
                            <a class="circle-btn" href="{{ route('login') }}" title="Masuk untuk like">
                                🤍
                            </a>
                            @endauth

                            {{-- SHARE BUTTON --}}
                            @auth
                            <form action="{{ route('artworks.share', ['artwork' => $artwork->artwork_id]) }}"
                                method="POST"
                                class="inline-block">
                                @csrf

                                <button
                                    class="circle-btn"
                                    type="submit"
                                    aria-label="Share">

                                    {{-- ICON SHARE (filled) --}}
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15.141 6 5.518 4.95a1.05 1.05 0 0 1 0 1.549l-5.612 5.088m-6.154-3.214v1.615a.95.95 0 0 0 1.525.845l5.108-4.251a1.1 1.1 0 0 0 0-1.646l-5.108-4.251a.95.95 0 0 0-1.525.846v1.7c-3.312 0-6 2.979-6 6.654v1.329a.7.7 0 0 0 1.344.353 5.174 5.174 0 0 1 4.652-3.191l.004-.003Z" />
                                    </svg>

                                    {{-- ICON SHARE (outline) --}}
                                    <svg id="icon-share-outline"
                                        class="w-6 h-6 text-gray-800 dark:text-white transition-all duration-200 hidden"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m15.141 6 5.518 4.95a1.05 1.05 0 0 1 0 1.549l-5.612 5.088m-6.154-3.214v1.615a.95.95 0 0 0 1.525.845l5.108-4.251a1.1 1.1 0 0 0 0-1.646l-5.108-4.251a.95.95 0 0 0-1.525.846v1.7c-3.312 0-6 2.979-6 6.654v1.329a.7.7 0 0 0 1.344.353 5.174 5.174 0 0 1 4.652-3.191l.004-.003Z" />
                                    </svg>

                                </button>
                            </form>
                            @else
                            <a class="circle-btn"
                                href="{{ route('login') }}"
                                title="Masuk untuk dapat membagikan artwork">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="m15.141 6 5.518 4.95a1.05 1.05 0 0 1 0 1.549l-5.612 5.088m-6.154-3.214v1.615a.95.95 0 0 0 1.525.845l5.108-4.251a1.1 1.1 0 0 0 0-1.646l-5.108-4.251a.95.95 0 0 0-1.525.846v1.7c-3.312 0-6 2.979-6 6.654v1.329a.7.7 0 0 0 1.344.353 5.174 5.174 0 0 1 4.652-3.191l.004-.003Z" />
                                </svg>
                            </a>
                            @endauth

                            @auth
                            <button
                                id="btn-bookmark"
                                class="circle-btn"
                                type="button"
                                aria-label="Bookmark"
                                data-bookmarked="{{ $userHasBookmarked ? '1' : '0' }}"
                                data-url="{{ route('artworks.bookmark', $artwork) }}">

                                {{-- Bookmark terisi (sudah bookmark) --}}
                                <svg id="icon-bookmark-filled"
                                    class="w-6 h-6 text-gray-800 dark:text-white transition-all duration-200 {{ $userHasBookmarked ? '' : 'hidden' }}"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M7.833 2c-.507 0-.98.216-1.318.576A1.92 1.92 0 0 0 6 3.89V21a1 1 0 0 0 1.625.78L12 18.28l4.375 3.5A1 1 0 0 0 18 21V3.889c0-.481-.178-.954-.515-1.313A1.808 1.808 0 0 0 16.167 2H7.833Z" />
                                </svg>

                                {{-- Bookmark kosong (belum bookmark) --}}
                                <svg id="icon-bookmark-outline"
                                    class="w-6 h-6 text-gray-800 dark:text-white transition-all duration-200 {{ $userHasBookmarked ? 'hidden' : '' }}"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m17 21-5-4-5 4V3.889a.92.92 0 0 1 .244-.629.808.808 0 0 1 .59-.26h8.333a.81.81 0 0 1 .589.26.92.92 0 0 1 .244.63V21Z" />
                                </svg>
                            </button>
                            @else
                            <a class="circle-btn" href="{{ route('login') }}" title="Masuk untuk menyimpan bookmark">
                                🤍
                            </a>
                            @endauth
                        </div>

                        <div class="actions-info">
                            <span>
                                Like:
                                <span id="like-count">{{ $artwork->likes_count ?? 0 }}</span>
                            </span>
                            <span>
                                Upload:
                                {{ $artwork->tanggal_upload
                                    ? \Carbon\Carbon::parse($artwork->tanggal_upload)->translatedFormat('d M Y')
                                    : ($artwork->created_at ? $artwork->created_at->translatedFormat('d M Y') : '-') }}
                            </span>
                        </div>
                    </div>

                    @auth
                    {{-- Indikator balasan --}}
                    <div id="reply-banner" class="reply-banner" style="display:none;
                            background: rgba(99, 89, 133, .25);
                            border:1px solid rgba(255,255,255,.12);
                            padding:8px 10px; border-radius:10px; font-size:.78rem; color:#e7e4ff;">
                        Membalas <strong id="reply-name"></strong>:
                        <span id="reply-text" style="opacity:.9"></span>
                        <button type="button" id="reply-cancel"
                            style="margin-left:8px; background:none; border:none; color:#ffd2d2; cursor:pointer;">
                            Batal
                        </button>
                    </div>

                    {{-- Form komentar --}}
                    <form id="comment-form"
                        action="{{ route('comments.store', $artwork) }}"
                        method="POST"
                        class="comment-form-wrap"
                        autocomplete="off">
                        @csrf

                        {{-- input visible (akan dijadikan dummy oleh showArtwork.js) --}}
                        <input type="text"
                            name="isi_komentar"
                            class="comment-input"
                            placeholder="Tulis komentar apresiatif di sini..."
                            required>

                        {{-- field real yang dikirim ke server (diisi JS saat submit) --}}
                        <input type="hidden" name="isi_komentar" id="realComment">

                        <input type="hidden" name="parent_comment_id" value="">
                        <button type="submit" class="btn-post">Kirim</button>
                    </form>
                    @else
                    <div class="comment-item" style="margin-top:8px">
                        <a href="{{ route('login') }}">Masuk</a> untuk menambahkan komentar.
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="{{ asset('js/showArtwork.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/alert.js') }}"></script>
    <script src="{{ asset('js/modalReport.js') }}"></script>
    <x-flash></x-flash>
</body>

</html>