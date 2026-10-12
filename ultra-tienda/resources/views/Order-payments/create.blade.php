@extends('layout.app')

@section('title', 'Nuevo pago · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Pagos de pedidos', 'Nuevo']"
        title="Nuevo pago"
        description="Registra un abono o pago recibido contra un pedido."
    >
        <x-slot:actions>
            <a href="{{ route('order-payments.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('order-payments.store') }}" class="ut-card">
        @csrf

        @include('Order-payments.form')

        <div class="ut-card-footer">
            <a href="{{ route('order-payments.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Guardar pago
            </button>
        </div>
    </form>
@endsection
