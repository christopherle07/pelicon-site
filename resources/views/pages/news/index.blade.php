<x-public-layout title="News - {{ config('app.name', 'Pelicon') }}">
    <section class="surface-panel p-8 sm:p-10">
        <p class="section-kicker">News</p>
        <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Product updates and release notes</h1>
        <p class="copy-muted mt-4 max-w-3xl text-base leading-7">
            News is where Pelicon posts releases, update notes, and the bigger changes worth calling out.
        </p>
    </section>

    <section class="mt-8 grid gap-5 lg:grid-cols-2">
        @forelse ($announcements as $announcement)
            <article class="surface-panel overflow-hidden">
                @if ($announcement->cover_image_url)
                    <img src="{{ $announcement->cover_image_url }}" alt="" class="h-56 w-full object-cover">
                @else
                    <div class="flex h-56 items-end p-6" style="background: var(--bg-elevated);">
                        <span class="font-display text-2xl font-bold tracking-[-0.04em] text-[color:var(--text-strong)]">{{ $announcement->title }}</span>
                    </div>
                @endif

                <div class="p-6 sm:p-8">
                    <div class="copy-faint flex flex-wrap items-center gap-3 text-sm">
                        <span>{{ $announcement->published_at?->format('M j, Y') }}</span>
                        <span>&middot;</span>
                        <span>{{ $announcement->comments_count }} comments</span>
                        <span>&middot;</span>
                        <span>{{ $announcement->reactions_count }} reactions</span>
                    </div>

                    <h2 class="mt-4 text-2xl font-bold tracking-[-0.04em] text-[color:var(--text-strong)]">{{ $announcement->title }}</h2>
                    <p class="copy-base mt-4 text-sm leading-7">{{ $announcement->excerpt }}</p>

                    <a href="{{ route('news.show', $announcement) }}" class="button-secondary mt-6 inline-flex px-5 py-3 text-sm font-semibold transition">
                        Read update
                    </a>
                </div>
            </article>
        @empty
            <div class="surface-panel p-8 text-sm leading-7 copy-muted lg:col-span-2">
                No announcements have been published yet.
            </div>
        @endforelse
    </section>

    <div class="mt-8">
        {{ $announcements->links() }}
    </div>
</x-public-layout>
