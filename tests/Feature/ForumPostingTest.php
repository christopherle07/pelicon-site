<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use App\Notifications\ForumReplyPosted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForumPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_forum_thread(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Bugs',
            'slug' => 'bugs',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('forum.threads.store', $category), [
            'title' => 'Window sizing issue on launch',
            'body' => 'The window opens too large every time I start the app and it covers the taskbar.',
        ]);

        $thread = ForumThread::query()->first();

        $response->assertRedirect(route('forum.threads.show', [$category, $thread]));

        $this->assertDatabaseHas('forum_threads', [
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Window sizing issue on launch',
        ]);
    }

    public function test_authenticated_user_can_reply_to_a_thread(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Questions',
            'slug' => 'questions',
            'description' => 'Questions and support',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'How do I resize the app window?',
            'slug' => 'how-do-i-resize-the-app-window',
            'body' => 'I am trying to make the app smaller but cannot find the right setting.',
            'last_posted_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'You can drag the edges of the window or reset it from settings.',
        ]);

        $response->assertRedirect(route('forum.threads.show', [$category, $thread]));

        $this->assertDatabaseHas('forum_replies', [
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'You can drag the edges of the window or reset it from settings.',
        ]);
    }

    public function test_forum_reply_notification_uses_dedicated_mailer_and_light_template(): void
    {
        config([
            'mail.forum_notifications.from.address' => 'notifications@pelicon.app',
            'mail.forum_notifications.from.name' => 'Pelicon Forum',
        ]);

        $owner = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'Thread Owner',
        ]);

        $author = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'Reply Author',
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'General',
            'slug' => 'general',
            'description' => 'General discussion',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $owner->id,
            'title' => 'Notification styling test',
            'slug' => 'notification-styling-test',
            'body' => 'Testing email output.',
            'last_posted_at' => now(),
        ]);

        $reply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $author->id,
            'body' => 'This email should be light and should not use the account verification mailbox.',
        ])->load('author', 'thread.category');

        $mail = (new ForumReplyPosted($reply))->toMail($owner);

        $this->assertSame('forum_notifications', $mail->mailer);
        $this->assertSame(['notifications@pelicon.app', 'Pelicon Forum'], $mail->from);
        $this->assertSame('emails.forum-reply', $mail->view);

        $html = view($mail->view, $mail->viewData)->render();

        $this->assertStringContainsString('background:#f4f1e8', $html);
        $this->assertStringContainsString('View reply', $html);
        $this->assertStringNotContainsString('background:#0f1010', $html);
        $this->assertStringNotContainsString('no-reply@pelicon.app', $html);
    }

    public function test_forum_reply_notifications_skip_users_who_disabled_them(): void
    {
        Notification::fake();

        $owner = User::factory()->create([
            'role' => UserRole::User,
            'forum_notifications_enabled' => false,
        ]);

        $author = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'General',
            'slug' => 'general',
            'description' => 'General discussion',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $owner->id,
            'title' => 'Notification opt out test',
            'slug' => 'notification-opt-out-test',
            'body' => 'The owner should not get an email when notifications are off.',
            'last_posted_at' => now(),
        ]);

        $this->actingAs($author)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'Reply that would normally notify the owner.',
        ])->assertRedirect(route('forum.threads.show', [$category, $thread]));

        Notification::assertNotSentTo($owner, ForumReplyPosted::class);
    }

    public function test_forum_reply_notifications_do_not_notify_poster_when_ids_hydrate_as_strings(): void
    {
        Notification::fake();

        $poster = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'General',
            'slug' => 'general-string-notify-ids',
            'description' => 'General discussion',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $poster->id,
            'title' => 'String notify ids',
            'slug' => 'string-notify-ids',
            'body' => 'The poster should not receive their own notification.',
            'last_posted_at' => now(),
        ]);

        ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $poster->id,
            'body' => 'Earlier reply from the same user.',
        ]);

        $thread->setRawAttributes(array_merge($thread->getAttributes(), [
            'user_id' => (string) $poster->id,
        ]), true);

        $this->actingAs($poster)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'This reply should not notify me.',
        ])->assertRedirect(route('forum.threads.show', [$category, $thread]));

        Notification::assertNotSentTo($poster, ForumReplyPosted::class);
    }

    public function test_forum_reply_still_posts_when_notification_delivery_fails(): void
    {
        Log::spy();
        Notification::shouldReceive('send')
            ->once()
            ->andThrow(new \RuntimeException('SMTP failed'));

        $owner = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $author = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'General',
            'slug' => 'general-failure-test',
            'description' => 'General discussion',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $owner->id,
            'title' => 'Notification failure test',
            'slug' => 'notification-failure-test',
            'body' => 'Reply posting should not depend on notification delivery.',
            'last_posted_at' => now(),
        ]);

        $this->actingAs($author)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'This reply should save even if notification delivery fails.',
        ])->assertRedirect(route('forum.threads.show', [$category, $thread]));

        $this->assertDatabaseHas('forum_replies', [
            'forum_thread_id' => $thread->id,
            'user_id' => $author->id,
            'body' => 'This reply should save even if notification delivery fails.',
        ]);

        Log::shouldHaveReceived('warning')
            ->with('Forum reply notification failed.', \Mockery::on(fn (array $context): bool => $context['thread_id'] === $thread->id));
    }

    public function test_authenticated_user_can_reply_to_an_existing_reply(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Suggestions',
            'slug' => 'suggestions',
            'description' => 'Feature requests and ideas',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Could we get more window presets?',
            'slug' => 'could-we-get-more-window-presets',
            'body' => 'Preset sizes would make it easier to switch layouts quickly.',
            'last_posted_at' => now(),
        ]);

        $parentReply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'I would especially like a compact preset for small monitors.',
        ]);

        $response = $this->actingAs($user)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'A couple default presets plus a custom option would be enough for me.',
            'parent_id' => $parentReply->id,
        ]);

        $response->assertRedirect(route('forum.threads.show', [$category, $thread]));

        $this->assertDatabaseHas('forum_replies', [
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => $parentReply->id,
            'body' => 'A couple default presets plus a custom option would be enough for me.',
        ]);
    }

    public function test_nested_forum_replies_are_visible_in_thread_markup(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'General',
            'slug' => 'general-nested-replies',
            'description' => 'General discussion',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Nested reply visibility',
            'slug' => 'nested-reply-visibility',
            'body' => 'Nested replies should render below the parent reply.',
            'last_posted_at' => now(),
        ]);

        $parentReply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Parent reply body.',
        ]);

        ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => $parentReply->id,
            'body' => 'Nested child reply body.',
        ]);

        $this->get(route('forum.threads.show', [$category, $thread]))
            ->assertOk()
            ->assertSee('Parent reply body.')
            ->assertSee('Nested child reply body.')
            ->assertDontSee('x-show="!collapsed" x-cloak', false);
    }

    public function test_nested_forum_reply_tree_handles_string_parent_ids_from_database(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'General',
            'slug' => 'general-string-parent-ids',
            'description' => 'General discussion',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'String parent id visibility',
            'slug' => 'string-parent-id-visibility',
            'body' => 'Nested replies should render even if parent ids hydrate as strings.',
            'last_posted_at' => now(),
        ]);

        $parentReply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Parent reply with string child.',
        ]);

        $childReply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => $parentReply->id,
            'body' => 'Child reply with string parent id.',
        ]);

        $childReply->setRawAttributes(array_merge($childReply->getAttributes(), [
            'parent_id' => (string) $parentReply->id,
        ]), true);

        $controller = app(\App\Http\Controllers\ForumController::class);
        $method = new \ReflectionMethod($controller, 'buildReplyTree');
        $method->setAccessible(true);

        $replyTree = $method->invoke($controller, new \Illuminate\Database\Eloquent\Collection([
            $parentReply,
            $childReply,
        ]));

        $this->assertCount(1, $replyTree);
        $this->assertCount(1, $replyTree->first()->children);
        $this->assertSame('Child reply with string parent id.', $replyTree->first()->children->first()->body);
    }

    public function test_authenticated_user_can_toggle_thread_reactions(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Questions',
            'slug' => 'questions',
            'description' => 'Questions and support',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'What is the best window size for Pelicon?',
            'slug' => 'what-is-the-best-window-size-for-pelicon',
            'body' => 'I am trying to find the best size for reading and browsing at the same time.',
            'last_posted_at' => now(),
        ]);

        $this->actingAs($user)->post(route('forum.threads.react', [$category, $thread]), [
            'type' => 'like',
        ])->assertRedirect();

        $this->assertDatabaseHas('reactions', [
            'reactable_type' => ForumThread::class,
            'reactable_id' => $thread->id,
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        $this->actingAs($user)->post(route('forum.threads.react', [$category, $thread]), [
            'type' => 'dislike',
        ])->assertRedirect();

        $this->assertDatabaseHas('reactions', [
            'reactable_type' => ForumThread::class,
            'reactable_id' => $thread->id,
            'user_id' => $user->id,
            'type' => 'dislike',
        ]);

        $this->actingAs($user)->post(route('forum.threads.react', [$category, $thread]), [
            'type' => 'dislike',
        ])->assertRedirect();

        $this->assertDatabaseMissing('reactions', [
            'reactable_type' => ForumThread::class,
            'reactable_id' => $thread->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_toggle_reply_reactions(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Bugs',
            'slug' => 'bugs',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Scroll bar disappears after resizing',
            'slug' => 'scroll-bar-disappears-after-resizing',
            'body' => 'After a few resizes, the app stops showing the vertical scroll bar.',
            'last_posted_at' => now(),
        ]);

        $reply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'I can reproduce this on a 1440p monitor after docking and undocking.',
        ]);

        $this->actingAs($user)->post(route('forum.replies.react', [$category, $thread, $reply]), [
            'type' => 'like',
        ])->assertRedirect();

        $this->assertDatabaseHas('reactions', [
            'reactable_type' => ForumReply::class,
            'reactable_id' => $reply->id,
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        $this->actingAs($user)->post(route('forum.replies.react', [$category, $thread, $reply]), [
            'type' => 'like',
        ])->assertRedirect();

        $this->assertDatabaseMissing('reactions', [
            'reactable_type' => ForumReply::class,
            'reactable_id' => $reply->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_reaction_requests_can_return_json_without_redirecting(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Bugs',
            'slug' => 'bugs',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Thread for async reactions',
            'slug' => 'thread-for-async-reactions',
            'body' => 'Checking async reaction responses.',
            'last_posted_at' => now(),
        ]);

        $reply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Reply for async reaction checks.',
        ]);

        $this->actingAs($user)
            ->postJson(route('forum.threads.react', [$category, $thread]), [
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

        $this->actingAs($user)
            ->postJson(route('forum.replies.react', [$category, $thread, $reply]), [
                'type' => 'dislike',
            ])
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'currentReaction' => 'dislike',
                'counts' => [
                    'like' => 0,
                    'dislike' => 1,
                ],
            ]);
    }

    public function test_original_poster_can_lock_and_delete_their_thread(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Questions',
            'slug' => 'questions',
            'description' => 'Questions and support',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Can I lock my own thread?',
            'slug' => 'can-i-lock-my-own-thread',
            'body' => 'I want to close this after the answer is posted.',
            'last_posted_at' => now(),
        ]);

        $this->actingAs($user)->post(route('forum.threads.lock', [$category, $thread]))
            ->assertRedirect();

        $this->assertTrue($thread->fresh()->is_locked);

        $this->actingAs($user)->delete(route('forum.threads.destroy', [$category, $thread]))
            ->assertRedirect(route('forum.show', $category));

        $this->assertDatabaseMissing('forum_threads', [
            'id' => $thread->id,
        ]);
    }

    public function test_reply_author_can_delete_their_reply_without_deleting_child_replies(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Questions',
            'slug' => 'questions',
            'description' => 'Questions and support',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Reply delete test',
            'slug' => 'reply-delete-test',
            'body' => 'Testing reply deletion behavior.',
            'last_posted_at' => now(),
        ]);

        $reply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Parent reply to remove.',
        ]);

        $childReply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => $reply->id,
            'body' => 'Child reply that should remain.',
        ]);

        $this->actingAs($user)->delete(route('forum.replies.destroy', [$category, $thread, $reply]))
            ->assertRedirect();

        $this->assertDatabaseMissing('forum_replies', [
            'id' => $reply->id,
        ]);

        $this->assertDatabaseHas('forum_replies', [
            'id' => $childReply->id,
            'parent_id' => null,
        ]);
    }

    public function test_staff_can_manage_any_thread_and_reply_to_locked_threads(): void
    {
        Notification::fake();

        $author = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Bugs',
            'slug' => 'bugs',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $author->id,
            'title' => 'Thread staff should be able to manage',
            'slug' => 'thread-staff-should-be-able-to-manage',
            'body' => 'Moderators should be able to lock and reply here.',
            'last_posted_at' => now(),
            'is_locked' => true,
        ]);

        $this->actingAs($moderator)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'Staff can still reply even when the thread is locked.',
        ])->assertRedirect(route('forum.threads.show', [$category, $thread]));

        $this->assertDatabaseHas('forum_replies', [
            'forum_thread_id' => $thread->id,
            'user_id' => $moderator->id,
            'body' => 'Staff can still reply even when the thread is locked.',
        ]);

        $this->actingAs($moderator)->post(route('forum.threads.lock', [$category, $thread]))
            ->assertRedirect();

        $this->assertFalse($thread->fresh()->is_locked);
    }

    public function test_staff_can_delete_any_reply(): void
    {
        $author = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Bugs',
            'slug' => 'bugs',
            'description' => 'Bug reports and fixes',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $author->id,
            'title' => 'Staff reply delete test',
            'slug' => 'staff-reply-delete-test',
            'body' => 'Testing moderator delete access.',
            'last_posted_at' => now(),
        ]);

        $reply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $author->id,
            'body' => 'Reply the moderator will remove.',
        ]);

        $this->actingAs($moderator)->delete(route('forum.replies.destroy', [$category, $thread, $reply]))
            ->assertRedirect();

        $this->assertDatabaseMissing('forum_replies', [
            'id' => $reply->id,
        ]);
    }

    public function test_regular_users_cannot_manage_other_users_threads_or_reply_when_locked(): void
    {
        $author = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $otherUser = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Suggestions',
            'slug' => 'suggestions',
            'description' => 'Feature requests and ideas',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $author->id,
            'title' => 'Only the OP or staff should manage this',
            'slug' => 'only-the-op-or-staff-should-manage-this',
            'body' => 'This thread is not owned by the second user.',
            'last_posted_at' => now(),
            'is_locked' => true,
        ]);

        $this->actingAs($otherUser)->post(route('forum.threads.lock', [$category, $thread]))
            ->assertForbidden();

        $this->actingAs($otherUser)->delete(route('forum.threads.destroy', [$category, $thread]))
            ->assertForbidden();

        $this->actingAs($otherUser)->post(route('forum.replies.store', [$category, $thread]), [
            'body' => 'I should not be able to reply here.',
        ])->assertForbidden();
    }

    public function test_regular_users_cannot_delete_other_users_replies(): void
    {
        $author = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $otherUser = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $category = ForumCategory::query()->create([
            'name' => 'Suggestions',
            'slug' => 'suggestions',
            'description' => 'Feature requests and ideas',
            'accent_color' => '#666666',
            'sort_order' => 1,
        ]);

        $thread = ForumThread::query()->create([
            'forum_category_id' => $category->id,
            'user_id' => $author->id,
            'title' => 'Reply ownership test',
            'slug' => 'reply-ownership-test',
            'body' => 'Testing reply ownership permissions.',
            'last_posted_at' => now(),
        ]);

        $reply = ForumReply::query()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $author->id,
            'body' => 'Only the author or staff should remove this.',
        ]);

        $this->actingAs($otherUser)->delete(route('forum.replies.destroy', [$category, $thread, $reply]))
            ->assertForbidden();

        $this->assertDatabaseHas('forum_replies', [
            'id' => $reply->id,
        ]);
    }
}
