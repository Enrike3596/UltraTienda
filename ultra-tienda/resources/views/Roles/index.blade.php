@extends('layout.app')

@section('title', 'Roles · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Administración', 'Roles']"
        title="Roles"
        description="Perfiles de acceso disponibles para asignar a los usuarios."
    >
        <x-slot:actions>
            <a href="{{ route('roles.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Nuevo rol
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
                    <label for="roles-search" class="sr-only">Buscar roles</label>
                    <input
                        id="roles-search"
                        type="search"
                        data-table-filter="roles-table"
                        placeholder="Buscar rol..."
                        class="ut-input ut-input-search"
                    >
                </div>
            </div>
        </div>

        <div class="ut-table-wrap">
            <table id="roles-table" class="ut-table min-w-[520px]">
                <thead>
                    <tr>
                        <th scope="col">Rol</th>
                        <th scope="col">Descripción</th>
                        <th scope="col" class="ut-th-right">Usuarios</th>
                        <th scope="col" class="ut-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr data-search="{{ $role->name }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                                        <x-icon name="shield" size="size-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold capitalize">{{ $role->name }}</p>
                                        <p class="font-mono text-xs text-muted-foreground">ID {{ $role->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted-foreground">Perfil de acceso en el panel</td>
                            <td class="ut-td-right font-mono font-medium">{{ $role->users_count }}</td>
                            <td class="ut-td-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('roles.show', $role) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Ver rol"
                                        aria-label="Ver rol"
                                    >
                                        <x-icon name="eye" />
                                    </a>
                                    <a
                                        href="{{ route('roles.edit', $role) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Editar rol"
                                        aria-label="Editar rol"
                                    >
                                        <x-icon name="pencil" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('roles.destroy', $role) }}"
                                        data-confirm="{{ '¿Eliminar rol?|El rol «' . $role->name . '» será eliminado. Esta acción no se puede deshacer.' }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="ut-icon-btn ut-icon-btn-danger"
                                            title="Eliminar rol"
                                            aria-label="Eliminar rol"
                                        >
                                            <x-icon name="trash-2" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="ut-empty">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                        <x-icon name="inbox" size="size-6" />
                                    </span>
                                    <p class="font-semibold">Sin roles registrados</p>
                                    <p class="text-sm text-muted-foreground">Crea un rol para asignar permisos.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ut-card-footer">
            {{ $roles->links('layout.pagination') }}
        </div>
    </div>
@endsection
