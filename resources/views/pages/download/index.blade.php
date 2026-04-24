<x-public-layout title="Download - {{ config('app.name', 'Pelicon') }}">
    <div class="download-page">
        <section class="flex flex-col items-center gap-6">
            <article class="download-card-shell w-full max-w-xl rounded-3xl p-8 sm:p-10">
                <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Download Pelicon</h1>

                <div class="mt-8 grid gap-3">
                    @foreach ($platforms as $platform)
                        <a
                            href="{{ $platform['download_url'] }}"
                            download="{{ $platform['filename'] }}"
                            class="platform-download-card rounded-2xl"
                        >
                            <span class="text-base font-semibold text-[color:var(--text-strong)]">{{ $platform['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </article>

            <aside class="tip-jar-card w-full max-w-xl rounded-3xl p-8 sm:p-10">
                <p class="section-kicker">Tip</p>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <button type="button" disabled class="tip-jar-card__chip rounded-2xl">
                        $3
                    </button>
                    <button type="button" disabled class="tip-jar-card__chip rounded-2xl">
                        $5
                    </button>
                    <button type="button" disabled class="tip-jar-card__chip rounded-2xl">
                        $10
                    </button>
                    <button type="button" disabled class="tip-jar-card__chip rounded-2xl">
                        Custom
                    </button>
                </div>

                <button type="button" disabled class="tip-jar-card__button mt-6 rounded-2xl">
                    Tip
                </button>
            </aside>
        </section>
    </div>
</x-public-layout>
