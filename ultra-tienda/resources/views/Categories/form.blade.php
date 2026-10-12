@php
    $category = $category ?? null;
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
                value="{{ old('name', $category?->name) }}"
                placeholder="Ej. Hogar y decoración"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('name')])
            >
            @error('name')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="orden">Orden en la tienda</label>
            <input
                type="number"
                id="orden"
                name="orden"
                min="0"
                value="{{ old('orden', $category?->orden ?? 0) }}"
                @class(['ut-input', 'ut-input-error' => $errors->has('orden')])
            >
            @error('orden')
                <p class="ut-error">{{ $message }}</p>
            @enderror
            <p class="ut-hint">Las categorías se muestran de menor a mayor.</p>
        </div>
    </div>

    <div class="ut-field">
        <label class="ut-label" for="description">Descripción</label>
        <textarea
            id="description"
            name="description"
            placeholder="Qué agrupa esta sección del catálogo"
            @class(['ut-textarea', 'ut-textarea-error' => $errors->has('description')])
        >{{ old('description', $category?->description) }}</textarea>
        @error('description')
            <p class="ut-error">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium">
        <input type="hidden" name="is_active" value="0">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            class="ut-check"
            @checked(old('is_active', $category?->is_active ?? true))
        >
        Categoría visible en la tienda
    </label>
</div>
