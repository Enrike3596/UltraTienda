@extends('layout.app')

@section('title', 'Nuevo pedido · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Pedidos', 'Nuevo']"
        title="Nuevo pedido"
        description="Registra los datos del cliente y los montos del pedido."
    >
        <x-slot:actions>
            <a href="{{ route('orders.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('orders.store') }}" class="ut-card">
        @csrf

        @include('Orders.form')

        <div class="ut-card-footer">
            <a href="{{ route('orders.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Guardar pedido
            </button>
        </div>
    </form>
@endsection
