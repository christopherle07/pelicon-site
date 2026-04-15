@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border px-4 py-3 focus:outline-none focus:ring-2', 'style' => 'border-color: var(--border-strong); background: var(--bg-elevated); color: var(--text-strong);']) !!}>
