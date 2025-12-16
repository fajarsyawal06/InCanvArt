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

  <script src="{{ asset('js/pin.js') }}"></script>
  <script src="{{ asset('js/alert.js') }}"></script>
  <x-flash></x-flash>
</body>

</html>
