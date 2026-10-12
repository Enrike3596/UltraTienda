@extends('layout.app')

@section('title', 'Editar categoría · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Catálogo', 'Categorías', 'Editar']"
        title="Editar categoría"
        description="Actualiza el nombre, el orden y la visibilidad de «{{ $category->name }}»."
    >
        <x-slot:actions>
            <a href="{{ route('categories.show', $category) }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="eye" />
                Ver ficha
            </a>
            <a href="{{ route('categories.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('categories.update', $category) }}" class="ut-card">
        @csrf
        @method('PUT')

        @include('Categories.form')

        <div class="ut-card-footer">
            <a href="{{ route('categories.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Actualizar categoría
            </button>
        </div>
    </form>
@endsection
