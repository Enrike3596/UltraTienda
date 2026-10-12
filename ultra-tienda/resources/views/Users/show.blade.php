@extends('layout.app')

@section('title', $user->name . ' · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Usuarios', $user->name]"
        :title="$user->name"
        description="Ficha de la cuenta, su correo y el rol asignado en el panel."
    >
        <x-slot:actions>
            <a href="{{ route('users.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
            <a href="{{ route('users.edit', $user) }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="pencil" />
                Editar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="ut-card">
        <div class="ut-card-header">
            <div class="flex items-center gap-3">
                <x-avatar :label="$user->name" size="size-12" />
                <div>
                    <p class="font-bold">{{ $user->name }}</p>
                    <p class="text-xs text-muted-foreground">{{ $user->email }}</p>
                </div>
            </div>

            <x-badge :tone="$user->role ? 'info' : 'neutral'">
                {{ $user->role?->name ?? 'Sin rol' }}
            </x-badge>
        </div>

        <div class="ut-card-body">
            <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="ut-hint">Correo</dt>
                    <dd class="font-semibold">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Rol</dt>
                    <dd>{{ $user->role?->name ?? 'Sin rol asignado' }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Pedidos</dt>
                    <dd class="font-mono font-semibold">{{ $user->orders()->count() }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Registrado</dt>
                    <dd>{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="ut-card-footer">
            <form
                method="POST"
                action="{{ route('users.destroy', $user) }}"
                data-confirm="{{ '¿Eliminar usuario?|La cuenta de «' . $user->name . '» será eliminada. Esta acción no se puede deshacer.' }}"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="ut-btn ut-btn-danger-soft ut-btn-sm">
                    <x-icon name="trash-2" />
                    Eliminar usuario
                </button>
            </form>

            <a href="{{ route('users.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Volver al listado</a>
        </div>
    </div>
@endsection
