<div class="md:col-span-1 flex justify-between">
    <div class="px-4 sm:px-0">
        <h3 class="text-lg font-semibold text-[color:var(--text-strong)]">{{ $title }}</h3>

        <p class="mt-2 text-sm leading-7 copy-muted">
            {{ $description }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>
