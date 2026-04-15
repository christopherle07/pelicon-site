<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Support\AnnouncementBodySanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(
        private AnnouncementBodySanitizer $sanitizer
    ) {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return view('admin.announcements.index', [
            'announcements' => Announcement::query()
                ->with('author')
                ->latest('updated_at')
                ->paginate(12),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return view('admin.announcements.form', [
            'announcement' => new Announcement(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $announcement = Announcement::query()->create(
            $this->validatedAnnouncementData($request)
        );

        return redirect()
            ->route('admin.news.edit', $announcement)
            ->with('status', $announcement->status === 'published' ? 'Announcement published.' : 'Draft saved.');
    }

    public function edit(Request $request, Announcement $announcement): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return view('admin.announcements.form', [
            'announcement' => $announcement,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $announcement->update(
            $this->validatedAnnouncementData($request, $announcement)
        );

        return redirect()
            ->route('admin.news.edit', $announcement)
            ->with('status', $announcement->status === 'published' ? 'Announcement updated.' : 'Draft updated.');
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $announcement->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'Announcement deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedAnnouncementData(Request $request, ?Announcement $announcement = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:4', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:280'],
            'body' => ['nullable', 'string', 'max:50000'],
            'cover_image_url' => ['nullable', 'url', 'max:2048'],
            'embed_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $sanitizedBody = $this->sanitizer->sanitize($validated['body'] ?? '');
        $plainBody = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($sanitizedBody))));

        if ($plainBody === '' && blank($validated['excerpt'] ?? null)) {
            throw ValidationException::withMessages([
                'body' => 'Announcement content cannot be empty.',
            ]);
        }

        $excerpt = trim((string) ($validated['excerpt'] ?? ''));

        return [
            'user_id' => $announcement?->user_id ?? $request->user()->id,
            'title' => trim($validated['title']),
            'slug' => $this->uniqueSlug($validated['title'], $announcement?->id),
            'excerpt' => $excerpt !== '' ? $excerpt : Str::limit($plainBody, 220),
            'body' => $sanitizedBody,
            'cover_image_url' => $validated['cover_image_url'] ?: null,
            'embed_url' => $validated['embed_url'] ?: null,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published'
                ? ($announcement?->published_at ?? now())
                : null,
        ];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $base = $base !== '' ? $base : 'announcement';

        $slug = $base;
        $suffix = 2;

        while (
            Announcement::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
