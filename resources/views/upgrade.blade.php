<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>
    <link rel="stylesheet" href="{{ asset('css/upgradeForm.css') }}">
</head>

<body class="bg-gray-50 dark:bg-[#393053]">
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="sm:ml-64 mt-16 px-0">
        <div class="upgrade-page">
            <div class="upgrade-wrap">

                <h1 class="upgrade-title">Upgrade ke Akun Seniman</h1>
                <div class="upgrade-topline"></div>
                

                <div class="upgrade-card">
                    <form action="{{ route('upgrade.submit') }}" method="POST">
                        @csrf

                        <div class="upgrade-grid">
                            {{-- Nama Seniman --}}
                            <div class="up-field-group">
                                <label class="up-label">Nama Seniman</label>
                                <input type="text" name="nama_seniman" class="up-input"
                                    value="{{ old('nama_seniman') }}"
                                    placeholder="Nama panggung / nama karya" required>
                                @error('nama_seniman')
                                <div class="up-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Instagram --}}
                            <div class="up-field-group">
                                <label class="up-label">Instagram</label>
                                <input type="text" name="instagram" class="up-input"
                                    value="{{ old('instagram') }}"
                                    placeholder="@username">
                                @error('instagram')
                                <div class="up-error">{{ $message }}</div>
                                @enderror
                                <div class="up-hint">Opsional, boleh dikosongkan.</div>
                            </div>

                            {{-- Facebook --}}
                            <div class="up-field-group">
                                <label class="up-label">Facebook</label>
                                <input type="text" name="facebook" class="up-input"
                                    value="{{ old('facebook') }}"
                                    placeholder="Link atau username">
                                @error('facebook')
                                <div class="up-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Twitter / X --}}
                            <div class="up-field-group">
                                <label class="up-label">Twitter / X</label>
                                <input type="text" name="twitter" class="up-input"
                                    value="{{ old('twitter') }}"
                                    placeholder="@username">
                                @error('twitter')
                                <div class="up-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Bio Seniman --}}
                            <div class="up-field-group">
                                <label class="up-label">Bio Seniman</label>
                                <textarea name="bio" class="up-textarea" rows="5"
                                    placeholder="Ceritakan tentang gaya berkarya, medium favorit, pengalaman pameran, atau hal lain yang merepresentasikan Anda."
                                    required>{{ old('bio') }}</textarea>
                                @error('bio')
                                <div class="up-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="up-divider">

                        <div class="up-bottom-btns">
                            <button type="submit" class="up-btn-primary">
                                Upgrade Sekarang
                            </button>

                            <a href="{{ route('dashboard') }}" class="up-btn-secondary">
                                Batal
                            </a>
                        </div>

                        <div class="up-footer-note">
                            Dengan mengajukan upgrade, Anda menyetujui bahwa profil Anda akan ditampilkan sebagai seniman kepada publik.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('js/alert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-flash></x-flash>
</body>

</html>