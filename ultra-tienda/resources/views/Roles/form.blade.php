@php
    $role = $role ?? null;
@endphp

<div class="ut-card-body flex flex-col gap-5">
    <div class="ut-field">
        <label class="ut-label" for="name">
            Nombre del rol <span class="text-destructive">*</span>
        </label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $role?->name) }}"
            placeholder="Ej. admin, vendedor, cliente"
            required
            @class(['ut-input', 'ut-input-error' => $errors->has('name')])
        >
        @error('name')
            <p class="ut-error">{{ $message }}</p>
        @enderror
        <p class="ut-hint">El nombre debe ser único dentro del sistema.</p>
    </div>
</div>
