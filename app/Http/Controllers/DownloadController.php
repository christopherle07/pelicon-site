<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DownloadController extends Controller
{
    /**
     * @var array<string, array<string, string>>
     */
    private const PLATFORM_FILES = [
        'macos' => [
            'name' => 'macOS',
            'copy' => 'Placeholder build for Apple Silicon and Intel Macs.',
            'filename' => 'pelicon-macos-placeholder.txt',
        ],
        'windows' => [
            'name' => 'Windows',
            'copy' => 'Placeholder build for Windows desktops and laptops.',
            'filename' => 'pelicon-windows-placeholder.txt',
        ],
    ];

    /**
     * @return array<string, array<string, string>>
     */
    public static function platforms(): array
    {
        return collect(self::PLATFORM_FILES)
            ->map(fn (array $platform) => [
                ...$platform,
                'download_url' => asset('downloads/'.$platform['filename']),
            ])
            ->all();
    }

    public function index(): View
    {
        return view('pages.download.index', [
            'platforms' => self::platforms(),
        ]);
    }
}
