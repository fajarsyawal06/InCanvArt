@if ($paginator->hasPages())
    <nav class="stats-pagination" role="navigation" aria-label="Pagination Navigation">
        <ul class="stats-pagination-list">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="stats-page-link stats-page-link-disabled">Previous</span>
                </li>
            @else
                <li>
                    <a class="stats-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)

                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span class="stats-page-link stats-page-link-dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="stats-page-link stats-page-link-active">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a class="stats-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif

            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="stats-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
                </li>
            @else
                <li>
                    <span class="stats-page-link stats-page-link-disabled">Next</span>
                </li>
            @endif

        </ul>
    </nav>
@endif
