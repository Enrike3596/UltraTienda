@extends('layout.app')

@section('title', 'Productos · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Catálogo', 'Productos']"
        title="Productos"
        description="Administra el catálogo, precios, disponibilidad y visibilidad de tus productos."
    >
        <x-slot:actions>
            <a href="{{ route('products.create') }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="plus" />
                Nuevo producto
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
                    <label for="products-search" class="sr-only">Buscar productos</label>
                    <input
                        id="products-search"
                        type="search"
                        data-table-filter="products-table"
                        placeholder="Buscar por nombre o categoría..."
                        class="ut-input ut-input-search"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <label for="products-filter" class="sr-only">Estado</label>
                    <select id="products-filter" data-table-select="products-table" class="ut-select h-9">
                        <option value="all">Estado: Todos</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="ut-table-wrap">
            <table id="products-table" class="ut-table">
                <thead>
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Categoría</th>
                        <th scope="col" class="ut-th-right">Precio</th>
                        <th scope="col" class="ut-th-center">Disponibilidad</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Actualizado</th>
                        <th scope="col" class="ut-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $image = $product->image;
                            $imageSrc = $image && str_starts_with($image, 'http') ? $image : ($image ? asset($image) : null);
                        @endphp

                        <tr
                            data-search="{{ $product->name }} {{ $product->category?->name }} {{ $product->description }}"
                            data-status="{{ $product->is_active ? 'active' : 'inactive' }}"
                        >
                            <td>
                                <div class="flex items-center gap-3">
                                    @if ($imageSrc)
                                        <img
                                            src="{{ $imageSrc }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                            class="size-10 shrink-0 rounded-lg border object-cover"
                                        >
                                    @else
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                                            <x-icon name="package" size="size-5" />
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold">{{ $product->name }}</p>
                                        <p class="font-mono text-xs text-muted-foreground">SKU-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted-foreground">{{ $product->category?->name ?? '—' }}</td>
                            <td class="ut-td-right">
                                <span class="font-mono font-medium"><x-money :value="$product->price" /></span>
                            </td>
                            <td class="ut-td-center">
                                <x-badge :tone="$product->disponible ? 'primary' : 'danger'">
                                    {{ $product->disponible ? 'Disponible' : 'Agotado' }}
                                </x-badge>
                            </td>
                            <td>
                                <x-badge :tone="$product->is_active ? 'info' : 'neutral'">
                                    {{ $product->is_active ? 'Activo' : 'Oculto' }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap text-muted-foreground">
                                {{ $product->updated_at?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="ut-td-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('products.show', $product) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Ver producto"
                                        aria-label="Ver producto"
                                    >
                                        <x-icon name="eye" />
                                    </a>
                                    <a
                                        href="{{ route('products.edit', $product) }}"
                                        class="ut-icon-btn ut-icon-btn-default"
                                        title="Editar producto"
                                        aria-label="Editar producto"
                                    >
                                        <x-icon name="pencil" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('products.destroy', $product) }}"
                                        data-confirm="{{ '¿Eliminar producto?|Se eliminará «' . $product->name . '» del catálogo. Esta acción no se puede deshacer.' }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="ut-icon-btn ut-icon-btn-danger"
                                            title="Eliminar producto"
                                            aria-label="Eliminar producto"
                                        >
                                            <x-icon name="trash-2" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="ut-empty">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                                        <x-icon name="inbox" size="size-6" />
                                    </span>
                                    <p class="font-semibold">Sin productos registrados</p>
                                    <p class="text-sm text-muted-foreground">Crea tu primer producto para comenzar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ut-card-footer">
            {{ $products->links('layout.pagination') }}
        </div>
    </div>
@endsection
