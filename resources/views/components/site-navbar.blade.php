<div>
    @php
        $downloadActive = request()->routeIs('download.*');
        $newsActive = request()->routeIs('news.*');
        $forumActive = request()->routeIs('forum.*');
    @endphp

    <nav
        x-data="{ open: false, scrolled: window.scrollY > 20 }"
        @scroll.window="scrolled = window.scrollY > 20"
        class="fixed inset-x-0 top-0 z-[70] isolate"
    >
        <div class="mx-auto w-full max-w-6xl px-5 pt-0 sm:px-8 lg:px-10">
            <div
                class="site-navbar__frame"
                :style="scrolled
                    ? 'background: var(--nav-glass); backdrop-filter: blur(16px); box-shadow: var(--nav-shadow); border-color: var(--border-subtle);'
                    : 'background: transparent; backdrop-filter: none; box-shadow: none; border-color: transparent;'"
            >
            <div class="h-14 w-full" style="display: flex; width: 100%; justify-content: space-between; gap: 1rem;">
                <div class="flex shrink-0 items-center">
                    <a
                        href="{{ route('home') }}"
                        class="site-navbar-brand"
                        :class="{ 'site-navbar-brand--scrolled': scrolled }"
                        aria-label="{{ config('app.name', 'Pelicon') }}"
                    >
                        <img src="{{ asset('assets/peli.svg') }}" alt="" class="site-navbar-brand__icon" aria-hidden="true">
                        <span class="site-navbar-brand__text" aria-hidden="true">
                            <span>e</span><span>l</span><span>i</span><span>c</span><span>o</span><span>n</span>
                        </span>
                    </a>
                </div>

                <div class="hidden sm:flex sm:items-center" style="margin-left: auto; gap: 0.55rem;">
                    <div class="hidden items-center gap-1.5 sm:flex">
                        <a
                            href="{{ route('download.index') }}"
                            class="site-nav-button"
                            :style="scrolled
                                ? 'background: var(--brand-green); color: #13210f; box-shadow: inset 0 0 0 1px rgba(31, 37, 29, 0.06);'
                                : '{{ $downloadActive ? 'background: var(--accent-soft); color: var(--text-strong);' : 'color: var(--text-muted);' }}'"
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
                        <a href="{{ route('login') }}" class="site-nav-button button-auth">
                            {{ __('Sign in') }}
                        </a>
                    @endauth
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center rounded-xl p-2 transition"
                        style="color: var(--text-muted);">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
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
            </div>
        </div>
    </nav>

    <div aria-hidden="true" class="h-20"></div>
</div>
