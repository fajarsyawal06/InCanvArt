<!DOCTYPE html>
<html lang="en">

<head>
    <x-header></x-header>
    <link rel="stylesheet" href="{{ asset('css/pin.css') }}">
</head>

<body class="bg-gray-50 dark:bg-[#393053]">
    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>
    <div class="sm:ml-64 mt-16 px-6">
        <br>
        @if ($favorites->isEmpty())
        <p class="text-gray-300 text-center py-10">
            Belum ada artwork yang kamu bookmark.
        </p>
        @else
        <div id="masonry" class="masonry">
            @foreach ($favorites as $fav)
            @php
            $art = $fav->artwork;
            @endphp

            @if ($art)
            <article class="card">
                <a href="{{ route('artworks.show', $art) }}" class="block">
                    <img src="{{ $art->file_url }}"
                        alt="{{ $art->judul }}"
                        loading="lazy"
                        class="card-img">
                </a>

                <div class="card-meta">
                    <div class="card-head">
                        <h3 class="card-title">
                            <a href="{{ route('artworks.show', $art) }}">
                                {{ $art->judul }}
                            </a>
                        </h3>

                        @if(optional($art->kategori)->nama_kategori)
                        <span class="card-tag">
                            {{ $art->kategori->nama_kategori }}
                        </span>
                        @endif
                    </div>

                    @if($art->deskripsi)
                    <p class="card-desc">
                        {{ $art->deskripsi }}
                    </p>
                    @endif

                    @isset($art->tanggal_upload)
                    <time class="card-time">
                        {{ \Carbon\Carbon::parse($art->tanggal_upload)->diffForHumans() }}
                    </time>
                    @endisset
                </div>
            </article>
            @endif
            @endforeach
        </div>
        @endif
    </div>
</body>

</html>