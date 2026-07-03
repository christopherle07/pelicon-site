<x-public-layout title="FAQ - {{ config('app.name', 'Pelicon') }}">
    <section class="surface-panel max-w-4xl p-8 sm:p-10">
        <p class="section-kicker">Help</p>
        <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Frequently Asked Questions</h1>

        <div class="mt-8 divide-y divide-[var(--border)]">

            @foreach ([
                ['q' => 'What is this app?', 'a' => 'A local-first reference board for organizing images, ideas, and inspiration.'],
                ['q' => 'Is it free?', 'a' => 'Yes. The app is free to use. Donations are optional and help support development.'],
                ['q' => 'Where are my files stored?', 'a' => "Everything is stored locally on your device. We don't store or access your boards, images, or files."],
                ['q' => 'Is there cloud-sync?', 'a' => 'No. This is not a cloud-based service. We do not provide a way to sync your workspaces or assets across devices. Currently.'],
                ['q' => 'Do I need an account?', 'a' => 'No. Pelicon works without an account, and this website no longer has account features.'],
                ['q' => 'What\'s the app built with?', 'a' => 'The app is built with Tauri, using Rust under the hood with a React and TypeScript frontend styled with Tailwind. The website is built with PHP and Laravel Blade.'],
            ] as $item)
                <div x-data="{ open: false }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="copy-base flex w-full items-center justify-between gap-4 py-5 text-left text-base font-semibold"
                        :aria-expanded="open"
                    >
                        <span>{{ $item['q'] }}</span>
                        <span class="faq-chevron shrink-0" :class="{ 'faq-chevron--open': open }" aria-hidden="true"></span>
                    </button>
                    <div class="faq-accordion" :class="{ 'is-open': open }">
                        <div class="overflow-hidden">
                            <p class="copy-base pb-5 text-base leading-8 opacity-80">{{ $item['a'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </section>
</x-public-layout>
