<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\AnnouncementComment;
use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocalDemoContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $admin = $this->upsertUser(
            name: 'PeliconAdmin',
            email: 'admin@pelicon.local',
            role: UserRole::Admin,
        );

        $moderator = $this->upsertUser(
            name: 'MinaMod',
            email: 'moderator@pelicon.local',
            role: UserRole::Moderator,
        );

        $member = $this->upsertUser(
            name: 'KaiArtist',
            email: 'kai@pelicon.local',
            role: UserRole::User,
        );

        $categories = ForumCategory::query()->get()->keyBy('slug');

        $announcement = Announcement::query()->updateOrCreate(
            ['slug' => 'pelicon-local-preview-build-notes'],
            [
                'user_id' => $admin->id,
                'title' => 'Local preview build: forum, downloads, and a cleaner light theme',
                'excerpt' => 'A preview post for local development so the homepage and news pages are not empty while styling.',
                'body' => <<<HTML
<p>This announcement exists only to make the local build feel real while you work on the public pages.</p>
<p>Current preview items include the direct-download flow, a calmer warm light palette, and the forum refresh.</p>
<p>Use this as throwaway sample content while iterating on spacing, cards, and thread presentation.</p>
HTML,
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
        );

        AnnouncementComment::query()->firstOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id' => $moderator->id,
                'body' => 'This is enough content to keep the homepage and news section from feeling empty in local.',
            ],
        );

        $this->react($announcement, $moderator, 'like');
        $this->react($announcement, $member, 'like');

        $sampleThreads = [
            [
                'category' => 'bugs',
                'author' => $member,
                'title' => 'Canvas thumbnails stutter when I scroll a board with a lot of images',
                'slug' => 'canvas-thumbnails-stutter-on-large-boards',
                'body' => 'Once a board gets into the 300 to 500 image range, scrolling starts to hitch and some thumbnails pop in late. It feels worse after dragging a few groups around.',
                'is_pinned' => true,
                'view_count' => 142,
                'last_posted_at' => now()->subHours(4),
                'replies' => [
                    [
                        'author' => $moderator,
                        'body' => 'We can reproduce this locally with a dense reference board. It looks tied to thumbnail re-layout after drag events.',
                    ],
                    [
                        'author' => $admin,
                        'body' => 'A render batching pass is already in progress. Keep this thread around as the local preview bug example for now.',
                    ],
                ],
            ],
            [
                'category' => 'suggestions',
                'author' => $member,
                'title' => 'A compact sidebar mode would make sorting references way faster',
                'slug' => 'compact-sidebar-mode-for-faster-sorting',
                'body' => 'It would help to have a denser sidebar option with smaller previews and less padding so I can sort folders faster without the UI feeling oversized.',
                'view_count' => 88,
                'last_posted_at' => now()->subHours(9),
                'replies' => [
                    [
                        'author' => $admin,
                        'body' => 'This is a strong candidate for the next UI pass. A compact density toggle would also help laptop screens a lot.',
                    ],
                ],
            ],
            [
                'category' => 'questions',
                'author' => $moderator,
                'title' => 'What is the best folder structure for big character reference libraries?',
                'slug' => 'best-folder-structure-for-character-reference-libraries',
                'body' => 'Curious how people are grouping pose, costume, material, and face references. Right now I am splitting by project first, but it gets messy pretty quickly.',
                'view_count' => 57,
                'last_posted_at' => now()->subDay(),
                'replies' => [
                    [
                        'author' => $member,
                        'body' => 'I ended up using project folders with shared tags for pose and material. That kept the folders cleaner than duplicating images everywhere.',
                    ],
                ],
            ],
            [
                'category' => 'bugs',
                'author' => $admin,
                'title' => 'Dragging images between groups sometimes leaves the old slot highlighted',
                'slug' => 'dragging-between-groups-leaves-old-slot-highlighted',
                'body' => 'The move still works, but the origin slot keeps its highlight until the next hover change. This looks cosmetic, but it makes the board feel less polished.',
                'view_count' => 34,
                'last_posted_at' => now()->subDays(2),
                'replies' => [],
            ],
        ];

        foreach ($sampleThreads as $threadData) {
            $category = $categories->get($threadData['category']);

            if (! $category) {
                continue;
            }

            $thread = ForumThread::query()->updateOrCreate(
                ['slug' => $threadData['slug']],
                [
                    'forum_category_id' => $category->id,
                    'user_id' => $threadData['author']->id,
                    'title' => $threadData['title'],
                    'body' => $threadData['body'],
                    'is_pinned' => $threadData['is_pinned'] ?? false,
                    'is_locked' => false,
                    'view_count' => $threadData['view_count'],
                    'last_posted_at' => $threadData['last_posted_at'],
                ],
            );

            foreach ($threadData['replies'] as $replyData) {
                ForumReply::query()->firstOrCreate(
                    [
                        'forum_thread_id' => $thread->id,
                        'user_id' => $replyData['author']->id,
                        'body' => $replyData['body'],
                    ],
                );
            }

            $this->react($thread, $admin, 'like');
            $this->react($thread, $moderator, 'like');

            if (($threadData['category'] ?? null) === 'bugs') {
                $this->react($thread, $member, 'dislike');
            }
        }
    }

    private function upsertUser(string $name, string $email, UserRole $role): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => $role->value,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }

    private function react($reactable, User $user, string $type): void
    {
        $reactable->reactions()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'type' => $type,
            ],
        );
    }
}
