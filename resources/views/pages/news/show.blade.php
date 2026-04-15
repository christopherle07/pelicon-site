<x-public-layout title="{{ $announcement->title }} - {{ config('app.name', 'Pelicon') }}">
    @php($commentErrors = $errors->getBag('announcementComment'))

    @if (session('status'))
        <section class="flash-toast surface-panel mx-auto mt-8 max-w-4xl p-6 text-sm font-medium" data-auto-dismiss="5000" style="color: var(--success);">
            {{ session('status') }}
        </section>
    @endif

    <article class="surface-panel mx-auto max-w-4xl overflow-hidden">
        @if ($announcement->cover_image_url)
            <img src="{{ $announcement->cover_image_url }}" alt="" class="h-72 w-full object-cover sm:h-96">
        @endif

        <div class="p-8 sm:p-10">
            <a href="{{ route('news.index') }}" class="text-sm font-semibold text-[color:var(--accent-strong)]">Back to news</a>
            <p class="section-kicker mt-6">{{ $announcement->published_at?->format('F j, Y') }}</p>
            <h1 class="title-hero mt-3 text-4xl sm:text-5xl">{{ $announcement->title }}</h1>

            <div class="copy-faint mt-6 flex items-center gap-3 text-sm">
                <span>
                    By
                    <a href="{{ route('users.show', ['user' => $announcement->author->name]) }}" class="font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                        {{ $announcement->author->name }}
                    </a>
                </span>
                <x-staff-badge :user="$announcement->author" />
            </div>

            @if ($announcement->embedFrameUrl())
                <div class="mt-8 overflow-hidden" style="background: var(--bg-elevated);">
                    <iframe
                        src="{{ $announcement->embedFrameUrl() }}"
                        title="Announcement embed"
                        class="aspect-video w-full"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
            @elseif ($announcement->embed_url)
                <div class="mt-8">
                    <a href="{{ $announcement->embed_url }}" target="_blank" rel="noopener noreferrer" class="button-secondary inline-flex px-5 py-3 text-sm font-semibold transition">
                        Open external media
                    </a>
                </div>
            @endif

            <div class="rich-copy copy-base mt-8 text-base leading-8">
                {!! $announcement->body ?: nl2br(e($announcement->excerpt ?? '')) !!}
            </div>

            <div class="mt-10 flex flex-wrap items-center gap-3 text-sm" style="color: var(--text-muted);">
                <span class="font-semibold text-[color:var(--text-strong)]">Reactions</span>

                @auth
                    <div class="reaction-group" data-reaction-group data-current-reaction="{{ $userReaction ?? '' }}">
                        <form method="POST" action="{{ route('news.react', $announcement) }}" data-reaction-form>
                            @csrf
                            <input type="hidden" name="type" value="like">
                            <button type="submit" data-reaction-button="like" aria-pressed="{{ $userReaction === 'like' ? 'true' : 'false' }}" class="reaction-button {{ $userReaction === 'like' ? 'reaction-button--active' : '' }} px-4 py-2 text-sm font-semibold transition">
                                Like <span data-reaction-count="like">{{ $reactionCounts->get('like', 0) }}</span>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('news.react', $announcement) }}" data-reaction-form>
                            @csrf
                            <input type="hidden" name="type" value="dislike">
                            <button type="submit" data-reaction-button="dislike" aria-pressed="{{ $userReaction === 'dislike' ? 'true' : 'false' }}" class="reaction-button {{ $userReaction === 'dislike' ? 'reaction-button--active' : '' }} px-4 py-2 text-sm font-semibold transition">
                                Dislike <span data-reaction-count="dislike">{{ $reactionCounts->get('dislike', 0) }}</span>
                            </button>
                        </form>
                    </div>
                @else
                    <span class="px-3 py-1" style="background: var(--bg-elevated);">Like {{ $reactionCounts->get('like', 0) }}</span>
                    <span class="px-3 py-1" style="background: var(--bg-elevated);">Dislike {{ $reactionCounts->get('dislike', 0) }}</span>
                @endauth
            </div>
        </div>
    </article>

    <section class="surface-panel mx-auto mt-8 max-w-4xl p-8 sm:p-10">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="section-kicker">Comments</p>
                <h2 class="title-section mt-2 text-3xl">{{ $announcement->comments->count() }} responses</h2>
            </div>
        </div>

        <div class="mt-8">
            @auth
                @if ($commentErrors->any())
                    <div class="mb-5">
                        <div class="font-medium" style="color: var(--danger);">Whoops! Something went wrong.</div>
                        <ul class="mt-3 list-disc list-inside text-sm" style="color: var(--danger);">
                            @foreach ($commentErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('news.comments.store', $announcement) }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-label for="announcement_comment_body" value="Comment" />
                        <textarea
                            id="announcement_comment_body"
                            name="body"
                            rows="5"
                            class="mt-2 block w-full px-4 py-3 focus:outline-none focus:ring-2"
                            style="border-color: var(--border-strong); background: var(--bg-elevated); color: var(--text-strong);"
                        >{{ old('body') }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="button-primary inline-flex px-5 py-3 text-sm font-semibold transition">
                            Post comment
                        </button>
                    </div>
                </form>
            @else
                <div class="p-6 text-sm leading-7 copy-muted" style="background: var(--bg-elevated);">
                    <a href="{{ route('login') }}" class="font-semibold text-[color:var(--accent-strong)]">Sign in</a> to comment on this announcement.
                </div>
            @endauth
        </div>

        <div class="mt-8 space-y-4">
            @forelse ($announcement->comments as $comment)
                @php($canManageComment = auth()->check() && auth()->user()->canManageAnnouncementComment($comment))

                <article x-data="{ openCommentMenu: false }" class="p-5" style="background: var(--bg-elevated);">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('users.show', ['user' => $comment->user->name]) }}" class="font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                                {{ $comment->user->name }}
                            </a>
                            <x-staff-badge :user="$comment->user" />
                            <span class="copy-faint text-sm">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>

                        @if ($canManageComment)
                            <div class="relative shrink-0">
                                <button type="button" @click="openCommentMenu = ! openCommentMenu" class="button-secondary inline-flex h-10 w-10 items-center justify-center text-lg font-semibold transition" aria-label="Comment actions">
                                    ...
                                </button>

                                <div x-show="openCommentMenu" x-cloak @click.outside="openCommentMenu = false" class="absolute right-0 top-12 z-10 flex w-44 flex-col gap-2 surface-panel p-3">
                                    <form method="POST" action="{{ route('news.comments.destroy', [$announcement, $comment]) }}" onsubmit="return confirm('Delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-4 py-2 text-left text-sm font-semibold transition" style="background: var(--bg-elevated); color: var(--danger);">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <p class="copy-base mt-3 whitespace-pre-line text-sm leading-7">{{ $comment->body }}</p>
                </article>
            @empty
                <div class="p-6 text-sm leading-7 copy-muted" style="background: var(--bg-elevated);">
                    No comments yet.
                </div>
            @endforelse
        </div>
    </section>
</x-public-layout>
