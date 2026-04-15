<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_threads_are_paginated_to_ten_per_page(): void
    {
        $user = User::factory()->create();

        $category = ForumCategory::query()->create([
            'name' => 'Bug Reports',
            'slug' => 'bug-reports',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        foreach (range(1, 11) as $index) {
            $label = str_pad((string) $index, 2, '0', STR_PAD_LEFT);

            ForumThread::query()->create([
                'forum_category_id' => $category->id,
                'user_id' => $user->id,
                'title' => "Paginated Thread {$label}",
                'slug' => "paginated-thread-{$label}",
                'body' => "This is the body for paginated thread {$label}.",
                'last_posted_at' => now()->subMinutes($index - 1),
            ]);
        }

        $firstPage = $this->get(route('forum.show', $category));
        $secondPage = $this->get(route('forum.show', ['category' => $category, 'page' => 2]));

        $firstPage->assertOk();
        $firstPage->assertSee('Paginated Thread 01');
        $firstPage->assertSee('Paginated Thread 10');
        $firstPage->assertDontSee('Paginated Thread 11');

        $secondPage->assertOk();
        $secondPage->assertSee('Paginated Thread 11');
        $secondPage->assertDontSee('Paginated Thread 01');
    }
}
