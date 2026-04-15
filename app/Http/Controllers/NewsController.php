<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('pages.news.index', [
            'announcements' => Announcement::query()
                ->published()
                ->with('author')
                ->withCount(['comments', 'reactions'])
                ->latest('published_at')
                ->paginate(9),
        ]);
    }

    public function show(Announcement $announcement): View
    {
        abort_unless($announcement->status === 'published', 404);

        $announcement->load([
            'author',
            'comments.user',
            'reactions',
        ]);

        return view('pages.news.show', [
            'announcement' => $announcement,
            'userReaction' => auth()->check()
                ? $announcement->reactions->firstWhere('user_id', auth()->id())?->type
                : null,
            'reactionCounts' => $announcement->reactions
                ->groupBy('type')
                ->map->count(),
        ]);
    }

    public function storeComment(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($announcement->status === 'published', 404);

        $validated = $request->validateWithBag('announcementComment', [
            'body' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $announcement->comments()->create([
            'user_id' => $request->user()->id,
            'body' => trim($validated['body']),
        ]);

        return redirect()
            ->route('news.show', $announcement)
            ->with('status', 'Comment posted.');
    }

    public function destroyComment(Request $request, Announcement $announcement, AnnouncementComment $comment): RedirectResponse
    {
        abort_unless($announcement->status === 'published', 404);
        abort_unless($comment->announcement_id === $announcement->id, 404);
        abort_unless($request->user()->canManageAnnouncementComment($comment), 403);

        $comment->delete();

        return redirect()
            ->route('news.show', $announcement)
            ->with('status', 'Comment deleted.');
    }

    public function react(Request $request, Announcement $announcement)
    {
        abort_unless($announcement->status === 'published', 404);

        $validated = $request->validate([
            'type' => ['required', 'in:like,dislike'],
        ]);

        $this->toggleReaction($request, $announcement, $validated['type']);

        if ($request->expectsJson()) {
            return response()->json($this->reactionPayload($request, $announcement));
        }

        return back();
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
}
