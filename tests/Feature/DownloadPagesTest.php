<?php

namespace Tests\Feature;

use Tests\TestCase;

class DownloadPagesTest extends TestCase
{
    public function test_download_index_page_loads(): void
    {
        $this->get(route('download.index'))
            ->assertOk()
            ->assertSee('Choose your platform.');
    }

    public function test_platform_download_pages_load(): void
    {
        foreach (['macos', 'windows', 'linux'] as $platform) {
            $this->get(route('download.show', $platform))
                ->assertOk()
                ->assertSee('Tip Jar');
        }
    }
}
