@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="surface-panel px-5 py-6 sm:p-8 {{ isset($actions) ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md' }}">
                <div class="grid grid-cols-6 gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end gap-3 border-t px-5 py-4 text-end sm:px-8 sm:rounded-bl-md sm:rounded-br-md" style="border-color: var(--border-subtle); background: var(--bg-surface-alt);">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
