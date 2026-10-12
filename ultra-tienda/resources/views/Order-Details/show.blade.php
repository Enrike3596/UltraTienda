@extends('layout.app')

@section('title', 'Detalle de pedido · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Detalles de pedido', '#' . $orderDetail->id]"
        :title="'Detalle #' . $orderDetail->id"
        description="Producto, cantidad y precio registrados en el pedido #{{ $orderDetail->order_id }}."
    >
        <x-slot:actions>
            <a href="{{ route('order-details.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
            <a href="{{ route('order-details.edit', $orderDetail) }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="pencil" />
                Editar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="ut-card">
        <div class="ut-card-header">
            <div class="flex items-center gap-3">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                    <x-icon name="package" size="size-6" />
                </span>
                <div>
                    <p class="font-bold">{{ $orderDetail->product?->name ?? 'Producto eliminado' }}</p>
                    <p class="font-mono text-xs text-muted-foreground">Detalle #{{ $orderDetail->id }}</p>
                </div>
            </div>
        </div>

        <div class="ut-card-body">
            <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="ut-hint">Pedido</dt>
                    <dd>
                        <a
                            href="{{ route('orders.show', $orderDetail->order_id) }}"
                            class="font-mono font-semibold text-primary hover:underline"
                        >
                            #{{ $orderDetail->order_id }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="ut-hint">Cantidad</dt>
                    <dd class="font-mono font-semibold">{{ $orderDetail->quantity }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Precio unitario</dt>
                    <dd class="font-mono font-semibold"><x-money :value="$orderDetail->price" /></dd>
                </div>
                <div>
                    <dt class="ut-hint">Subtotal</dt>
                    <dd class="font-mono font-bold text-primary">
                        <x-money :value="$orderDetail->price * $orderDetail->quantity" />
                    </dd>
                </div>
            </dl>
        </div>

        <div class="ut-card-footer">
            <form
                method="POST"
                action="{{ route('order-details.destroy', $orderDetail) }}"
                data-confirm="¿Eliminar detalle?|Se eliminará este producto del pedido #{{ $orderDetail->order_id }}. Esta acción no se puede deshacer."
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="ut-btn ut-btn-danger-soft ut-btn-sm">
                    <x-icon name="trash-2" />
                    Eliminar detalle
                </button>
            </form>

            <a href="{{ route('order-details.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Volver al listado</a>
        </div>
    </div>
@endsection
