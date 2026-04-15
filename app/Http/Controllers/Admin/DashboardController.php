<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'announcementCount' => Announcement::count(),
            'publishedAnnouncementCount' => Announcement::where('status', 'published')->count(),
            'threadCount' => ForumThread::count(),
            'staffCount' => User::query()->whereIn('role', ['admin', 'moderator'])->count(),
        ]);
    }
}
