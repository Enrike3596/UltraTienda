@extends('layout.app')

@section('title', 'Categorías · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Catálogo', 'Categorías']"
        title="Categorías"
        description="Organiza el catálogo en secciones y controla su orden de aparición en la tienda."
    >
        <x-slot:actions>
            <a href="{{ route('categories.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Nueva categoría
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
                    <label for="categories-search" class="sr-only">Buscar categorías</label>
                    <input
                        id="categories-search"
                        type="search"
                        data-table-filter="categories-table"
                        placeholder="Buscar por nombre..."
                        class="ut-input ut-input-search"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <label for="categories-filter" class="sr-only">Estado</label>
                    <select id="categories-filter" data-table-select="categories-table" class="ut-select h-9">
                        <option value="all">Estado: Todas</option>
                        <option value="active">Activas</option>
                        <option value="inactive">Inactivas</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="ut-table-wrap">
            <table id="categories-table" class="ut-table">
                <thead>
                    <tr>
                        <th scope="col">Categoría</th>
                        <th scope="col">Descripción</th>
                        <th scope="col" class="ut-th-center">Orden</th>
                        <th scope="col" class="ut-th-right">Productos</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="ut-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr
                            data-search="{{ $category->name }} {{ $category->description }}"
                            data-status="{{ $category->is_active ? 'active' : 'inactive' }}"
                        >
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                                        <x-icon name="folder-tree" size="size-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold">{{ $category->name }}</p>
                                        <p class="font-mono text-xs text-muted-foreground">ID {{ $category->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="max-w-xs truncate text-muted-foreground">
                                {{ $category->description ?: '—' }}
                            </td>
                            <td class="ut-td-center font-mono">{{ $category->orden }}</td>
                            <td class="ut-td-right font-mono font-medium">{{ $category->products_count }}</td>
                            <td>
                                <x-badge :tone="$category->is_active ? 'primary' : 'neutral'">
                                    {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                </x-badge>
                            </td>
                            <td class="ut-td-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('categories.show', $category) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Ver categoría"
                                        aria-label="Ver categoría"
                                    >
                                        <x-icon name="eye" />
                                    </a>
                                    <a
                                        href="{{ route('categories.edit', $category) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Editar categoría"
                                        aria-label="Editar categoría"
                                    >
                                        <x-icon name="pencil" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('categories.destroy', $category) }}"
                                        data-confirm="{{ '¿Eliminar categoría?|Se eliminará «' . $category->name . '». Esta acción no se puede deshacer.' }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="ut-icon-btn ut-icon-btn-danger"
                                            title="Eliminar categoría"
                                            aria-label="Eliminar categoría"
                                        >
                                            <x-icon name="trash-2" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="ut-empty">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                        <x-icon name="inbox" size="size-6" />
                                    </span>
                                    <p class="font-semibold">Sin categorías registradas</p>
                                    <p class="text-sm text-muted-foreground">Crea una categoría para organizar tus productos.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ut-card-footer">
            {{ $categories->links('layout.pagination') }}
        </div>
    </div>
@endsection
