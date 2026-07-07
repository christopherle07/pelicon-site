@props(['active'])

@php
$classes = ($active ?? false)
            ? 'site-nav-button site-nav-button--active focus:outline-none'
            : 'site-nav-button focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes.' font-unicase', 'style' => ($active ?? false) ? 'color: var(--text-strong); background: var(--accent-soft);' : 'color: var(--text-muted);']) }}>
    {{ $slot }}
</a>
