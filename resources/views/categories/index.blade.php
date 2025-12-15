<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>

    <!-- IMPORT CSS CUSTOM -->
    <link rel="stylesheet" href="{{ asset('css/showCategory.css') }}">
</head>

<body class="categories-page">

    <!-- NAVBAR -->
    <x-navbar></x-navbar>
    <div class="sm:mt-16">
        <div class="cat-wrap">

            <!-- HEADER -->
            <div class="cat-header">
                <h1 class="cat-title">Daftar Kategori</h1>
                <div class="cat-topline"></div>
            </div>

            <!-- TOPBAR: SEARCH + TOMBOL TAMBAH -->
            <div class="cat-topbar">
                <!-- SEARCH BOX -->
                <div class="cat-search">
                    <label for="search-category" class="cat-search-label">Cari kategori</label>

                    <div class="cat-search-field">
                        <span class="cat-search-icon">
                            <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                            </svg>

                        </span>

                        <input type="text" id="search-category" class="cat-search-input"
                            placeholder="Cari kategori...">
                    </div>
                </div>

                <!-- TOMBOL TAMBAH KATEGORI -->
                <a href="{{ route('categories.create') }}" class="cat-add-btn">
                    + Tambah Kategori
                </a>
            </div>

            <!-- TABLE (tanpa card) -->
            <table class="cat-table no-card">
                <thead>
                    <tr>
                        <th style="width: 260px;">Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 140px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categories as $category)

                    <tr>
                        <!-- NAMA -->
                        <td class="cat-name">
                            {{ $category->nama_kategori }}
                        </td>

                        <!-- DESKRIPSI FULL -->
                        <td class="cat-desc">
                            {{ $category->deskripsi }}
                        </td>

                        <!-- ACTION -->
                        <td class="cat-action">
                            <a href="{{ route('categories.edit', $category->kategori_id) }}"
                                class="cat-action-link cat-action-edit">
                                Edit
                            </a>

                            <button type="button"
                                class="btn-delete cat-action-link cat-action-delete"
                                data-form="delete-form-{{ $category->kategori_id }}">
                                Hapus
                            </button>

                            <form id="delete-form-{{ $category->kategori_id }}"
                                action="{{ route('categories.destroy', $category->kategori_id) }}"
                                method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                            </form>

                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="3" style="text-align:center; padding:20px; color:var(--text-sub);">
                            Belum ada kategori.
                        </td>
                    </tr>

                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
    
    <script src="{{ asset('js/deleteButton.js') }}"></script>
    <script src="{{ asset('js/alert.js') }}"></script>
    <script src="{{ asset('js/categorySearch.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-flash></x-flash>
</body>

</html>