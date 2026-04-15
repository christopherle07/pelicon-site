<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="section-kicker">Admin</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-[color:var(--text-strong)]">Control panel</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'staff-two-factor-required')
                <div class="flash-toast rounded-3xl border px-5 py-4 text-sm" data-auto-dismiss="5000" style="border-color: rgba(255, 200, 87, 0.25); background: rgba(255, 200, 87, 0.12); color: #ffd98b;">
                    Staff accounts are required to enable two-factor authentication before using admin routes.
                </div>
            @endif

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
                <h2 class="text-lg font-bold text-[color:var(--text-strong)]">What is ready</h2>
                <ul class="copy-base mt-4 space-y-3 text-sm leading-7">
                    <li>Public home, news, and forum pages are wired to real models and routes.</li>
                    <li>Staff-only admin access is protected by login, email verification, and required 2FA.</li>
                    <li>User roles are in place for `Admin`, `Moderator`, and regular `User` accounts.</li>
                </ul>

                @if (auth()->user()?->isAdmin())
                    <div class="mt-6">
                        <a href="{{ route('admin.news.index') }}" class="button-secondary inline-flex px-5 py-3 text-sm font-semibold transition">
                            Manage news
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
