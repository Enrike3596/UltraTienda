@php
    $user = $user ?? null;
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
                value="{{ old('name', $user?->name) }}"
                placeholder="Nombre completo"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('name')])
            >
            @error('name')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="email">
                Correo electrónico <span class="text-destructive">*</span>
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user?->email) }}"
                placeholder="usuario@correo.com"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('email')])
            >
            @error('email')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="password">
                Contraseña {!! $user ? '' : '<span class="text-destructive">*</span>' !!}
            </label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                placeholder="{{ $user ? 'Dejar en blanco para mantenerla' : 'Mínimo 8 caracteres' }}"
                {!! $user ? '' : 'required' !!}
                @class(['ut-input', 'ut-input-error' => $errors->has('password')])
            >
            @error('password')
                <p class="ut-error">{{ $message }}</p>
            @enderror
            <p class="ut-hint">
                {{ $user ? 'Si no cambias la contraseña, se mantiene la actual.' : 'Mínimo 8 caracteres.' }}
            </p>
        </div>

        <div class="ut-field">
            <label class="ut-label" for="role_id">Rol</label>
            <select id="role_id" name="role_id" @class(['ut-select', 'ut-select-error' => $errors->has('role_id')])>
                <option value="">Sin rol asignado</option>
                @foreach ($roles as $role)
                    <option
                        value="{{ $role->id }}"
                        @selected((string) old('role_id', $user?->role_id) === (string) $role->id)
                    >
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
