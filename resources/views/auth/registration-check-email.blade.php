<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="space-y-5 text-center">
            <div>
                <h1 class="text-xl font-bold text-[color:var(--text-strong)]">
                    {{ __('Check your email') }}
                </h1>

                <p class="copy-muted mt-3 text-sm leading-6">
                    {{ __('We sent a registration link to your email address. Open it to choose your username and password.') }}
                </p>
            </div>

            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-bold transition button-auth">
                {{ __('Go to login') }}
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>
