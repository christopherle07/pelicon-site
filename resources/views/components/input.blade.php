@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'site-input rounded-2xl border-0 px-4 py-3.5 focus:outline-none focus:ring-0 focus:border-transparent']) !!}>
