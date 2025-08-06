@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" class="d-flex justify-content-center">
        <ul class="pagination pagination-lg">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fe fe-chevron-left"></i>
                        <span class="d-none d-sm-inline ms-1">Previous</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Previous page">
                        <i class="fe fe-chevron-left"></i>
                        <span class="d-none d-sm-inline ms-1">Previous</span>
                    </a>
                </li>
            @endif

            {{-- First Page Link (if not near the beginning) --}}
            @if($paginator->currentPage() > 3)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($paginator->currentPage() > 4)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($i = max(1, $paginator->currentPage() - 2); $i <= min($paginator->lastPage(), $paginator->currentPage() + 2); $i++)
                @if ($i == $paginator->currentPage())
                    <li class="page-item active">
                        <span class="page-link">
                            {{ $i }}
                            <span class="sr-only">(current)</span>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($i) }}" title="Go to page {{ $i }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last Page Link (if not near the end) --}}
            @if($paginator->currentPage() < $paginator->lastPage() - 2)
                @if($paginator->currentPage() < $paginator->lastPage() - 3)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Next page">
                        <span class="d-none d-sm-inline me-1">Next</span>
                        <i class="fe fe-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <span class="d-none d-sm-inline me-1">Next</span>
                        <i class="fe fe-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    {{-- Pagination Info --}}
    <div class="d-flex justify-content-center mt-3">
        <div class="pagination-info">
            <small class="text-muted px-3 py-2 bg-light rounded">
                <i class="fe fe-info me-1"></i>
                Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> 
                of <strong>{{ $paginator->total() }}</strong> results
            </small>
        </div>
    </div>
@endif
