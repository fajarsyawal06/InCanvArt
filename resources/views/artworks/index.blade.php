<!DOCTYPE html>
<html lang="id">

<head>
  <x-header></x-header>
  {{-- Masonry CSS --}}
  <link rel="stylesheet" href="{{ asset('css/pin.css') }}">
</head>

<body class="bg-gray-50 dark:bg-[#393053]">

  {{-- Navbar dan Sidebar --}}
  <x-navbar></x-navbar>
  <x-sidebar></x-sidebar>

  {{-- Konten utama --}}
  <div class="sm:ml-64 mt-16 px-6">
    <br>
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
      {{ session('success') }}
    </div>
    @endif

    <x-masonry :artworks="$artworks"></x-masonry>

    <script src="{{ asset('js/pin.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>