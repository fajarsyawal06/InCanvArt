<div id="masonry" class="masonry">
    @forelse($artworks as $art)
        <article class="card">

            {{-- Link utama pakai slug --}}
            <a href="{{ route('artworks.show', $art) }}" class="block">
                <img src="{{ $art->file_url }}" alt="{{ $art->judul }}" loading="lazy" class="card-img">
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
                    <p class="card-desc">{{ $art->deskripsi }}</p>
                @endif

                @isset($art->tanggal_upload)
                    <time class="card-time">
                        {{ \Carbon\Carbon::parse($art->tanggal_upload)->diffForHumans() }}
                    </time>
                @endisset
            </div>
        </article>
    @empty
        <p class="text-gray-600 dark:text-gray-300 text-center py-10">
            Belum ada artwork diunggah.
        </p>
    @endforelse
</div>
