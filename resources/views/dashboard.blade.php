<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="section-kicker">Account</p>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-[color:var(--text-strong)]">{{ __('Dashboard') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="surface-panel p-6">
                <h3 class="text-lg font-bold text-[color:var(--text-strong)]">Welcome back</h3>
                <p class="copy-base mt-3 max-w-2xl text-sm leading-7">
                    This is your account hub for settings, password changes, email verification, and optional two-factor authentication.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('home') }}" class="button-secondary px-5 py-3 text-sm font-semibold">
                        Back to site
                    </a>

                    <a href="{{ route('settings') }}" class="button-primary px-5 py-3 text-sm font-semibold">
                        Open settings
                    </a>

                    @if (auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="button-secondary px-5 py-3 text-sm font-semibold">
                            Open admin
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
