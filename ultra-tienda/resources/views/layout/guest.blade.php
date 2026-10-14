<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-background">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#061A40">

        <title>@yield('title', 'Acceso · '.config('app.name', 'Ultra Tienda'))</title>

        <script>
            (function () {
                try {
                    var theme = localStorage.getItem('theme');
                    if (theme !== 'dark' && theme !== 'light') {
                        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    }
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    }
                } catch (error) {}
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="min-h-full bg-background font-sans text-foreground antialiased">
        <main class="ut-auth-page min-h-dvh">
            <div class="w-full">
                @yield('content')
            </div>
        </main>

        @stack('scripts')
    </body>
</html>
