@extends('layout.guest')

@section('title', 'Iniciar sesión · Ultra Tienda')

@section('content')
    <div class="ut-auth-shell">
        <aside class="ut-auth-hero" aria-label="Información de Ultra Tienda">
            <div class="ut-auth-hero-brand">
                <span class="ut-auth-hero-logo"><x-icon name="shopping-bag" size="size-5" /></span>
                <span>Ultra Tienda</span>
            </div>
            <div class="ut-auth-hero-art" aria-hidden="true">
                <span class="ut-box ut-box-top"></span>
                <span class="ut-box ut-box-middle"></span>
                <span class="ut-box ut-box-bottom"></span>
            </div>
            <div class="ut-auth-hero-copy">
                <span>Panel de administración</span>
                <h2>Todo tu comercio,<br>gestionado desde<br>un solo lugar.</h2>
                <p>Productos, pedidos, clientes e inventario con la información siempre al día.</p>
            </div>
            <div class="ut-auth-hero-security">
                <x-icon name="shield" size="size-4" />
                Conexión cifrada · Acceso exclusivo para personal autorizado
            </div>
        </aside>
        <section class="ut-auth-content">
    {{-- Encabezado / Hero --}}
    <div class="ut-auth-mobile-header relative w-full overflow-hidden px-5 pt-4 pb-6 text-center">
        <div class="pointer-events-none absolute -top-12 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-primary-soft/50 blur-3xl"></div>
        <div class="pointer-events-none absolute top-10 right-4 h-28 w-28 rounded-full bg-secondary/60 blur-2xl"></div>

        <button
            type="button"
            class="ut-icon-btn ut-icon-btn-default absolute top-2 right-2 z-20"
            data-theme-toggle
            title="Cambiar de tema"
            aria-label="Cambiar entre modo claro y modo oscuro"
        >
            <x-icon name="moon" size="size-5" class="ut-theme-moon" />
            <x-icon name="sun" size="size-5" class="ut-theme-sun" />
        </button>

        <div class="relative z-10 flex flex-col items-center gap-3 pb-2">
            <span class="flex size-16 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-lg">
                <x-icon name="shopping-bag" size="size-8" />
            </span>
            <div>
                <h1 class="text-xl font-extrabold tracking-tight">Ultra Tienda</h1>
                <p class="text-sm text-muted-foreground">Panel de administración · Acceso seguro</p>
            </div>
        </div>
    </div>

    {{-- Contenedor principal --}}
    <div class="px-1">
        <div class="ut-auth-card">
                {{-- Tabs Login / Recuperar --}}
                <div class="ut-tabs mb-5" role="tablist">
                    <button
                        type="button"
                        id="tab-login"
                        role="tab"
                        aria-selected="{{ $tab === 'login' ? 'true' : 'false' }}"
                        data-auth-tab="login"
                        class="ut-tab"
                    >
                        <x-icon name="log-in" size="size-4" />
                        Iniciar Sesión
                    </button>
                    <button
                        type="button"
                        id="tab-recovery"
                        role="tab"
                        aria-selected="{{ $tab === 'recovery' ? 'true' : 'false' }}"
                        data-auth-tab="recovery"
                        class="ut-tab"
                    >
                        <x-icon name="rotate-ccw" size="size-4" />
                        Recuperar Cuenta
                    </button>
                </div>

                {{-- Banner de notificaciones --}}
                @include('layout.flash')

                {{-- VISTA 1: LOGIN --}}
                <div id="view-login" class="{{ $tab === 'login' ? 'flex flex-col' : 'hidden' }}">
                    <div class="ut-auth-intro">
                        <h1>Bienvenido de nuevo</h1>
                        <p>Ingresa tus credenciales para acceder al panel de administración.</p>
                    </div>
                    <form id="form-login" method="POST" action="{{ route('login.attempt') }}" class="flex flex-col gap-5" novalidate>
                        @csrf

                        {{-- Campo 1: Correo --}}
                        <div class="flex flex-col gap-2">
                            <label class="ut-label" for="email">
                                Correo electrónico <span class="text-destructive">*</span>
                            </label>
                            <div class="ut-field">
                                <span class="ut-field-icon"><x-icon name="mail" size="size-[18px]" /></span>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="ej. maria@email.com"
                                    autocomplete="username"
                                    required
                                    autofocus
                                    @class([
                                        'ut-input pl-11',
                                        'ut-input-error' => $errors->has('email'),
                                    ])
                                >
                            </div>
                            @error('email')
                                <p class="ut-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo 2: Contraseña --}}
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <label class="ut-label" for="password">
                                    Contraseña <span class="text-destructive">*</span>
                                </label>
                                <button
                                    type="button"
                                    data-auth-switch="recovery"
                                    class="text-xs font-semibold text-primary hover:underline"
                                >
                                    ¿Olvidaste tu contraseña?
                                </button>
                            </div>
                            <div class="ut-field">
                                <span class="ut-field-icon"><x-icon name="key" size="size-[18px]" /></span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Ingresa tu contraseña"
                                    autocomplete="current-password"
                                    required
                                    @class([
                                        'ut-input pl-11 pr-12',
                                        'ut-input-error' => $errors->has('password'),
                                    ])
                                >
                                <button
                                    type="button"
                                    class="absolute right-2 flex size-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                    data-password-toggle="password"
                                    aria-label="Mostrar u ocultar contraseña"
                                >
                                    <x-icon name="eye" size="size-5" data-password-open />
                                    <x-icon name="eye-off" size="size-5" class="hidden" data-password-closed />
                                </button>
                            </div>
                        </div>

                        {{-- Recordar sesión --}}
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex cursor-pointer items-center gap-2.5 select-none" for="remember-me">
                                <input
                                    type="checkbox"
                                    id="remember-me"
                                    name="remember"
                                    value="1"
                                    class="ut-check"
                                    @checked(old('remember'))
                                >
                                <span class="text-sm text-foreground">Recordar mi sesión en este dispositivo</span>
                            </label>
                        </div>

                        {{-- CTA Login --}}
                        <button
                            type="submit"
                            class="ut-btn-primary mt-1"
                        >
                            <span>Acceder al panel</span>
                            <x-icon name="arrow-right" size="size-5" />
                        </button>
                    </form>
                </div>

                {{-- VISTA 2: RECUPERACIÓN --}}
                <div id="view-recovery" class="{{ $tab === 'recovery' ? 'flex flex-col' : 'hidden' }}">
                    @include('auth.recovery')
            </div>
        </div>

        {{-- Soporte --}}
        <div class="mt-8 flex items-start gap-3.5 rounded-2xl border bg-card/70 p-4 shadow-sm backdrop-blur-sm">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary">
                <x-icon name="life-buoy" size="size-5" />
            </span>
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-foreground">¿Inconvenientes con tu acceso?</span>
                <p class="mt-0.5 text-xs leading-relaxed text-muted-foreground">
                    Comunícate con el equipo de soporte en
                    <a class="font-semibold text-primary hover:underline" href="mailto:{{ config('mail.from.address') }}">
                        {{ config('mail.from.address') }}
                    </a>.
                </p>
                <div class="mt-2 flex items-center gap-4">
                    <a class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline" href="mailto:{{ config('mail.from.address') }}">
                        <x-icon name="mail" size="size-3.5" />
                        Escribir a soporte
                    </a>
                    <span class="text-border">•</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-muted-foreground">
                        <span class="size-2 rounded-full bg-primary"></span>
                        Sesión cifrada
                    </span>
                </div>
            </div>
        </div>
    </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var viewLogin = document.getElementById('view-login');
            var viewRecovery = document.getElementById('view-recovery');
            var tabButtons = document.querySelectorAll('[data-auth-tab]');

            function setView(element, visible) {
                element.classList.toggle('hidden', !visible);
                element.classList.toggle('flex', visible);
                element.classList.toggle('flex-col', visible);
            }

            function setActiveTab(name) {
                tabButtons.forEach(function (button) {
                    var isActive = button.getAttribute('data-auth-tab') === name;
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
            }

            function switchAuthView(name) {
                setView(viewLogin, name === 'login');
                setView(viewRecovery, name === 'recovery');
                setActiveTab(name);
            }

            window.switchAuthView = switchAuthView;

            tabButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    switchAuthView(button.getAttribute('data-auth-tab'));
                });
            });

            document.querySelectorAll('[data-auth-switch]').forEach(function (element) {
                element.addEventListener('click', function () {
                    switchAuthView(element.getAttribute('data-auth-switch'));
                });
            });

            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                var input = document.getElementById(button.getAttribute('data-password-toggle'));
                if (!input) return;

                var openIcon = button.querySelector('[data-password-open]');
                var closedIcon = button.querySelector('[data-password-closed]');

                button.addEventListener('click', function () {
                    var reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';
                    if (openIcon) openIcon.classList.toggle('hidden', reveal);
                    if (closedIcon) closedIcon.classList.toggle('hidden', !reveal);
                });
            });

            var otpBoxes = Array.from(document.querySelectorAll('[data-otp-box]'));
            var otpValue = document.getElementById('code');

            function syncOtp() {
                if (otpValue) {
                    otpValue.value = otpBoxes.map(function (box) {
                        return box.value;
                    }).join('');
                }
            }

            otpBoxes.forEach(function (box, index) {
                box.addEventListener('input', function () {
                    box.value = box.value.replace(/\D/g, '').slice(-1);
                    syncOtp();
                    if (box.value && otpBoxes[index + 1]) otpBoxes[index + 1].focus();
                });

                box.addEventListener('keydown', function (event) {
                    if (event.key === 'Backspace' && !box.value && otpBoxes[index - 1]) {
                        otpBoxes[index - 1].focus();
                    }
                });

                box.addEventListener('paste', function (event) {
                    var pasted = (event.clipboardData || window.clipboardData).getData('text')
                        .replace(/\D/g, '')
                        .slice(0, otpBoxes.length);

                    if (!pasted) return;

                    event.preventDefault();
                    pasted.split('').forEach(function (digit, digitIndex) {
                        if (otpBoxes[index + digitIndex]) otpBoxes[index + digitIndex].value = digit;
                    });
                    syncOtp();
                    var nextIndex = Math.min(index + pasted.length, otpBoxes.length - 1);
                    otpBoxes[nextIndex].focus();
                });
            });

            syncOtp();
        })();
    </script>
@endpush
