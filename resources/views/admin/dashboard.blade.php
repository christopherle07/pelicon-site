<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="surface-panel rounded-3xl p-5">
                    <p class="copy-faint text-sm">Announcements</p>
                    <p class="mt-3 text-3xl font-bold text-[color:var(--text-strong)]">{{ $announcementCount }}</p>
                </div>
                <div class="surface-panel rounded-3xl p-5">
                    <p class="copy-faint text-sm">Published</p>
                    <p class="mt-3 text-3xl font-bold text-[color:var(--text-strong)]">{{ $publishedAnnouncementCount }}</p>
                </div>
                <div class="surface-panel rounded-3xl p-5">
                    <p class="copy-faint text-sm">Threads</p>
                    <p class="mt-3 text-3xl font-bold text-[color:var(--text-strong)]">{{ $threadCount }}</p>
                </div>
                <div class="surface-panel rounded-3xl p-5">
                    <p class="copy-faint text-sm">Staff</p>
                    <p class="mt-3 text-3xl font-bold text-[color:var(--text-strong)]">{{ $staffCount }}</p>
                </div>
            </div>

            <div class="surface-panel rounded-3xl p-6">
                @if (auth()->user()?->isAdmin())
                    <a href="{{ route('admin.news.index') }}" class="button-primary inline-flex px-5 py-3 text-sm font-semibold transition">
                        Manage news
                    </a>
                @else
                    <p class="copy-muted text-sm leading-7">
                        Moderator access is active. Announcement management is limited to admins.
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
