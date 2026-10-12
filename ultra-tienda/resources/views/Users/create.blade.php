@extends('layout.app')

@section('title', 'Nuevo usuario · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Usuarios', 'Nuevo']"
        title="Nuevo usuario"
        description="Crea una cuenta y asígnale un rol dentro del panel."
    >
        <x-slot:actions>
            <a href="{{ route('users.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('users.store') }}" class="ut-card">
        @csrf

        @include('Users.form')

        <div class="ut-card-footer">
            <a href="{{ route('users.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Guardar usuario
            </button>
        </div>
    </form>
@endsection
