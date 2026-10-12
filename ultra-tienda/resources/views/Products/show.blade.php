@extends('layout.app')

@section('title', $product->name . ' · Ultra Tienda')

@section('content')
    @php
        $image = $product->image;
        $imageSrc = $image && str_starts_with($image, 'http') ? $image : ($image ? asset($image) : null);
    @endphp

    <x-page-header
        :breadcrumb="['Catálogo', 'Productos', $product->name]"
        :title="$product->name"
        description="Ficha completa del producto, su categoría, precio y estado en la tienda."
    >
        <x-slot:actions>
            <a href="{{ route('products.index') }}" class="ut-btn ut-btn-outline ut-btn-md">
                <x-icon name="chevron-left" />
                Volver
            </a>
            <a href="{{ route('products.edit', $product) }}" class="ut-btn ut-btn-primary ut-btn-md">
                <x-icon name="pencil" />
                Editar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="ut-card">
        <div class="ut-card-header">
            <div class="flex items-center gap-3">
                @if ($imageSrc)
                    <img
                        src="{{ $imageSrc }}"
                        alt="{{ $product->name }}"
                        class="size-12 shrink-0 rounded-lg border object-cover"
                    >
                @else
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-lg border bg-primary-soft text-primary">
                        <x-icon name="package" size="size-6" />
                    </span>
                @endif
                <div>
                    <p class="font-bold">{{ $product->name }}</p>
                    <p class="font-mono text-xs text-muted-foreground">
                        SKU-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-badge :tone="$product->is_active ? 'info' : 'neutral'">
                    {{ $product->is_active ? 'Activo' : 'Oculto' }}
                </x-badge>
                <x-badge :tone="$product->disponible ? 'primary' : 'danger'">
                    {{ $product->disponible ? 'Disponible' : 'Agotado' }}
                </x-badge>
            </div>
        </div>

        <div class="ut-card-body flex flex-col gap-6">
            <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="ut-hint">Categoría</dt>
                    <dd class="font-semibold">{{ $product->category?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Precio</dt>
                    <dd class="font-mono font-semibold"><x-money :value="$product->price" /></dd>
                </div>
                <div>
                    <dt class="ut-hint">Creado</dt>
                    <dd>{{ $product->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="ut-hint">Actualizado</dt>
                    <dd>{{ $product->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>

            <div class="border-t pt-5">
                <p class="ut-hint mb-2">Descripción</p>
                <p class="text-sm leading-relaxed text-foreground">
                    {{ $product->description ?: 'Sin descripción registrada.' }}
                </p>
            </div>
        </div>

        <div class="ut-card-footer">
            <form
                method="POST"
                action="{{ route('products.destroy', $product) }}"
                data-confirm="{{ '¿Eliminar producto?|Se eliminará «' . $product->name . '» del catálogo. Esta acción no se puede deshacer.' }}"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="ut-btn ut-btn-danger-soft ut-btn-sm">
                    <x-icon name="trash-2" />
                    Eliminar producto
                </button>
            </form>

            <a href="{{ route('products.index') }}" class="ut-btn ut-btn-outline ut-btn-sm">Volver al listado</a>
        </div>
    </div>
@endsection
