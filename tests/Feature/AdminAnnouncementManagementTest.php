<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_publish_an_announcement(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Pelicon 0.4 patch notes',
            'excerpt' => '',
            'body' => '<p><strong>Important</strong> update <span style="color: #ff0000; font-size: 1.25rem;">today</span><script>alert(1)</script></p>',
            'cover_image_url' => 'https://example.com/cover.png',
            'embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'published',
        ]);

        $announcement = Announcement::query()->firstOrFail();

        $response->assertRedirect(route('admin.news.edit', $announcement));

        $announcement = $announcement->fresh();

        $this->assertSame('published', $announcement->status);
        $this->assertNotNull($announcement->published_at);
        $this->assertNotEmpty($announcement->excerpt);
        $this->assertStringContainsString('<strong>Important</strong>', $announcement->body);
        $this->assertStringNotContainsString('<script>', $announcement->body);
    }

    public function test_moderators_cannot_access_news_management(): void
    {
        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
            'email_verified_at' => now(),
            'two_factor_secret' => 'staff-secret',
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($moderator)
            ->get(route('admin.news.index'))
            ->assertForbidden();
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
            'two_factor_secret' => 'admin-secret',
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
