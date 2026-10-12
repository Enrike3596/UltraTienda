@php
    $authUser = auth()->user();
    $displayName = $authUser->name ?? 'Invitado';
    $roleName = $authUser?->role?->name ?? 'Sin sesión';
    $initials = collect(explode(' ', trim($displayName)))
        ->filter()
        ->take(2)
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

    $quickNavItems = collect(config('navigation.groups'))
        ->flatMap(fn (array $group) => $group['items'])
        ->map(fn (array $item) => [
            'label' => $item['label'],
            'url' => $item['url'] ?? route($item['route']),
        ])
        ->values()
        ->all();
@endphp

<header class="ut-topbar">
    <button type="button" class="ut-icon-btn lg:hidden" data-sidebar-open aria-label="Abrir menú">
        <x-icon name="menu" size="size-5" />
    </button>

    <button
        type="button"
        class="ut-icon-btn hidden lg:flex"
        data-sidebar-collapse
        aria-pressed="false"
        aria-label="Colapsar menú"
        title="Colapsar menú"
    >
        <x-icon name="panel-left-close" size="size-5" class="ut-collapse-icon-close" />
        <x-icon name="panel-left-open" size="size-5" class="ut-collapse-icon-open" />
    </button>

    <div class="relative hidden max-w-md flex-1 md:block">
        <x-icon
            name="search"
            size="size-4"
            class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground"
        />
        <label for="ut-quick-nav" class="sr-only">Buscar en el panel</label>
        <input
            id="ut-quick-nav"
            type="search"
            list="ut-quick-nav-options"
            data-quick-nav
            data-quick-nav-items="{{ json_encode($quickNavItems) }}"
            autocomplete="off"
            placeholder="Buscar productos, pedidos, usuarios..."
            class="ut-input ut-input-search"
        >
        <datalist id="ut-quick-nav-options">
            @foreach ($quickNavItems as $item)
                <option value="{{ $item['label'] }}"></option>
            @endforeach
        </datalist>
        <kbd class="ut-kbd">Ctrl K</kbd>
    </div>

    <div class="ml-auto flex items-center gap-1.5">
        <button
            type="button"
            class="ut-icon-btn"
            data-theme-toggle
            title="Cambiar de tema"
            aria-label="Cambiar entre modo claro y modo oscuro"
        >
            <x-icon name="moon" size="size-5" class="ut-theme-moon" />
            <x-icon name="sun" size="size-5" class="ut-theme-sun" />
        </button>

        <div class="mx-2 hidden h-8 w-px bg-border sm:block" aria-hidden="true"></div>

        <div class="flex items-center gap-3 rounded-lg p-1 pr-2">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">
                {{ $initials }}
            </span>
            <span class="hidden text-left sm:block">
                <span class="block text-sm leading-tight font-semibold">{{ $displayName }}</span>
                <span class="block text-xs leading-tight text-muted-foreground">{{ $roleName }}</span>
            </span>
        </div>
    </div>
</header>
