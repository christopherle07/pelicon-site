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
        <div class="relative z-[1] flex min-h-screen w-full flex-col">
            <x-site-navbar />

            <main class="flex-1 px-5 py-10 sm:px-8 sm:py-14 lg:px-10">
                <div class="mx-auto w-full max-w-7xl">
                    {{ $slot }}
                </div>
            </main>

            <footer class="site-footer">
                <div class="site-footer__inner">
                    <div>
                        <a href="{{ route('home') }}" class="site-footer__brand">
                            Pelicon
                        </a>
                        <p class="site-footer__copy">
                            App suite for visual work.
                        </p>
                    </div>

                    <nav class="site-footer__links" aria-label="Footer navigation">
                        <a href="{{ route('download.index') }}">Products</a>
                        <a href="https://ko-fi.com/peliconapp" target="_blank" rel="noreferrer">Ko-Fi</a>
                        <a href="{{ route('contact') }}">Contact</a>
                        <a href="{{ route('privacy') }}">Privacy</a>
                        <a href="{{ route('terms') }}">Terms</a>
                    </nav>
                </div>

                <div class="site-footer__meta">
                    &copy; Pelicon App 2026
                </div>
            </footer>
        </div>

        <script src="https://storage.ko-fi.com/cdn/scripts/overlay-widget.js"></script>
        <script>
            kofiWidgetOverlay.draw('peliconapp', {
                'type': 'floating-chat',
                'floating-chat.donateButton.text': 'Tip',
                'floating-chat.donateButton.background-color': '#00b9fe',
                'floating-chat.donateButton.text-color': '#fff'
            });
        </script>

    </body>
</html>
