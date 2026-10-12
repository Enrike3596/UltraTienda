@extends('layout.app')

@section('title', $category->name . ' · Ultra Tienda')

@section('content')
    <x-page-header
        :breadcrumb="['Catálogo', 'Categorías', $category->name]"
        :title="$category->name"
        description="Ficha de la categoría y su estado dentro del catálogo."
    >
        <x-slot:actions>
            <a href="{{ route('categories.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
            <a href="{{ route('categories.edit', $category) }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="pencil" />
                Editar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="ut-card">
        <div class="ut-card-header">
            <div class="flex items-center gap-3">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                    <x-icon name="folder-tree" size="size-6" />
                </span>
                <div>
                    <p class="font-bold">{{ $category->name }}</p>
                    <p class="font-mono text-xs text-muted-foreground">ID {{ $category->id }}</p>
                </div>
            </div>

            <x-badge :tone="$category->is_active ? 'primary' : 'neutral'">
                {{ $category->is_active ? 'Activa' : 'Inactiva' }}
            </x-badge>
        </div>

        <div class="ut-card-body flex flex-col gap-6">
            <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="ut-hint">Orden en tienda</dt>
                    <dd class="font-mono font-semibold">{{ $category->orden }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Productos asociados</dt>
                    <dd class="font-mono font-semibold">{{ $category->products()->count() }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Creada</dt>
                    <dd>{{ $category->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Actualizada</dt>
                    <dd>{{ $category->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>

            <div class="border-t pt-5">
                <p class="ut-hint mb-2">Descripción</p>
                <p class="text-sm leading-relaxed text-foreground">
                    {{ $category->description ?: 'Sin descripción registrada.' }}
                </p>
            </div>
        </div>

        <div class="ut-card-footer">
            <form
                method="POST"
                action="{{ route('categories.destroy', $category) }}"
                data-confirm="{{ '¿Eliminar categoría?|Se eliminará «' . $category->name . '». Esta acción no se puede deshacer.' }}"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="ut-btn ut-btn-danger-soft ut-btn-sm">
                    <x-icon name="trash-2" />
                    Eliminar categoría
                </button>
            </form>

            <a href="{{ route('categories.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Volver al listado</a>
        </div>
    </div>
@endsection
