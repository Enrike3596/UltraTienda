@extends('layout.app')

@section('title', 'Usuarios · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Usuarios']"
        title="Usuarios"
        description="Cuentas del sistema, su correo y el rol asignado."
    >
        <x-slot:actions>
            <a href="{{ route('users.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Nuevo usuario
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="ut-card">
        <div class="ut-toolbar">
            <div class="ut-toolbar-group">
                <div class="relative w-full sm:max-w-xs">
                    <x-icon
                        name="search"
                        size="size-4"
                        class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground"
                    />
                    <label for="users-search" class="sr-only">Buscar usuarios</label>
                    <input
                        id="users-search"
                        type="search"
                        data-table-filter="users-table"
                        placeholder="Buscar por nombre, correo o rol..."
                        class="ut-input ut-input-search"
                    >
                </div>
            </div>
        </div>

        <div class="ut-table-wrap">
            <table id="users-table" class="ut-table">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Registrado</th>
                        <th scope="col" class="ut-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr data-search="{{ $user->name }} {{ $user->email }} {{ $user->role?->name }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-avatar :label="$user->name" size="size-9" />
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-muted-foreground">
                                            ID {{ $user->id }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <x-badge :tone="$user->role ? 'info' : 'neutral'">
                                    {{ $user->role?->name ?? 'Sin rol' }}
                                </x-badge>
                            </td>
                            <td class="text-muted-foreground">{{ $user->email }}</td>
                            <td class="whitespace-nowrap text-muted-foreground">
                                {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="ut-td-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('users.show', $user) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Ver usuario"
                                        aria-label="Ver usuario"
                                    >
                                        <x-icon name="eye" />
                                    </a>
                                    <a
                                        href="{{ route('users.edit', $user) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Editar usuario"
                                        aria-label="Editar usuario"
                                    >
                                        <x-icon name="pencil" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('users.destroy', $user) }}"
                                        data-confirm="{{ '¿Eliminar usuario?|La cuenta de «' . $user->name . '» será eliminada. Esta acción no se puede deshacer.' }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="ut-icon-btn ut-icon-btn-danger"
                                            title="Eliminar usuario"
                                            aria-label="Eliminar usuario"
                                        >
                                            <x-icon name="trash-2" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="ut-empty">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                        <x-icon name="inbox" size="size-6" />
                                    </span>
                                    <p class="font-semibold">Sin usuarios registrados</p>
                                    <p class="text-sm text-muted-foreground">Crea una cuenta para dar acceso al panel.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ut-card-footer">
            {{ $users->links('layout.pagination') }}
        </div>
    </div>
@endsection
