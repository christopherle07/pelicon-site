<x-public-layout title="{{ $profileUser->name }} - Profile">
    <div class="mx-auto max-w-5xl space-y-8">
        <section class="surface-panel p-8 sm:p-10">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-4">
                    <p class="section-kicker">Profile</p>
                    <div>
                        <h1 class="title-section text-3xl sm:text-4xl">{{ $profileUser->name }}</h1>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm copy-faint">
                            <span>Joined {{ $profileUser->created_at->format('M Y') }}</span>
                            <x-staff-badge :user="$profileUser" />
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('users.show', ['user' => $profileUser->name]) }}" class="w-full sm:max-w-xs">
                    <label for="profile_sort" class="section-kicker">Sort Posts</label>
                    <div class="select-shell mt-3">
                        <select id="profile_sort" name="sort" class="select-input" onchange="this.form.submit()">
                            @foreach ($sortOptions as $key => $option)
                                <option value="{{ $key }}" @selected($sort === $key)>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </section>

        <section class="space-y-4">
            <div>
                <p class="section-kicker">Forum Posts</p>
                <h2 class="title-section mt-2 text-3xl">Thread snippets</h2>
            </div>

            @forelse ($threads as $thread)
                <article class="surface-panel p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <a href="{{ route('forum.show', $thread->category) }}" class="section-kicker transition hover:text-[color:var(--text-strong)]">
                            {{ $thread->category->name }}
                        </a>
                        <span class="copy-faint">{{ $thread->created_at->format('M j, Y') }}</span>
                    </div>

                    <h3 class="mt-4 text-2xl font-bold text-[color:var(--text-strong)]">
                        <a href="{{ route('forum.threads.show', [$thread->category, $thread]) }}" class="transition hover:text-[color:var(--accent-strong)]">
                            {{ $thread->title }}
                        </a>
                    </h3>

                    <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm copy-faint">
                        <span>Like {{ $thread->likes_count }}</span>
                        <span>Dislike {{ $thread->dislikes_count }}</span>
                        <span>Views {{ $thread->view_count }}</span>
                        <span>{{ $thread->replies_count }} replies</span>
                    </div>
                </article>
            @empty
                <div class="surface-panel p-8 text-sm leading-7 copy-muted">
                    No forum threads posted yet.
                </div>
            @endforelse

            @if ($threads->hasPages())
                <div class="pt-2">
                    {{ $threads->links() }}
                </div>
            @endif
        </section>
    </div>
</x-public-layout>
