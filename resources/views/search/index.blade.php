<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>

    {{-- CSS masonry & kartu artwork --}}
    <link rel="stylesheet" href="{{ asset('css/pin.css') }}">

    {{-- CSS khusus halaman search (tidak mengganggu pin.css) --}}
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
</head>

<body class="dark bg-[#18122B] text-white">

    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <main class="search-shell sm:ml-64 mt-16 px-0">
        <div class="search-inner">

            {{-- Tombol Back --}}
            <div class="search-back">
                <a href="{{ url()->previous() }}"
                    class="search-back-link inline-flex items-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>

            {{-- Header Pencarian --}}
            <header class="search-header">
                <h1 class="search-header-title">
                    Hasil Pencarian
                </h1>
                <div class="search-header-line"></div>
                <p class="search-header-sub">
                    Kata kunci:
                    <span>"{{ $q }}"</span>
                </p>
            </header>

            {{-- BAGIAN AKUN / PROFILE --}}
            <section class="search-section-block">
                <h2 class="search-section-title">Akun</h2>

                @if($users->isEmpty())
                <p class="search-section-empty">
                    Tidak ada akun ditemukan.
                </p>
                @else
                <div class="search-section-card">
                    <ul class="search-account-list divide-y divide-gray-700/60">
                        @foreach($users as $user)
                        @php
                        $profile = $user->profile ?? null;
                        $avatar = $profile && $profile->foto_profil
                        ? asset('storage/' . $profile->foto_profil)
                        : asset('images/avatar-sample.jpg');

                        // pakai user_id kalau ada, fallback ke id jika beda skema
                        $profileUrl = route('profiles.show', $user->user_id ?? $user->id);
                        @endphp

                        <li class="search-account-item py-2">
                            <a href="{{ $profileUrl }}" class="flex items-center gap-3 group w-full text-left">
                                <img
                                    class="search-account-avatar object-cover"
                                    src="{{ $avatar }}"
                                    alt="Avatar {{ $user->username }}"
                                    loading="lazy">

                                <div class="flex-1 min-w-0">
                                    <p class="search-account-name truncate group-hover:underline">
                                        {{ $user->username }}
                                    </p>
                                    <p class="search-account-email truncate">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </section>

            {{-- BAGIAN ARTWORK --}}
            <section class="search-section-block">
                <h2 class="search-section-title">Artwork</h2>

                @if($artworks->isEmpty())
                <p class="search-section-empty">
                    Tidak ada artwork ditemukan.
                </p>
                @else
                {{-- Wrapper tipis agar tidak mengganggu .masonry dari pin.css --}}
                <div class="search-artwork-wrap">
                    {{-- Component masonry akan tetap menggunakan .masonry & .card dari pin.css --}}
                    <x-masonry :artworks="$artworks"></x-masonry>
                </div>
                @endif
            </section>

        </div>
    </main>

    <script src="{{ asset('js/pin.js') }}"></script>
</body>

</html>