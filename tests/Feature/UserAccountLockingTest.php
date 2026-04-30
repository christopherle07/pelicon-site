<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccountLockingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_lock_and_unlock_moderators_and_regular_users(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $moderator = User::factory()->create(['role' => UserRole::Moderator]);
        $user = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($admin)
            ->post(route('users.lock', ['user' => $moderator->name]))
            ->assertRedirect();

        $this->assertTrue($moderator->fresh()->isLocked());
        $this->assertSame($admin->id, $moderator->fresh()->locked_by_user_id);

        $this->actingAs($admin)
            ->post(route('users.lock', ['user' => $user->name]))
            ->assertRedirect();

        $this->assertTrue($user->fresh()->isLocked());

        $this->actingAs($admin)
            ->delete(route('users.unlock', ['user' => $moderator->name]))
            ->assertRedirect();

        $this->assertFalse($moderator->fresh()->isLocked());
    }

    public function test_moderator_can_only_lock_regular_users(): void
    {
        $moderator = User::factory()->create(['role' => UserRole::Moderator]);
        $otherModerator = User::factory()->create(['role' => UserRole::Moderator]);
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $user = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($moderator)
            ->post(route('users.lock', ['user' => $user->name]))
            ->assertRedirect();

        $this->assertTrue($user->fresh()->isLocked());

        $this->actingAs($moderator)
            ->post(route('users.lock', ['user' => $otherModerator->name]))
            ->assertForbidden();

        $this->actingAs($moderator)
            ->post(route('users.lock', ['user' => $admin->name]))
            ->assertForbidden();

        $this->actingAs($moderator)
            ->post(route('users.lock', ['user' => $moderator->name]))
            ->assertForbidden();
    }

    public function test_regular_users_cannot_lock_accounts(): void
    {
        $actor = User::factory()->create(['role' => UserRole::User]);
        $target = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($actor)
            ->post(route('users.lock', ['user' => $target->name]))
            ->assertForbidden();

        $this->assertFalse($target->fresh()->isLocked());
    }

    public function test_locked_users_cannot_create_news_comments_forum_threads_or_forum_replies(): void
    {
        $lockedUser = User::factory()->create([
            'role' => UserRole::User,
            'locked_at' => now(),
        ]);

        $announcement = $this->publishedAnnouncement();
        $category = ForumCategory::query()->create([
            'name' => 'General',
            'slug' => 'general',
            'description' => 'General discussion',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);
        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => User::factory()->create(['role' => UserRole::User])->id,
            'title' => 'Existing thread',
            'slug' => 'existing-thread',
            'body' => 'A thread that already exists for reply testing.',
            'last_posted_at' => now(),
        ]);

        $this->actingAs($lockedUser)->post(route('news.comments.store', $announcement), [
            'body' => 'This should not post.',
        ])->assertForbidden();

        $this->actingAs($lockedUser)->post(route('forum.threads.store', $category), [
            'title' => 'Locked user thread attempt',
            'body' => 'This thread should not be created because the account is locked.',
        ])->assertForbidden();

        $this->actingAs($lockedUser)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'This reply should not post.',
        ])->assertForbidden();

        $this->assertDatabaseMissing('announcement_comments', [
            'user_id' => $lockedUser->id,
        ]);
        $this->assertDatabaseMissing('forum_threads', [
            'user_id' => $lockedUser->id,
        ]);
        $this->assertDatabaseMissing('forum_replies', [
            'user_id' => $lockedUser->id,
        ]);
    }

    private function publishedAnnouncement(): Announcement
    {
        return Announcement::query()->create([
            'user_id' => User::factory()->create(['role' => UserRole::Admin])->id,
            'title' => 'Pelicon update',
            'slug' => 'pelicon-update',
            'excerpt' => 'Default excerpt',
            'body' => '<p>Default body.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
