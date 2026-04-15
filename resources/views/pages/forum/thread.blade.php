<x-public-layout title="{{ $thread->title }} - Forum">
    @php
        $replyErrors = $errors->getBag('replyThread');
        $threadUserReaction = auth()->check() ? optional($thread->reactions->firstWhere('user_id', auth()->id()))->type : null;
        $threadLikes = $thread->reactions->where('type', 'like')->count();
        $threadDislikes = $thread->reactions->where('type', 'dislike')->count();
        $canManageThread = auth()->check() && auth()->user()->canManageForumThread($thread);
        $canReplyToThread = auth()->check() && auth()->user()->canReplyToForumThread($thread);
    @endphp

    @if (session('status'))
        <section class="flash-toast surface-panel mt-8 p-6 text-sm font-medium" data-auto-dismiss="5000" style="color: var(--success);">
            {{ session('status') }}
        </section>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
        <a href="{{ route('forum.index') }}" class="font-semibold text-[color:var(--accent-strong)]">Forum</a>
        <span class="copy-faint">/</span>
        <a href="{{ route('forum.show', $category) }}" class="font-semibold text-[color:var(--accent-strong)]">{{ $category->name }}</a>
        <span class="copy-faint">/</span>
        <span class="copy-faint">{{ $thread->title }}</span>
    </div>

    <section x-data="{ openMainReply: @js($replyErrors->any() && ! old('parent_id')), openThreadMenu: false }" class="surface-panel p-8 sm:p-10">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="h-3 w-3" style="background-color: {{ $category->accent_color }}"></span>
                <p class="section-kicker">{{ $category->name }}</p>
            </div>

            @if ($canManageThread)
                <div class="relative">
                    <button type="button" @click="openThreadMenu = ! openThreadMenu" class="button-secondary inline-flex h-10 w-10 items-center justify-center text-lg font-semibold transition" aria-label="Thread actions">
                        ...
                    </button>

                    <div x-show="openThreadMenu" x-cloak @click.outside="openThreadMenu = false" class="absolute right-0 top-12 z-10 flex w-44 flex-col gap-2 surface-panel p-3">
                        <form method="POST" action="{{ route('forum.threads.lock', [$category, $thread]) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 text-left text-sm font-semibold transition" style="background: var(--bg-elevated); color: var(--text-strong);">
                                {{ $thread->is_locked ? 'Unlock' : 'Lock' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('forum.threads.destroy', [$category, $thread]) }}" onsubmit="return confirm('Delete this thread permanently?');">
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

        <h1 class="title-hero mt-3 text-4xl sm:text-5xl">{{ $thread->title }}</h1>

        <div class="copy-faint mt-6 flex flex-wrap items-center gap-3 text-sm">
            <span>
                Started by
                <a href="{{ route('users.show', ['user' => $thread->author->name]) }}" class="font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                    {{ $thread->author->name }}
                </a>
            </span>
            <x-staff-badge :user="$thread->author" />
            <span>&middot;</span>
            <span>{{ $thread->created_at->format('M j, Y') }}</span>
            <span>&middot;</span>
            <span>{{ $thread->replies_count }} replies</span>
            @if ($thread->is_locked)
                <span>&middot;</span>
                <span>Locked</span>
            @endif
        </div>

        <p class="copy-base mt-8 whitespace-pre-line text-base leading-8">{{ $thread->body }}</p>

        <div class="mt-8 flex flex-wrap items-center gap-3">
            @auth
                <div class="reaction-group" data-reaction-group data-current-reaction="{{ $threadUserReaction ?? '' }}">
                    <form method="POST" action="{{ route('forum.threads.react', [$category, $thread]) }}" data-reaction-form>
                        @csrf
                        <input type="hidden" name="type" value="like">
                        <button type="submit" data-reaction-button="like" aria-pressed="{{ $threadUserReaction === 'like' ? 'true' : 'false' }}" class="reaction-button {{ $threadUserReaction === 'like' ? 'reaction-button--active' : '' }} px-4 py-2 text-sm font-semibold transition">
                            Like <span data-reaction-count="like">{{ $threadLikes }}</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('forum.threads.react', [$category, $thread]) }}" data-reaction-form>
                        @csrf
                        <input type="hidden" name="type" value="dislike">
                        <button type="submit" data-reaction-button="dislike" aria-pressed="{{ $threadUserReaction === 'dislike' ? 'true' : 'false' }}" class="reaction-button {{ $threadUserReaction === 'dislike' ? 'reaction-button--active' : '' }} px-4 py-2 text-sm font-semibold transition">
                            Dislike <span data-reaction-count="dislike">{{ $threadDislikes }}</span>
                        </button>
                    </form>
                </div>

                @if ($canReplyToThread)
                    <button type="button" @click="openMainReply = !openMainReply" class="button-secondary inline-flex px-5 py-2 text-sm font-semibold transition">
                        Reply
                    </button>
                @elseif ($thread->is_locked)
                    <span class="px-4 py-2 text-sm font-semibold" style="background: var(--bg-elevated);">Locked</span>
                @endif
            @else
                <span class="px-4 py-2 text-sm font-semibold" style="background: var(--bg-elevated);">Like {{ $threadLikes }}</span>
                <span class="px-4 py-2 text-sm font-semibold" style="background: var(--bg-elevated);">Dislike {{ $threadDislikes }}</span>
                @if ($thread->is_locked)
                    <span class="px-4 py-2 text-sm font-semibold" style="background: var(--bg-elevated);">Locked</span>
                @else
                    <a href="{{ route('login') }}" class="button-secondary inline-flex px-5 py-2 text-sm font-semibold transition">Sign in to reply</a>
                @endif
            @endauth
        </div>

        @auth
            @if ($canReplyToThread)
                <div x-show="openMainReply" x-cloak class="mt-8">
                    @if ($replyErrors->any() && ! old('parent_id'))
                        <div>
                            <div class="font-medium" style="color: var(--danger);">Whoops! Something went wrong.</div>
                            <ul class="mt-3 list-disc list-inside text-sm" style="color: var(--danger);">
                                @foreach ($replyErrors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('forum.replies.store', [$category, $thread]) }}" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <x-label for="reply_body" value="Message" />
                            <textarea id="reply_body" name="body" rows="8" class="mt-1 block w-full px-4 py-3 focus:outline-none focus:ring-2"
                                style="border-color: var(--border-strong); background: var(--bg-elevated); color: var(--text-strong);">{{ old('parent_id') ? '' : old('body') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="button-primary inline-flex px-5 py-3 text-sm font-semibold transition">
                                Post reply
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth
    </section>

    <section class="mt-8 space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="section-kicker">Replies</p>
                <h2 class="title-section mt-2 text-3xl">{{ $thread->replies_count }} replies</h2>
            </div>
        </div>

        @forelse ($replyTree as $reply)
            @include('pages.forum.partials.reply', [
                'reply' => $reply,
                'category' => $category,
                'thread' => $thread,
                'depth' => 0,
                'replyErrors' => $replyErrors,
                'canReplyToThread' => $canReplyToThread,
            ])
        @empty
            <div class="surface-panel p-8 text-sm leading-7 copy-muted">
                No replies yet.
            </div>
        @endforelse
    </section>
</x-public-layout>
