@if ($paginator->hasPages())
<div class="col-sm-12 d-flex align-items-center justify-content-center justify-content-between">
    <div class="dataTables_paginate paging_simple_numbers">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </div>
    <div class="datatable-pager-info my-2 mb-sm-0">
        <div class="dropdown bootstrap-select datatable-pager-size w-60px">

            <div class="dropdown-menu">
                <div class="inner show" role="listbox" id="bs-select-1" tabindex="-1">
                    <ul class="dropdown-menu inner show" role="presentation"></ul>
                </div>
            </div>
        </div>

        <span class="datatable-pager-detail">
                <b> Mostrando {{$paginator->currentPage()  == '1' ? 1 : $paginator->perPage() *  ($paginator->currentPage() -1) + 1  }} - {{ $paginator->total() < $paginator->perPage() * $paginator->currentPage() ? $paginator->total() : $paginator->perPage() * $paginator->currentPage() }} de {{$paginator->total()}} </b>
            </span>
    </div>

</div>
@endif
