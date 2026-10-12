@extends('layout.app')

@section('title', 'Editar detalle de pedido · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Detalles de pedido', 'Editar']"
        title="Editar detalle"
        description="Actualiza la cantidad o el precio del producto en el pedido #{{ $orderDetail->order_id }}."
    >
        <x-slot:actions>
            <a href="{{ route('order-details.show', $orderDetail) }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="eye" />
                Ver ficha
            </a>
            <a href="{{ route('order-details.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('order-details.update', $orderDetail) }}" class="ut-card">
        @csrf
        @method('PUT')

        @include('Order-Details.form', ['detail' => $orderDetail])

        <div class="ut-card-footer">
            <a href="{{ route('order-details.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Actualizar detalle
            </button>
        </div>
    </form>
@endsection
