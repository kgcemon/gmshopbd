@if ($paginator->hasPages())
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center gap-1">

            {{-- Previous Page Link --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link rounded-pill px-3" href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}" tabindex="-1">
                    &laquo; Previous
                </a>
            </li>

            {{-- Display limited page numbers --}}
            @php
                $start = max($paginator->currentPage() - 1, 1);
                $end = min($start + 2, $paginator->lastPage());
            @endphp

            @for ($page = $start; $page <= $end; $page++)
                <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                    <a class="page-link rounded-circle px-3 py-2" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            {{-- Next Page Link --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link rounded-pill px-3" href="{{ $paginator->hasMorePages() ? $paginator->nextPageUrl() : '#' }}">
                    Next &raquo;
                </a>
            </li>

        </ul>
    </nav>
@endif
