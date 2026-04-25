<x-public-layout title="News - {{ config('app.name', 'Pelicon') }}">
    <div class="news-page">
        <section class="news-hero">
            <p class="section-kicker">News</p>
            <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Product updates and release notes</h1>
        </section>

        <section class="news-archive">
            <div class="news-grid">
                @forelse ($announcements as $announcement)
                    <a href="{{ route('news.show', $announcement) }}" class="news-card">
                        @if ($announcement->cover_image_url)
                            <img src="{{ $announcement->cover_image_url }}" alt="" class="news-card__image">
                        @else
                            <div class="news-card__image news-card__image--placeholder"></div>
                        @endif

                        <div class="news-card__body">
                            <div class="news-card__meta">
                                <span>{{ $announcement->published_at?->format('M j, Y') }}</span>
                                <span>&middot;</span>
                                <span>{{ $announcement->comments_count }} comments</span>
                                <span>&middot;</span>
                                <span>{{ $announcement->reactions_count }} reactions</span>
                            </div>

                            <h3 class="news-card__title">{{ $announcement->title }}</h3>

                            <p class="copy-base mt-3 text-sm leading-7">{{ $announcement->excerpt }}</p>
                        </div>
                    </a>
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
