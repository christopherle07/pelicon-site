<x-public-layout title="Licensing - {{ config('app.name', 'Pelicon') }}">
    <section class="surface-panel max-w-4xl p-8 sm:p-10">
        <p class="section-kicker">Licensing</p>
        <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Licensing</h1>
        <div class="copy-base mt-6 space-y-4 text-base leading-8">
            <p>Pelicon offers separate paths for personal use, business use, and optional support.</p>
            <p>This page is the placeholder for the full licensing terms, permitted usage, and purchase rules for teams and commercial work.</p>
        </div>

        <div class="mt-8">
            <a href="{{ route('download.index') }}" class="button-secondary inline-flex px-6 py-3 text-sm font-semibold transition">
                View Download Plans
            </a>
        </div>
    </section>
</x-public-layout>
