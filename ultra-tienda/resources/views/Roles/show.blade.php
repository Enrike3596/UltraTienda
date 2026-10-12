@extends('layout.app')

@section('title', 'Rol · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Roles', $role->name]"
        :title="$role->name"
        description="Ficha del rol y los usuarios que lo tienen asignado."
    >
        <x-slot:actions>
            <a href="{{ route('roles.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
            <a href="{{ route('roles.edit', $role) }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="pencil" />
                Editar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 flex flex-col gap-6">
        <div class="ut-card">
            <div class="ut-card-header">
                <div class="flex items-center gap-3">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                        <x-icon name="shield" size="size-6" />
                    </span>
                    <div>
                        <p class="font-bold capitalize">{{ $role->name }}</p>
                        <p class="font-mono text-xs text-muted-foreground">ID {{ $role->id }}</p>
                    </div>
                </div>

                <x-badge tone="info">{{ $role->users()->count() }} {{ Str::plural('usuario', $role->users()->count()) }}</x-badge>
            </div>

            <div class="ut-card-body">
                <dl class="grid gap-5 sm:grid-cols-3">
                    <div>
                        <dt class="ut-hint">Usuarios con este rol</dt>
                        <dd class="font-mono font-semibold">{{ $role->users()->count() }}</dd>
                    </div>
                    <div>
                        <dt class="ut-hint">Creado</dt>
                        <dd>{{ $role->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="ut-hint">Actualizado</dt>
                        <dd>{{ $role->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="ut-card">
            <div class="ut-card-header">
                <div>
                    <h2 class="font-bold">Usuarios con este rol</h2>
                    <p class="text-xs text-muted-foreground">Cuentas que heredan este perfil de acceso.</p>
                </div>
            </div>

            <div class="ut-table-wrap">
                <table class="ut-table min-w-[480px]">
                    <thead>
                        <tr>
                            <th scope="col">Usuario</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Registrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($role->users as $roleUser)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <x-avatar :label="$roleUser->name" size="size-9" />
                                        <span class="font-semibold">{{ $roleUser->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted-foreground">{{ $roleUser->email }}</td>
                                <td class="whitespace-nowrap text-muted-foreground">
                                    {{ $roleUser->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="ut-empty">
                                        <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                            <x-icon name="inbox" size="size-6" />
                                        </span>
                                        <p class="font-semibold">Sin usuarios con este rol</p>
                                        <p class="text-sm text-muted-foreground">
                                            Asigna el rol desde
                                            <a class="font-semibold text-primary" href="{{ route('users.index') }}">Usuarios</a>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <form
                method="POST"
                action="{{ route('roles.destroy', $role) }}"
                data-confirm="{{ '¿Eliminar rol?|El rol «' . $role->name . '» será eliminado. Esta acción no se puede deshacer.' }}"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="ut-btn ut-btn-danger-soft ut-btn-sm">
                    <x-icon name="trash-2" />
                    Eliminar rol
                </button>
            </form>

            <a href="{{ route('roles.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Volver al listado</a>
        </div>
    </div>
@endsection
