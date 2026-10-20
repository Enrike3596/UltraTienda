@extends('layout.app')

@section('title', 'Dashboard · Ultra Tienda')

@section('content')
    @php
        $statusMap = [
            'pending' => ['label' => 'Pendiente', 'tone' => 'outline', 'bar' => 'neutral'],
            'processing' => ['label' => 'En preparación', 'tone' => 'neutral', 'bar' => 'neutral'],
            'paid' => ['label' => 'Pagado', 'tone' => 'primary', 'bar' => 'primary'],
            'shipped' => ['label' => 'Enviado', 'tone' => 'info', 'bar' => 'info'],
            'delivered' => ['label' => 'Entregado', 'tone' => 'primary', 'bar' => 'primary'],
            'cancelled' => ['label' => 'Cancelado', 'tone' => 'danger', 'bar' => 'danger'],
        ];

        $trendLabel = fn (?float $trend): string => ($trend >= 0 ? '+' : '−') . number_format(abs($trend), 1) . '%';
    @endphp

    <x-page-header
        :breadcrumb="['General', 'Dashboard']"
        title="Dashboard"
        description="Resumen de ventas, pedidos y catálogo de Ultra Tienda."
    >
        <x-slot:actions>
            <a href="{{ route('orders.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Nuevo pedido
            </a>
            <a href="{{ route('products.create') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="package" />
                Nuevo producto
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Métricas principales --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="ut-card ut-stat-card">
            <div class="ut-card-body">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="ut-stat-icon ut-stat-icon-primary">
                        <x-icon name="coins" size="size-5" />
                    </span>
                    @if ($salesTrend !== null)
                        <span @class(['ut-trend', $salesTrend >= 0 ? 'ut-trend-up' : 'ut-trend-down'])>
                            <x-icon :name="$salesTrend >= 0 ? 'arrow-up' : 'arrow-down'" size="size-3.5" />
                            {{ $trendLabel($salesTrend) }}
                        </span>
                    @endif
                </div>
                <p class="ut-stat-label">Ventas del mes</p>
                <p class="ut-stat-value"><x-money :value="$monthSales" /></p>
                <p class="ut-stat-hint">
                    Histórico:
                    <span class="font-mono font-semibold"><x-money :value="$totalSales" /></span>
                </p>
            </div>
        </article>

        <article class="ut-card ut-stat-card">
            <div class="ut-card-body">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="ut-stat-icon ut-stat-icon-info">
                        <x-icon name="shopping-bag" size="size-5" />
                    </span>
                    @if ($ordersTrend !== null)
                        <span @class(['ut-trend', $ordersTrend >= 0 ? 'ut-trend-up' : 'ut-trend-down'])>
                            <x-icon :name="$ordersTrend >= 0 ? 'arrow-up' : 'arrow-down'" size="size-3.5" />
                            {{ $trendLabel($ordersTrend) }}
                        </span>
                    @endif
                </div>
                <p class="ut-stat-label">Pedidos del mes</p>
                <p class="ut-stat-value">{{ number_format($monthOrders) }}</p>
                <p class="ut-stat-hint">
                    <span class="{{ $pendingOrders > 0 ? 'font-semibold text-destructive' : 'font-semibold' }}">
                        {{ $pendingOrders }} pendientes
                    </span>
                    · {{ number_format($totalOrders) }} en total
                </p>
            </div>
        </article>

        <article class="ut-card ut-stat-card">
            <div class="ut-card-body">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="ut-stat-icon ut-stat-icon-accent">
                        <x-icon name="package" size="size-5" />
                    </span>
                    @if ($outOfStockProducts > 0)
                        <x-badge tone="danger">{{ $outOfStockProducts }} agotados</x-badge>
                    @else
                        <x-badge tone="primary">En stock</x-badge>
                    @endif
                </div>
                <p class="ut-stat-label">Productos activos</p>
                <p class="ut-stat-value">{{ number_format($activeProducts) }}</p>
                <p class="ut-stat-hint">
                    {{ $outOfStockProducts > 0
                        ? $outOfStockProducts . ' sin disponibilidad en el catálogo'
                        : 'Todo el catálogo disponible para la venta' }}
                </p>
            </div>
        </article>

        <article class="ut-card ut-stat-card">
            <div class="ut-card-body">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="ut-stat-icon ut-stat-icon-neutral">
                        <x-icon name="users" size="size-5" />
                    </span>
                    @if ($usersTrend !== null)
                        <span @class(['ut-trend', $usersTrend >= 0 ? 'ut-trend-up' : 'ut-trend-down'])>
                            <x-icon :name="$usersTrend >= 0 ? 'arrow-up' : 'arrow-down'" size="size-3.5" />
                            {{ $trendLabel($usersTrend) }}
                        </span>
                    @endif
                </div>
                <p class="ut-stat-label">Usuarios registrados</p>
                <p class="ut-stat-value">{{ number_format($totalUsers) }}</p>
                <p class="ut-stat-hint">
                    +{{ number_format($monthUsers) }} {{ Str::plural('nuevo', $monthUsers) }} este mes
                </p>
            </div>
        </article>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            {{-- Ventas de los últimos 7 días --}}
            <section class="ut-card">
                <div class="ut-card-header">
                    <div>
                        <h2 class="text-base font-bold">Ventas de los últimos 7 días</h2>
                        <p class="text-xs text-muted-foreground">Total diario de pedidos no cancelados</p>
                    </div>
                    <span class="ut-badge ut-badge-primary ut-badge-plain">
                        Semana: <span class="font-mono"><x-money :value="$weeklyTotal" /></span>
                    </span>
                </div>

                <div class="ut-card-body">
                    <div class="ut-chart" role="img" aria-label="Gráfico de ventas diarias de los últimos 7 días">
                        @foreach ($weeklySales as $day)
                            @php
                                $height = $weeklyPeak > 0
                                    ? max((int) round($day['total'] / $weeklyPeak * 100), 4)
                                    : 4;
                            @endphp

                            <div class="ut-chart-col">
                                <div
                                    class="ut-chart-track"
                                    title="{{ $day['date'] }} · ${{ number_format($day['total'], 2, ',', '.') }}"
                                >
                                    <span class="ut-chart-bar" style="height: {{ $height }}%"></span>
                                </div>
                                <span class="ut-chart-label">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Últimos pedidos --}}
            <section class="ut-card">
                <div class="ut-card-header">
                    <h2 class="text-base font-bold">Últimos pedidos</h2>
                    <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-primary hover:underline">
                        Ver todos
                    </a>
                </div>

                @if ($latestOrders->isEmpty())
                    <div class="ut-empty">
                        <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                            <x-icon name="inbox" size="size-6" />
                        </span>
                        <p class="font-semibold">Sin pedidos recientes</p>
                        <p class="text-sm text-muted-foreground">Los nuevos pedidos aparecerán aquí.</p>
                    </div>
                @else
                    <ul class="divide-y">
                        @foreach ($latestOrders as $order)
                            @php
                                $status = $statusMap[$order->status]
                                    ?? ['label' => $order->status, 'tone' => 'neutral'];
                            @endphp

                            <li>
                                <a href="{{ route('orders.show', $order) }}" class="ut-feed-item">
                                    <x-avatar :label="$order->customer_name" size="size-10" />

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold">
                                            <span class="font-mono text-primary">#{{ $order->id }}</span>
                                            · {{ $order->customer_name }}
                                        </p>
                                        <p class="truncate text-xs text-muted-foreground">
                                            {{ $order->details_count }}
                                            {{ Str::plural('artículo', $order->details_count) }}
                                            · {{ $order->created_at?->format('d/m/Y H:i') ?? '—' }}
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 flex-col items-end gap-1">
                                        <span class="font-mono text-sm font-bold">
                                            <x-money :value="$order->total" />
                                        </span>
                                        <x-badge :tone="$status['tone']">{{ $status['label'] }}</x-badge>
                                    </div>

                                    <x-icon name="chevron-right" size="size-4" class="shrink-0 text-muted-foreground" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <div class="space-y-6">
            {{-- Productos más vendidos --}}
            <section class="ut-card">
                <div class="ut-card-header">
                    <h2 class="text-base font-bold">Productos más vendidos</h2>
                    <x-icon name="chart-column" size="size-4" class="text-muted-foreground" />
                </div>

                <div class="ut-card-body">
                    @if ($topProducts->isEmpty())
                        <div class="ut-empty px-0 py-8">
                            <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                <x-icon name="chart-no-axes-column" size="size-6" />
                            </span>
                            <p class="font-semibold">Aún no hay ventas</p>
                            <p class="text-sm text-muted-foreground">El ranking aparecerá con los primeros pedidos.</p>
                        </div>
                    @else
                        <ol class="space-y-4">
                            @foreach ($topProducts as $entry)
                                <li class="flex items-center gap-3">
                                    <span class="ut-rank">{{ $loop->iteration }}</span>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold">
                                            {{ $entry->product?->name ?? 'Producto eliminado' }}
                                        </p>
                                        <p class="truncate text-xs text-muted-foreground">
                                            {{ number_format($entry->units_sold) }} unidades
                                            · {{ $entry->product?->category?->name ?? 'Sin categoría' }}
                                        </p>
                                    </div>

                                    <span class="shrink-0 font-mono text-sm font-bold">
                                        <x-money :value="$entry->revenue" />
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            {{-- Estado de los pedidos --}}
            <section class="ut-card">
                <div class="ut-card-header">
                    <h2 class="text-base font-bold">Estado de los pedidos</h2>
                    <span class="text-xs font-semibold text-muted-foreground">
                        {{ number_format($totalOrders) }} en total
                    </span>
                </div>

                <div class="ut-card-body space-y-4">
                    @foreach ($statusMap as $value => $meta)
                        @php
                            $count = (int) ($ordersByStatus[$value] ?? 0);
                            $percent = $totalOrders > 0 ? min(100, round($count / $totalOrders * 100)) : 0;
                        @endphp

                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                <span class="text-muted-foreground">{{ $meta['label'] }}</span>
                                <span class="font-semibold">
                                    {{ $count }}
                                    <span class="text-xs font-normal text-muted-foreground">({{ $percent }}%)</span>
                                </span>
                            </div>
                            <div class="ut-progress">
                                <span
                                    @class(['ut-progress-bar', 'ut-progress-bar-' . $meta['bar']])
                                    style="width: {{ $percent }}%"
                                ></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Accesos rápidos --}}
            <section class="ut-card">
                <div class="ut-card-header">
                    <h2 class="text-base font-bold">Accesos rápidos</h2>
                </div>

                <div class="ut-card-body grid grid-cols-2 gap-3">
                    @foreach ([
                        ['label' => 'Productos', 'icon' => 'package', 'route' => 'products.index'],
                        ['label' => 'Categorías', 'icon' => 'folder-tree', 'route' => 'categories.index'],
                        ['label' => 'Pedidos', 'icon' => 'shopping-bag', 'route' => 'orders.index'],
                        ['label' => 'Pagos', 'icon' => 'credit-card', 'route' => 'order-payments.index'],
                        ['label' => 'Usuarios', 'icon' => 'users', 'route' => 'users.index'],
                        ['label' => 'Roles', 'icon' => 'shield', 'route' => 'roles.index'],
                    ] as $shortcut)
                        <a href="{{ route($shortcut['route']) }}" class="ut-quick-link">
                            <x-icon :name="$shortcut['icon']" size="size-4" />
                            <span>{{ $shortcut['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
