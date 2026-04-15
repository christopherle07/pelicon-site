<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="section-kicker">Admin</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-[color:var(--text-strong)]">News</h1>
            </div>

            <a href="{{ route('admin.news.create') }}" class="button-primary inline-flex px-5 py-3 text-sm font-semibold transition">
                New announcement
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="flash-toast surface-panel p-5 text-sm font-medium" data-auto-dismiss="5000" style="color: var(--success);">
                    {{ session('status') }}
                </div>
            @endif

            <div class="surface-panel overflow-hidden">
                <div class="grid grid-cols-[1.4fr_0.8fr_0.7fr_0.8fr] gap-4 px-6 py-4 text-xs font-bold uppercase tracking-[0.16em] copy-faint">
                    <span>Title</span>
                    <span>Status</span>
                    <span>Updated</span>
                    <span>Actions</span>
                </div>

                @forelse ($announcements as $announcement)
                    <div class="grid grid-cols-[1.4fr_0.8fr_0.7fr_0.8fr] gap-4 border-t px-6 py-5" style="border-color: var(--border-subtle);">
                        <div>
                            <p class="font-semibold text-[color:var(--text-strong)]">{{ $announcement->title }}</p>
                            <p class="copy-faint mt-2 text-sm">{{ $announcement->excerpt }}</p>
                        </div>

                        <div class="text-sm">
                            <span class="inline-flex px-3 py-2 font-semibold" style="background: var(--bg-elevated); color: var(--text-strong);">
                                {{ ucfirst($announcement->status) }}
                            </span>
                        </div>

                        <div class="copy-faint text-sm">
                            {{ $announcement->updated_at->diffForHumans() }}
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            <a href="{{ route('admin.news.edit', $announcement) }}" class="font-semibold text-[color:var(--accent-strong)]">Edit</a>
                            @if ($announcement->status === 'published')
                                <a href="{{ route('news.show', $announcement) }}" class="font-semibold text-[color:var(--accent-strong)]">View</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-sm leading-7 copy-muted">
                        No announcements yet.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
