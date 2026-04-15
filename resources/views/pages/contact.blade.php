<x-public-layout title="Contact - {{ config('app.name', 'Pelicon') }}">
    <section class="surface-panel max-w-4xl p-8 sm:p-10">
        <p class="section-kicker">Contact</p>
        <h1 class="title-hero mt-3 text-4xl sm:text-5xl">Contact the Pelicon team</h1>
        <div class="copy-base mt-6 space-y-4 text-base leading-8">
            <p>The best place to reach the team right now is the forum. It keeps questions, bug reports, and suggestions in one place so nothing gets lost.</p>
            <p>If you want support or feedback, open a thread in the forum and include as much detail as you can.</p>
        </div>

        <div class="mt-8">
            <a href="{{ route('forum.index') }}" class="button-secondary inline-flex px-6 py-3 text-sm font-semibold transition">
                Open the Forum
            </a>
        </div>
    </section>
</x-public-layout>
