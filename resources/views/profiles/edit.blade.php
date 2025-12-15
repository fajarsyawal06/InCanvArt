<!DOCTYPE html>
<html lang="en">

<head>
    <x-header></x-header>

    {{-- Cropper.js --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
    <link rel="stylesheet" href="{{ asset('css/editProfile.css') }}">
</head>

<body>
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="sm:ml-64 mt-16 pt-10">
        <div class="edit-profile-wrap">
            <h1 class="edit-profile-title">Edit Profil</h1>
            <div class="edit-profile-topline"></div>

            <div class="edit-profile-card">
                <form action="{{ route('profiles.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="edit-profile-grid">
                        {{-- KIRI: AVATAR & SOSIAL --}}
                        <div class="ep-col-left">
                            <h2 class="ep-section-title">Foto Profil</h2>

                            <div class="ep-profile-photo-wrap">
                                <div id="profilePreview" class="ep-profile-circle"
                                    style="{{ $profileUrl ? "background-image:url('$profileUrl')" : '' }}">
                                    @unless($profileUrl) Foto @endunless
                                </div>

                                <button type="button" class="ep-btn"
                                    onclick="document.getElementById('profileInput').click()">
                                    Ganti Profil
                                </button>

                                <input id="profileInput" type="file" accept="image/*" hidden>
                                <input type="hidden" name="cropped_foto_profil" id="croppedFotoProfil">
                            </div>

                            {{-- FIELD USERNAME --}}
                            <div class="ep-field-group">
                                <label class="ep-field-label">Username</label>
                                <input name="username" class="ep-input-pill"
                                    value="{{ old('username', $user->username) }}">
                            </div>

                            <div class="ep-field-group">
                                <label class="ep-field-label">Nama Lengkap</label>
                                <input name="nama_lengkap" class="ep-input-pill"
                                    value="{{ old('nama_lengkap', $profile->nama_lengkap) }}">
                            </div>

                            @auth
                            @if (auth()->user()->role === 'seniman')
                            <div class="ep-field-group">
                                <label class="ep-field-label">Instagram</label>
                                <input name="instagram" class="ep-input-pill"
                                    value="{{ old('instagram', $instagram) }}">
                            </div>
                            <div class="ep-field-group">
                                <label class="ep-field-label">Facebook</label>
                                <input name="facebook" class="ep-input-pill"
                                    value="{{ old('facebook', $facebook) }}">
                            </div>
                            <div class="ep-field-group">
                                <label class="ep-field-label">Twitter / X</label>
                                <input name="twitter" class="ep-input-pill"
                                    value="{{ old('twitter', $twitter) }}">
                            </div>
                            @endif
                            @endauth
                        </div>


                        {{-- KANAN: COVER & BIO --}}
                        <div class="ep-col-right">
                            <h2 class="ep-section-title">Foto Cover</h2>

                            <div class="ep-cover-wrap">
                                <div id="coverPreview"
                                    class="ep-cover-box"
                                    style="{{ $coverUrl ? "background-image:url('$coverUrl')" : '' }}">
                                </div>

                                <!-- Tombol di bawah gambar, bukan overlay, center -->
                                <button type="button"
                                    class="ep-change-cover-btn"
                                    onclick="document.getElementById('coverInput').click()">
                                    Ganti Cover
                                </button>

                                <input id="coverInput" type="file" accept="image/*" hidden>
                                <input type="hidden" name="cropped_foto_cover" id="croppedFotoCover">
                            </div>

                            @auth
                            @if (auth()->user()->role === 'seniman')
                            <div class="ep-field-group" style="margin-top: 18px;">
                                <label class="ep-bio-label">Bio</label>
                                <textarea name="bio" class="ep-bio-textarea"
                                    placeholder="Tuliskan deskripsi singkat tentang diri dan karya Anda.">{{ old('bio', $profile->bio) }}</textarea>
                            </div>
                            @endif
                            @endauth
                        </div>

                    </div>
                    <div class="ep-bottom-btns">
                        <a href="{{ route('profiles.index') }}" class="ep-btn ep-btn-cancel">Kembali</a>
                        <button type="submit" class="ep-btn">Simpan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- MODAL CROP --}}
    <div id="cropperContainer"
        class="fixed inset-0 hidden z-[99999] bg-black/80 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-[#18122B] rounded-2xl shadow-2xl p-6 w-[92%] max-w-[860px] flex flex-col space-y-4 border border-[#635985]">
            <h3 id="cropperTitle" class="text-lg font-semibold text-white text-center">Sesuaikan Gambar</h3>
            <div class="relative w-full max-h-[70vh] bg-black flex items-center justify-center rounded-xl overflow-hidden">
                <img id="cropperImage" src="" alt="Crop Preview"
                    class="max-w-full max-h-[70vh] block object-contain" />
                <div id="circleMask"></div>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" id="cropperCancelBtn"
                    class="px-4 py-2 rounded-full bg-gray-500 text-white hover:bg-gray-400">Batal</button>
                <button type="button" id="cropperSaveBtn"
                    class="px-4 py-2 rounded-full bg-emerald-500 text-white hover:bg-emerald-600">Simpan Crop</button>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="{{ asset('js/editProfile.js') }}"></script>
</body>

</html>