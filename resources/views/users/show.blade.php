<!doctype html>
<html lang="id">

<head>
    <x-header></x-header>
    <title>Detail User</title>
    <link rel="stylesheet" href="{{ asset('css/showUser.css') }}">
</head>

<body class="user-show-page">
    <x-navbar></x-navbar>

    <div class="sm:mt-16">
        <div class="usr-wrap">

            @php
                // Amankan semua field profil agar selalu string saat di-echo

                $profile = $user->profile ?? null;

                $namaLengkap = $profile->nama_lengkap ?? null;
                if (is_array($namaLengkap)) {
                    $namaLengkap = implode(', ', $namaLengkap);
                }

                $kontak = $profile->kontak ?? null;
                if (is_array($kontak)) {
                    $kontak = implode(', ', $kontak);
                }

                $bio = $profile->bio ?? null;
                if (is_array($bio)) {
                    // jika bio berupa array kata/kalimat, gabungkan saja
                    $bio = implode(' ', $bio);
                }
            @endphp

            {{-- HEADER --}}
            <header class="usr-header">
                <h1 class="usr-title">Detail User</h1>
                <p class="usr-subtitle">
                    Informasi lengkap akun dan profil pengguna.
                </p>
                <div class="usr-topline"></div>
            </header>

            {{-- GRID: DATA AKUN + PROFIL --}}
            <div class="usr-main">
                {{-- Kartu Data Akun --}}
                <section class="usr-card">
                    <div class="usr-card-header">
                        <h2 class="usr-card-title">Data Akun</h2>
                        <span class="usr-card-pill">
                            User ID #{{ $user->user_id }}
                        </span>
                    </div>

                    <ul class="usr-meta-list">
                        <li class="usr-meta-item">
                            <span class="usr-meta-label">Username</span>
                            <span class="usr-meta-value">{{ $user->username }}</span>
                        </li>

                        <li class="usr-meta-item">
                            <span class="usr-meta-label">Email</span>
                            <span class="usr-meta-value">{{ $user->email }}</span>
                        </li>

                        <li class="usr-meta-item">
                            <span class="usr-meta-label">Role</span>
                            <span class="usr-meta-value">{{ ucfirst($user->role) }}</span>
                        </li>

                        <li class="usr-meta-item">
                            <span class="usr-meta-label">Status</span>
                            <span class="usr-meta-value">
                                @if($user->status === 'aktif')
                                    <span class="usr-badge usr-badge-status-aktif">
                                        <span class="usr-badge-dot"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="usr-badge usr-badge-status-nonaktif">
                                        <span class="usr-badge-dot"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </span>
                        </li>

                        <li class="usr-meta-item">
                            <span class="usr-meta-label">Tgl Registrasi</span>
                            <span class="usr-meta-value">
                                {{ $user->tanggal_registrasi
                                    ? \Carbon\Carbon::parse($user->tanggal_registrasi)->format('d-m-Y H:i')
                                    : '-' }}
                            </span>
                        </li>
                    </ul>

                    {{-- Badge Role tanpa array --}}
                    <div class="usr-badges">
                        <span class="usr-badge
                            @if($user->role === 'admin') usr-badge-role-admin
                            @elseif($user->role === 'seniman') usr-badge-role-seniman
                            @else usr-badge-role-pengunjung
                            @endif
                        ">
                            <span class="usr-badge-dot"></span>
                            Role: {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </section>

                {{-- Kartu Profil --}}
                <section class="usr-card">
                    <div class="usr-card-header">
                        <h2 class="usr-card-title">Profil User</h2>
                    </div>

                    <div class="usr-avatar-wrap">
                        <img
                            src="{{ $profile && $profile->foto_profil
                                    ? asset('storage/'.$profile->foto_profil)
                                    : asset('images/avatar-sample.jpg') }}"
                            alt="Avatar user"
                            class="usr-avatar">

                        <div class="usr-name-block">
                            <span class="usr-name-main">
                                {{ $namaLengkap ?? $user->username }}
                            </span>
                            <span class="usr-name-username">
                                {{ '@'.$user->username }}
                            </span>
                        </div>
                    </div>

                    <ul class="usr-meta-list">
                        <li class="usr-meta-item">
                            <span class="usr-meta-label">Nama Lengkap</span>
                            <span class="usr-meta-value">
                                {{ $namaLengkap ?? '-' }}
                            </span>
                        </li>

                        <li class="usr-meta-item">
                            <span class="usr-meta-label">Kontak</span>
                            <span class="usr-meta-value">
                                {{ $kontak ?? '-' }}
                            </span>
                        </li>
                    </ul>

                    <div class="usr-bio">
                        {{ $bio ?? 'Belum ada bio yang diisi.' }}
                    </div>
                </section>
            </div>

            {{-- Aksi Admin --}}
            <div class="usr-actions">
                @if($user->status === 'aktif')
                    <form action="{{ route('users.deactivate', $user->user_id) }}"
                          method="POST"
                          onsubmit="return confirm('Nonaktifkan akun ini?');">
                        @csrf
                        <button type="submit" class="usr-btn usr-btn-danger">
                            Nonaktifkan Akun
                        </button>
                    </form>
                @else
                    <form action="{{ route('users.activate', $user->user_id) }}"
                          method="POST"
                          onsubmit="return confirm('Aktifkan kembali akun ini?');">
                        @csrf
                        <button type="submit" class="usr-btn usr-btn-primary">
                            Aktifkan Akun
                        </button>
                    </form>
                @endif

                <a href="{{ route('users.index') }}" class="usr-btn usr-btn-ghost">
                    Kembali ke Daftar User
                </a>
            </div>
        </div>
    </div>
</body>
</html>
