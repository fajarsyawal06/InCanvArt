<!DOCTYPE html>
<html lang="id">

<head>
    <x-header></x-header>
    <link rel="stylesheet" href="{{ asset('css/loginregister.css') }}" />
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
                <h2 class="auth-title">Masuk Akun</h2>
                <p class="auth-subtitle">
                    Selamat datang di Galeri Online, silakan melakukan proses Login
                </p>
            </div>
            <form class="login-form" action="{{ route('login.submit') }}" method="post">
                @csrf

                <div class="login-field">
                    <label for="username">Username atau Email</label>
                    <input id="username" type="text" name="username" required autocomplete="username" placeholder="Masukkan username/email">
                </div>

                <div class="login-field">
                    <label for="password">Password</label>

                    <div class="password-wrap">
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">

                        <!-- ID harus sama persis dengan JS kamu -->
                        <button id="togglePassword1" type="button" aria-label="Tampilkan password" title="Tampilkan password">
                            <!-- Mata terbuka -->
                            <svg id="eyeOpen1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>

                            <!-- Mata tertutup (default hidden) -->
                            <svg id="eyeClosed1" class="hidden" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a20.24 20.24 0 0 1 5.06-4.94"></path>
                                <path d="M1 1l22 22"></path>
                                <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"></path>
                            </svg>
                        </button>
                    </div>

                    @if (session('failed'))
                    <div class="login-error">{{ session('failed') }}</div>
                    @endif
                </div>

                <button class="login-btn" type="submit">Submit</button>
            </form>

            <div class="login-footer">
                Belum mempunyai akun? <a href="/register">Buat Akun</a>
            </div>
        </div>
    </div>
</body>

</html>