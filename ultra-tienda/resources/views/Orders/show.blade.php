@extends('layout.app')

@section('title', 'Pedido #' . $order->id . ' Â· Ultra Tienda')

@section('content')
    @php
        $statusMap = [
            'pending' => ['label' => 'Pendiente', 'tone' => 'outline'],
            'processing' => ['label' => 'En preparaciÃ³n', 'tone' => 'neutral'],
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

        $status = $statusMap[$order->status] ?? ['label' => $order->status, 'tone' => 'neutral'];
        $payment = $paymentMap[$order->payment_status] ?? ['label' => $order->payment_status, 'tone' => 'neutral'];
    @endphp

    <x-page-header
        :breadcrumb="['Ventas', 'Pedidos', '#' . $order->id]"
        :title="'Pedido #' . $order->id"
        description="Detalle del pedido, sus productos y los pagos registrados."
    >
        <x-slot:actions>
            <a href="{{ route('orders.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
            <a href="{{ route('orders.edit', $order) }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="pencil" />
                Editar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 flex flex-col gap-6">
        <div class="ut-card">
            <div class="ut-card-header">
                <div class="flex items-center gap-3">
                    <x-avatar :label="$order->customer_name" size="size-12" />
                    <div>
                        <p class="font-bold">{{ $order->customer_name }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ $order->customer_email ?: 'Sin correo registrado' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <x-badge :tone="$status['tone']">{{ $status['label'] }}</x-badge>
                    <x-badge :tone="$payment['tone']">Pago: {{ $payment['label'] }}</x-badge>
                </div>
            </div>

            <div class="ut-card-body">
                <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="ut-hint">TelÃ©fono</dt>
                        <dd class="font-semibold">{{ $order->customer_phone }}</dd>
                    </div>
                    <div>
                        <dt class="ut-hint">DirecciÃ³n</dt>
                        <dd class="font-semibold">{{ $order->customer_address }}</dd>
                    </div>
                    <div>
                        <dt class="ut-hint">Usuario</dt>
                        <dd>{{ $order->user?->name ?? 'Sin usuario vinculado' }}</dd>
                    </div>
                    <div>
                        <dt class="ut-hint">MÃ©todo de pago</dt>
                        <dd>{{ $order->payment_method ? ucfirst($order->payment_method) : 'â€”' }}</dd>
                    </div>
                    <div>
                        <dt class="ut-hint">Subtotal</dt>
                        <dd class="font-mono font-semibold"><x-money :value="$order->subtotal" /></dd>
                    </div>
                    <div>
                        <dt class="ut-hint">Domicilio</dt>
                        <dd class="font-mono font-semibold"><x-money :value="$order->shipping_cost" /></dd>
                    </div>
                    <div>
                        <dt class="ut-hint">Total</dt>
                        <dd class="font-mono font-bold text-primary"><x-money :value="$order->total" /></dd>
                    </div>
                    <div>
                        <dt class="ut-hint">Registrado</dt>
                        <dd>{{ $order->created_at?->format('d/m/Y H:i') ?? 'â€”' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="ut-card">
            <div class="ut-card-header">
                <div>
                    <h2 class="font-bold">Productos del pedido</h2>
                    <p class="text-xs text-muted-foreground">
                        {{ $order->details->count() }} {{ Str::plural('registro', $order->details->count()) }}
                    </p>
                </div>
            </div>

            <div class="ut-table-wrap">
                <table class="ut-table min-w-[520px]">
                    <thead>
                        <tr>
                            <th scope="col">Producto</th>
                            <th scope="col" class="ut-th-center">Cantidad</th>
                            <th scope="col" class="ut-th-right">Precio unitario</th>
                            <th scope="col" class="ut-th-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->details as $detail)
                            <tr>
                                <td class="font-semibold">{{ $detail->product?->name ?? 'Producto eliminado' }}</td>
                                <td class="ut-td-center font-mono">{{ $detail->quantity }}</td>
                                <td class="ut-td-right font-mono"><x-money :value="$detail->price" /></td>
                                <td class="ut-td-right font-mono font-semibold">
                                    <x-money :value="$detail->price * $detail->quantity" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="ut-empty">
                                        <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                            <x-icon name="inbox" size="size-6" />
                                        </span>
                                        <p class="font-semibold">Sin productos en el pedido</p>
                                        <p class="text-sm text-muted-foreground">
                                            Agrega productos desde
                                            <a class="font-semibold text-primary" href="{{ route('order-details.create') }}">Detalles de pedido</a>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ut-card">
            <div class="ut-card-header">
                <div>
                    <h2 class="font-bold">Pagos registrados</h2>
                    <p class="text-xs text-muted-foreground">
                        {{ $order->payments->count() }} {{ Str::plural('abono', $order->payments->count()) }}
                    </p>
                </div>
            </div>

            <div class="ut-table-wrap">
                <table class="ut-table min-w-[560px]">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">MÃ©todo</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="ut-th-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->payments as $paymentRecord)
                            @php
                                $recordStatus = $paymentMap[$paymentRecord->payment_status]
                                    ?? ['label' => $paymentRecord->payment_status, 'tone' => 'neutral'];
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap text-muted-foreground">
                                    {{ $paymentRecord->payment_date?->format('d/m/Y H:i') ?? 'â€”' }}
                                </td>
                                <td>{{ ucfirst($paymentRecord->payment_method) }}</td>
                                <td>
                                    <x-badge :tone="$recordStatus['tone']">{{ $recordStatus['label'] }}</x-badge>
                                </td>
                                <td class="ut-td-right font-mono font-semibold">
                                    <x-money :value="$paymentRecord->amount" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="ut-empty">
                                        <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                            <x-icon name="inbox" size="size-6" />
                                        </span>
                                        <p class="font-semibold">Sin pagos registrados</p>
                                        <p class="text-sm text-muted-foreground">
                                            Registra un abono desde
                                            <a class="font-semibold text-primary" href="{{ route('order-payments.create') }}">Pagos de pedidos</a>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
        <form
            method="POST"
            action="{{ route('orders.destroy', $order) }}"
            data-confirm="{{ 'Â¿Eliminar pedido?|El pedido #' . $order->id . ' de Â«' . $order->customer_name . 'Â» serÃ¡ eliminado permanentemente.' }}"
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="ut-btn ut-btn-danger-soft ut-btn-sm">
                <x-icon name="trash-2" />
                Eliminar pedido
            </button>
        </form>

        <a href="{{ route('orders.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Volver al listado</a>
    </div>
@endsection
