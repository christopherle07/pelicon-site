<x-public-layout title="{{ config('app.name', 'Pelicon') }}">
    <div class="home-page">
        <section class="home-hero">
            <div class="home-hero__layout">
                <div class="home-hero__copy">
                    <h1 class="title-hero home-hero-title text-4xl sm:text-6xl">
                        Say Hi to <span class="home-hero-title__accent">Pelicon</span>.
                    </h1>

                    <div class="home-hero__body">
                        <p class="copy-base text-base leading-8">
                            Your personal reference-image organizer made for artists who see value in organization. Import your personal folders and sort, organize, and create a moodboard. Arrange everything your way, locally.
                        </p>
                    </div>

                    <div class="home-hero__actions">
                        <div x-data="{ openDownloads: false }" class="relative">
                            <button type="button" @click="openDownloads = ! openDownloads" class="home-cta-primary">
                                <span>Try Pelicon</span>
                                <span class="text-xs">▾</span>
                            </button>

                            <div x-show="openDownloads" x-cloak @click.outside="openDownloads = false" class="absolute left-0 top-[calc(100%+0.75rem)] z-20 flex min-w-52 flex-col gap-2 rounded-xl p-3 home-cta-menu">
                                @foreach ($downloadPlatforms as $platform)
                                    <a
                                        href="{{ $platform['download_url'] }}"
                                        download="{{ $platform['filename'] }}"
                                        @click="openDownloads = false"
                                        class="home-cta-menu__item"
                                    >
                                        {{ $platform['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <a href="{{ route('news.index') }}" class="home-cta-secondary">
                            Latest news
                        </a>

                        <a href="{{ route('forum.index') }}" class="home-inline-link">
                            Forum
                        </a>
                    </div>
                </div>

                <div class="home-preview-shell" aria-label="Pelicon layout preview">
                    <img
                        src="{{ asset('build/assets/preview-1.png') }}"
                        alt="Pelicon board preview"
                        class="home-preview-image"
                    >
                </div>
            </div>
        </section>

        <section class="home-strip" aria-label="Core benefits">
            <article class="home-strip__card">
                <p class="section-kicker">1</p>
                <h2 class="title-section text-2xl">Import your own personal workspace</h2>
            </article>

            <article class="home-strip__card">
                <p class="section-kicker">2</p>
                <h2 class="title-section text-2xl">Organize and view all your reference images with ease</h2>
            </article>

            <article class="home-strip__card">
                <p class="section-kicker">3</p>
                <h2 class="title-section text-2xl">Create aesthetic mood boards with all of our neat tools and features</h2>
            </article>
        </section>

        <section class="home-bottom-grid">
            <article class="home-announcement">
                <p class="section-kicker">Latest News</p>

                @if ($latestAnnouncement)
                    <div class="home-announcement__meta">
                        <span>{{ $latestAnnouncement->published_at?->format('M j, Y') }}</span>
                        <span>&middot;</span>
                        <span>{{ $latestAnnouncement->comments_count }} comments</span>
                        <span>&middot;</span>
                        <span>{{ $latestAnnouncement->reactions_count }} reactions</span>
                    </div>

                    <h2 class="title-section mt-4 max-w-3xl text-3xl">{{ $latestAnnouncement->title }}</h2>
                    <p class="copy-base mt-4 max-w-3xl text-base leading-8">
                        {{ $latestAnnouncement->excerpt }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('news.show', $latestAnnouncement) }}" class="home-news-card__button">
                            Open post
                        </a>
                        <a href="{{ route('news.index') }}" class="home-news-card__button home-news-card__button--ghost">
                            All news
                        </a>
                    </div>
                @else
                    <h2 class="title-section mt-4 max-w-3xl text-3xl">No update posted yet.</h2>
                    <div class="mt-8">
                        <a href="{{ route('news.index') }}" class="home-news-card__button">
                            Open news
                        </a>
                    </div>
                @endif
            </article>

            <aside class="home-links-panel">
                <p class="section-kicker">Quick Links</p>

                <div class="home-links-list mt-3">
                    <a href="{{ route('download.index') }}" class="home-links-item">
                        <span>Download</span>
                        <span>Open</span>
                    </a>

                    <a href="{{ route('news.index') }}" class="home-links-item">
                        <span>News</span>
                        <span>Open</span>
                    </a>

                    <a href="{{ route('forum.index') }}" class="home-links-item">
                        <span>Forum</span>
                        <span>Open</span>
                    </a>

                    <a href="https://discord.gg/WFFCqzAb" class="home-links-item" target="_blank" rel="noreferrer">
                        <span>Discord</span>
                        <span>Join</span>
                    </a>
                </div>
            </aside>
        </section>
    </div>
</x-public-layout>
