<x-public-layout title="{{ config('app.name', 'Pelicon') }}">
    <div class="home-page">
        <section class="home-hero">
            <div class="home-hero__layout">
                <div class="home-hero__copy">
                    <h1 class="title-hero home-hero-title text-4xl sm:text-6xl" data-typing-title aria-label="Say Hi to Pelicon.">
                        <span data-type-part data-type-text="Say Hi to ">Say Hi to </span><span class="home-hero-title__accent" data-type-part data-type-text="Pelicon">Pelicon</span><span data-type-part data-type-text=".">.</span><span class="home-typing-cursor" aria-hidden="true"></span>
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
                                <span class="home-cta-chevron" aria-hidden="true"></span>
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
                            Updates
                        </a>
                    </div>
                </div>

                <div class="home-preview-shell" aria-label="Pelicon layout preview">
                    <img
                        src="{{ asset('assets/preview-1.png') }}"
                        alt="Pelicon board preview"
                        class="home-preview-image"
                    >
                </div>
            </div>
        </section>

        <a href="#home-demos" class="home-read-more" data-read-more-cue>
            <span>Read More</span>
            <span class="home-read-more__chevrons" aria-hidden="true"></span>
        </a>

        <section id="home-demos" class="home-demo-section" aria-label="Pelicon demos">
            <div class="home-demo-section__header" data-scroll-reveal>
                <p class="section-kicker">Demos</p>
                <h2 class="title-section text-3xl">See Pelicon in motion</h2>
            </div>

            <article class="home-demo-feature" data-scroll-reveal>
                <div class="home-demo-feature__media" data-scroll-reveal>
                    <video
                        class="home-demo-video"
                        data-demo-video
                        muted
                        loop
                        playsinline
                        autoplay
                        preload="none"
                        aria-label="Pelicon reference image demo"
                    >
                        <source data-src="{{ asset('assets/Demo-image.mp4') }}" type="video/mp4">
                    </video>
                </div>

                <div class="home-demo-feature__copy" data-scroll-reveal>
                    <p class="section-kicker">Workspace</p>
                    <h3 class="title-section text-3xl">Import your reference library without changing how you work.</h3>
                    <p class="copy-base text-base leading-8">
                        Bring local folders into Pelicon and browse everything visually, with image details close at hand and no cloud workflow in the way.
                    </p>
                </div>
            </article>

            <article class="home-demo-feature home-demo-feature--reverse" data-scroll-reveal>
                <div class="home-demo-feature__media" data-scroll-reveal>
                    <video
                        class="home-demo-video"
                        data-demo-video
                        muted
                        loop
                        playsinline
                        autoplay
                        preload="none"
                        aria-label="Pelicon board organization demo"
                    >
                        <source data-src="{{ asset('assets/Demo-board.mp4') }}" type="video/mp4">
                    </video>
                </div>

                <div class="home-demo-feature__copy" data-scroll-reveal>
                    <p class="section-kicker">Boards</p>
                    <h3 class="title-section text-3xl">Build mood boards that stay fluid while you arrange ideas.</h3>
                    <p class="copy-base text-base leading-8">
                        Drag, scale, group, and compare references on a canvas made for visual exploration instead of file management chores.
                    </p>
                </div>
            </article>

            <article class="home-demo-feature" data-scroll-reveal>
                <div class="home-demo-feature__media" data-scroll-reveal>
                    <video
                        class="home-demo-video"
                        data-demo-video
                        muted
                        loop
                        playsinline
                        autoplay
                        preload="none"
                        aria-label="Pelicon customization demo"
                    >
                        <source data-src="{{ asset('assets/Demo-customize.mp4') }}" type="video/mp4">
                    </video>
                </div>

                <div class="home-demo-feature__copy" data-scroll-reveal>
                    <p class="section-kicker">Customize</p>
                    <h3 class="title-section text-3xl">Tune each board to match the way the project wants to feel.</h3>
                    <p class="copy-base text-base leading-8">
                        Adjust board details and presentation quickly, then keep moving while the visual direction is still fresh.
                    </p>
                </div>
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
