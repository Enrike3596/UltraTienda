@if ($step === 1)
    {{-- Paso 1: solicitud del código --}}
    <div class="mb-7 flex flex-col gap-2">
        <span class="mb-2 flex size-11 items-center justify-center rounded-xl bg-primary-soft text-primary">
            <x-icon name="shield" size="size-5" />
        </span>
        <h2 class="text-balance text-2xl font-bold tracking-tight text-foreground">Recuperar acceso</h2>
        <p class="text-pretty text-sm leading-relaxed text-muted-foreground">
            Ingresa el correo electrónico registrado. Te enviaremos un código de seguridad de 6 dígitos
            para restablecer tu contraseña.
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5" novalidate>
        @csrf

        <div class="flex flex-col gap-2">
            <label class="ut-label" for="recovery-email">
                Correo electrónico registrado <span class="text-destructive">*</span>
            </label>
            <div class="ut-field">
                <span class="ut-field-icon"><x-icon name="mail" size="size-[18px]" /></span>
                <input
                    type="email"
                    id="recovery-email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="ej. maria@email.com"
                    autocomplete="email"
                    required
                    @class([
                        'ut-input pl-11',
                        'ut-input-error' => $errors->has('email'),
                    ])
                >
            </div>
            @error('email')
                <p class="ut-error">{{ $message }}</p>
            @enderror
            <p class="ut-hint">El código tendrá una vigencia de 15 minutos.</p>
        </div>

        <button
            type="submit"
            class="ut-btn-primary mt-1"
        >
            <x-icon name="send" size="size-5" />
            <span>Enviar código</span>
        </button>

        <button
            type="button"
            data-auth-switch="login"
            class="ut-btn-ghost"
        >
            <x-icon name="arrow-left" size="size-4" />
            <span>Volver a iniciar sesión</span>
        </button>
    </form>
@else
    {{-- Paso 2: código + nueva contraseña --}}
    <div class="mb-7 flex flex-col gap-2">
        <span class="mb-2 flex size-11 items-center justify-center rounded-xl bg-primary-soft text-primary">
            <x-icon name="key" size="size-5" />
        </span>
        <h2 class="text-balance text-2xl font-bold tracking-tight text-foreground">Restablecer contraseña</h2>
        <p class="text-pretty text-sm leading-relaxed text-muted-foreground">
            Ingresa el código de 6 dígitos que enviamos a
            <span class="font-semibold text-foreground">{{ session('recovery_email') }}</span>
            y define tu nueva contraseña.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5" novalidate>
        @csrf

        {{-- Código de 6 dígitos --}}
        <fieldset class="flex flex-col gap-2">
            <legend class="ut-label mb-2">Código de verificación</legend>
            @php($oldCode = str_split(str_pad((string) old('code'), 6)))
            <div class="grid grid-cols-6 gap-2" data-otp="code">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" inputmode="numeric" maxlength="1" value="{{ trim($oldCode[$i] ?? '') }}"
                        autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                        aria-label="Dígito {{ $i + 1 }} de 6" class="ut-otp" data-otp-box>
                @endfor
            </div>
            <input type="hidden" id="code" name="code" value="{{ old('code') }}">
            @error('code')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </fieldset>
        {{-- Nueva contraseña --}}
        <div class="flex flex-col gap-2">
            <label class="ut-label" for="recovery-password">
                Nueva contraseña <span class="text-destructive">*</span>
            </label>
            <div class="ut-field">
                <span class="ut-field-icon"><x-icon name="key" size="size-[18px]" /></span>
                <input
                    type="password"
                    id="recovery-password"
                    name="password"
                    placeholder="Mínimo 8 caracteres"
                    autocomplete="new-password"
                    required
                    @class([
                        'ut-input pl-11 pr-12',
                        'ut-input-error' => $errors->has('password'),
                    ])
                >
                <button
                    type="button"
                    class="absolute right-2 flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    data-password-toggle="recovery-password"
                    aria-label="Mostrar u ocultar contraseña"
                >
                    <x-icon name="eye" size="size-5" data-password-open />
                    <x-icon name="eye-off" size="size-5" class="hidden" data-password-closed />
                </button>
            </div>
            @error('password')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div class="flex flex-col gap-2">
            <label class="ut-label" for="recovery-password-confirmation">
                Confirmar nueva contraseña <span class="text-destructive">*</span>
            </label>
            <div class="ut-field">
                <span class="ut-field-icon"><x-icon name="key" size="size-[18px]" /></span>
                <input
                    type="password"
                    id="recovery-password-confirmation"
                    name="password_confirmation"
                    placeholder="Repite la nueva contraseña"
                    autocomplete="new-password"
                    required
                    class="ut-input pl-11 pr-12"
                >
                <button
                    type="button"
                    class="absolute right-2 flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    data-password-toggle="recovery-password-confirmation"
                    aria-label="Mostrar u ocultar contraseña"
                >
                    <x-icon name="eye" size="size-5" data-password-open />
                    <x-icon name="eye-off" size="size-5" class="hidden" data-password-closed />
                </button>
            </div>
        </div>

        <button
            type="submit"
            class="ut-btn-primary mt-1"
        >
            <x-icon name="rotate-ccw" size="size-5" />
            <span>Restablecer contraseña</span>
        </button>

        <div class="flex flex-col gap-1">
            <a
                href="{{ route('password.request') }}"
                class="ut-btn-ghost"
            >
                <x-icon name="mail" size="size-4" />
                <span>Solicitar otro código</span>
            </a>
            <button
                type="button"
                data-auth-switch="login"
                class="ut-btn-ghost"
            >
                <x-icon name="arrow-left" size="size-4" />
                <span>Volver a iniciar sesión</span>
            </button>
        </div>
    </form>
@endif
