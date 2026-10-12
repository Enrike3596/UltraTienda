<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <body class="ut-auth-page">
        <main class="flex min-h-dvh flex-col items-center justify-center px-4 py-8">
            <div class="w-full max-w-md">
                @yield('content')
            </div>
        </main>

        @stack('scripts')
    </body>
</html>
