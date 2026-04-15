@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 ps-3 pe-4 py-2 text-start text-base font-medium transition duration-150 ease-in-out'
            : 'block w-full border-l-4 border-transparent ps-3 pe-4 py-2 text-start text-base font-medium transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => ($active ?? false) ? 'border-color: var(--accent); color: var(--text-strong); background: var(--accent-soft);' : 'color: var(--text-muted);']) }}>
    {{ $slot }}
</a>
