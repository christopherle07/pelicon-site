<x-public-layout title="Download - {{ config('app.name', 'Pelicon') }}">
    <section class="surface-panel p-8 sm:p-10">
        <p class="section-kicker">Download</p>
        <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Choose your platform.</h1>
        <p class="copy-muted mt-4 max-w-3xl text-base leading-8">
            Each platform has its own download page with the current plan options, purchase placeholders, and a simple tip jar section we can wire into Stripe later.
        </p>
    </section>

    <section class="mt-8 grid gap-6 md:grid-cols-3">
        @foreach ($platforms as $key => $platform)
            <article class="surface-panel p-8">
                <p class="section-kicker">{{ $platform['name'] }}</p>
                <h2 class="title-section mt-3 text-3xl">{{ $platform['headline'] }}</h2>
                <p class="copy-muted mt-4 text-sm leading-7">
                    {{ $platform['copy'] }}
                </p>

                <a href="{{ route('download.show', $key) }}" class="button-primary mt-8 inline-flex px-6 py-3 text-sm font-semibold transition">
                    Open {{ $platform['name'] }} page
                </a>
            </article>
        @endforeach
    </section>
</x-public-layout>
