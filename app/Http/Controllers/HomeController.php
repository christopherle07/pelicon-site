<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'downloadPlatforms' => DownloadController::platforms(),
            'latestAnnouncement' => Announcement::query()
                ->published()
                ->with('author')
                ->withCount(['comments', 'reactions'])
                ->latest('published_at')
                ->first(),
        ]);
    }
}
