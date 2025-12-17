<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>
    <link rel="stylesheet" href="{{ asset('css/indexUser.css') }}">
</head>

<body>
    <x-navbar></x-navbar>

    <div class="sm:mt-16">
        <div class="users-wrap">

            <h1 class="users-title">Kelola User</h1>

            {{-- ===================== FILTER & PENCARIAN ===================== --}}
            <section class="users-section">
                <h2 class="users-section-title">Filter & Pencarian</h2>

                <form method="GET" class="users-filter-bar">
                    <div class="users-filter-group">
                        <label for="search" class="users-filter-label">Cari User</label>
                        <div class="users-filter-field">
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari username / email / nama lengkap"
                                   class="users-filter-input">
                        </div>
                    </div>

                    <div class="users-filter-group">
                        <label for="role" class="users-filter-label">Role</label>
                        <select id="role" name="role" class="users-filter-select">
                            <option value="">Semua Role</option>
                            <option value="pengunjung" {{ request('role')=='pengunjung' ? 'selected' : '' }}>Pengunjung</option>
                            <option value="seniman"    {{ request('role')=='seniman' ? 'selected' : '' }}>Seniman</option>
                            <option value="admin"      {{ request('role')=='admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div class="users-filter-group">
                        <label for="status" class="users-filter-label">Status</label>
                        <select id="status" name="status" class="users-filter-select">
                            <option value="">Semua Status</option>
                            <option value="aktif"    {{ request('status')=='aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status')=='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="users-filter-actions">
                        <button type="submit" class="users-filter-btn">
                            Terapkan
                        </button>
                    </div>
                </form>
            </section>

            {{-- ===================== TABEL DAFTAR USER ===================== --}}
            <section class="users-section">
                <h2 class="users-section-title">Daftar User Terdaftar</h2>

                <div class="users-main-table">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama Seniman</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tgl Registrasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td class="user-col-username">
                                        {{ $user->username }}
                                    </td>
                                    <td>
                                        {{ $user->profile?->nama_lengkap ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        {{ ucfirst($user->role) }}
                                    </td>
                                    <td>
                                        @if($user->status === 'aktif')
                                            <span class="user-status-badge user-status-aktif">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="user-status-badge user-status-nonaktif">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->tanggal_registrasi)
                                            {{ \Carbon\Carbon::parse($user->tanggal_registrasi)->format('d-m-Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="user-col-action">
                                        <a href="{{ route('users.show', $user->user_id) }}"
                                           class="user-action-link">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Belum ada data user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="users-main-pagination">
                    {{ $users->onEachSide(1)->links('components.pagination') }}
                </div>
            </section>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>

</html>
