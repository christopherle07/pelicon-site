<x-public-layout title="News - {{ config('app.name', 'Pelicon') }}">
    @php
        $isFirstPage = ! request()->filled('page') || (int) request()->query('page', 1) === 1;
        $featuredAnnouncement = $isFirstPage ? $announcements->first() : null;
        $archiveAnnouncements = $featuredAnnouncement ? $announcements->skip(1) : $announcements;
    @endphp

    <div class="news-page">
        <section class="news-hero">
            <p class="section-kicker">News</p>
            <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Product updates and release notes</h1>
            <p class="copy-muted mt-4 max-w-3xl text-base leading-8">
                Releases, dev notes, and the bigger changes worth calling out without burying everything in a bland archive wall.
            </p>
        </section>

        @if ($featuredAnnouncement)
            <section class="news-feature">
                @if ($featuredAnnouncement->cover_image_url)
                    <div class="news-feature__media">
                        <img src="{{ $featuredAnnouncement->cover_image_url }}" alt="" class="h-full w-full object-cover">
                    </div>
                @else
                    <div class="news-feature__media news-feature__media--placeholder">
                        <span>{{ $featuredAnnouncement->title }}</span>
                    </div>
                @endif

                <div class="news-feature__copy">
                    <div class="news-card__meta">
                        <span>{{ $featuredAnnouncement->published_at?->format('M j, Y') }}</span>
                        <span>&middot;</span>
                        <span>{{ $featuredAnnouncement->comments_count }} comments</span>
                        <span>&middot;</span>
                        <span>{{ $featuredAnnouncement->reactions_count }} reactions</span>
                    </div>

                    <h2 class="news-feature__title">
                        <a href="{{ route('news.show', $featuredAnnouncement) }}">{{ $featuredAnnouncement->title }}</a>
                    </h2>

                    <p class="copy-base mt-4 text-base leading-8">
                        {{ $featuredAnnouncement->excerpt }}
                    </p>

                    <a href="{{ route('news.show', $featuredAnnouncement) }}" class="button-secondary mt-8 inline-flex px-5 py-3 text-sm font-semibold transition">
                        Read update
                    </a>
                </div>
            </section>
        @endif

        <section class="news-archive">
            <div class="news-archive__head">
                <div>
                    <p class="section-kicker">Archive</p>
                    <h2 class="title-section mt-2 text-2xl">More updates</h2>
                </div>
            </div>

            <div class="news-grid">
                @forelse ($archiveAnnouncements as $announcement)
                    <article class="news-card">
                        @if ($announcement->cover_image_url)
                            <img src="{{ $announcement->cover_image_url }}" alt="" class="news-card__image">
                        @else
                            <div class="news-card__image news-card__image--placeholder">
                                <span>{{ $announcement->title }}</span>
                            </div>
                        @endif

                        <div class="news-card__body">
                            <div class="news-card__meta">
                                <span>{{ $announcement->published_at?->format('M j, Y') }}</span>
                                <span>&middot;</span>
                                <span>{{ $announcement->comments_count }} comments</span>
                                <span>&middot;</span>
                                <span>{{ $announcement->reactions_count }} reactions</span>
                            </div>

                            <h3 class="news-card__title">
                                <a href="{{ route('news.show', $announcement) }}">{{ $announcement->title }}</a>
                            </h3>

                            <p class="copy-base mt-3 text-sm leading-7">{{ $announcement->excerpt }}</p>

                            <a href="{{ route('news.show', $announcement) }}" class="news-card__link">
                                Read update
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="news-empty">
                        No announcements have been published yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-8">
        {{ $announcements->links() }}
    </div>
</x-public-layout>
