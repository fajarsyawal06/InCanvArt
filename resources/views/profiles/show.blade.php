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

    {{-- COVER PROFIL (CREATOR) --}}
    <div class="pg-cover"
      style="--cover:url('{{ $creatorProfile && $creatorProfile->foto_cover
            ? asset('storage/'.$creatorProfile->foto_cover)
            : asset('images/bgDashboard.png') }}');">
    </div>

    {{-- CARD PROFIL : 3 KOLUM --}}
    <div class="pg-identity container">

      {{-- KIRI: Avatar + tombol (Edit / Follow) --}}
      <div class="pg-left">
        <div class="pg-avatar-wrap">
          <img src="{{ $creatorProfile && $creatorProfile->foto_profil
                ? asset('storage/'.$creatorProfile->foto_profil)
                : asset('images/avatar-sample.jpg') }}"
            alt="Foto profil creator"
            class="pg-avatar"
            loading="lazy">
        </div>

        <div class="pg-left-info">
          @auth
          @if (auth()->user()->user_id === $creator->user_id)
          {{-- Profil sendiri: tombol Edit profil --}}
          <a href="{{ route('profiles.edit') }}" class="pg-btn pg-btn-edit">
            Edit profil
          </a>
          @else
          {{-- Profil orang lain: tombol Follow / Unfollow --}}
          <form action="{{ route('users.follow', $creator) }}" method="POST">
            @csrf
            <button type="submit" class="pg-btn pg-btn-edit">
              {{ $isFollowing ? 'Unfollow' : 'Follow' }}
            </button>
          </form>
          @endif
          @endauth
        </div>
      </div>

      {{-- TENGAH: nama, sosial, stats --}}
      <div class="pg-center">
        {{-- Header nama --}}
        <div class="pg-center-header">
          <div class="pg-center-name-row">
            <h1 class="pg-center-username">
              {{ '@' . ($creator->username ?? 'user') }}
            </h1>

            {{-- Badge role (disamakan dengan index) --}}
            @if($creator->role === 'pengunjung')
            <span class="pg-badge" title="Pengunjung">
              <svg aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round"
                  stroke-linejoin="round" stroke-width="2"
                  d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z" />
              </svg>
            </span>
            @elseif($creator->role === 'seniman')
            <span class="pg-badge" title="Seniman">
              <svg aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                  d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z"
                  clip-rule="evenodd" />
              </svg>
            </span>
            @endif
          </div>

          @if($creatorProfile && !empty($creatorProfile->nama_lengkap))
          <div class="pg-center-realname">
            {{ $creatorProfile->nama_lengkap }}
          </div>
          @endif
        </div>

        {{-- SENIMAN: sosial media / PENGUNJUNG: abaikan --}}
        @if ($creator->role === 'seniman')
        @if ($creatorProfile && !empty($creatorProfile->kontak) && is_array($creatorProfile->kontak))
        <div class="pg-social-list">
          @if (!empty($creatorProfile->kontak['facebook']))
          <a href="{{ $creatorProfile->kontak['facebook'] }}"
            target="_blank" rel="noopener"
            class="pg-social-item" title="Facebook">
            {{-- ICON FACEBOOK --}}
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path fill-rule="evenodd"
                d="M13.135 6H15V3h-1.865a4.147 4.147 0 0 0-4.142 4.142V9H7v3h2v9.938h3V12h2.021l.592-3H12V6.591A.6.6 0 0 1 12.592 6h.543Z"
                clip-rule="evenodd" />
            </svg>
            <span>Facebook</span>
          </a>
          @endif

          @if (!empty($creatorProfile->kontak['instagram']))
          <a href="{{ $creatorProfile->kontak['instagram'] }}"
            target="_blank" rel="noopener"
            class="pg-social-item" title="Instagram">
            {{-- ICON INSTAGRAM --}}
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path fill="currentColor" fill-rule="evenodd"
                d="M3 8a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8Zm5-3a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H8Zm7.597 2.214a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2h-.01a1 1 0 0 1-1-1ZM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm-5 3a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z"
                clip-rule="evenodd" />
            </svg>
            <span>Instagram</span>
          </a>
          @endif

          @if (!empty($creatorProfile->kontak['twitter']))
          <a href="{{ $creatorProfile->kontak['twitter'] }}"
            target="_blank" rel="noopener"
            class="pg-social-item" title="Twitter">
            {{-- ICON TWITTER --}}
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path fill-rule="evenodd"
                d="M22 5.892a8.178 8.178 0 0 1-2.355.635 4.074 4.074 0 0 0 1.8-2.235 8.343 8.343 0 0 1-2.605.981A4.13 4.13 0 0 0 15.85 4a4.068 4.068 0 0 0-4.1 4.038c0 .31.035.618.105.919A11.705 11.705 0 0 1 3.4 4.734a4.006 4.006 0 0 0 1.268 5.392 4.165 4.165 0 0 1-1.859-.5v.05A4.057 4.057 0 0 0 6.1 13.635a4.192 4.192 0 0 1-1.856.07 4.108 4.108 0 0 0 3.831 2.807A8.36 8.36 0 0 1 2 18.184 11.732 11.732 0 0 0 8.291 20 11.502 11.502 0 0 0 19.964 8.5c0-.177 0-.349-.012-.523A8.143 8.143 0 0 0 22 5.892Z"
                clip-rule="evenodd" />
            </svg>
            <span>Twitter</span>
          </a>
          @endif
        </div>
        @endif
        @endif

        {{-- Statistik --}}
        <ul class="pg-stats" aria-label="Statistik akun">
          @if($creator->role === 'seniman')
          <li class="pg-pill">
            <span class="pg-pill-label">Jumlah posts</span>
            <span class="pg-pill-value">{{ $creatorArtworks->total() }}</span>
          </li>
          @endif

          <li class="pg-pill">
            <span class="pg-pill-label">Followers</span>
            <span class="pg-pill-value">{{ $creatorFollowersCount }}</span>
          </li>
          <li class="pg-pill">
            <span class="pg-pill-label">Following</span>
            <span class="pg-pill-value">{{ $creatorFollowingCount }}</span>
          </li>
        </ul>
      </div>

      {{-- KANAN: bio / panel info --}}
      <div class="pg-right">
        @if($creator->role === 'seniman')
        <div class="pg-bio" role="region" aria-label="Deskripsi">
          {{ $creatorProfile->bio ?? 'Deskripsi singkat user. Bisa beberapa kalimat untuk memperkenalkan diri dan karya.' }}
        </div>
        @else
        {{-- Creator masih pengunjung: panel info tanpa tombol upgrade --}}
        <div class="pg-upgrade-panel">
          <div class="upgrade-icon-wrapper mb-2">
            <svg class="w-12 h-12 text-gray-200 dark:text-gray-100 opacity-90"
              xmlns="http://www.w3.org/2000/svg"
              fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
            </svg>
          </div>
          <p class="text-gray-200 text-sm mb-2 leading-relaxed">
            Pengguna ini belum mengaktifkan profil seniman.
          </p>
          <p class="text-gray-200 text-xs leading-relaxed">
            Bio, sosial media, dan karya publik hanya akan tampil ketika akun telah menjadi seniman.
          </p>
        </div>
        @endif
      </div>

    </div>{{-- /.pg-identity --}}

    {{-- SECTION POSTINGAN: hanya SENIMAN --}}
    @if($creator->role === 'seniman')
    <section class="container">
      <h2 class="pg-section-title">Postingan</h2>
      <x-masonry :artworks="$creatorArtworks"></x-masonry>
    </section>
    @endif

  </div>{{-- /.sm:ml-64 wrapper --}}

  {{-- Scripts --}}
  <script src="{{ asset('js/pin.js') }}"></script>
  <script src="{{ asset('js/alert.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <x-flash></x-flash>

</body>

</html>