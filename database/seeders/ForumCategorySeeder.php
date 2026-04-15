<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bugs',
                'slug' => 'bugs',
                'description' => 'Crash reports, broken behavior, and reproducible issues.',
                'accent_color' => '#c2410c',
                'sort_order' => 1,
            ],
            [
                'name' => 'Suggestions',
                'slug' => 'suggestions',
                'description' => 'Feature requests, workflow ideas, and quality-of-life improvements.',
                'accent_color' => '#1d4ed8',
                'sort_order' => 2,
            ],
            [
                'name' => 'Questions',
                'slug' => 'questions',
                'description' => 'Setup help, usage questions, and answers from the team and community.',
                'accent_color' => '#15803d',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
