<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\Reaction;
use App\Models\ForumThread;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        return view('pages.forum.index', [
            'search' => $search,
            'searchResults' => $search !== '' ? $this->searchThreads($search) : collect(),
            'categories' => ForumCategory::query()
                ->withCount('threads')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function show(Request $request, ForumCategory $category): View
    {
        $search = trim((string) $request->query('q', ''));

        return view('pages.forum.show', [
            'search' => $search,
            'searchResults' => $search !== '' ? $this->searchThreads($search, $category) : collect(),
            'category' => $category->loadCount('threads'),
            'threads' => $search === ''
                ? $this->paginatedCategoryThreads($category)
                : null,
        ]);
    }

    public function showThread(ForumCategory $category, ForumThread $thread): View
    {
        abort_unless($thread->forum_category_id === $category->id, 404);

        $thread->increment('view_count');

        return view('pages.forum.thread', [
            'category' => $category,
            'thread' => $thread
                ->load([
                    'author',
                    'reactions',
                ])
                ->loadCount('replies'),
            'replyTree' => $this->buildReplyTree(
                $thread->replies()
                    ->with(['author', 'reactions', 'parent.author'])
                    ->oldest()
                    ->get()
            ),
        ]);
    }

    public function storeThread(Request $request, ForumCategory $category): RedirectResponse
    {
        $validated = $request->validateWithBag('createThread', [
            'title' => ['required', 'string', 'min:4', 'max:140'],
            'body' => ['required', 'string', 'min:10', 'max:20000'],
        ]);

        $thread = $category->threads()->create([
            'user_id' => $request->user()->id,
            'title' => trim($validated['title']),
            'slug' => $this->uniqueThreadSlug($validated['title']),
            'body' => trim($validated['body']),
            'last_posted_at' => now(),
        ]);

        return redirect()
            ->route('forum.threads.show', [$category, $thread])
            ->with('status', 'Thread created.');
    }

    public function storeReply(Request $request, ForumCategory $category, ForumThread $thread): RedirectResponse
    {
        abort_unless($thread->forum_category_id === $category->id, 404);
        abort_unless($request->user()->canReplyToForumThread($thread), 403);

        $validated = $request->validateWithBag('replyThread', [
            'body' => ['required', 'string', 'min:2', 'max:10000'],
            'parent_id' => ['nullable', 'integer', 'exists:forum_replies,id'],
        ]);

        $parentReply = null;

        if (filled($validated['parent_id'] ?? null)) {
            $parentReply = ForumReply::query()
                ->whereKey($validated['parent_id'])
                ->where('forum_thread_id', $thread->id)
                ->firstOrFail();
        }

        $thread->replies()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentReply?->id,
            'body' => trim($validated['body']),
        ]);

        $thread->forceFill([
            'last_posted_at' => now(),
        ])->save();

        return redirect()
            ->route('forum.threads.show', [$category, $thread])
            ->with('status', 'Reply posted.');
    }

    public function toggleThreadLock(Request $request, ForumCategory $category, ForumThread $thread): RedirectResponse
    {
        abort_unless($thread->forum_category_id === $category->id, 404);
        abort_unless($request->user()->canManageForumThread($thread), 403);

        $thread->forceFill([
            'is_locked' => ! $thread->is_locked,
        ])->save();

        return back()->with('status', $thread->is_locked ? 'Thread locked.' : 'Thread unlocked.');
    }

    public function destroyThread(Request $request, ForumCategory $category, ForumThread $thread): RedirectResponse
    {
        abort_unless($thread->forum_category_id === $category->id, 404);
        abort_unless($request->user()->canManageForumThread($thread), 403);

        $replyIds = $thread->replies()->withTrashed()->pluck('id');

        if ($replyIds->isNotEmpty()) {
            Reaction::query()
                ->where('reactable_type', ForumReply::class)
                ->whereIn('reactable_id', $replyIds)
                ->delete();
        }

        $thread->reactions()->delete();
        $thread->forceDelete();

        return redirect()
            ->route('forum.show', $category)
            ->with('status', 'Thread deleted.');
    }

    public function destroyReply(Request $request, ForumCategory $category, ForumThread $thread, ForumReply $reply): RedirectResponse
    {
        abort_unless($thread->forum_category_id === $category->id, 404);
        abort_unless($reply->forum_thread_id === $thread->id, 404);
        abort_unless($request->user()->canManageForumReply($reply), 403);

        $reply->reactions()->delete();
        $reply->forceDelete();

        $thread->forceFill([
            'last_posted_at' => now(),
        ])->save();

        return back()->with('status', 'Reply deleted.');
    }

    public function reactToThread(Request $request, ForumCategory $category, ForumThread $thread)
    {
        abort_unless($thread->forum_category_id === $category->id, 404);

        $validated = $request->validate([
            'type' => ['required', 'in:like,dislike'],
        ]);

        $this->toggleReaction($request, $thread, $validated['type']);

        if ($request->expectsJson()) {
            return response()->json($this->reactionPayload($request, $thread));
        }

        return back();
    }

    public function reactToReply(Request $request, ForumCategory $category, ForumThread $thread, ForumReply $reply)
    {
        abort_unless($thread->forum_category_id === $category->id, 404);
        abort_unless($reply->forum_thread_id === $thread->id, 404);

        $validated = $request->validate([
            'type' => ['required', 'in:like,dislike'],
        ]);

        $this->toggleReaction($request, $reply, $validated['type']);

        if ($request->expectsJson()) {
            return response()->json($this->reactionPayload($request, $reply));
        }

        return back();
    }

    private function uniqueThreadSlug(string $title): string
    {
        $base = Str::slug($title);
        $base = $base !== '' ? $base : 'thread';

        $slug = $base;
        $suffix = 2;

        while (ForumThread::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function toggleReaction(Request $request, Model $reactable, string $type): void
    {
        $existingReaction = $reactable->reactions()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReaction?->type === $type) {
            $existingReaction->delete();

            return;
        }

        if ($existingReaction) {
            $existingReaction->update([
                'type' => $type,
            ]);

            return;
        }

        $reactable->reactions()->create([
            'user_id' => $request->user()->id,
            'type' => $type,
        ]);
    }

    private function reactionPayload(Request $request, Model $reactable): array
    {
        $currentReaction = $reactable->reactions()
            ->where('user_id', $request->user()->id)
            ->value('type');

        $counts = $reactable->reactions()
            ->selectRaw('type, count(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');

        return [
            'status' => 'ok',
            'currentReaction' => $currentReaction,
            'counts' => [
                'like' => (int) ($counts['like'] ?? 0),
                'dislike' => (int) ($counts['dislike'] ?? 0),
            ],
        ];
    }

    private function searchThreads(string $search, ?ForumCategory $category = null): EloquentCollection
    {
        return ForumThread::query()
            ->with(['author', 'category'])
            ->withCount([
                'replies',
                'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
            ])
            ->when($category, fn ($query) => $query->where('forum_category_id', $category->id))
            ->where(function ($query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('replies', function ($replyQuery) use ($search): void {
                        $replyQuery->where('body', 'like', "%{$search}%");
                    });
            })
            ->latest('last_posted_at')
            ->get();
    }

    private function paginatedCategoryThreads(ForumCategory $category): LengthAwarePaginator
    {
        return $category->threads()
            ->with('author')
            ->withCount([
                'replies',
                'reactions as likes_count' => fn ($query) => $query->where('type', 'like'),
                'reactions as dislikes_count' => fn ($query) => $query->where('type', 'dislike'),
            ])
            ->paginate(10)
            ->withQueryString();
    }

    private function buildReplyTree(EloquentCollection $replies, ?int $parentId = null): EloquentCollection
    {
        $branch = $replies
            ->filter(fn (ForumReply $reply) => $reply->parent_id === $parentId)
            ->values();

        $branch->each(function (ForumReply $reply) use ($replies): void {
            $reply->setRelation('children', $this->buildReplyTree($replies, $reply->id));
        });

        return $branch;
    }
}
