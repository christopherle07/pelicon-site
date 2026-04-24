<x-public-layout title="{{ $category->name }} - Forum">
    @php($threadFormErrors = $errors->getBag('createThread'))
    @php($searchIcon = asset('icons/search.svg'))

    @if (session('status'))
        <section class="flash-toast surface-panel mb-8 p-6 text-sm font-medium" data-auto-dismiss="5000" style="color: var(--success);">
            {{ session('status') }}
        </section>
    @endif

    <div x-data="{ openThreadComposer: @js($threadFormErrors->any()) }">
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
            <a href="{{ route('forum.index') }}" class="font-semibold text-[color:var(--accent-strong)]">Forum</a>
            <span class="copy-faint">/</span>
            <span class="copy-faint">{{ $category->name }}</span>
        </div>

        <section class="forum-category-hero">
            <div class="flex items-center gap-3">
                <span class="forum-thread-card__category-dot" style="background-color: {{ $category->accent_color }}"></span>
                <p class="section-kicker">{{ $category->name }}</p>
            </div>
            <h1 class="title-section mt-4 text-4xl sm:text-5xl">{{ $category->name }}</h1>
            <p class="copy-muted mt-4 max-w-2xl text-base leading-8">{{ $category->description }}</p>
            <div class="copy-faint mt-6 flex flex-wrap items-center gap-4 text-sm">
                <span>{{ $category->threads_count }} threads</span>
                <span>Category feed</span>
            </div>
        </section>

        <section class="forum-category-toolbar">
            <form method="GET" action="{{ route('forum.show', $category) }}" class="w-full sm:max-w-xl">
                <div class="forum-search-shell">
                <x-input
                    id="forum_search"
                    name="q"
                    type="search"
                    class="forum-search-input block w-full pr-14"
                    :value="$search"
                    placeholder="Search this category"
                />
                    <button type="submit" class="forum-search-button" aria-label="Search category">
                        <img src="{{ $searchIcon }}" alt="" class="forum-search-button__icon" aria-hidden="true">
                    </button>
                </div>
            </form>

            <div class="shrink-0">
                @auth
                    <button type="button" @click="openThreadComposer = !openThreadComposer" class="button-primary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                        Create +
                    </button>
                @else
                    <a href="{{ route('login') }}" class="button-secondary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                        Create +
                    </a>
                @endauth
            </div>
        </section>

        @auth
            <section x-show="openThreadComposer" x-cloak class="surface-panel mt-8 p-8 sm:p-10">
                <p class="section-kicker">New Thread</p>
                <h2 class="title-section mt-3 text-3xl">Start a discussion in {{ $category->name }}</h2>

                @if ($threadFormErrors->any())
                    <div class="mt-6">
                        <div class="font-medium" style="color: var(--danger);">Whoops! Something went wrong.</div>
                        <ul class="mt-3 list-disc list-inside text-sm" style="color: var(--danger);">
                            @foreach ($threadFormErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('forum.threads.store', $category) }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <x-label for="thread_title" value="Title" />
                        <x-input id="thread_title" class="mt-1 block w-full" type="text" name="title" :value="old('title')" maxlength="140" required />
                    </div>

                    <div>
                        <x-label for="thread_body" value="Message" />
                        <textarea id="thread_body" name="body" rows="8" class="site-textarea mt-1 block w-full px-4 py-3 focus:outline-none focus:ring-0">{{ old('body') }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="button-primary inline-flex px-5 py-3 text-sm font-semibold transition">
                            Post thread
                        </button>
                    </div>
                </form>
            </section>
        @endauth
    </div>

    <section class="mt-8 space-y-4">
        @if ($search !== '')
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="section-kicker">Search Results</p>
                    <h2 class="title-section mt-2 text-2xl">{{ $searchResults->count() }} results in {{ $category->name }}</h2>
                </div>

                <a href="{{ route('forum.show', $category) }}" class="button-secondary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                    Clear search
                </a>
            </div>
        @endif

        <div class="forum-thread-list">
            @forelse (($search !== '' ? $searchResults : $threads) as $thread)
                <a href="{{ route('forum.threads.show', [$category, $thread]) }}" class="forum-thread-card forum-card-link">
                    <div class="forum-thread-card__meta">
                        <span class="copy-faint text-sm font-semibold text-[color:var(--text-strong)]">{{ $thread->author->name }}</span>
                        <x-staff-badge :user="$thread->author" size="sm" tone="forum" />
                        @if ($thread->is_pinned)
                            <span class="section-kicker">Pinned</span>
                        @endif
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
                    {{ $search !== '' ? 'No threads matched your search in this category.' : 'No threads here yet. Be the first to start one.' }}
                </div>
            @endforelse
        </div>

        @if ($search === '' && $threads->hasPages())
            <div class="pt-2">
                {{ $threads->links() }}
            </div>
        @endif
    </section>
</x-public-layout>
