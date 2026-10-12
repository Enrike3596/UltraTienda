@extends('layout.app')

@section('title', 'Editar pedido · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Pedidos', 'Editar']"
        title="Editar pedido"
        description="Actualiza los datos del pedido #{{ $order->id }}."
    >
        <x-slot:actions>
            <a href="{{ route('orders.show', $order) }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="eye" />
                Ver ficha
            </a>
            <a href="{{ route('orders.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('orders.update', $order) }}" class="ut-card">
        @csrf
        @method('PUT')

        @include('Orders.form')

        <div class="ut-card-footer">
            <a href="{{ route('orders.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Actualizar pedido
            </button>
        </div>
    </form>
@endsection
