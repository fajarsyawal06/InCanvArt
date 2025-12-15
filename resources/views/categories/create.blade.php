<!DOCTYPE html>
<html lang="en">

<head>
    <x-header></x-header>
    <title>Tambah Kategori</title>
    <link rel="stylesheet" href="{{ asset('css/createCategory.css') }}">
</head>

<body class="add-category-page">
    <x-navbar></x-navbar>

    <div class="sm:mt-16">
        <div class="add-cat-wrap">

            <!-- TITLE -->
            <h2 class="add-cat-title">
                Tambah Kategori Baru
            </h2>
            <div class="add-cat-topline"></div>

            <!-- CARD FORM -->
            <div class="add-cat-card">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf

                    <!-- NAMA KATEGORI -->
                    <div class="ac-field-group">
                        <label class="ac-field-label">
                            Nama Kategori
                        </label>
                        <input
                            type="text"
                            name="nama_kategori"
                            required
                            class="ac-input-pill"
                            placeholder="Misalnya: Lukisan, Sketsa, Digital Art">
                    </div>

                    <!-- DESKRIPSI -->
                    <div class="ac-field-group">
                        <label class="ac-field-label">
                            Deskripsi (Opsional)
                        </label>
                        <textarea
                            name="deskripsi"
                            rows="4"
                            class="ac-textarea"
                            placeholder="Tuliskan deskripsi singkat mengenai kategori ini"></textarea>
                    </div>

                    <!-- BUTTON -->
                    <div class="ac-actions">
                        <a href="{{ route('categories.index') }}"
                            class="ac-btn ac-btn-outline">
                            Batal
                        </a>

                        <button type="submit"
                            class="ac-btn">
                            Simpan Kategori
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- kalau mau flash alert di sini --}}
    {{-- <x-flash></x-flash> --}}
</body>

</html>
