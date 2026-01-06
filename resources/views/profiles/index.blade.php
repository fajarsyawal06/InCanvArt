<!doctype html>
<html lang="id">

<head>
  <x-header></x-header>

  {{-- CSS --}}
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pin.css') }}">
</head>

<body class="bg-gray-50 dark:bg-[#393053]">
  <x-navbar></x-navbar>
  <x-sidebar></x-sidebar>

  <div class="sm:ml-64 mt-16 px-0">

    {{-- COVER PROFIL --}}
    <div class="pg-cover"
      style="--cover:url('{{ $profile->foto_cover
             ? asset(ltrim($profile->foto_cover, '/'))
             : asset('images/bgDashboard.png') }}');">
    </div>

    {{-- CARD PROFIL : 3 KOLUM --}}
    <div class="pg-identity container">

      {{-- KIRI: Avatar + tombol edit --}}
      <div class="pg-left">
        <div class="pg-avatar-wrap">
          <img src="{{ $profile->foto_profil
              ? asset(ltrim($profile->foto_profil, '/'))
              : asset('images/avatar-sample.jpg') }}"
            alt="Foto profil user"
            class="pg-avatar"
            loading="lazy">
        </div>

        <div class="pg-left-info">
          <a href="{{ route('profiles.edit') }}" class="pg-btn pg-btn-edit">
            Edit profil
          </a>
        </div>
      </div>

      {{-- TENGAH --}}
      <div class="pg-center">
        <div class="pg-center-header">
          <div class="pg-center-name-row">
            <h1 class="pg-center-username">
              {{ '@' . ($user->username ?? 'user') }}
            </h1>
          </div>

          @if(auth()->user()->role === 'seniman' && !empty($profile->nama_lengkap))
          <div class="pg-center-realname">
            {{ $profile->nama_lengkap }}
          </div>
          @endif
        </div>

        {{-- SOSIAL MEDIA --}}
        @php
        $kontak = is_array($profile->kontak) ? $profile->kontak : [];

        $igRaw = $kontak['instagram'] ?? null;
        $xRaw = $kontak['twitter'] ?? null; // key di DB: twitter
        $fbRaw = $kontak['facebook'] ?? null;

        $toUrl = function ($val, $base) {
        if (!$val) return null;
        $val = trim($val);

        // sudah URL penuh
        if (preg_match('~^https?://~i', $val)) return $val;

        // kalau user isi @username
        $val = ltrim($val, '@');

        return rtrim($base, '/') . '/' . $val;
        };

        $igUrl = $toUrl($igRaw, 'https://www.instagram.com');
        $xUrl = $toUrl($xRaw, 'https://x.com');
        $fbUrl = $toUrl($fbRaw, 'https://www.facebook.com');
        @endphp

        @if($igUrl || $xUrl || $fbUrl)
        <div class="pg-social-list">

          @if($igUrl)
          <a class="pg-social-item"
            href="{{ $igUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Instagram">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9A5.5 5.5 0 0 1 16.5 22h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2Zm9 2h-9A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4ZM12 7a5 5 0 1 1 0 10a5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6a3 3 0 0 0 0-6Zm5.25-.9a1.15 1.15 0 1 1 0 2.3a1.15 1.15 0 0 1 0-2.3Z" />
            </svg>
            <span>Instagram</span>
          </a>
          @endif

          @if($xUrl)
          <a class="pg-social-item"
            href="{{ $xUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="X">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M18.9 2H22l-6.8 7.8L23 22h-6.8l-5.3-7.1L4.7 22H2l7.3-8.3L1 2h6.9l4.8 6.4L18.9 2Zm-1.2 18h1.7L7.2 4H5.4l12.3 16Z" />
            </svg>
            <span>X</span>
          </a>
          @endif

          @if($fbUrl)
          <a class="pg-social-item"
            href="{{ $fbUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Facebook">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M13.5 22v-7.1h2.4l.4-2.8h-2.8V10.3c0-.8.2-1.4 1.4-1.4h1.5V6.4c-.3 0-1.2-.1-2.4-.1c-2.3 0-3.9 1.4-3.9 4v1.8H7.5v2.8H10V22h3.5Z" />
            </svg>
            <span>Facebook</span>
          </a>
          @endif

        </div>
        @endif

        {{-- Statistik --}}
        <ul class="pg-stats">
          @if($user->role === 'seniman')
          <li class="pg-pill">
            <span class="pg-pill-label">Jumlah posts</span>
            <span class="pg-pill-value">{{ $artworks->total() }}</span>
          </li>
          @endif

          <li class="pg-pill">
            <span class="pg-pill-label">Followers</span>
            <span class="pg-pill-value">{{ $followersCount }}</span>
          </li>

          <li class="pg-pill">
            <span class="pg-pill-label">Following</span>
            <span class="pg-pill-value">{{ $followingCount }}</span>
          </li>
        </ul>
      </div>

      {{-- KANAN --}}
      <div class="pg-right">
        @if($user->role === 'seniman')
        <div class="pg-bio">
          {{ $profile->bio ?: 'Deskripsi singkat user.' }}
        </div>
        @endif
      </div>

    </div>

    {{-- POSTINGAN --}}
    @if($user->role === 'seniman')
    <section class="container">
      <h2 class="pg-section-title">Postingan</h2>
      <x-masonry :artworks="$artworks"></x-masonry>
    </section>
    @endif

  </div>

  {{-- JS --}}
  <script src="{{ asset('js/pin.js') }}"></script>
  <script src="{{ asset('js/alert.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <x-flash></x-flash>
</body>

</html>