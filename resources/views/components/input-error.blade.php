@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-sm', 'style' => 'color: var(--danger);']) }}>{{ $message }}</p>
@enderror
