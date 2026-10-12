@extends('layout.app')

@section('title', 'Nuevo detalle de pedido · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Ventas', 'Detalles de pedido', 'Nuevo']"
        title="Nuevo detalle"
        description="Agrega un producto con su cantidad y precio a un pedido."
    >
        <x-slot:actions>
            <a href="{{ route('order-details.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('order-details.store') }}" class="ut-card">
        @csrf

        @include('Order-Details.form')

        <div class="ut-card-footer">
            <a href="{{ route('order-details.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Guardar detalle
            </button>
        </div>
    </form>
@endsection
