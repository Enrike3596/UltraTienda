<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Ultra Tienda'))</title>

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
                    if (localStorage.getItem('sidebar-collapsed') === '1') {
                        document.documentElement.setAttribute('data-sidebar', 'collapsed');
                    }
                } catch (error) {}
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <div class="flex min-h-dvh">
            @include('layout.Sidebar.sidebar')

            <div class="flex min-w-0 flex-1 flex-col">
                @include('layout.Topbar.topbar')

                <main class="ut-main">
                    <div class="ut-container">
                        @include('layout.flash')

                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
