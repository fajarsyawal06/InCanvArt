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
    <div class="login-card-top"></div>

    <div class="login-logo-wrapper">
      <img src="{{ asset('images/logoincanvart.png') }}" alt="Logo InCanvArt">
    </div>

    <div class="login-card-bottom">
      <form action="{{ route('login.submit') }}" method="post" class="login-form">
        @csrf

        <div>
          <label>Username atau Email</label>
          <input type="text" name="username" required>
        </div>

        <div>
          <label>Password</label>
          <div class="password-wrapper">
            <input id="password" type="password" name="password" required>
            <button id="togglePassword1" type="button">
              👁
            </button>
          </div>

          @if (session('failed'))
            <div class="login-error">{{ session('failed') }}</div>
          @endif
        </div>

        <button type="submit" class="login-btn">Submit</button>
      </form>

      <div class="login-footer">
        Belum mempunyai akun?
        <a href="/register">Buat Akun</a>
      </div>
    </div>
  </div>
</body>
</html>
