<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Pelicon') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=unica-one" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="theme-shell min-h-screen antialiased">
        <div class="flex min-h-screen w-full flex-col">
            <x-site-navbar />

            <main class="flex-1 px-5 py-10 sm:px-8 sm:py-14 lg:px-10">
                <div class="mx-auto w-full max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </body>
</html>
