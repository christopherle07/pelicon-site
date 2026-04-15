<button {{ $attributes->merge(['type' => 'submit', 'class' => 'button-primary inline-flex items-center px-4 py-2 font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
