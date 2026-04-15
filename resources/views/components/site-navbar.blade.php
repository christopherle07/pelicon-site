<div>
    @php
        $downloadActive = request()->routeIs('download.*');
        $newsActive = request()->routeIs('news.*');
        $forumActive = request()->routeIs('forum.*');
    @endphp

    <nav
        x-data="{ open: false, scrolled: window.scrollY > 0 }"
        @scroll.window="scrolled = window.scrollY > 0"
        class="fixed inset-x-0 top-0 z-[70] isolate transition-all duration-300"
        :style="scrolled
            ? 'background: rgba(24, 24, 24, 0.92); backdrop-filter: blur(18px); box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);'
            : 'background: rgba(21, 21, 21, 0.16); backdrop-filter: blur(10px); box-shadow: none;'"
    >
        <div class="mx-auto w-full max-w-7xl px-5 sm:px-8 lg:px-10">
            <div class="h-16 w-full" style="display: flex; width: 100%; justify-content: space-between;">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('home') }}" class="font-display text-lg font-bold tracking-tight text-[color:var(--text-strong)]">
                        {{ config('app.name', 'Pelicon') }}
                    </a>
                </div>

                <div class="hidden sm:ms-6 sm:flex sm:items-stretch" style="margin-left: auto; gap: 1.5rem;">
                    <div class="hidden space-x-8 sm:-my-px sm:flex">
                        <a
                            href="{{ route('download.index') }}"
                            class="inline-flex h-full items-center border-b-2 px-4 text-sm font-medium leading-5 transition duration-200 ease-in-out"
                            :style="scrolled
                                ? 'border-color: transparent; background: #b91c1c; color: #fff5f5;'
                                : '{{ $downloadActive ? 'border-color: var(--accent); color: var(--text-strong); background: transparent;' : 'border-color: transparent; color: var(--text-muted); background: transparent;' }}'"
                        >
                            {{ __('Download') }}
                        </a>

                        <x-nav-link href="{{ route('news.index') }}" :active="$newsActive">
                            {{ __('News') }}
                        </x-nav-link>

                        <x-nav-link href="{{ route('forum.index') }}" :active="$forumActive">
                            {{ __('Forum') }}
                        </x-nav-link>
                    </div>

                    @auth
                        <div class="relative flex h-full items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded-full">
                                        <button
                                            type="button"
                                            class="account-trigger"
                                            aria-label="Open account menu for {{ Auth::user()->name }}"
                                            title="{{ Auth::user()->name }}"
                                        >
                                            <span class="account-trigger__initial" aria-hidden="true">
                                                {{ str(Auth::user()->name)->trim()->substr(0, 1)->upper() }}
                                            </span>
                                            <span class="sr-only">{{ Auth::user()->name }}</span>
                                        </button>
                                    </span>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="block px-4 py-2 text-xs" style="color: var(--text-faint);">
                                        {{ __('Manage Account') }}
                                    </div>

                                    <x-dropdown-link href="{{ route('dashboard') }}">
                                        {{ __('Dashboard') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link href="{{ route('settings') }}">
                                        {{ __('Settings') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link href="{{ route('users.show', ['user' => Auth::user()->name]) }}">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    @if (auth()->user()->isStaff())
                                        <x-dropdown-link href="{{ route('admin.dashboard') }}">
                                            {{ __('Admin') }}
                                        </x-dropdown-link>
                                    @endif

                                    <div class="border-t" style="border-color: var(--border-subtle);"></div>

                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf

                                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="button-auth inline-flex h-full items-center px-4 text-sm font-medium transition">
                            {{ __('Sign in') }}
                        </a>
                    @endauth
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 transition"
                        style="color: var(--text-muted);">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="space-y-1 pt-2 pb-3">
                <x-responsive-nav-link href="{{ route('download.index') }}" :active="request()->routeIs('download.*')">
                    {{ __('Download') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('news.index') }}" :active="request()->routeIs('news.*')">
                    {{ __('News') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('forum.index') }}" :active="request()->routeIs('forum.*')">
                    {{ __('Forum') }}
                </x-responsive-nav-link>
            </div>

            @auth
                <div class="border-t pt-4 pb-1" style="border-color: var(--border-subtle);">
                    <div class="px-4">
                        <div class="font-medium text-base text-[color:var(--text-strong)]">{{ Auth::user()->name }}</div>
                        <div class="copy-faint font-medium text-sm">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('profile.show') || request()->routeIs('settings')">
                            {{ __('Settings') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('users.show', ['user' => Auth::user()->name]) }}" :active="request()->routeIs('users.show') && request()->route('user')?->is(auth()->user())">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>

                        @if (auth()->user()->isStaff())
                            <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')">
                                {{ __('Admin') }}
                            </x-responsive-nav-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf

                            <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            @else
                <div class="border-t pt-4 pb-1" style="border-color: var(--border-subtle);">
                    <div class="space-y-1">
                        <x-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                            {{ __('Sign in') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <div aria-hidden="true" class="h-16"></div>
</div>
