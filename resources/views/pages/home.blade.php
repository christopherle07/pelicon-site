<x-public-layout title="{{ config('app.name', 'Pelicon') }}">
    <div class="mx-auto max-w-6xl space-y-20 sm:space-y-24">
        <section class="grid gap-10 lg:grid-cols-[1.35fr_0.8fr] lg:items-start lg:gap-14">
            <div class="py-2 sm:py-6">
                <p class="section-kicker">Pelicon</p>
                <h1 class="title-hero mt-6 max-w-4xl text-4xl sm:text-6xl">
                    Download Pelicon
                </h1>
                <p class="copy-base mt-6 max-w-2xl text-lg leading-8 sm:text-xl">
                    Get the latest build, catch up on updates, and jump into the forum without digging through separate product pages.
                </p>

                <div class="mt-14 flex flex-wrap items-center gap-5 sm:mt-16">
                    <div x-data="{ openDownloads: false }" class="relative">
                        <button type="button" @click="openDownloads = ! openDownloads" class="button-primary inline-flex items-center gap-3 px-8 py-4 text-base font-semibold transition">
                            <span>Download</span>
                            <span class="text-xs">+</span>
                        </button>

                        <div x-show="openDownloads" x-cloak @click.outside="openDownloads = false" class="absolute left-0 top-[calc(100%+0.9rem)] z-20 flex min-w-64 flex-col gap-2 surface-panel p-3">
                            <a href="{{ route('download.show', 'macos') }}" class="button-secondary px-4 py-3 text-left text-sm font-semibold transition">
                                MacOS
                            </a>
                            <a href="{{ route('download.show', 'windows') }}" class="button-secondary px-4 py-3 text-left text-sm font-semibold transition">
                                Windows
                            </a>
                            <a href="{{ route('download.show', 'linux') }}" class="button-secondary px-4 py-3 text-left text-sm font-semibold transition">
                                Linux
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('news.index') }}" class="button-secondary px-7 py-4 text-base font-semibold transition">
                        Read News
                    </a>
                </div>
            </div>

            <div class="surface-panel-alt p-8 sm:p-10 lg:mt-8">
                <p class="section-kicker">What is Pelicon?</p>
                <div class="mt-6 space-y-6">
                    <div>
                        <p class="text-sm font-semibold text-[color:var(--text-strong)]">Get the current build</p>
                        <p class="copy-muted mt-2 text-sm leading-6">Download the latest version of Pelicon without hunting through update posts and support pages.</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-[color:var(--text-strong)]">Catch up on changes</p>
                        <p class="copy-muted mt-2 text-sm leading-6">Read release notes, announcement posts, and future plans from the team.</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-[color:var(--text-strong)]">Talk to the team</p>
                        <p class="copy-muted mt-2 text-sm leading-6">Use the forum for bug reports, suggestions, setup questions, and general feedback.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-8">
            <div class="max-w-3xl">
                <p class="section-kicker">Preview</p>
                <h2 class="title-section mt-3 text-3xl sm:text-4xl">A longer look at the app.</h2>
                <p class="copy-muted mt-4 text-base leading-8">
                    This section is the placeholder space for screenshots, feature callouts, and quick visual walkthroughs. We can replace these blocks with real images later without changing the structure.
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-start">
                <article class="surface-panel p-8 sm:p-10">
                    <div class="flex min-h-[26rem] flex-col justify-between bg-[color:var(--bg-elevated)] p-6 sm:p-8">
                        <div class="flex items-center justify-between text-xs uppercase tracking-[0.16em] copy-faint">
                            <span>Main Preview</span>
                            <span>Placeholder</span>
                        </div>

                        <div class="space-y-4">
                            <div class="h-4 w-28 bg-[color:var(--bg-shell)]"></div>
                            <div class="h-24 bg-[color:var(--bg-shell)]"></div>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div class="h-24 bg-[color:var(--bg-shell)]"></div>
                                <div class="h-24 bg-[color:var(--bg-shell)]"></div>
                                <div class="h-24 bg-[color:var(--bg-shell)]"></div>
                            </div>
                        </div>
                    </div>

                    <h3 class="mt-8 text-2xl font-bold text-[color:var(--text-strong)]">Desktop view placeholder</h3>
                    <p class="copy-muted mt-3 text-sm leading-7">
                        Use this area for the main app screenshot, a hero preview of the interface, or a before-and-after comparison once we have real media.
                    </p>
                </article>

                <div class="grid gap-8">
                    <article class="surface-panel p-8 sm:p-10">
                        <div class="flex min-h-[12rem] items-end bg-[color:var(--bg-elevated)] p-6">
                            <span class="section-kicker">Secondary Preview</span>
                        </div>
                        <h3 class="mt-8 text-xl font-bold text-[color:var(--text-strong)]">Feature highlight placeholder</h3>
                        <p class="copy-muted mt-3 text-sm leading-7">
                            Good spot for a smaller screenshot that shows one key part of the experience.
                        </p>
                    </article>

                    <article class="surface-panel p-8 sm:p-10">
                        <div class="flex min-h-[12rem] items-end bg-[color:var(--bg-elevated)] p-6">
                            <span class="section-kicker">Detail Preview</span>
                        </div>
                        <h3 class="mt-8 text-xl font-bold text-[color:var(--text-strong)]">Workflow placeholder</h3>
                        <p class="copy-muted mt-3 text-sm leading-7">
                            This can later show another screen, a settings panel, or a focused crop of the app.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section class="space-y-8">
            <div class="max-w-2xl">
                <p class="section-kicker">Explore</p>
                <h2 class="title-section mt-3 text-3xl sm:text-4xl">Three clear paths, without overpacking the homepage.</h2>
                <p class="copy-muted mt-4 text-base leading-8">
                    The landing page should point people in the right direction quickly, then get out of the way. These links stay useful without turning the whole page into a dense control panel.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-3">
                <article class="surface-panel p-8 sm:p-10">
                    <p class="section-kicker">Download</p>
                    <h2 class="title-section mt-3 text-3xl">Get the app.</h2>
                    <p class="copy-muted mt-4 text-sm leading-7">
                        Platform-specific download pages keep the plans and future payment flow organized.
                    </p>
                    <a href="{{ route('download.index') }}" class="mt-8 inline-flex text-sm font-semibold text-[color:var(--accent-strong)]">Open Download Pages</a>
                </article>

                <article class="surface-panel p-8 sm:p-10">
                    <p class="section-kicker">News</p>
                    <h2 class="title-section mt-3 text-3xl">Read updates.</h2>
                    <p class="copy-muted mt-4 text-sm leading-7">
                        Release notes and announcement posts live in News so they are easy to browse later.
                    </p>
                    <a href="{{ route('news.index') }}" class="mt-8 inline-flex text-sm font-semibold text-[color:var(--accent-strong)]">Go to News</a>
                </article>

                <article class="surface-panel p-8 sm:p-10">
                    <p class="section-kicker">Forum</p>
                    <h2 class="title-section mt-3 text-3xl">Ask questions.</h2>
                    <p class="copy-muted mt-4 text-sm leading-7">
                        The forum is where users can report bugs, ask for help, and share suggestions.
                    </p>
                    <a href="{{ route('forum.index') }}" class="mt-8 inline-flex text-sm font-semibold text-[color:var(--accent-strong)]">Go to Forum</a>
                </article>
            </div>
        </section>

        <section>
            <article class="surface-panel p-8 sm:p-10">
                <p class="section-kicker">Latest News</p>
                @if ($latestAnnouncement)
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm copy-faint">
                        <span>{{ $latestAnnouncement->published_at?->format('M j, Y') }}</span>
                        <span>&middot;</span>
                        <span>{{ $latestAnnouncement->comments_count }} comments</span>
                        <span>&middot;</span>
                        <span>{{ $latestAnnouncement->reactions_count }} reactions</span>
                    </div>

                    <h2 class="title-section mt-4 max-w-3xl text-3xl sm:text-4xl">{{ $latestAnnouncement->title }}</h2>
                    <p class="copy-base mt-4 max-w-3xl text-base leading-8">
                        {{ $latestAnnouncement->excerpt }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('news.show', $latestAnnouncement) }}" class="button-secondary inline-flex px-6 py-3 text-sm font-semibold transition">
                            Read latest announcement
                        </a>
                        <a href="{{ route('news.index') }}" class="button-secondary inline-flex px-6 py-3 text-sm font-semibold transition">
                            Browse all News
                        </a>
                    </div>
                @else
                    <h2 class="title-section mt-4 max-w-3xl text-3xl sm:text-4xl">The latest announcement will show here.</h2>
                    <p class="copy-muted mt-4 max-w-3xl text-base leading-8">
                        Once the first News post is published, the landing page will show a quick snippet here so visitors can jump straight into the newest update.
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('news.index') }}" class="button-secondary inline-flex px-6 py-3 text-sm font-semibold transition">
                            Open News
                        </a>
                    </div>
                @endif
            </article>
        </section>
    </div>
</x-public-layout>
