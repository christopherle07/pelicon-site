<x-public-layout title="{{ $announcement->title }} - {{ config('app.name', 'Pelicon') }}">
    @php
        $commentErrors = $errors->getBag('announcementComment');
        $commentIcon = asset('icons/comment.svg');
        $reactionIcon = asset('icons/like-dislike.svg');
        $reactionIconFilled = asset('icons/like-dislike-filled.svg').'?v=2';
    @endphp

    @if (session('status'))
        <section class="flash-toast surface-panel mx-auto mt-8 max-w-4xl p-6 text-sm font-medium" data-auto-dismiss="5000" style="color: var(--success);">
            {{ session('status') }}
        </section>
    @endif

    <article class="news-article">
        @if ($announcement->cover_image_url)
            <img src="{{ $announcement->cover_image_url }}" alt="" class="news-article__image">
        @endif

        <div class="news-article__body">
            <a href="{{ route('news.index') }}" class="news-article__back">Back to news</a>
            <p class="section-kicker mt-6">{{ $announcement->published_at?->format('F j, Y') }}</p>
            <h1 class="title-hero mt-3 text-4xl sm:text-5xl">{{ $announcement->title }}</h1>

            <div class="copy-faint mt-6 flex flex-wrap items-center gap-3 text-sm">
                <span>
                    By
                    <a href="{{ route('users.show', ['user' => $announcement->author->name]) }}" class="font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                        {{ $announcement->author->name }}
                    </a>
                </span>
                <x-staff-badge :user="$announcement->author" />
            </div>

<div class="rich-copy copy-base mt-8 text-base leading-8">
                {!! $announcement->body ?: nl2br(e($announcement->excerpt ?? '')) !!}
            </div>

            <div class="mt-10 flex flex-wrap items-center gap-3 text-sm" style="color: var(--text-muted);">
                @auth
                    <div class="forum-action-group" data-reaction-group data-current-reaction="{{ $userReaction ?? '' }}">
                        <form method="POST" action="{{ route('news.react', $announcement) }}" data-reaction-form>
                            @csrf
                            <input type="hidden" name="type" value="like">
                            <button type="submit" data-reaction-button="like" aria-pressed="{{ $userReaction === 'like' ? 'true' : 'false' }}" class="forum-action-button {{ $userReaction === 'like' ? 'forum-action-button--active' : '' }}">
                                <img src="{{ $userReaction === 'like' ? $reactionIconFilled : $reactionIcon }}" alt="" class="forum-action-button__icon" aria-hidden="true" data-reaction-icon data-inactive-src="{{ $reactionIcon }}" data-active-src="{{ $reactionIconFilled }}">
                                <span class="sr-only">Like</span>
                                <span class="forum-action-button__count" data-reaction-count="like">{{ $reactionCounts->get('like', 0) }}</span>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('news.react', $announcement) }}" data-reaction-form>
                            @csrf
                            <input type="hidden" name="type" value="dislike">
                            <button type="submit" data-reaction-button="dislike" aria-pressed="{{ $userReaction === 'dislike' ? 'true' : 'false' }}" class="forum-action-button {{ $userReaction === 'dislike' ? 'forum-action-button--active' : '' }}">
                                <img src="{{ $userReaction === 'dislike' ? $reactionIconFilled : $reactionIcon }}" alt="" class="forum-action-button__icon forum-action-button__icon--flipped" aria-hidden="true" data-reaction-icon data-inactive-src="{{ $reactionIcon }}" data-active-src="{{ $reactionIconFilled }}">
                                <span class="sr-only">Dislike</span>
                                <span class="forum-action-button__count" data-reaction-count="dislike">{{ $reactionCounts->get('dislike', 0) }}</span>
                            </button>
                        </form>

                        <button type="button" @click="$dispatch('toggle-news-comment')" class="forum-action-button">
                            <img src="{{ $commentIcon }}" alt="" class="forum-action-button__icon" aria-hidden="true">
                            <span class="sr-only">Comment</span>
                            <span class="forum-action-button__count">{{ $announcement->comments->count() }}</span>
                        </button>
                    </div>
                @else
                    <span class="forum-action-button">
                        <img src="{{ $reactionIcon }}" alt="" class="forum-action-button__icon" aria-hidden="true">
                        <span class="forum-action-button__count">{{ $reactionCounts->get('like', 0) }}</span>
                    </span>
                    <span class="forum-action-button">
                        <img src="{{ $reactionIcon }}" alt="" class="forum-action-button__icon forum-action-button__icon--flipped" aria-hidden="true">
                        <span class="forum-action-button__count">{{ $reactionCounts->get('dislike', 0) }}</span>
                    </span>
                    <span class="forum-action-button">
                        <img src="{{ $commentIcon }}" alt="" class="forum-action-button__icon" aria-hidden="true">
                        <span class="forum-action-button__count">{{ $announcement->comments->count() }}</span>
                    </span>
                @endauth
            </div>
        </div>
    </article>

    <section class="news-comments" x-data="{ openComposer: @js($commentErrors->any()) }" @toggle-news-comment.window="openComposer = !openComposer">
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

                <div class="news-comment-composer">
                    <button type="button" @click="openComposer = !openComposer" class="button-secondary inline-flex px-5 py-3 text-sm font-semibold transition">
                        Comment
                    </button>

                    <form x-show="openComposer" x-cloak method="POST" action="{{ route('news.comments.store', $announcement) }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <textarea
                                id="announcement_comment_body"
                                name="body"
                                rows="5"
                                class="site-textarea block w-full px-4 py-3 focus:outline-none focus:ring-0"
                                placeholder="Write a comment"
                            >{{ old('body') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="button-primary inline-flex px-5 py-3 text-sm font-semibold transition">
                                Post comment
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="news-comments__signin">
                    <a href="{{ route('login') }}" class="font-semibold text-[color:var(--accent-strong)]">Sign in</a> to comment on this announcement.
                </div>
            @endauth
        </div>

        <div class="mt-8 space-y-4">
            @forelse ($announcement->comments as $comment)
                @php($canManageComment = auth()->check() && auth()->user()->canManageAnnouncementComment($comment))

                <article x-data="{ openCommentMenu: false }" class="news-comment">
                    <div class="flex items-start justify-between gap-4">
                        <div class="news-comment__meta">
                            <a href="{{ route('users.show', ['user' => $comment->user->name]) }}" class="font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                                {{ $comment->user->name }}
                            </a>
                            <x-staff-badge :user="$comment->user" />
                            <span class="copy-faint">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>

                        @if ($canManageComment)
                            <div class="relative shrink-0">
                                <button type="button" @click="openCommentMenu = ! openCommentMenu" class="news-comment__menu-toggle" aria-label="Comment actions">
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

                    <p class="news-comment__body">{{ $comment->body }}</p>
                </article>
            @empty
                <div class="news-comments__empty">
                    No comments yet.
                </div>
            @endforelse
        </div>
    </section>
</x-public-layout>
