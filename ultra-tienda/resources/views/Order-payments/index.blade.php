@extends('layout.app')

@section('title', 'Pagos de pedidos · Ultra Tienda')

@section('content')
    @php
        $paymentStatusOptions = [
            'pending' => ['label' => 'Pendiente', 'tone' => 'outline'],
            'partial' => ['label' => 'Parcial', 'tone' => 'info'],
            'paid' => ['label' => 'Pagado', 'tone' => 'primary'],
            'failed' => ['label' => 'Fallido', 'tone' => 'danger'],
            'refunded' => ['label' => 'Reembolsado', 'tone' => 'neutral'],
        ];
    @endphp

    <x-page-header
        :breadcrumb="['Ventas', 'Pagos de pedidos']"
        title="Pagos de pedidos"
        description="Abonos y estados de pago registrados contra cada pedido."
    >
        <x-slot:actions>
            <a href="{{ route('order-payments.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Nuevo pago
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
                    <label for="payments-search" class="sr-only">Buscar pagos</label>
                    <input
                        id="payments-search"
                        type="search"
                        data-table-filter="payments-table"
                        placeholder="Buscar por pedido o método..."
                        class="ut-input ut-input-search"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <label for="payments-filter" class="sr-only">Estado del pago</label>
                    <select id="payments-filter" data-table-select="payments-table" class="ut-select h-9">
                        <option value="all">Estado: Todos</option>
                        @foreach ($paymentStatusOptions as $value => $status)
                            <option value="{{ $value }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="ut-table-wrap">
            <table id="payments-table" class="ut-table">
                <thead>
                    <tr>
                        <th scope="col">Pago</th>
                        <th scope="col">Pedido</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Método</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="ut-th-right">Monto</th>
                        <th scope="col" class="ut-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orderPayments as $payment)
                        @php
                            $status = $paymentStatusOptions[$payment->payment_status]
                                ?? ['label' => $payment->payment_status, 'tone' => 'neutral'];
                        @endphp

                        <tr
                            data-search="Pago {{ $payment->id }} Pedido {{ $payment->order_id }} {{ $payment->payment_method }}"
                            data-status="{{ $payment->payment_status }}"
                        >
                            <td>
                                <span class="font-mono font-semibold text-primary">P-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <a
                                    href="{{ route('orders.show', $payment->order_id) }}"
                                    class="font-mono font-medium hover:underline"
                                >
                                    #{{ $payment->order_id }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap text-muted-foreground">
                                {{ $payment->payment_date?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="capitalize">{{ $payment->payment_method }}</td>
                            <td>
                                <x-badge :tone="$status['tone']">{{ $status['label'] }}</x-badge>
                            </td>
                            <td class="ut-td-right">
                                <span class="font-mono font-semibold"><x-money :value="$payment->amount" /></span>
                            </td>
                            <td class="ut-td-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('order-payments.show', $payment) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Ver pago"
                                        aria-label="Ver pago"
                                    >
                                        <x-icon name="eye" />
                                    </a>
                                    <a
                                        href="{{ route('order-payments.edit', $payment) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Editar pago"
                                        aria-label="Editar pago"
                                    >
                                        <x-icon name="pencil" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('order-payments.destroy', $payment) }}"
                                        data-confirm="¿Eliminar pago?|El abono de {{ '$' . number_format((float) $payment->amount, 2, ',', '.') }} del pedido #{{ $payment->order_id }} será eliminado."
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="ut-icon-btn ut-icon-btn-danger"
                                            title="Eliminar pago"
                                            aria-label="Eliminar pago"
                                        >
                                            <x-icon name="trash-2" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="ut-empty">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                        <x-icon name="inbox" size="size-6" />
                                    </span>
                                    <p class="font-semibold">Sin pagos registrados</p>
                                    <p class="text-sm text-muted-foreground">Registra el primer abono de un pedido.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ut-card-footer">
            {{ $orderPayments->links('layout.pagination') }}
        </div>
    </div>
@endsection
