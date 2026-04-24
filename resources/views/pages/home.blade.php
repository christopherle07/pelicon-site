<x-public-layout title="{{ config('app.name', 'Pelicon') }}">
    <div class="home-page space-y-16 sm:space-y-20">
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

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <div x-data="{ openDownloads: false }" class="relative">
                            <button type="button" @click="openDownloads = ! openDownloads" class="home-cta-primary">
                                <span>Try Now!</span>
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
                            Updates
                        </a>
                    </div>
                </div>

                <div class="home-preview-card" aria-label="Placeholder image">
                    <span>Placeholder<br>Image</span>
                </div>
            </div>
        </section>

        <section class="home-nav-row grid gap-8 md:grid-cols-3">
            <a href="https://discord.gg/WFFCqzAb" class="home-nav-row__item" target="_blank" rel="noreferrer">
                <span class="section-kicker">Discord</span>
                <strong class="mt-3 block text-2xl text-[color:var(--text-strong)]">Join our Discord</strong>
                <span class="copy-muted mt-3 block text-sm leading-7">Talk with the community and keep up with the project.</span>
            </a>

            <a href="{{ route('news.index') }}" class="home-nav-row__item">
                <span class="section-kicker">News</span>
                <strong class="mt-3 block text-2xl text-[color:var(--text-strong)]">Read updates</strong>
                <span class="copy-muted mt-3 block text-sm leading-7">Follow announcements and release notes.</span>
            </a>

            <a href="{{ route('forum.index') }}" class="home-nav-row__item">
                <span class="section-kicker">Forum</span>
                <strong class="mt-3 block text-2xl text-[color:var(--text-strong)]">Join the discussion</strong>
                <span class="copy-muted mt-3 block text-sm leading-7">Ask questions, share feedback, and report issues.</span>
            </a>
        </section>

        <section class="home-announcement">
            <p class="section-kicker">Latest News</p>

            @if ($latestAnnouncement)
                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm copy-faint">
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
                        Open Post
                    </a>
                    <a href="{{ route('news.index') }}" class="home-news-card__button">
                        All News
                    </a>
                </div>
            @else
                <h2 class="title-section mt-4 max-w-3xl text-3xl">No update posted yet.</h2>
                <div class="mt-8">
                    <a href="{{ route('news.index') }}" class="home-news-card__button">
                        Open News
                    </a>
                </div>
            @endif
        </section>
    </div>
</x-public-layout>
