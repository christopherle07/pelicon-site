<div>
    @php
        $downloadActive = request()->routeIs('download.*');
        $contactActive = request()->routeIs('contact');
        $faqActive = request()->routeIs('faq');
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
                        <a
                            href="{{ route('download.index') }}"
                            class="site-nav-button"
                            :style="scrolled
                                ? 'background: var(--brand-green); color: #13210f; box-shadow: inset 0 0 0 1px rgba(31, 37, 29, 0.06);'
                                : '{{ $downloadActive ? 'background: var(--accent-soft); color: var(--text-strong);' : 'color: var(--text-muted);' }}'"
                        >
                            {{ __('Download') }}
                        </a>

                        <x-nav-link href="{{ route('faq') }}" :active="$faqActive">
                            {{ __('FAQ') }}
                        </x-nav-link>

                        <x-nav-link href="{{ route('contact') }}" :active="$contactActive">
                            {{ __('Contact') }}
                        </x-nav-link>
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
                        <x-responsive-nav-link href="{{ route('download.index') }}" :active="$downloadActive">
                            {{ __('Download') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('faq') }}" :active="$faqActive">
                            {{ __('FAQ') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('contact') }}" :active="$contactActive">
                            {{ __('Contact') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div aria-hidden="true" class="h-20"></div>
</div>
