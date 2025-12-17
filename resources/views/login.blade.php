<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | InCanvArt</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="{{ asset('js/showPassword.js') }}" defer></script>
</head>

<body>
    <div class="login-card">
        <div class="login-card__top"></div>

        <div class="login-logo">
            <img src="{{ asset('images/logoincanvart.png') }}" alt="Logo">
        </div>

        <div class="login-card__bottom">
            <form class="login-form" action="{{ route('login.submit') }}" method="post">
                @csrf

                <div class="login-field">
                    <label for="username">Username atau Email</label>
                    <input id="username" name="username" type="text" required>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input id="password" name="password" type="password" required>
                        <button id="togglePassword1" type="button" aria-label="toggle password">👁</button>
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