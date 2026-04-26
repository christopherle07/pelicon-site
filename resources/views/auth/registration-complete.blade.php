<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="space-y-5 text-center">
            <div>
                <h1 class="text-xl font-bold text-[color:var(--text-strong)]">
                    {{ __('Thank you for registering with us!') }}
                </h1>

                <p class="copy-muted mt-3 text-sm leading-6">
                    {{ __('Your account is ready. Return to your original tab or use the button below to log in.') }}
                </p>
            </div>

            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-bold transition button-auth">
                {{ __('Go to login') }}
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>
