<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Pelicon') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="theme-shell min-h-screen antialiased">
        <div class="flex min-h-screen w-full flex-col">
            <x-site-navbar />

            <main class="flex-1 px-5 py-10 sm:px-8 sm:py-14 lg:px-10">
                <div class="mx-auto w-full max-w-7xl">
                    {{ $slot }}
                </div>
            </main>

            <footer class="w-full text-sm" style="background: var(--bg-shell); color: var(--text-muted);">
                <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-10 lg:py-16">
                    <div class="grid gap-14 border-t pt-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.8fr)] lg:gap-20" style="border-color: var(--border-subtle);">
                        <div class="max-w-sm">
                            <a href="{{ route('home') }}" class="font-display text-3xl font-bold tracking-tight text-[color:var(--text-strong)]">
                                Pelicon
                            </a>
                            <nav class="mt-8 flex items-center gap-5">
                                <a href="https://discord.gg/WFFCqzAb" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70" target="_blank" rel="noreferrer">Discord</a>
                                <a href="{{ route('contact') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">Contact</a>
                            </nav>

                            <p class="copy-faint mt-8">
                                &copy; Pelicon App 2026
                            </p>
                        </div>

                        <div class="grid gap-x-12 gap-y-10 sm:grid-cols-2 xl:grid-cols-3">
                            <div>
                                <p class="section-kicker">Product</p>
                                <nav class="mt-4 flex flex-col gap-3">
                                    <a href="{{ route('download.index') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">Download</a>
                                    <a href="{{ route('news.index') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">News</a>
                                    <a href="{{ route('forum.index') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">Forum</a>
                                </nav>
                            </div>

                            <div>
                                <p class="section-kicker">Documentation</p>
                                <nav class="mt-4 flex flex-col gap-3">
                                    <a href="{{ route('faq') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">FAQ</a>
                                    <a href="https://docs.pelicon.app" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70" target="_blank" rel="noreferrer">App Documentation</a>
                                    <a href="https://dev.pelicon.app" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70" target="_blank" rel="noreferrer">Plugin Documentation</a>
                                </nav>
                            </div>

                            <div>
                                <p class="section-kicker">Legal</p>
                                <nav class="mt-4 flex flex-col gap-3">
                                    <a href="{{ route('terms') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">Terms of Service</a>
                                    <a href="{{ route('privacy') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">Privacy Policy</a>
                                    <a href="{{ route('developer.policies') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">Developer Policy</a>
                                    <a href="{{ route('licensing') }}" class="font-semibold text-[color:var(--text-strong)] transition hover:opacity-70">Licensing Agreement</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        @livewireScriptConfig
    </body>
</html>
