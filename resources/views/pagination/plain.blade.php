@if ($paginator->hasPages())
    <style>
        .pagination-plain {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            padding: 16px 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .pagination-plain__link {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            color: #333;
            text-decoration: none;
        }

        .pagination-plain__link:hover:not(.is-disabled):not(.is-active) {
            background-color: #f0f0f0;
        }

        .pagination-plain__link.is-active {
            background-color: #333;
            color: #fff;
            border-color: #333;
        }

        .pagination-plain__link.is-disabled {
            color: #aaa;
            border-color: #eee;
        }

        .pagination-plain__dots {
            padding: 6px 4px;
            color: #999;
        }
    </style>

    <nav class="pagination-plain" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="pagination-plain__link is-disabled">&laquo; Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-plain__link">&laquo; Previous</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-plain__dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-plain__link is-active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-plain__link">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-plain__link">Next &raquo;</a>
        @else
            <span class="pagination-plain__link is-disabled">Next &raquo;</span>
        @endif
    </nav>
@endif
