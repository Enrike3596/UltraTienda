@extends('layout.app')

@section('title', 'Pago · Ultra Tienda')

@section('content')
    @php
        $paymentStatusOptions = [
            'pending' => ['label' => 'Pendiente', 'tone' => 'outline'],
            'partial' => ['label' => 'Parcial', 'tone' => 'info'],
            'paid' => ['label' => 'Pagado', 'tone' => 'primary'],
            'failed' => ['label' => 'Fallido', 'tone' => 'danger'],
            'refunded' => ['label' => 'Reembolsado', 'tone' => 'neutral'],
        ];

        $status = $paymentStatusOptions[$orderPayment->payment_status]
            ?? ['label' => $orderPayment->payment_status, 'tone' => 'neutral'];
    @endphp

    <x-page-header
        :breadcrumb="['Ventas', 'Pagos de pedidos', 'P-' . str_pad($orderPayment->id, 4, '0', STR_PAD_LEFT)]"
        :title="'Pago P-' . str_pad($orderPayment->id, 4, '0', STR_PAD_LEFT)"
        description="Abono registrado contra el pedido #{{ $orderPayment->order_id }}."
    >
        <x-slot:actions>
            <a href="{{ route('order-payments.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
            <a href="{{ route('order-payments.edit', $orderPayment) }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="pencil" />
                Editar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="ut-card">
        <div class="ut-card-header">
            <div class="flex items-center gap-3">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                    <x-icon name="credit-card" size="size-6" />
                </span>
                <div>
                    <p class="font-mono font-bold">
                        <x-money :value="$orderPayment->amount" />
                    </p>
                    <p class="text-xs text-muted-foreground">
                        Pedido
                        <a class="font-semibold text-primary hover:underline" href="{{ route('orders.show', $orderPayment->order_id) }}">
                            #{{ $orderPayment->order_id }}
                        </a>
                    </p>
                </div>
            </div>

            <x-badge :tone="$status['tone']">{{ $status['label'] }}</x-badge>
        </div>

        <div class="ut-card-body">
            <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="ut-hint">Método</dt>
                    <dd class="font-semibold capitalize">{{ $orderPayment->payment_method }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Fecha de pago</dt>
                    <dd>{{ $orderPayment->payment_date?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Registrado</dt>
                    <dd>{{ $orderPayment->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Actualizado</dt>
                    <dd>{{ $orderPayment->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="ut-card-footer">
            <form
                method="POST"
                action="{{ route('order-payments.destroy', $orderPayment) }}"
                data-confirm="¿Eliminar pago?|El abono de {{ '$' . number_format((float) $orderPayment->amount, 2, ',', '.') }} del pedido #{{ $orderPayment->order_id }} será eliminado."
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="ut-btn ut-btn-danger-soft ut-btn-sm">
                    <x-icon name="trash-2" />
                    Eliminar pago
                </button>
            </form>

            <a href="{{ route('order-payments.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Volver al listado</a>
        </div>
    </div>
@endsection
