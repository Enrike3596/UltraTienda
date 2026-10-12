@php
    $product = $product ?? null;
@endphp

<div class="ut-card-body flex flex-col gap-5">
    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="name">
                Nombre <span class="text-destructive">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $product?->name) }}"
                placeholder="Ej. Taza cerámica Ultra"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('name')])
            >
            @error('name')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="category_id">
                Categoría <span class="text-destructive">*</span>
            </label>
            <select
                id="category_id"
                name="category_id"
                required
                @class(['ut-select', 'ut-select-error' => $errors->has('category_id')])
            >
                <option value="">Selecciona una categoría</option>
                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected((string) old('category_id', $product?->category_id) === (string) $category->id)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="price">
                Precio <span class="text-destructive">*</span>
            </label>
            <input
                type="number"
                id="price"
                name="price"
                step="0.01"
                min="0"
                value="{{ old('price', $product?->price) }}"
                placeholder="0.00"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('price')])
            >
            @error('price')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="image">Imagen</label>
            <input
                type="text"
                id="image"
                name="image"
                value="{{ old('image', $product?->image) }}"
                placeholder="https://... o ruta dentro de /public"
                @class(['ut-input', 'ut-input-error' => $errors->has('image')])
            >
            @error('image')
                <p class="ut-error">{{ $message }}</p>
            @enderror
            <p class="ut-hint">Deja este campo vacío para usar el icono por defecto.</p>
        </div>
    </div>

    <div class="ut-field">
        <label class="ut-label" for="description">Descripción</label>
        <textarea
            id="description"
            name="description"
            placeholder="Características, materiales, medidas..."
            @class(['ut-textarea', 'ut-textarea-error' => $errors->has('description')])
        >{{ old('description', $product?->description) }}</textarea>
        @error('description')
            <p class="ut-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:gap-6">
        <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium">
            <input type="hidden" name="disponible" value="0">
            <input
                type="checkbox"
                name="disponible"
                value="1"
                class="ut-check"
                @checked(old('disponible', $product?->disponible ?? true))
            >
            Disponible para vender
        </label>

        <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium">
            <input type="hidden" name="is_active" value="0">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="ut-check"
                @checked(old('is_active', $product?->is_active ?? true))
            >
            Visible en la tienda
        </label>
    </div>
</div>
