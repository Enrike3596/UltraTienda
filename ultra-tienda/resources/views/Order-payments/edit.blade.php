@extends('layout.app')

@section('title', 'Editar pago · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Pagos de pedidos', 'Editar']"
        title="Editar pago"
        description="Actualiza los datos del pago P-{{ str_pad($orderPayment->id, 4, '0', STR_PAD_LEFT) }}."
    >
        <x-slot:actions>
            <a href="{{ route('order-payments.show', $orderPayment) }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="eye" />
                Ver ficha
            </a>
            <a href="{{ route('order-payments.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('order-payments.update', $orderPayment) }}" class="ut-card">
        @csrf
        @method('PUT')

        @include('Order-payments.form', ['payment' => $orderPayment])

        <div class="ut-card-footer">
            <a href="{{ route('order-payments.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Actualizar pago
            </button>
        </div>
    </form>
@endsection
