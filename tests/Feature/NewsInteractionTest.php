<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\AnnouncementComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_announcement_page_does_not_render_excerpt_as_a_second_body_block(): void
    {
        $announcement = $this->publishedAnnouncement([
            'excerpt' => 'This should only appear on the card.',
            'body' => '<p><strong>Formatted</strong> body content.</p>',
        ]);

        $response = $this->get(route('news.show', $announcement));

        $response->assertOk();
        $response->assertDontSee('This should only appear on the card.');
        $response->assertSee('Formatted');
    }

    public function test_authenticated_user_can_comment_on_a_published_announcement(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $announcement = $this->publishedAnnouncement();

        $response = $this->actingAs($user)->post(route('news.comments.store', $announcement), [
            'body' => 'This update clears up the issue I was having.',
        ]);

        $response->assertRedirect(route('news.show', $announcement));

        $this->assertDatabaseHas('announcement_comments', [
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
            'body' => 'This update clears up the issue I was having.',
        ]);
    }

    public function test_authenticated_user_can_react_to_a_published_announcement_without_redirecting(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $announcement = $this->publishedAnnouncement();

        $this->actingAs($user)
            ->postJson(route('news.react', $announcement), [
                'type' => 'like',
            ])
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'currentReaction' => 'like',
                'counts' => [
                    'like' => 1,
                    'dislike' => 0,
                ],
            ]);
    }

    public function test_comment_author_can_delete_their_own_announcement_comment(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $announcement = $this->publishedAnnouncement();
        $comment = AnnouncementComment::query()->create([
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
            'body' => 'I only need to remove my own comment.',
        ]);

        $this->actingAs($user)
            ->delete(route('news.comments.destroy', [$announcement, $comment]))
            ->assertRedirect(route('news.show', $announcement));

        $this->assertSoftDeleted('announcement_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_moderator_can_delete_any_announcement_comment(): void
    {
        $owner = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $announcement = $this->publishedAnnouncement();
        $comment = AnnouncementComment::query()->create([
            'announcement_id' => $announcement->id,
            'user_id' => $owner->id,
            'body' => 'Please moderate this comment away.',
        ]);

        $this->actingAs($moderator)
            ->delete(route('news.comments.destroy', [$announcement, $comment]))
            ->assertRedirect(route('news.show', $announcement));

        $this->assertSoftDeleted('announcement_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_admin_can_delete_any_announcement_comment(): void
    {
        $owner = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $announcement = $this->publishedAnnouncement();
        $comment = AnnouncementComment::query()->create([
            'announcement_id' => $announcement->id,
            'user_id' => $owner->id,
            'body' => 'Admin should be able to remove this.',
        ]);

        $this->actingAs($admin)
            ->delete(route('news.comments.destroy', [$announcement, $comment]))
            ->assertRedirect(route('news.show', $announcement));

        $this->assertSoftDeleted('announcement_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_regular_user_cannot_delete_someone_elses_announcement_comment(): void
    {
        $owner = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $otherUser = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $announcement = $this->publishedAnnouncement();
        $comment = AnnouncementComment::query()->create([
            'announcement_id' => $announcement->id,
            'user_id' => $owner->id,
            'body' => 'This should stay put.',
        ]);

        $this->actingAs($otherUser)
            ->delete(route('news.comments.destroy', [$announcement, $comment]))
            ->assertForbidden();

        $this->assertDatabaseHas('announcement_comments', [
            'id' => $comment->id,
            'deleted_at' => null,
        ]);
    }

    private function publishedAnnouncement(array $overrides = []): Announcement
    {
        $author = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        return Announcement::query()->create(array_merge([
            'user_id' => $author->id,
            'title' => 'Pelicon update',
            'slug' => 'pelicon-update',
            'excerpt' => 'Default excerpt',
            'body' => '<p>Default body.</p>',
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }
}
