@php
    /**
     * Vista de paginación del panel (basada en el diseño `diseno-interfaz-ecommerce`).
     *
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @var array $elements
     */
    $from = $paginator->firstItem() ?? 0;
    $to = $paginator->lastItem() ?? $paginator->count();
@endphp

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-muted-foreground">
        Mostrando
        <span class="font-mono font-medium text-foreground">{{ $from }}</span>–<span
            class="font-mono font-medium text-foreground">{{ $to }}</span>
        de <span class="font-mono font-medium text-foreground">{{ $paginator->total() }}</span>
    </p>

    @if ($paginator->hasPages())
        <nav aria-label="Paginación" class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="ut-page-btn" aria-disabled="true">
                    <x-icon name="chevrons-left" />
                    <span class="sr-only">Primera página</span>
                </span>
            @else
                <a class="ut-page-btn" href="{{ $paginator->url(1) }}" title="Primera página" rel="prev">
                    <x-icon name="chevrons-left" />
                    <span class="sr-only">Primera página</span>
                </a>
            @endif

            @if ($paginator->onFirstPage())
                <span class="ut-page-btn" aria-disabled="true">
                    <x-icon name="chevron-left" />
                    <span class="sr-only">Página anterior</span>
                </span>
            @else
                <a class="ut-page-btn" href="{{ $paginator->previousPageUrl() }}" title="Página anterior" rel="prev">
                    <x-icon name="chevron-left" />
                    <span class="sr-only">Página anterior</span>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="ut-page-span" aria-hidden="true">{{ $element }}</span>
                @elseif (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="ut-page-btn ut-page-btn-active" aria-current="page">
                                {{ $page }}
                                <span class="sr-only">Página actual</span>
                            </span>
                        @else
                            <a class="ut-page-btn" href="{{ $url }}" aria-label="Ir a la página {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="ut-page-btn" href="{{ $paginator->nextPageUrl() }}" title="Página siguiente" rel="next">
                    <x-icon name="chevron-right" />
                    <span class="sr-only">Página siguiente</span>
                </a>
            @else
                <span class="ut-page-btn" aria-disabled="true">
                    <x-icon name="chevron-right" />
                    <span class="sr-only">Página siguiente</span>
                </span>
            @endif

            @if ($paginator->hasMorePages())
                <a class="ut-page-btn" href="{{ $paginator->url($paginator->lastPage()) }}" title="Última página" rel="next">
                    <x-icon name="chevrons-right" />
                    <span class="sr-only">Última página</span>
                </a>
            @else
                <span class="ut-page-btn" aria-disabled="true">
                    <x-icon name="chevrons-right" />
                    <span class="sr-only">Última página</span>
                </span>
            @endif
        </nav>
    @endif
</div>
