@extends('layout.app')

@section('title', 'Editar rol · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Roles', 'Editar']"
        title="Editar rol"
        description="Actualiza el nombre del rol «{{ $role->name }}»."
    >
        <x-slot:actions>
            <a href="{{ route('roles.show', $role) }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="eye" />
                Ver ficha
            </a>
            <a href="{{ route('roles.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('roles.update', $role) }}" class="ut-card">
        @csrf
        @method('PUT')

        @include('Roles.form')

        <div class="ut-card-footer">
            <a href="{{ route('roles.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Actualizar rol
            </button>
        </div>
    </form>
@endsection
