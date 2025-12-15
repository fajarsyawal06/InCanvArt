<!doctype html>
<html lang="id">

<head>
    <x-header></x-header>

    {{-- CSS --}}
    <style>
        :root {
            --c-bg: #393053;
            --c-card: #443C68;
            --c-card-soft: #4b4272;
            --c-text: #f5f5ff;
            --c-sub: #c9c6e6;
            --c-accent: #635985;
            --c-accent-soft: #7f72c7;
            --radius-xl: 32px;
            --radius-pill: 999px;
            --shadow: 0 18px 40px rgba(0, 0, 0, 0.45);
            --container: 1200px;
        }

        /* Reset dasar */
        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial,
                sans-serif;
            background-color: var(--c-bg);
        }

        /* Container umum */
        .container {
            max-width: var(--container);
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===== COVER PROFIL ===== */
        .pg-cover {
            aspect-ratio: 4 / 1;
            width: 100%;
            background-image: var(--cover);
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-color: #18122b;
        }

        /* ===== CARD PROFIL : mirip layout adviser ===== */
        .pg-identity {
            position: relative;
            margin: -80px auto 24px;
            background: var(--c-card);
            padding: 28px 32px 30px;
            border-radius: 38px;
            box-shadow: var(--shadow);

            display: flex;
            gap: 32px;
            align-items: flex-start;
            color: var(--c-text);
        }

        /* ===== KOLUM AVATAR KIRI ===== */
        .pg-left {
            width: 220px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-right: 0;
            border-right: none;
        }

        /* Avatar muncul sedikit menimpa cover */
        .pg-avatar-wrap {
            width: 170px;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.95);
            overflow: hidden;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.55);
            background: #18122b;
            transform: translateY(-38px);
        }

        .pg-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
        }

        /* ===== KOLUM KANAN : isi utama ===== */
        .pg-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding-left: 4px;
        }

        /* Header: nama + badge + tombol (seperti adviser) */
        .pg-header {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
        }

        .pg-header-left {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        /* Nama utama */
        .pg-display-wrap {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pg-display {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
        }

        /* Sub-line: username, nama lengkap, dll. */
        .pg-subline {
            font-size: 14px;
            color: var(--c-sub);
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .pg-subline span {
            display: inline-flex;
            align-items: center;
        }

        .pg-subline-dot {
            opacity: 0.6;
        }

        /* Username (handle) */
        .pg-handle {
            font-weight: 600;
            color: #ffffff;
        }

        /* Badge role berada di samping nama seperti chip kecil */
        .pg-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 12px;
            gap: 4px;
        }

        /* Tombol umum di sisi kanan header */
        .pg-header-actions {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .pg-actions {
            margin-top: 0;
        }

        .pg-btn {
            padding: 9px 24px;
            border-radius: var(--radius-pill);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #fff;
            background: var(--c-accent);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.5);
            transition: 0.2s ease;
        }

        .pg-btn-soft {
            background: var(--c-accent);
        }

        .pg-btn:hover,
        .pg-btn-soft:hover {
            background: var(--c-accent-soft);
            transform: translateY(-2px);
        }

        /* ===== BAR META (lokasi / kontak singkat) ===== */
        .pg-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            font-size: 13px;
            color: var(--c-sub);
        }

        .pg-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ===== BIO CARD ===== */
        .pg-bio {
            width: 100%;
            min-height: 110px;
            background: var(--c-card-soft);
            padding: 22px 24px;
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
            font-size: 15px;
            line-height: 1.9;
            color: var(--c-text);
        }

        /* ===== PANEL UPGRADE KE SENIMAN ===== */
        .pg-upgrade-panel {
            width: 100%;
            background: var(--c-card-soft);
            padding: 22px 24px 24px;
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);

            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            text-align: center;
        }

        .upgrade-icon-wrapper {
            display: flex;
            justify-content: center;
        }

        .pg-btn-upgrade {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 10px 28px;
            border-radius: var(--radius-pill);
            background: var(--c-accent);
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;

            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.55);
            transition: 0.2s ease;
        }

        .pg-btn-upgrade:hover {
            background: var(--c-accent-soft);
            transform: translateY(-2px);
        }

        /* ===== STATS ===== */
        .pg-stats {
            width: 100%;
            list-style: none;
            margin: 6px 0 0;
            padding: 0;
            display: flex;
            gap: 22px;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .pg-pill {
            min-width: 200px;
            background: var(--c-card-soft);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            text-align: left;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45);

            display: flex;
            flex-direction: column;
            padding: 12px 18px;
            gap: 2px;
        }

        .pg-pill-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--c-sub);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .pg-pill-value {
            font-weight: 800;
            font-size: 18px;
            color: #ffffff;
            margin-top: 2px;
        }

        /* ===== SECTION POSTINGAN ===== */
        .pg-section-title {
            text-align: center;
            margin: 26px 0 14px;
            font-size: 18px;
            font-weight: 700;
            color: #f5f5ff;
        }

        .pg-section-title::after {
            content: "";
            display: block;
            width: 90px;
            height: 2px;
            background: var(--c-accent);
            margin: 6px auto 0;
            border-radius: 999px;
        }

        .masonry {
            margin-bottom: 40px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .pg-identity {
                flex-direction: column;
                padding: 24px 18px 26px;
                margin-top: -60px;
            }

            .pg-left {
                width: 100%;
                justify-content: center;
            }

            .pg-avatar-wrap {
                transform: translateY(-40px);
            }

            .pg-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .pg-header-actions {
                width: 100%;
                margin-top: 6px;
            }

            .pg-btn {
                width: 100%;
                justify-content: center;
            }

            .pg-meta {
                flex-direction: column;
            }

            .pg-bio,
            .pg-stats,
            .pg-upgrade-panel {
                width: 100%;
            }

            .pg-stats {
                gap: 16px;
            }

            .pg-pill {
                min-width: 46%;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/pin.css') }}">
</head>

<body class="bg-gray-50 dark:bg-[#393053]">
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="sm:ml-64 mt-16 px-0">
        {{-- Cover profil --}}
        <div class="pg-cover"
            style="--cover:url('{{ $profile->foto_cover
             ? asset('storage/'.$profile->foto_cover)
             : asset('images/bgDashboard.png') }}');">
        </div>

        {{-- Bar identitas / Kartu profil --}}
        <div class="pg-identity container">

            {{-- KOLUM KIRI: avatar saja --}}
            <div class="pg-left">
                <div class="pg-avatar-wrap">
                    <img src="{{ $profile->foto_profil
              ? asset('storage/'.$profile->foto_profil)
              : asset('images/avatar-sample.jpg') }}"
                        alt="Foto profil user"
                        class="pg-avatar"
                        loading="lazy">
                </div>
            </div>

            {{-- KOLUM KANAN: nama, info, bio, dll. --}}
            <div class="pg-right">

                {{-- HEADER: nama + role + tombol --}}
                <div class="pg-header">
                    <div class="pg-header-left">
                        <div class="pg-display-wrap">
                            <h1 class="pg-display">
                                {{ $profile->nama_lengkap }}
                            </h1>

                            {{-- Badge Role --}}
                            @if($user->role === 'pengunjung')
                            <span class="pg-badge" title="Pengunjung">
                                <svg class="w-4 h-4 text-gray-100" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z" />
                                </svg>
                                <span>Pengunjung</span>
                            </span>
                            @elseif($user->role === 'seniman')
                            <span class="pg-badge" title="Seniman">
                                <svg class="w-4 h-4 text-gray-100" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Seniman</span>
                            </span>
                            @endif
                        </div>

                        {{-- Subline: username, email atau info lain --}}
                        <div class="pg-subline">
                            <span class="pg-handle">
                                {{ '@' . ($user->username ?? $profile->nama_lengkap) }}
                            </span>

                            <span class="pg-subline-dot">•</span>

                            <span>
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="pg-header-actions">
                        <div class="pg-actions">
                            <a href="{{ route('profiles.edit') }}" class="pg-btn pg-btn-soft">
                                Edit profil
                            </a>
                        </div>
                    </div>
                </div>

                {{-- META SINGKAT: contoh lokasi --}}
                <div class="pg-meta">
                    <div class="pg-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 21s-6-5.373-6-10a6 6 0 1112 0c0 4.627-6 10-6 10z" />
                            <circle cx="12" cy="11" r="2.5" stroke-width="2"
                                stroke="currentColor" fill="none" />
                        </svg>
                        <span>{{ $profile->lokasi ?? 'Lokasi belum diisi' }}</span>
                    </div>
                </div>

                {{-- BIO --}}
                <div class="pg-bio" role="region" aria-label="Deskripsi">
                    {{ $profile->bio ?: 'Deskripsi singkat user. Bisa beberapa kalimat untuk memperkenalkan diri, minat, dan karya.' }}
                </div>

                {{-- SENIMAN: sosial media / PENGUNJUNG: panel upgrade --}}
                @if ($user->role === 'seniman')
                @if (!empty($profile->kontak) && is_array($profile->kontak))
                <div class="pg-meta">
                    @if (!empty($profile->kontak['facebook']))
                    <div class="pg-meta-item">
                        <span>Facebook:</span>
                        <a href="{{ $profile->kontak['facebook'] }}" target="_blank" rel="noopener"
                            class="underline">
                            {{ $profile->kontak['facebook'] }}
                        </a>
                    </div>
                    @endif

                    @if (!empty($profile->kontak['instagram']))
                    <div class="pg-meta-item">
                        <span>Instagram:</span>
                        <a href="{{ $profile->kontak['instagram'] }}" target="_blank" rel="noopener"
                            class="underline">
                            {{ $profile->kontak['instagram'] }}
                        </a>
                    </div>
                    @endif

                    @if (!empty($profile->kontak['twitter']))
                    <div class="pg-meta-item">
                        <span>Twitter:</span>
                        <a href="{{ $profile->kontak['twitter'] }}" target="_blank" rel="noopener"
                            class="underline">
                            {{ $profile->kontak['twitter'] }}
                        </a>
                    </div>
                    @endif
                </div>
                @endif
                @else
                {{-- PENGUNJUNG: panel upgrade --}}
                <div class="pg-upgrade-panel w-full text-center flex flex-col items-center p-6">
                    <div class="upgrade-icon-wrapper mb-4">
                        <svg class="w-16 h-16 text-gray-200 dark:text-gray-100 opacity-90"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                        </svg>
                    </div>

                    <p class="text-gray-200 dark:text-gray-200 text-base mb-4 max-w-md leading-relaxed">
                        Lengkapi profil Anda dengan bio, media sosial, dan tampilkan karya sebagai seniman.
                        Upgrade akun Anda untuk membuka fitur ini.
                    </p>

                    <a href="{{ route('upgrade.akun') }}"
                        class="pg-btn-upgrade">
                        <span>Upgrade ke Seniman</span>
                    </a>
                </div>
                @endif

                {{-- Statistik: mirip chip di bawah seperti adviser --}}
                <ul class="pg-stats" aria-label="Statistik akun">
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

            </div> {{-- /.pg-right --}}

        </div> {{-- /.pg-identity --}}

        {{-- Section Postingan: hanya SENIMAN --}}
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