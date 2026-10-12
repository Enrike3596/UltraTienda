@extends('layout.app')

@section('title', 'Nueva categoría · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Catálogo', 'Categorías', 'Nueva']"
        title="Nueva categoría"
        description="Crea una sección para agrupar los productos de tu catálogo."
    >
        <x-slot:actions>
            <a href="{{ route('categories.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('categories.store') }}" class="ut-card">
        @csrf

        @include('Categories.form')

        <div class="ut-card-footer">
            <a href="{{ route('categories.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Guardar categoría
            </button>
        </div>
    </form>
@endsection
