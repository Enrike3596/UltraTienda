@extends('layout.app')

@section('title', 'Editar usuario · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Usuarios', 'Editar']"
        title="Editar usuario"
        description="Actualiza los datos y el rol de «{{ $user->name }}»."
    >
        <x-slot:actions>
            <a href="{{ route('users.show', $user) }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="eye" />
                Ver ficha
            </a>
            <a href="{{ route('users.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('users.update', $user) }}" class="ut-card">
        @csrf
        @method('PUT')

        @include('Users.form')

        <div class="ut-card-footer">
            <a href="{{ route('users.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Cancelar</a>
            <button type="submit" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="check" />
                Actualizar usuario
            </button>
        </div>
    </form>
@endsection
