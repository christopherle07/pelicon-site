<button {{ $attributes->merge(['type' => 'button', 'class' => 'button-secondary inline-flex items-center px-4 py-2 font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
