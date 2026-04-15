<x-public-layout title="{{ $category->name }} - Forum">
    @php($threadFormErrors = $errors->getBag('createThread'))

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

        <section class="surface-panel p-8 sm:p-10">
            <h1 class="title-section text-4xl sm:text-5xl">{{ $category->name }}</h1>
        </section>

        <section class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('forum.show', $category) }}" class="flex w-full items-center gap-3 sm:max-w-md">
                <x-input
                    id="forum_search"
                    name="q"
                    type="search"
                    class="block w-full"
                    :value="$search"
                    placeholder="Search this category"
                />
                <button type="submit" class="button-secondary inline-flex shrink-0 items-center justify-center px-4 py-3 text-sm font-semibold transition">
                    Search
                </button>
            </form>

            <div class="shrink-0">
                @auth
                    <button type="button" @click="openThreadComposer = !openThreadComposer" class="button-primary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                        Post
                    </button>
                @else
                    <a href="{{ route('login') }}" class="button-secondary inline-flex items-center justify-center px-5 py-3 text-sm font-semibold transition">
                        Post
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
                        <textarea id="thread_body" name="body" rows="8" class="mt-1 block w-full px-4 py-3 focus:outline-none focus:ring-2"
                            style="border-color: var(--border-strong); background: var(--bg-elevated); color: var(--text-strong);">{{ old('body') }}</textarea>
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

        @forelse (($search !== '' ? $searchResults : $threads) as $thread)
            <article class="surface-panel p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('users.show', ['user' => $thread->author->name]) }}" class="copy-faint text-sm font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                        {{ $thread->author->name }}
                    </a>
                    <x-staff-badge :user="$thread->author" size="sm" tone="forum" />
                </div>
                <div class="mt-3">
                    <a href="{{ route('forum.threads.show', [$category, $thread]) }}" class="font-semibold text-[color:var(--text-strong)] hover:text-[color:var(--accent-strong)]">
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
                {{ $search !== '' ? 'No threads matched your search in this category.' : 'No threads here yet. Be the first to start one.' }}
            </div>
        @endforelse

        @if ($search === '' && $threads->hasPages())
            <div class="pt-2">
                {{ $threads->links() }}
            </div>
        @endif
    </section>
</x-public-layout>
