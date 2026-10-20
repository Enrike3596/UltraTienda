@extends('layout.app')

@section('title', 'Editar producto · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Catálogo', 'Productos', 'Editar']"
        title="Editar producto"
        description="Actualiza la información, el precio y la visibilidad de «{{ $product->name }}»."
    >
        <x-slot:actions>
            <a href="{{ route('products.show', $product) }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="eye" />
                Ver ficha
            </a>
            <a href="{{ route('products.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="ut-card">
        @csrf
        @method('PUT')

        @include('Products.form')

        <div class="ut-card-footer">
            <a href="{{ route('products.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Actualizar producto
            </button>
        </div>
    </form>
@endsection
