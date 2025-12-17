<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>

    <!-- PAKAI CSS LOGIN YANG SAMA -->
    <link rel="stylesheet" href="{{ asset('css/loginregister.css') }}" />

    <!-- JS TETAP -->
    <script src="{{ asset('js/showPassword.js') }}" defer></script>
</head>

<body>
    <div class="login-card">
        <div class="login-card__top"></div>

        <div class="login-logo">
            <img src="{{ asset('images/logoincanvart.png') }}" alt="Logo InCanvArt">
        </div>

        <div class="login-card__bottom">
            <div class="auth-header">
                <h2 class="auth-title">Registrasi Akun</h2>
                <p class="auth-subtitle">
                    Selamat datang di Galeri Online, silakan lengkapi data Anda
                </p>
            </div>
            <form class="login-form" action="{{ route('register.submit') }}" method="post"
                @csrf

                <!-- Username -->
                <div class="login-field">
                    <label for="username">Username</label>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autocomplete="username"
                        placeholder="Masukkan username">
                    @error('username')
                    <div class="login-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="login-field">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="Masukkan email">
                    @error('email')
                    <div class="login-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="login-field">
                    <label for="password">Password</label>

                    <div class="password-wrap">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Masukkan password">

                        <button id="togglePassword1" type="button"
                            aria-label="Tampilkan password"
                            title="Tampilkan password">

                            <!-- Mata terbuka -->
                            <svg id="eyeOpen1" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>

                            <!-- Mata tertutup -->
                            <svg id="eyeClosed1" class="hidden" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a20.24 20.24 0 0 1 5.06-4.94"></path>
                                <path d="M1 1l22 22"></path>
                                <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"></path>
                            </svg>
                        </button>
                    </div>

                    @error('password')
                    <div class="login-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="login-field">
                    <label for="password_confirmation">Konfirmasi Password</label>

                    <div class="password-wrap">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Konfirmasi password">

                        <button id="togglePassword2" type="button"
                            aria-label="Tampilkan password"
                            title="Tampilkan password">

                            <!-- Mata terbuka -->
                            <svg id="eyeOpen2" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>

                            <!-- Mata tertutup -->
                            <svg id="eyeClosed2" class="hidden" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a20.24 20.24 0 0 1 5.06-4.94"></path>
                                <path d="M1 1l22 22"></path>
                                <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button class="login-btn" type="submit">Sign Up</button>
            </form>

            <div class="login-footer">
                Sudah memiliki akun? <a href="/login">Masuk Akun</a>
            </div>
        </div>
    </div>
</body>

</html>