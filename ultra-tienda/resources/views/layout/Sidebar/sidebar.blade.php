<div class="ut-sidebar-backdrop" data-sidebar-backdrop aria-hidden="true"></div>

<aside class="ut-sidebar" aria-label="Navegación principal">
    <div class="ut-sidebar-header">
        <span class="ut-sidebar-brand-icon">
            <x-icon name="shopping-bag" size="size-5" />
        </span>

        <div class="ut-sidebar-label min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-sidebar-accent-foreground">Ultra Tienda</p>
            <p class="truncate text-xs text-sidebar-muted">Panel administrativo</p>
        </div>

        <button
            type="button"
            class="ut-icon-btn -mr-1 text-sidebar-muted hover:bg-sidebar-accent hover:text-sidebar-accent-foreground lg:hidden"
            data-sidebar-close
            aria-label="Cerrar menú"
        >
            <x-icon name="x" size="size-4" />
        </button>
    </div>

    <nav class="ut-nav ut-scroll">
        @foreach (config('navigation.groups') as $group)
            <div class="ut-nav-group">
                <p class="ut-nav-heading ut-sidebar-heading">{{ $group['title'] }}</p>

                @foreach ($group['items'] as $item)
                    @php
                        $url = $item['url'] ?? route($item['route']);
                        $active = isset($item['route'])
                            ? request()->routeIs($item['route'])
                            : ($item['url'] === '/'
                                ? request()->path() === '/'
                                : request()->is(trim($item['url'], '/')));
                    @endphp

                    <a
                        href="{{ $url }}"
                        title="{{ $item['label'] }}"
                        @if ($active) aria-current="page" @endif
                        @class([
                            'ut-nav-link',
                            'ut-nav-link-active' => $active,
                            'ut-nav-link-inactive' => !$active,
                        ])
                    >
                        <x-icon :name="$item['icon']" />
                        <span class="ut-nav-label">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="ut-sidebar-footer">
        <p class="ut-nav-heading ut-sidebar-heading">Ultra Tienda</p>
        <p class="ut-sidebar-label px-3 text-xs text-sidebar-muted">v1.0 · Panel de administración</p>
    </div>
</aside>
