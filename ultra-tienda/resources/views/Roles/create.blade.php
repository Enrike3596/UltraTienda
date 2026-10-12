@extends('layout.app')

@section('title', 'Nuevo rol · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Roles', 'Nuevo']"
        title="Nuevo rol"
        description="Define un perfil de acceso para asignarlo a los usuarios."
    >
        <x-slot:actions>
            <a href="{{ route('roles.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('roles.store') }}" class="ut-card">
        @csrf

        @include('Roles.form')

        <div class="ut-card-footer">
            <a href="{{ route('roles.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Guardar rol
            </button>
        </div>
    </form>
@endsection
