<x-public-layout title="Apps - {{ config('app.name', 'Pelicon') }}">
    <div class="download-page">
        <section class="download-stack">
            <article
                x-data="{ selectedPlatform: null, hasSelectedPlatform: false }"
                class="download-card-shell w-full"
            >
                <h1 class="download-page-title">Pelicon Apps</h1>

                <div class="platform-picker" aria-label="Operating systems">
                    <span
                        x-cloak
                        x-show="selectedPlatform"
                        class="platform-picker__indicator"
                        :style="selectedPlatform === 'windows' ? 'transform: translateX(100%);' : 'transform: translateX(0);'"
                        aria-hidden="true"
                    ></span>

                    @foreach ($platforms as $key => $platform)
                        <button
                            type="button"
                            class="platform-picker__button"
                            :class="{ 'platform-picker__button--active': selectedPlatform === '{{ $key }}' }"
                            @click="hasSelectedPlatform = selectedPlatform === null; selectedPlatform = '{{ $key }}'"
                        >
                            <img
                                src="{{ asset('assets/'.($key === 'macos' ? 'mac.svg' : 'windows.svg')) }}"
                                alt=""
                                class="platform-picker__icon"
                                aria-hidden="true"
                            >
                            <span>{{ $platform['name'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="app-selection-panel">
                    <p x-show="! selectedPlatform" class="app-selection-panel__empty">
                        Choose an operating system.
                    </p>

                    @foreach ($platforms as $key => $platform)
                        <div
                            x-cloak
                            x-show="selectedPlatform === '{{ $key }}'"
                            :class="{ 'app-download-list--pop': hasSelectedPlatform && selectedPlatform === '{{ $key }}' }"
                            @animationend="hasSelectedPlatform = false"
                            class="app-download-list"
                        >
                            <a
                                href="{{ $platform['download_url'] }}"
                                download="{{ $platform['filename'] }}"
                                class="app-download-card"
                            >
                                <span>
                                    <span class="app-download-card__name">Pelicon Boards</span>
                                    <span class="app-download-card__meta">Blue Jay (Alpha)</span>
                                </span>
                                <span class="app-download-card__action">Download</span>
                            </a>

                            <article class="app-download-card app-download-card--disabled" aria-disabled="true">
                                <span>
                                    <span class="app-download-card__name">Pelicon Cast</span>
                                    <span class="app-download-card__meta">{{ $platform['name'] }}</span>
                                </span>
                                <span class="app-download-card__status">In development</span>
                            </article>

                            <article class="app-download-card app-download-card--disabled" aria-disabled="true">
                                <span>
                                    <span class="app-download-card__name">Pelicon Write</span>
                                    <span class="app-download-card__meta">{{ $platform['name'] }}</span>
                                </span>
                                <span class="app-download-card__status">In development</span>
                            </article>
                        </div>
                    @endforeach
                </div>
            </article>

            <aside
                x-data="{ supportOpen: false }"
                @keydown.escape.window="supportOpen = false"
                class="tip-jar-card"
            >
                <button
                    type="button"
                    class="support-us-button"
                    @click="supportOpen = true"
                >
                    Support us
                </button>

                <a
                    href="https://ko-fi.com/peliconapp"
                    class="support-page-link"
                    target="_blank"
                    rel="noreferrer"
                >
                    Our Ko-Fi Page
                </a>

                <div
                    x-cloak
                    x-show="supportOpen"
                    x-transition.opacity
                    class="support-modal"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Support Pelicon"
                >
                    <button
                        type="button"
                        class="support-modal__backdrop"
                        aria-label="Close support form"
                        @click="supportOpen = false"
                    ></button>

                    <div
                        x-show="supportOpen"
                        x-transition
                        class="support-modal__panel"
                    >
                        <button
                            type="button"
                            class="support-modal__close"
                            aria-label="Close support form"
                            @click="supportOpen = false"
                        >
                            &times;
                        </button>

                        <template x-if="supportOpen">
                            <iframe
                                id="kofiframe"
                                class="ko-fi-embed"
                                src="https://ko-fi.com/peliconapp/?hidefeed=true&widget=true&embed=true&preview=true"
                                height="620"
                                title="peliconapp"
                            ></iframe>
                        </template>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</x-public-layout>
