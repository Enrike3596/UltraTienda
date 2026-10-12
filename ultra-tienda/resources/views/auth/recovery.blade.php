@if ($step === 1)
    {{-- Paso 1: solicitud del código --}}
    <div class="mb-5 rounded-2xl bg-card p-5 text-center shadow-sm">
        <div class="mx-auto mb-3 flex size-14 items-center justify-center rounded-2xl bg-secondary text-secondary-foreground">
            <x-icon name="shield" size="size-7" />
        </div>
        <h2 class="text-lg font-semibold text-foreground">Recuperar acceso</h2>
        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
            Ingresa el correo electrónico registrado. Te enviaremos un código de seguridad de 6 dígitos
            para restablecer tu contraseña (vigente 15 minutos).
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div class="flex flex-col">
            <label class="ut-label mb-1.5 px-1" for="recovery-email">
                Correo electrónico registrado <span class="text-destructive">*</span>
            </label>
            <div class="relative flex items-center">
                <span class="pointer-events-none absolute left-4 text-muted-foreground">
                    <x-icon name="mail" size="size-5" />
                </span>
                <input
                    type="email"
                    id="recovery-email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="ej. maria@email.com"
                    autocomplete="email"
                    required
                    @class([
                        'ut-input h-[52px] pl-11 pr-4 text-sm',
                        'ut-input-error' => $errors->has('email'),
                    ])
                >
            </div>
            @error('email')
                <p class="ut-error px-1">{{ $message }}</p>
            @enderror
            <p class="mt-1.5 px-1 text-xs text-muted-foreground">
                El código llegará al correo registrado (vigente 15 minutos).
            </p>
        </div>

        <button
            type="submit"
            class="ut-auth-btn mt-2 flex h-[52px] w-full items-center justify-center gap-2 rounded-full text-sm font-semibold transition-transform active:scale-[0.98]"
        >
            <x-icon name="send" size="size-5" />
            <span>Enviar código de recuperación</span>
        </button>

        <button
            type="button"
            data-auth-switch="login"
            class="flex h-11 w-full items-center justify-center gap-2 rounded-full text-sm font-semibold text-primary transition-colors hover:bg-muted"
        >
            <x-icon name="arrow-left" size="size-4" />
            <span>Volver a Iniciar Sesión</span>
        </button>
    </form>
@else
    {{-- Paso 2: código + nueva contraseña --}}
    <div class="mb-5 rounded-2xl bg-card p-5 text-center shadow-sm">
        <div class="mx-auto mb-3 flex size-14 items-center justify-center rounded-2xl bg-primary-soft text-primary">
            <x-icon name="key" size="size-7" />
        </div>
        <h2 class="text-lg font-semibold text-foreground">Restablecer contraseña</h2>
        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
            Ingresa el código de 6 dígitos que enviamos a
            <span class="font-semibold text-foreground">{{ session('recovery_email') }}</span>
            y define tu nueva contraseña.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        {{-- Código de 6 dígitos --}}
        <div class="flex flex-col">
            <label class="ut-label mb-1.5 px-1" for="code">
                Código de 6 dígitos <span class="text-destructive">*</span>
            </label>
            <div class="relative flex items-center">
                <span class="pointer-events-none absolute left-4 text-muted-foreground">
                    <x-icon name="map-pin" size="size-5" />
                </span>
                <input
                    type="text"
                    id="code"
                    name="code"
                    value="{{ old('code') }}"
                    placeholder="000000"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="one-time-code"
                    required
                    @class([
                        'ut-input h-[52px] pl-11 pr-4 text-center text-base tracking-[0.3em]',
                        'ut-input-error' => $errors->has('code'),
                    ])
                >
            </div>
            @error('code')
                <p class="ut-error px-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nueva contraseña --}}
        <div class="flex flex-col">
            <label class="ut-label mb-1.5 px-1" for="recovery-password">
                Nueva contraseña <span class="text-destructive">*</span>
            </label>
            <div class="relative flex items-center">
                <span class="pointer-events-none absolute left-4 text-muted-foreground">
                    <x-icon name="key" size="size-5" />
                </span>
                <input
                    type="password"
                    id="recovery-password"
                    name="password"
                    placeholder="Mínimo 8 caracteres"
                    autocomplete="new-password"
                    required
                    @class([
                        'ut-input h-[52px] pl-11 pr-12 text-sm',
                        'ut-input-error' => $errors->has('password'),
                    ])
                >
                <button
                    type="button"
                    class="absolute right-3 flex items-center justify-center p-1.5 text-muted-foreground transition-colors hover:text-primary"
                    data-password-toggle="recovery-password"
                    aria-label="Mostrar u ocultar contraseña"
                >
                    <x-icon name="eye" size="size-5" data-password-open />
                    <x-icon name="eye-off" size="size-5" class="hidden" data-password-closed />
                </button>
            </div>
            @error('password')
                <p class="ut-error px-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div class="flex flex-col">
            <label class="ut-label mb-1.5 px-1" for="recovery-password-confirmation">
                Confirmar nueva contraseña <span class="text-destructive">*</span>
            </label>
            <div class="relative flex items-center">
                <span class="pointer-events-none absolute left-4 text-muted-foreground">
                    <x-icon name="key" size="size-5" />
                </span>
                <input
                    type="password"
                    id="recovery-password-confirmation"
                    name="password_confirmation"
                    placeholder="Repite la nueva contraseña"
                    autocomplete="new-password"
                    required
                    class="ut-input h-[52px] pl-11 pr-12 text-sm"
                >
                <button
                    type="button"
                    class="absolute right-3 flex items-center justify-center p-1.5 text-muted-foreground transition-colors hover:text-primary"
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
            class="ut-auth-btn mt-2 flex h-[52px] w-full items-center justify-center gap-2 rounded-full text-sm font-semibold transition-transform active:scale-[0.98]"
        >
            <x-icon name="rotate-ccw" size="size-5" />
            <span>Restablecer contraseña</span>
        </button>

        <div class="flex flex-col gap-1">
            <a
                href="{{ route('password.request') }}"
                class="flex h-11 w-full items-center justify-center gap-2 rounded-full text-sm font-semibold text-primary transition-colors hover:bg-muted"
            >
                <x-icon name="mail" size="size-4" />
                <span>Solicitar otro código</span>
            </a>
            <button
                type="button"
                data-auth-switch="login"
                class="flex h-11 w-full items-center justify-center gap-2 rounded-full text-sm font-semibold text-primary transition-colors hover:bg-muted"
            >
                <x-icon name="arrow-left" size="size-4" />
                <span>Volver a Iniciar Sesión</span>
            </button>
        </div>
    </form>
@endif
