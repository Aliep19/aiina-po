@props([
    'paginator',        // hasil ->paginate()
    'searchParam' => 'search',
    'sortByParam'  => 'sort_by',
    'orderParam'   => 'order',
    'perPageParam' => 'per_page',
])

@php
    $currentPage = $paginator->currentPage();
    $lastPage    = $paginator->lastPage();
    $from        = $paginator->firstItem() ?? 0;
    $to          = $paginator->lastItem()  ?? 0;
    $total       = $paginator->total();

    // Buat window halaman (max 5 tombol)
    $start = max(1, $currentPage - 2);
    $end   = min($lastPage, $start + 4);
    $start = max(1, $end - 4);
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
    {{-- Info --}}
    <small class="text-secondary">
        Menampilkan <strong>{{ $from }}</strong>–<strong>{{ $to }}</strong>
        dari <strong>{{ $total }}</strong> data
    </small>

    {{-- Navigasi --}}
    @if ($lastPage > 1)
    <nav>
        <ul class="pagination pagination-sm mb-0">
            {{-- Prev --}}
            <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                <a class="page-link pagination-link" href="#" data-page="{{ $currentPage - 1 }}">&laquo;</a>
            </li>

            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="1">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
            @endif

            @for ($p = $start; $p <= $end; $p++)
                <li class="page-item {{ $p == $currentPage ? 'active' : '' }}">
                    <a class="page-link pagination-link" href="#" data-page="{{ $p }}">{{ $p }}</a>
                </li>
            @endfor

            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="{{ $lastPage }}">{{ $lastPage }}</a>
                </li>
            @endif

            {{-- Next --}}
            <li class="page-item {{ $currentPage == $lastPage ? 'disabled' : '' }}">
                <a class="page-link pagination-link" href="#" data-page="{{ $currentPage + 1 }}">&raquo;</a>
            </li>
        </ul>
    </nav>
    @endif
</div>
