<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | InCanvArt</title>

    <!-- CSS LOGIN -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />

    <!-- OPTIONAL: jika kamu tetap pakai showPassword.js -->
    <script src="{{ asset('js/showPassword.js') }}" defer></script>
</head>

<body>
    <div class="login-card">
        <div class="login-card__top"></div>

        <div class="login-logo">
            <img src="{{ asset('images/logoincanvart.png') }}" alt="Logo InCanvArt">
        </div>

        <div class="login-card__bottom">
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

                        <!-- tombol show/hide (tanpa library) -->
                        <button id="togglePassword1" type="button" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password">
                            👁
                        </button>
                    </div>

                    @if (session('failed'))
                    <div class="login-error">{{ session('failed') }}</div>
                    @endif
                </div>

                <button class="login-btn" type="submit">Submit</button>
            </form>

            <div class="login-footer">
                Belum mempunyai akun?
                <a href="/register">Buat Akun</a>
            </div>
        </div>
    </div>

    <!-- Toggle password (kalau kamu belum punya showPassword.js, ini sudah cukup) -->
    <script>
        const btn = document.getElementById('togglePassword1');
        const input = document.getElementById('password');

        if (btn && input) {
            btn.addEventListener('click', () => {
                const isPw = input.type === 'password';
                input.type = isPw ? 'text' : 'password';
                btn.setAttribute('aria-pressed', String(isPw));
                btn.textContent = isPw ? '🙈' : '👁';
            });
        }
    </script>
</body>

</html>