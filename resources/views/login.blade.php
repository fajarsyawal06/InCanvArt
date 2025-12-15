<!DOCTYPE html>
<html lang="en">

<head>
    <x-header></x-header>
    @vite('resources/css/app.css')
    @vite('resources/js/showPassword.js')
</head>

<body>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img src="{{ asset('images/logoincanvart.png') }}" alt="Logo" class="mx-auto h-16 w-auto" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Selamat Datang InCanvArt<br>Silahkan Login</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <br>
            <!-- Input Username atau Email -->
            <form action="{{ route('login.submit') }}" method="post" class="space-y-6">
                @csrf
                <div>
                    <label for="username" class="block text-sm/6 font-medium text-gray-900">Username atau Email</label>
                    <div class="mt-2">
                        <input id="username" type="username" name="username" required autocomplete="username" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                    <div class="mt-2 relative">
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="block w-full rounded-md bg-white px-3 py-1.5 pr-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />

                        <!-- Tombol Show/Hide -->
                        <button id="togglePassword1" type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/4 inline-flex items-center justify-center rounded p-1 text-gray-500 hover:text-gray-700 focus:outline-none"
                            aria-label="Tampilkan password" title="Tampilkan password" aria-pressed="false">
                            <!-- Mata terbuka -->
                            <svg id="eyeOpen1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>

                            <!-- Mata tertutup -->
                            <svg id="eyeClosed1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a20.24 20.24 0 0 1 5.06-4.94"></path>
                                <path d="M1 1l22 22"></path>
                                <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"></path>
                            </svg>
                        </button>
                    </div>
                    @if (session('failed'))
                    <p class="text-sm text-red-600 mt-1">
                        {{ session('failed') }}
                    </p>
                    @endif
                </div>

                <!-- Tombol submit -->
                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-[#004aad] px-3 py-1.5 text-sm/6 font-semibold
                     text-white shadow-xs hover:bg-[#38b6ff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
                </div>
            </form>

            <!-- Link Registrasi -->
            <p class="mt-10 text-center text-sm/6 text-gray-500">
                Belum mempunyai akun?
                <a href="/register" class="font-semibold text-[#004aad] hover:text-[#38b6ff]">Buat Akun</a>
            </p>
        </div>
    </div>
</body>

</html>