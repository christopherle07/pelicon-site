<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status') === 'registration-link-sent')
            <div class="flash-toast mb-4 font-medium text-sm text-green-600" data-auto-dismiss="5000">
                {{ __('We sent a registration link to that email address. Use it to finish creating your account.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="text-sm underline transition hover:text-[color:var(--accent-strong)]" href="{{ route('login') }}" style="color: var(--text-muted);">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Continue') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
