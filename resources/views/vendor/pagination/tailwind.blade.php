@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="hf-pagination">
        <div class="hf-pagination-info">
            @if ($paginator->firstItem())
                Menampilkan <span class="hf-pagination-highlight">{{ $paginator->firstItem() }}</span>–<span class="hf-pagination-highlight">{{ $paginator->lastItem() }}</span> dari <span class="hf-pagination-highlight">{{ $paginator->total() }}</span> data
            @else
                Menampilkan <span class="hf-pagination-highlight">{{ $paginator->count() }}</span> data
            @endif
        </div>

        @if ($paginator->hasPages())
            <div class="hf-pagination-links">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="hf-page-item hf-page-disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}" title="Halaman sebelumnya">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="hf-page-item" aria-label="{{ __('pagination.previous') }}" title="Halaman sebelumnya">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="hf-page-item hf-page-dots" aria-disabled="true">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="hf-page-item hf-page-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="hf-page-item" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="hf-page-item" aria-label="{{ __('pagination.next') }}" title="Halaman selanjutnya">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                @else
                    <span class="hf-page-item hf-page-disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}" title="Halaman selanjutnya">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif
