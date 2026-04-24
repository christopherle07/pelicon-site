@php
    $isTargetReply = $replyErrors->any() && (string) old('parent_id') === (string) $reply->id;
    $replyUserReaction = auth()->check() ? optional($reply->reactions->firstWhere('user_id', auth()->id()))->type : null;
    $replyLikes = $reply->reactions->where('type', 'like')->count();
    $replyDislikes = $reply->reactions->where('type', 'dislike')->count();
    $indent = min($depth, 4) * 1.5;
    $hasChildren = $reply->children->isNotEmpty();
    $canManageReply = auth()->check() && auth()->user()->canManageForumReply($reply);
    $descendantCount = $reply->descendantCount();
    $replyTargetId = $replyErrors->any() && old('parent_id') ? (int) old('parent_id') : null;
    $shouldAutoCollapse = $hasChildren && $depth >= 3 && ! ($replyTargetId && $reply->containsReplyInTree($replyTargetId));
    $commentIcon = asset('build/assets/comment.svg');
    $reactionIcon = asset('build/assets/like-dislike.svg');
    $reactionIconFilled = asset('build/assets/like-dislike-filled.svg').'?v=2';
@endphp

<div x-data="{ openReplyComposer: @js($isTargetReply), collapsed: @js($shouldAutoCollapse), openReplyMenu: false }" class="forum-reply-thread{{ $depth ? ' forum-reply-thread--nested' : '' }}" style="{{ $depth ? 'margin-left: '.$indent.'rem;' : '' }}">
    <article id="reply-{{ $reply->id }}" class="forum-reply{{ $depth ? ' forum-reply--nested' : '' }}">
        <div class="forum-reply__header">
            <div class="flex items-start gap-3">
                @if ($hasChildren)
                    <div class="shrink-0">
                        <button type="button" @click="collapsed = !collapsed" class="reply-chain-toggle" :aria-label="collapsed ? 'Expand reply chain' : 'Collapse reply chain'">
                            <span x-text="collapsed ? '+' : '−'"></span>
                        </button>
                    </div>
                @endif

                <div class="space-y-2">
                    <div class="forum-reply__meta">
                        <a href="{{ route('users.show', ['user' => $reply->author->name]) }}" class="font-semibold text-[color:var(--text-strong)] transition hover:text-[color:var(--accent-strong)]">
                            {{ $reply->author->name }}
                        </a>
                        <x-staff-badge :user="$reply->author" />
                        @if ($reply->parent && $reply->parent->author)
                            <span class="copy-faint">replied to</span>
                            <a href="#reply-{{ $reply->parent->id }}" class="copy-faint transition hover:text-[color:var(--text-strong)]">
                                {{ $reply->parent->author->name }}
                            </a>
                        @endif
                        <span class="copy-faint">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>

                    @if ($hasChildren)
                        <p class="forum-reply__summary" x-text="collapsed ? '{{ $descendantCount }} {{ \Illuminate\Support\Str::plural('reply', $descendantCount) }} hidden' : '{{ $descendantCount }} {{ \Illuminate\Support\Str::plural('reply', $descendantCount) }} below'"></p>
                    @endif
                </div>
            </div>

            @if ($canManageReply)
                <div class="relative shrink-0">
                    <button type="button" @click="openReplyMenu = ! openReplyMenu" class="forum-reply__menu-toggle" aria-label="Reply actions">
                        ...
                    </button>

                    <div x-show="openReplyMenu" x-cloak @click.outside="openReplyMenu = false" class="absolute right-0 top-12 z-10 flex w-44 flex-col gap-2 surface-panel p-3">
                        <form method="POST" action="{{ route('forum.replies.destroy', [$category, $thread, $reply]) }}" onsubmit="return confirm('Delete this reply permanently?');">
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

        <p class="forum-reply__body">{{ $reply->body }}</p>

        <div class="forum-action-group mt-3">
            @auth
                <div class="forum-action-group" data-reaction-group data-current-reaction="{{ $replyUserReaction ?? '' }}">
                    <form method="POST" action="{{ route('forum.replies.react', [$category, $thread, $reply]) }}" data-reaction-form>
                        @csrf
                        <input type="hidden" name="type" value="like">
                        <button type="submit" data-reaction-button="like" aria-pressed="{{ $replyUserReaction === 'like' ? 'true' : 'false' }}" class="forum-action-button forum-action-button--compact {{ $replyUserReaction === 'like' ? 'forum-action-button--active' : '' }}">
                            <img src="{{ $replyUserReaction === 'like' ? $reactionIconFilled : $reactionIcon }}" alt="" class="forum-action-button__icon" aria-hidden="true" data-reaction-icon data-inactive-src="{{ $reactionIcon }}" data-active-src="{{ $reactionIconFilled }}">
                            <span class="sr-only">Like</span>
                            <span class="forum-action-button__count" data-reaction-count="like">{{ $replyLikes }}</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('forum.replies.react', [$category, $thread, $reply]) }}" data-reaction-form>
                        @csrf
                        <input type="hidden" name="type" value="dislike">
                        <button type="submit" data-reaction-button="dislike" aria-pressed="{{ $replyUserReaction === 'dislike' ? 'true' : 'false' }}" class="forum-action-button forum-action-button--compact {{ $replyUserReaction === 'dislike' ? 'forum-action-button--active' : '' }}">
                            <img src="{{ $replyUserReaction === 'dislike' ? $reactionIconFilled : $reactionIcon }}" alt="" class="forum-action-button__icon forum-action-button__icon--flipped" aria-hidden="true" data-reaction-icon data-inactive-src="{{ $reactionIcon }}" data-active-src="{{ $reactionIconFilled }}">
                            <span class="sr-only">Dislike</span>
                            <span class="forum-action-button__count" data-reaction-count="dislike">{{ $replyDislikes }}</span>
                        </button>
                    </form>
                </div>

                @if ($canReplyToThread)
                    <button type="button" @click="openReplyComposer = !openReplyComposer" class="forum-action-button forum-action-button--compact">
                        <img src="{{ $commentIcon }}" alt="" class="forum-action-button__icon" aria-hidden="true">
                        <span class="sr-only">Reply</span>
                    </button>
                @elseif ($thread->is_locked)
                    <span class="px-4 py-2 text-sm font-semibold" style="background: var(--bg-elevated);">Locked</span>
                @endif
            @else
                <span class="forum-action-button forum-action-button--compact">
                    <img src="{{ $reactionIcon }}" alt="" class="forum-action-button__icon" aria-hidden="true">
                    <span class="forum-action-button__count">{{ $replyLikes }}</span>
                </span>
                <span class="forum-action-button forum-action-button--compact">
                    <img src="{{ $reactionIcon }}" alt="" class="forum-action-button__icon forum-action-button__icon--flipped" aria-hidden="true">
                    <span class="forum-action-button__count">{{ $replyDislikes }}</span>
                </span>
                @if ($thread->is_locked)
                    <span class="px-4 py-2 text-sm font-semibold" style="background: var(--bg-elevated);">Locked</span>
                @endif
            @endauth
        </div>

        @auth
            @if ($canReplyToThread)
                <div x-show="openReplyComposer" x-cloak class="mt-6">
                    @if ($isTargetReply)
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
                        <input type="hidden" name="parent_id" value="{{ $reply->id }}">

                        <div>
                            <x-label for="reply_body_{{ $reply->id }}" value="Reply" />
                            <textarea id="reply_body_{{ $reply->id }}" name="body" rows="6" class="site-textarea mt-1 block w-full px-4 py-3 focus:outline-none focus:ring-0">{{ $isTargetReply ? old('body') : '' }}</textarea>
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
    </article>

    @if ($hasChildren)
        <div x-show="!collapsed" x-cloak class="forum-reply__children">
            @foreach ($reply->children as $childReply)
                @include('pages.forum.partials.reply', [
                    'reply' => $childReply,
                    'category' => $category,
                    'thread' => $thread,
                    'depth' => $depth + 1,
                    'replyErrors' => $replyErrors,
                    'canReplyToThread' => $canReplyToThread,
                ])
            @endforeach
        </div>
    @endif
</div>
