<x-public-layout title="{{ $platform['name'] }} Download - {{ config('app.name', 'Pelicon') }}">
    @php
        $initialCurrency = $currencies[$defaultCurrency];
        $initialTip = $initialCurrency['tips'][1] ?? $initialCurrency['tips'][0] ?? null;
        $initialBusinessPrice = $initialCurrency['symbol'].$initialCurrency['business_onetime'].' per 3 users';
    @endphp

    <div
        class="space-y-10"
        data-download-page
        data-default-currency="{{ $defaultCurrency }}"
        data-default-tip="{{ $initialTip }}"
        data-currencies='@json($currencies)'
    >
        <div class="copy-faint text-sm">
            <a href="{{ route('download.index') }}" class="transition hover:text-[color:var(--text-strong)]">Download</a>
            <span class="mx-2">/</span>
            <span>{{ $platform['name'] }}</span>
        </div>

        <section class="surface-panel p-8 sm:p-10">
            <p class="section-kicker">{{ $platform['name'] }}</p>
            <h1 class="title-hero mt-3 text-4xl sm:text-5xl">{{ $platform['headline'] }}</h1>
            <p class="copy-muted mt-4 max-w-3xl text-base leading-8">
                {{ $platform['copy'] }}
            </p>

            <div class="mt-8">
                <p class="section-kicker">Currency</p>
                <div class="mt-4 max-w-sm">
                    <label for="download_currency" class="sr-only">Currency</label>
                    <div class="select-shell">
                        <select id="download_currency" class="select-input" data-currency-select>
                            @foreach ($currencies as $key => $currency)
                                <option value="{{ $key }}" @selected($key === $defaultCurrency)>{{ $currency['code'] }} - {{ $currency['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <p class="copy-faint mt-4 text-sm leading-7">
                    Display pricing is rounded for readability and local fit. Final checkout pricing can later be locked in directly in Stripe.
                </p>
            </div>
        </section>

        <section class="grid gap-8 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="grid gap-8 md:grid-cols-2">
                <article class="surface-panel p-8">
                    <p class="section-kicker">Personal</p>
                    <h2 class="title-section mt-3 text-3xl">Free</h2>
                    <p class="copy-muted mt-4 text-sm leading-7">
                        For personal use for personal projects.
                    </p>

                    <div class="mt-10 space-y-3 text-sm leading-7">
                        <div class="bg-[color:var(--bg-elevated)] px-4 py-3">
                            Personal use
                        </div>
                        <div class="bg-[color:var(--bg-elevated)] px-4 py-3">
                            Single user
                        </div>
                        <div class="bg-[color:var(--bg-elevated)] px-4 py-3">
                            Community forum support
                        </div>
                    </div>

                    <button type="button" class="button-primary mt-10 inline-flex px-6 py-3 text-sm font-semibold transition">
                        Free
                    </button>
                </article>

                <article class="download-business-card p-8">
                    <p class="section-kicker">Business</p>
                    <h2 class="title-section mt-3 text-3xl" data-business-price>{{ $initialBusinessPrice }}</h2>
                    <p class="copy-muted mt-4 text-sm leading-7">
                        Business use for commercial projects.
                    </p>

                    <div class="mt-10 space-y-3 text-sm leading-7">
                        <div class="download-business-card__feature px-4 py-3">
                            Commercial use
                        </div>
                        <div class="download-business-card__feature px-4 py-3">
                            Up to 3 users
                        </div>
                        <div class="download-business-card__feature px-4 py-3">
                            Direct support
                        </div>
                    </div>

                    <div class="download-business-card__checkout mt-10 space-y-5 p-5" data-business-checkout>
                        <div>
                            <p class="section-kicker">Checkout</p>
                            <h3 class="mt-2 text-xl font-bold text-[color:var(--text-strong)]" data-checkout-title>Business one-time license</h3>
                            <p class="copy-muted mt-3 text-sm leading-7" data-checkout-copy>
                                Review the current plan, then continue into the payment step.
                            </p>
                        </div>

                        <div class="space-y-3 text-sm leading-7">
                            <div class="flex items-center justify-between gap-4">
                                <span class="copy-muted">Plan</span>
                                <span class="font-semibold text-[color:var(--text-strong)]" data-checkout-plan>One-time</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="copy-muted">Currency</span>
                                <span class="font-semibold text-[color:var(--text-strong)]" data-checkout-currency>{{ $initialCurrency['code'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="copy-muted">Price</span>
                                <span class="font-semibold text-[color:var(--text-strong)]" data-checkout-price>{{ $initialBusinessPrice }}</span>
                            </div>
                        </div>

                        <button type="button" class="button-primary inline-flex w-full items-center justify-center px-6 py-3 text-sm font-semibold transition" data-open-payment-modal>
                            Buy
                        </button>
                    </div>
                </article>
            </div>

            <aside class="surface-panel-alt p-8 sm:p-10">
                <p class="section-kicker">Tip Jar</p>
                <h2 class="title-section mt-3 text-3xl">Optional support.</h2>
                <p class="copy-muted mt-4 text-sm leading-7">
                    Want to support the development of this project?
                </p>

                <form class="mt-10 space-y-6">
                    <fieldset>
                        <legend class="text-sm font-semibold text-[color:var(--text-strong)]">Choose an amount</legend>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2" data-tip-options>
                            @foreach ($initialCurrency['tips'] as $amount)
                                <button
                                    type="button"
                                    class="tip-chip {{ $initialTip === $amount ? 'tip-chip--active' : '' }}"
                                    data-tip-button="{{ $amount }}"
                                >
                                    <span class="tip-chip__control" aria-hidden="true"></span>
                                    <span>{{ $initialCurrency['symbol'] }}{{ $amount }}</span>
                                </button>
                            @endforeach

                            <button
                                type="button"
                                class="tip-chip sm:col-span-2"
                                data-tip-button="custom"
                            >
                                <span class="tip-chip__control" aria-hidden="true"></span>
                                <span>Custom</span>
                            </button>
                        </div>

                        <div class="mt-4 hidden" data-custom-tip-wrap>
                            <div class="flex items-center gap-3">
                                <span class="copy-faint text-sm font-semibold" data-custom-tip-symbol>{{ $initialCurrency['symbol'] }}</span>
                                <input type="number" min="1" step="{{ $initialCurrency['custom_step'] }}" placeholder="Enter amount" class="block w-full px-4 py-3 focus:outline-none focus:ring-2" style="border-color: var(--border-strong); background: var(--bg-elevated); color: var(--text-strong);" data-custom-tip-input>
                            </div>
                        </div>
                    </fieldset>

                    <button type="button" class="button-secondary inline-flex w-full items-center justify-center px-6 py-3 text-sm font-semibold transition">
                        Continue to tip checkout later
                    </button>
                </form>

                <div class="mt-10 bg-[color:var(--bg-elevated)] px-5 py-4 text-sm leading-7 copy-muted">
                    Selected tip: <span class="font-semibold text-[color:var(--text-strong)]" data-tip-summary>{{ $initialCurrency['symbol'] }}{{ $initialTip }}</span>. The payment logic is intentionally not live yet. This page is only setting the structure for the real Stripe flow later.
                </div>
            </aside>
        </section>

        <section class="surface-panel p-8 sm:p-10">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="section-kicker">Other Platforms</p>
                    <p class="copy-muted mt-3 text-sm leading-7">
                        Need a different build? Jump directly to another platform page.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @foreach ($platforms as $key => $item)
                        @continue($key === $platformKey)

                        <a href="{{ route('download.show', $key) }}" class="button-secondary inline-flex px-5 py-3 text-sm font-semibold transition">
                            {{ $item['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="modal-shell hidden" data-payment-modal>
            <div class="modal-backdrop" data-close-payment-modal></div>
            <div class="modal-panel p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="section-kicker">Stripe Checkout</p>
                        <h2 class="title-section mt-3 text-3xl">Payment modal placeholder</h2>
                    </div>

                    <button type="button" class="button-secondary inline-flex h-10 w-10 items-center justify-center text-lg font-semibold transition" data-close-payment-modal aria-label="Close payment modal">
                        ×
                    </button>
                </div>

                <div class="mt-6 space-y-4 text-sm leading-7">
                    <p class="copy-muted">
                        This is where the Stripe payment modal will open later. The selected plan below is already being passed through the page state.
                    </p>

                    <div class="bg-[color:var(--bg-elevated)] px-5 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <span class="copy-muted">Selected plan</span>
                            <span class="font-semibold text-[color:var(--text-strong)]" data-modal-plan>One-time</span>
                        </div>
                    </div>

                    <div class="bg-[color:var(--bg-elevated)] px-5 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <span class="copy-muted">Selected price</span>
                            <span class="font-semibold text-[color:var(--text-strong)]" data-modal-price>{{ $initialBusinessPrice }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" class="button-secondary inline-flex px-6 py-3 text-sm font-semibold transition" data-close-payment-modal>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
