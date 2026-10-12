@extends('layout.app')

@section('title', 'Pedidos · Ultra Tienda')

@section('content')
    @php
        $statusMap = [
            'pending' => ['label' => 'Pendiente', 'tone' => 'outline'],
            'processing' => ['label' => 'En preparación', 'tone' => 'neutral'],
            'paid' => ['label' => 'Pagado', 'tone' => 'primary'],
            'shipped' => ['label' => 'Enviado', 'tone' => 'info'],
            'delivered' => ['label' => 'Entregado', 'tone' => 'primary'],
            'cancelled' => ['label' => 'Cancelado', 'tone' => 'danger'],
        ];

        $paymentMap = [
            'pending' => ['label' => 'Pendiente', 'tone' => 'outline'],
            'partial' => ['label' => 'Parcial', 'tone' => 'info'],
            'paid' => ['label' => 'Pagado', 'tone' => 'primary'],
            'failed' => ['label' => 'Fallido', 'tone' => 'danger'],
            'refunded' => ['label' => 'Reembolsado', 'tone' => 'neutral'],
        ];
    @endphp

    <x-page-header
        :breadcrumb="['Ventas', 'Pedidos']"
        title="Pedidos"
        description="Consulta, actualiza y da seguimiento a los pedidos de tus clientes."
    >
        <x-slot:actions>
            <a href="{{ route('orders.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Crear pedido
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="ut-card">
        <div class="ut-toolbar">
            <div class="ut-toolbar-group">
                <div class="relative w-full sm:max-w-xs">
                    <x-icon
                        name="search"
                        size="size-4"
                        class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground"
                    />
                    <label for="orders-search" class="sr-only">Buscar pedidos</label>
                    <input
                        id="orders-search"
                        type="search"
                        data-table-filter="orders-table"
                        placeholder="Buscar por cliente, correo o ID..."
                        class="ut-input ut-input-search"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <label for="orders-filter" class="sr-only">Estado</label>
                    <select id="orders-filter" data-table-select="orders-table" class="ut-select h-9">
                        <option value="all">Estado: Todos</option>
                        @foreach ($statusMap as $value => $status)
                            <option value="{{ $value }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="ut-table-wrap">
            <table id="orders-table" class="ut-table">
                <thead>
                    <tr>
                        <th scope="col">Pedido</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Fecha</th>
                        <th scope="col" class="ut-th-center">Artículos</th>
                        <th scope="col" class="ut-th-right">Total</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Pago</th>
                        <th scope="col" class="ut-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $status = $statusMap[$order->status] ?? ['label' => $order->status, 'tone' => 'neutral'];
                            $payment = $paymentMap[$order->payment_status]
                                ?? ['label' => $order->payment_status, 'tone' => 'neutral'];
                        @endphp

                        <tr
                            data-search="Pedido {{ $order->id }} {{ $order->customer_name }} {{ $order->customer_email }}"
                            data-status="{{ $order->status }}"
                        >
                            <td>
                                <span class="font-mono font-semibold text-primary">#{{ $order->id }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-avatar :label="$order->customer_name" size="size-9" />
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold">{{ $order->customer_name }}</p>
                                        <p class="truncate text-xs text-muted-foreground">
                                            {{ $order->customer_email ?: $order->customer_phone }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap text-muted-foreground">
                                {{ $order->created_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="ut-td-center font-mono">{{ $order->details_count }}</td>
                            <td class="ut-td-right">
                                <span class="font-mono font-semibold"><x-money :value="$order->total" /></span>
                            </td>
                            <td>
                                <x-badge :tone="$status['tone']">{{ $status['label'] }}</x-badge>
                            </td>
                            <td>
                                <x-badge :tone="$payment['tone']">{{ $payment['label'] }}</x-badge>
                            </td>
                            <td class="ut-td-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('orders.show', $order) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Ver pedido"
                                        aria-label="Ver pedido"
                                    >
                                        <x-icon name="eye" />
                                    </a>
                                    <a
                                        href="{{ route('orders.edit', $order) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Editar pedido"
                                        aria-label="Editar pedido"
                                    >
                                        <x-icon name="pencil" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('orders.destroy', $order) }}"
                                        data-confirm="{{ '¿Eliminar pedido?|El pedido #' . $order->id . ' de «' . $order->customer_name . '» será eliminado permanentemente.' }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="ut-icon-btn ut-icon-btn-danger"
                                            title="Eliminar pedido"
                                            aria-label="Eliminar pedido"
                                        >
                                            <x-icon name="trash-2" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="ut-empty">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                        <x-icon name="inbox" size="size-6" />
                                    </span>
                                    <p class="font-semibold">Sin pedidos registrados</p>
                                    <p class="text-sm text-muted-foreground">Los nuevos pedidos aparecerán aquí.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ut-card-footer">
            {{ $orders->links('layout.pagination') }}
        </div>
    </div>
@endsection
