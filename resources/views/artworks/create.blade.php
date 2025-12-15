<!DOCTYPE html>
<html lang="id">

<head>
  <x-header></x-header>
  <link rel="stylesheet" href="{{ asset('css/createArtwork.css') }}">
</head>

<body>

  <x-navbar></x-navbar>
  <x-sidebar></x-sidebar>

  <div class="sm:ml-64 mt-16 pt-10">
    <div class="create-art-wrap">
      <h1 class="create-art-title">Tambah Artwork</h1>
      <div class="create-art-topline"></div>
      <div class="create-art-card">
        <form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="create-art-grid">

            {{-- Kolom kiri: Preview + File --}}
            <div class="ca-col-left">
              <p class="ca-section-title">Preview Artwork</p>
              <p class="ca-section-hint">Unggah karya untuk melihat pratinjau sesuai ukuran aslinya.</p>

              <div class="ca-preview-box" id="preview-container">
                <img id="preview-image" src="" alt="Preview Artwork" style="display:none;">
                <div id="preview-placeholder" class="ca-preview-placeholder">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.8"></circle>
                    <path d="M21 15l-3.5-3.5L14 15l-2.5-2.5L7 17"></path>
                  </svg>
                  <span>Pilih file gambar untuk mulai.</span>
                </div>
              </div>

              {{-- Bar file yang rapi --}}
              <div class="ca-file-wrapper">
                <label for="file" class="ca-file-label-btn">
                  Pilih File Artwork
                </label>
                <span id="file-name" class="ca-file-name">
                  Belum ada file dipilih
                </span>
              </div>

              <input type="file" name="file" id="file" accept="image/*" required
                class="ca-file-input-hidden">

              @error('file')
              <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
              @enderror
              <br>
              <p class="ca-file-note">
                Format: JPG/PNG. Ukuran gambar akan otomatis menyesuaikan rasio aslinya.
              </p>
            </div>

            {{-- Kolom kanan --}}
            <div class="ca-col-right">
              <div class="ca-field-group">
                <label for="judul" class="ca-field-label">Judul Artwork</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" class="ca-input-pill" required>
              </div>

              <div class="ca-field-group">
                <label for="kategori_id" class="ca-field-label">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="ca-select-pill" required>
                  <option value="" disabled {{ old('kategori_id') ? '' : 'selected' }}>Pilih kategori</option>
                  @foreach($categories as $category)
                  <option value="{{ $category->kategori_id }}" {{ old('kategori_id') == $category->kategori_id ? 'selected' : '' }}>
                    {{ $category->nama_kategori }}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="ca-field-group">
                <label for="deskripsi" class="ca-field-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="ca-textarea" placeholder="Ceritakan konsep, teknik, atau makna dari karya ini...">{{ old('deskripsi') }}</textarea>
              </div>

              <!-- tombol diposisikan di kolom kanan -->
              <div class="ca-actions">
                <a href="{{ url()->previous() }}" class="ca-btn ca-btn-cancel">Kembali</a>
                <button type="submit" class="ca-btn ca-btn-save">Simpan</button>
              </div>
            </div>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/createArtwork.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>