@if ($paginator->hasPages())
    <nav class="stats-pagination" aria-label="Page navigation">
        <ul class="stats-pagination-list">
            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="stats-page-link stats-page-link-disabled">
                        Previous
                    </span>
                @else
                    <a
                        href="{{ $paginator->previousPageUrl() }}"
                        rel="prev"
                        class="stats-page-link">
                        Previous
                    </a>
                @endif
            </li>

            {{-- Numbered pages --}}
            @foreach ($elements as $element)
                {{-- "..." --}}
                @if (is_string($element))
                    <li>
                        <span class="stats-page-link stats-page-link-dots">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="stats-page-link stats-page-link-active"
                                    aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $url }}"
                                    class="stats-page-link">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a
                        href="{{ $paginator->nextPageUrl() }}"
                        rel="next"
                        class="stats-page-link">
                        Next
                    </a>
                @else
                    <span class="stats-page-link stats-page-link-disabled">
                        Next
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
