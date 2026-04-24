<x-public-layout title="Forum - {{ config('app.name', 'Pelicon') }}">
    @php($searchIcon = asset('build/assets/search.svg'))
    <div class="forum-shell">
        <section class="forum-hero">
            <p class="section-kicker">Forum</p>
            <h1 class="title-section mt-3 text-4xl sm:text-5xl">Community Forum</h1>
            <p class="copy-muted mt-4 max-w-2xl text-base leading-8">
                Browse current discussions, preview real thread states while styling locally, and keep the forum feeling alive during development.
            </p>
        </section>

        <section class="mt-4">
            <form method="GET" action="{{ route('forum.index') }}" class="w-full max-w-xl">
                <div class="forum-search-shell">
                    <x-input
                        id="forum_search"
                        name="q"
                        type="search"
                        class="forum-search-input block w-full pr-14"
                        :value="$search"
                        placeholder="Search all threads and replies"
                    />
                    <button type="submit" class="forum-search-button" aria-label="Search forum">
                        <img src="{{ $searchIcon }}" alt="" class="forum-search-button__icon" aria-hidden="true">
                    </button>
                </div>
            </form>
        </section>

        @if ($search !== '')
            <section class="mt-6 space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="section-kicker">Search Results</p>
                        <h2 class="title-section mt-2 text-2xl">{{ $searchResults->count() }} results for "{{ $search }}"</h2>
                    </div>

                    <a href="{{ route('forum.index') }}" class="button-secondary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                        Clear search
                    </a>
                </div>

                <div class="forum-thread-list">
                    @forelse ($searchResults as $thread)
                        <a href="{{ route('forum.threads.show', [$thread->category, $thread]) }}" class="forum-thread-card forum-card-link">
                            <div class="forum-thread-card__meta">
                                <span class="forum-thread-card__category-dot" style="background-color: {{ $thread->category->accent_color }}"></span>
                                <span class="section-kicker">{{ $thread->category->name }}</span>
                                <span class="copy-faint">&middot;</span>
                                <span class="copy-faint text-sm font-semibold text-[color:var(--text-strong)]">{{ $thread->author->name }}</span>
                                <x-staff-badge :user="$thread->author" size="sm" tone="forum" />
                            </div>

                            <div class="mt-3">
                                <span class="forum-thread-card__title">{{ $thread->title }}</span>
                            </div>

                            <p class="copy-base mt-3 line-clamp-3 text-sm leading-7">{{ $thread->body }}</p>

                            <div class="forum-thread-card__stats">
                                <span>Posted {{ $thread->created_at->format('M j, Y') }}</span>
                                <span>Views {{ $thread->view_count ?? 0 }}</span>
                                <span>{{ $thread->replies_count }} replies</span>
                            </div>
                        </a>
                    @empty
                        <div class="forum-empty">
                            No threads matched "{{ $search }}".
                        </div>
                    @endforelse
                </div>
            </section>
        @endif

        @if ($search === '')
            <section class="forum-overview">
                <div class="forum-section-head">
                    <div>
                        <p class="section-kicker">Categories</p>
                        <h2 class="title-section mt-2 text-2xl">Browse by topic</h2>
                    </div>
                </div>

                <div class="forum-category-list">
                    @foreach ($categories as $category)
                        <a href="{{ route('forum.show', $category) }}" class="forum-category-card forum-card-link">
                            <div class="flex items-center gap-3">
                                <span class="forum-thread-card__category-dot" style="background-color: {{ $category->accent_color }}"></span>
                                <p class="copy-faint text-sm font-bold uppercase tracking-[0.18em]">{{ $category->name }}</p>
                            </div>

                            <p class="copy-base mt-4 text-base leading-8">{{ $category->description }}</p>

                            <div class="mt-6 flex items-center justify-between">
                                <span class="copy-faint text-sm">{{ $category->threads_count }} threads</span>
                                <span class="text-sm font-semibold text-[color:var(--accent-strong)]">Open</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="forum-recent-section">
                <div class="forum-section-head">
                    <div>
                        <p class="section-kicker">Recent Threads</p>
                        <h2 class="title-section mt-2 text-2xl">Latest discussions</h2>
                    </div>
                </div>

                <div class="forum-thread-list">
                    @forelse ($recentThreads as $thread)
                        <a href="{{ route('forum.threads.show', [$thread->category, $thread]) }}" class="forum-thread-card forum-card-link">
                            <div class="forum-thread-card__meta">
                                <span class="forum-thread-card__category-dot" style="background-color: {{ $thread->category->accent_color }}"></span>
                                <span class="section-kicker">{{ $thread->category->name }}</span>
                                <span class="copy-faint">&middot;</span>
                                <span class="copy-faint text-sm font-semibold text-[color:var(--text-strong)]">{{ $thread->author->name }}</span>
                                <x-staff-badge :user="$thread->author" size="sm" tone="forum" />
                            </div>

                            <div class="mt-3">
                                <span class="forum-thread-card__title">{{ $thread->title }}</span>
                            </div>

                            <p class="copy-base mt-3 line-clamp-3 text-base leading-8">{{ $thread->body }}</p>

                            <div class="forum-thread-card__stats">
                                <span>Posted {{ $thread->created_at->format('M j, Y') }}</span>
                                <span>Views {{ $thread->view_count ?? 0 }}</span>
                                <span>{{ $thread->replies_count }} replies</span>
                            </div>
                        </a>
                    @empty
                        <div class="forum-empty">
                            No threads yet.
                        </div>
                    @endforelse
                </div>
            </section>
        @endif
    </div>
</x-public-layout>
