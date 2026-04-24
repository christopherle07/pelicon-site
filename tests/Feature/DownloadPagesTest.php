<?php

namespace Tests\Feature;

use Tests\TestCase;

class DownloadPagesTest extends TestCase
{
    public function test_download_index_page_loads(): void
    {
        $this->get(route('download.index'))
            ->assertOk()
            ->assertSee('Download Pelicon')
            ->assertSee('macOS')
            ->assertSee('Tip');
    }
}
