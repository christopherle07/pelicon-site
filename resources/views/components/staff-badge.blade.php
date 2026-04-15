@props([
    'user',
    'size' => 'sm',
    'tone' => null,
])

@if ($user->isStaff())
    @php
        $variantClass = 'staff-badge--site';
    @endphp

    <span
        {{ $attributes->class([
            'staff-badge',
            'staff-badge--sm' => $size === 'sm',
            $variantClass,
        ]) }}
        data-role-description="{{ $user->roleDescription() }}"
        title="{{ $user->roleDescription() }}"
    >
        {{ $user->roleLabel() }}
    </span>
@endif
