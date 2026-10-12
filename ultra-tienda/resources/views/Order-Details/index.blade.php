@extends('layout.app')

@section('title', 'Detalles de pedido · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Detalles de pedido']"
        title="Detalles de pedido"
        description="Productos, cantidades y precios fotografiados en cada pedido."
    >
        <x-slot:actions>
            <a href="{{ route('order-details.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Nuevo detalle
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
                    <label for="details-search" class="sr-only">Buscar detalles</label>
                    <input
                        id="details-search"
                        type="search"
                        data-table-filter="details-table"
                        placeholder="Buscar por producto o pedido..."
                        class="ut-input ut-input-search"
                    >
                </div>
            </div>
        </div>

        <div class="ut-table-wrap">
            <table id="details-table" class="ut-table">
                <thead>
                    <tr>
                        <th scope="col">Pedido</th>
                        <th scope="col">Producto</th>
                        <th scope="col" class="ut-th-center">Cantidad</th>
                        <th scope="col" class="ut-th-right">Precio unitario</th>
                        <th scope="col" class="ut-th-right">Subtotal</th>
                        <th scope="col" class="ut-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orderDetails as $detail)
                        <tr
                            data-search="Pedido {{ $detail->order_id }} {{ $detail->product?->name }}"
                        >
                            <td>
                                <a
                                    href="{{ route('orders.show', $detail->order_id) }}"
                                    class="font-mono font-semibold text-primary hover:underline"
                                >
                                    #{{ $detail->order_id }}
                                </a>
                            </td>
                            <td class="font-semibold">{{ $detail->product?->name ?? 'Producto eliminado' }}</td>
                            <td class="ut-td-center font-mono">{{ $detail->quantity }}</td>
                            <td class="ut-td-right font-mono"><x-money :value="$detail->price" /></td>
                            <td class="ut-td-right font-mono font-semibold">
                                <x-money :value="$detail->price * $detail->quantity" />
                            </td>
                            <td class="ut-td-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('order-details.show', $detail) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Ver detalle"
                                        aria-label="Ver detalle"
                                    >
                                        <x-icon name="eye" />
                                    </a>
                                    <a
                                        href="{{ route('order-details.edit', $detail) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Editar detalle"
                                        aria-label="Editar detalle"
                                    >
                                        <x-icon name="pencil" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('order-details.destroy', $detail) }}"
                                        data-confirm="¿Eliminar detalle?|Se eliminará este producto del pedido #{{ $detail->order_id }}. Esta acción no se puede deshacer."
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="ut-icon-btn ut-icon-btn-danger"
                                            title="Eliminar detalle"
                                            aria-label="Eliminar detalle"
                                        >
                                            <x-icon name="trash-2" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="ut-empty">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                        <x-icon name="inbox" size="size-6" />
                                    </span>
                                    <p class="font-semibold">Sin detalles de pedido</p>
                                    <p class="text-sm text-muted-foreground">Asocia productos a un pedido para comenzar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ut-card-footer">
            {{ $orderDetails->links('layout.pagination') }}
        </div>
    </div>
@endsection
