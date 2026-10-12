@extends('layout.app')

@section('title', 'Nuevo producto · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Catálogo', 'Productos', 'Nuevo']"
        title="Nuevo producto"
        description="Registra un producto del catálogo con su categoría, precio y disponibilidad."
    >
        <x-slot:actions>
            <a href="{{ route('products.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('products.store') }}" class="ut-card">
        @csrf

        @include('Products.form')

        <div class="ut-card-footer">
            <a href="{{ route('products.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Guardar producto
            </button>
        </div>
    </form>
@endsection
