<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_forum_search_matches_thread_titles_bodies_and_replies(): void
    {
        $user = User::factory()->create();

        $bugs = ForumCategory::query()->create([
            'name' => 'Bug Reports',
            'slug' => 'bug-reports',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $suggestions = ForumCategory::query()->create([
            'name' => 'Suggestions',
            'slug' => 'suggestions',
            'description' => 'Feature requests and ideas',
            'accent_color' => '#555555',
            'sort_order' => 2,
        ]);

        $titleMatch = ForumThread::query()->create([
            'forum_category_id' => $bugs->id,
            'user_id' => $user->id,
            'title' => 'Window snapping breaks after resize',
            'slug' => 'window-snapping-breaks-after-resize',
            'body' => 'The layout shifts after resizing the app a few times.',
            'last_posted_at' => now(),
        ]);

        $bodyMatch = ForumThread::query()->create([
            'forum_category_id' => $suggestions->id,
            'user_id' => $user->id,
            'title' => 'Preset layouts',
            'slug' => 'preset-layouts',
            'body' => 'Please add a compact window mode for smaller displays.',
            'last_posted_at' => now()->subMinute(),
        ]);

        $replyMatch = ForumThread::query()->create([
            'forum_category_id' => $bugs->id,
            'user_id' => $user->id,
            'title' => 'General thread',
            'slug' => 'general-thread',
            'body' => 'Just a general discussion thread.',
            'last_posted_at' => now()->subMinutes(2),
        ]);

        ForumReply::query()->create([
            'forum_thread_id' => $replyMatch->id,
            'user_id' => $user->id,
            'body' => 'The compact window mode would help me a lot on a laptop.',
        ]);

        $unrelated = ForumThread::query()->create([
            'forum_category_id' => $suggestions->id,
            'user_id' => $user->id,
            'title' => 'Mouse shortcuts',
            'slug' => 'mouse-shortcuts',
            'body' => 'Would be nice to bind side buttons to navigation.',
            'last_posted_at' => now()->subMinutes(3),
        ]);

        $response = $this->get(route('forum.index', ['q' => 'window']));

        $response->assertOk();
        $response->assertSee('Community Forum');
        $response->assertSee($titleMatch->title);
        $response->assertSee($bodyMatch->title);
        $response->assertSee($replyMatch->title);
        $response->assertDontSee($unrelated->title);
        $response->assertDontSee('Categories');
    }

    public function test_category_search_only_returns_threads_from_that_category(): void
    {
        $user = User::factory()->create();

        $bugs = ForumCategory::query()->create([
            'name' => 'Bug Reports',
            'slug' => 'bug-reports',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $suggestions = ForumCategory::query()->create([
            'name' => 'Suggestions',
            'slug' => 'suggestions',
            'description' => 'Feature requests and ideas',
            'accent_color' => '#555555',
            'sort_order' => 2,
        ]);

        $bugThread = ForumThread::query()->create([
            'forum_category_id' => $bugs->id,
            'user_id' => $user->id,
            'title' => 'Window snapping bug',
            'slug' => 'window-snapping-bug',
            'body' => 'Window snapping breaks after resize.',
            'last_posted_at' => now(),
        ]);

        $suggestionThread = ForumThread::query()->create([
            'forum_category_id' => $suggestions->id,
            'user_id' => $user->id,
            'title' => 'Window layout presets',
            'slug' => 'window-layout-presets',
            'body' => 'Preset window sizes would be useful.',
            'last_posted_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('forum.show', ['category' => $bugs, 'q' => 'window']));

        $response->assertOk();
        $response->assertSee($bugThread->title);
        $response->assertDontSee($suggestionThread->title);
    }
}
