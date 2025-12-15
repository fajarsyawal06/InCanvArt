<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Online</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logoincanvart.png') }}">
    @vite('resources/css/app.css')
    @vite('resources/js/showPassword.js')
</head>
<body>
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img src="{{ asset('images/logoincanvart.png') }}" alt="Logo" class="mx-auto h-16 w-auto" />
        <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
            Selamat Datang di Galeri Online<br>Silakan Registrasi
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        
        <!-- Pesan Alert -->
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-100 border border-green-300 text-green-700 px-4 py-2 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('failed'))
            <div class="mb-4 rounded-md bg-red-100 border border-red-300 text-red-700 px-4 py-2 text-sm">
                {{ session('failed') }}
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Input Username -->
            <div>
                <label for="username" class="block text-sm/6 font-medium text-gray-900">Username</label>
                <div class="mt-2">
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required
                           autocomplete="username"
                           class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1
                           -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2
                           focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </div>
                @error('username')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input Email -->
            <div>
                <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
                <div class="mt-2">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           autocomplete="email"
                           class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1
                           -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2
                           focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </div>
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input Password -->
            <div>
                <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                <div class="mt-2 relative">
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           class="block w-full rounded-md bg-white px-3 py-1.5 pr-10 text-base text-gray-900 outline-1
                           -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2
                           focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />

                    <!-- Tombol Show/Hide -->
                    <button id="togglePassword1" type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center
                            rounded p-1 text-gray-500 hover:text-gray-700 focus:outline-none"
                            aria-label="Tampilkan password" title="Tampilkan password">
                        <!-- Mata terbuka -->
                        <svg id="eyeOpen1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <!-- Mata tertutup -->
                        <svg id="eyeClosed1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a20.24 20.24 0 0 1 5.06-4.94"></path>
                            <path d="M1 1l22 22"></path>
                            <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input Konfirmasi Password -->
            <div class="mt-4">
                <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900">Konfirmasi Password</label>
                <div class="mt-2 relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           autocomplete="new-password"
                           class="block w-full rounded-md bg-white px-3 py-1.5 pr-10 text-base text-gray-900 outline-1
                           -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2
                           focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />

                    <!-- Tombol Show/Hide -->
                    <button id="togglePassword2" type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center
                            rounded p-1 text-gray-500 hover:text-gray-700 focus:outline-none"
                            aria-label="Tampilkan password" title="Tampilkan password">
                        <!-- Mata terbuka -->
                        <svg id="eyeOpen2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <!-- Mata tertutup -->
                        <svg id="eyeClosed2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a20.24 20.24 0 0 1 5.06-4.94"></path>
                            <path d="M1 1l22 22"></path>
                            <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div>
                <button type="submit"
                        class="flex w-full justify-center rounded-md bg-[#004aad] px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs
                        hover:bg-[#38b6ff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Sign Up
                </button>
            </div>
        </form>

        <!-- Link Login -->
        <p class="mt-10 text-center text-sm/6 text-gray-500">
            Sudah memiliki akun?
            <a href="/login" class="font-semibold text-[#004aad] hover:text-[#38b6ff]">Masuk Akun</a>
        </p>
    </div>
</div>
</body>
</html>
