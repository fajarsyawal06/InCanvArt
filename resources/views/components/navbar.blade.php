<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-[#18122B] dark:border-[#635985]">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    {{-- PARENT WAJIB relative UNTUK CENTER ABSOLUTE --}}
    <div class="relative flex items-center justify-between">

      {{-- KIRI: Logo --}}
      <div class="flex items-center">
        <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24">
          <img src="{{ asset('images/logoincanvart.png') }}" class="h-8 me-3" alt="InCanvArt Logo" />
          <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">
            InCanvArt
          </span>
        </a>
      </div>

      {{-- TENGAH --}}
      @auth
      {{-- MODE PENGUNJUNG / SENIMAN: SEARCH BAR DI AREA TENGAH (FLEKSIBEL) --}}
      @if (auth()->user()->role === 'pengunjung' || auth()->user()->role === 'seniman')
      <div class="flex-1 flex justify-center">
        <form class="w-full max-w-2xl" action="{{ route('search.index') }}" method="GET">
          <div class="flex items-center gap-2">
            <div class="relative flex-1">
              <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                  fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                    d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
              </div>

              <input
                type="search"
                id="navbar-search"
                name="q"
                autocomplete="off"
                value="{{ request('q') }}"
                class="block w-full p-3 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50
                      focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                      dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Search artworks, artists..."
                data-user-search-url="{{ route('search.users') }}" />

              <div
                id="navbar-search-modal"
                class="hidden absolute left-0 right-0 mt-2 z-50 max-h-80 overflow-y-auto
                      bg-white dark:bg-[#18122B] border border-gray-200 dark:border-[#443C68]
                      rounded-xl shadow-lg">
                <div id="navbar-search-results" class="py-2">
                  {{-- hasil via JS --}}
                </div>
              </div>
            </div>

            <button
              type="submit"
              class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300
                    font-medium rounded-lg text-sm px-4 py-2 dark:bg-[#443C68] dark:hover:bg-[#635985] dark:focus:ring-blue-800">
              Cari
            </button>
          </div>
        </form>
      </div>
      @endif

      {{-- MODE ADMIN: MENU TEPAT DI TENGAH HORIZONTAL & VERTICAL --}}
      @if (auth()->user()->role === 'admin')
      <div class="absolute inset-x-0 top-0 bottom-0 flex justify-center items-center pointer-events-none">
        <div class="flex gap-10 font-medium text-white dark:text-gray-200 pointer-events-auto">
          <a href="{{ route('admin') }}" class="hover:text-blue-400 transition">Dashboard</a>
          <a href="{{ route('statistics.index') }}" class="hover:text-blue-400 transition">Statistic</a>
          <a href="{{ route('categories.index') }}" class="hover:text-blue-400 transition">Kategori</a>
          <a href="{{ route('moderations.index') }}" class="hover:text-blue-400 transition">Moderasi</a>
          <a href="{{ route('users.index') }}" class="hover:text-blue-400 transition">Users</a>
        </div>
      </div>
      @endif
      @endauth

      {{-- KANAN: USER AVATAR + DROPDOWN --}}
      <div class="flex items-center">
        @auth
        <div class="flex items-center ms-3">
          <div>
            <button type="button"
              class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
              aria-expanded="false" data-dropdown-toggle="dropdown-user">
              <span class="sr-only">Open user menu</span>
              <img
                class="rounded-full w-10 h-10 object-cover"
                src="{{ $profile && $profile->foto_profil
                        ? asset('storage/' . $profile->foto_profil)
                        : asset('images/avatar-sample.jpg') }}"
                alt="Foto Profil"
                loading="lazy">
            </button>
          </div>
          <div
            class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm
                dark:bg-gray-700 dark:divide-gray-600"
            id="dropdown-user">
            <div class="px-4 py-3" role="none">
              <p class="text-sm text-gray-900 dark:text-white" role="none">
                {{ Auth::user()->username }}
              </p>
              <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                {{ Auth::user()->email }}
              </p>
            </div>
            <ul class="py-1" role="none">
              <li>
                <a href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100
                      dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                  role="menuitem"
                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                  @csrf
                </form>
              </li>
            </ul>
          </div>
        </div>
        @endauth
      </div>

    </div>
  </div>
</nav>

<script src="{{ asset('js/liveSearch.js') }}"></script>