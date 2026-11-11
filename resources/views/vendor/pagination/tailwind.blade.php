@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-xs tabular-nums text-zinc-500">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </p>
        <div class="flex w-full items-center justify-between gap-2 sm:w-auto">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="btn-secondary cursor-default !text-zinc-400"><x-icon name="chevron" class="h-4 w-4 rotate-180"/>{{ __('pagination.previous') }}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-secondary"><x-icon name="chevron" class="h-4 w-4 rotate-180"/>{{ __('pagination.previous') }}</a>
            @endif

            <div class="hidden flex-wrap items-center gap-1 lg:flex">
                @foreach ($elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" aria-label="Halaman {{ $page }}" class="flex h-11 min-w-11 items-center justify-center rounded-md bg-zinc-900 px-3 text-sm font-medium tabular-nums text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="icon-button tabular-nums" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @else
                        <span class="px-2 text-xs text-zinc-500" aria-label="Halaman lainnya">Lainnya</span>
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-secondary">{{ __('pagination.next') }}<x-icon name="chevron" class="h-4 w-4"/></a>
            @else
                <span aria-disabled="true" class="btn-secondary cursor-default !text-zinc-400">{{ __('pagination.next') }}<x-icon name="chevron" class="h-4 w-4"/></span>
            @endif
        </div>
    </nav>
@endif
