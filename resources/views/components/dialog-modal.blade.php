@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4">
        <div class="text-lg font-medium text-[color:var(--text-strong)]">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm copy-muted">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end gap-3 border-t px-6 py-4 text-end" style="border-color: var(--border-subtle); background: var(--bg-surface-alt);">
        {{ $footer }}
    </div>
</x-modal>
