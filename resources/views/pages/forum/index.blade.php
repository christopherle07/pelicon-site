<x-public-layout title="Forum - {{ config('app.name', 'Pelicon') }}">
    <section>
        <p class="section-kicker">Forum</p>
        <h1 class="title-section mt-3 text-4xl sm:text-5xl">Community Forum</h1>
    </section>

    <section class="mt-4">
        <form method="GET" action="{{ route('forum.index') }}" class="w-full max-w-lg">
            <div class="flex items-center gap-3">
                <x-input
                    id="forum_search"
                    name="q"
                    type="search"
                    class="block w-full"
                    :value="$search"
                    placeholder="Search all threads and replies"
                />
                <button type="submit" class="button-primary inline-flex shrink-0 items-center justify-center px-5 py-3 text-sm font-semibold transition">
                    Search
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

            @forelse ($searchResults as $thread)
                <article class="surface-panel p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('users.show', ['user' => $thread->author->name]) }}" class="copy-faint text-sm font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                            {{ $thread->author->name }}
                        </a>
                        <x-staff-badge :user="$thread->author" size="sm" tone="forum" />
                        <span class="section-kicker">{{ $thread->category->name }}</span>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('forum.threads.show', [$thread->category, $thread]) }}" class="font-semibold text-[color:var(--text-strong)] hover:text-[color:var(--accent-strong)]">
                            {{ $thread->title }}
                        </a>
                    </div>

                    <p class="copy-base mt-3 line-clamp-3 text-sm leading-7">{{ $thread->body }}</p>

                    <div class="copy-faint mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                        <span>Posted {{ $thread->created_at->format('M j, Y') }}</span>
                        <span>Like {{ $thread->likes_count ?? 0 }}</span>
                        <span>Dislike {{ $thread->dislikes_count ?? 0 }}</span>
                        <span>Views {{ $thread->view_count ?? 0 }}</span>
                        <span>{{ $thread->replies_count }} replies</span>
                    </div>
                </article>
            @empty
                <div class="surface-panel p-8 text-sm leading-7 copy-muted">
                    No threads matched "{{ $search }}".
                </div>
            @endforelse
        </section>
    @endif

    @if ($search === '')
        <section class="mt-6">
            <div class="mb-4">
                <p class="section-kicker">Categories</p>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <article class="surface-panel p-6">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3" style="background-color: {{ $category->accent_color }}"></span>
                            <p class="copy-faint text-sm font-bold uppercase tracking-[0.18em]">{{ $category->name }}</p>
                        </div>

                        <p class="copy-base mt-4 text-base leading-7">{{ $category->description }}</p>

                        <div class="mt-8 flex items-center justify-between">
                            <span class="copy-faint text-sm">{{ $category->threads_count }} threads</span>
                            <a href="{{ route('forum.show', $category) }}" class="text-sm font-semibold text-[color:var(--accent-strong)]">Open</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</x-public-layout>
