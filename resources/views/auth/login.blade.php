<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        @if (Route::has('register'))
            <x-slot name="after">
                <a href="{{ route('register') }}" class="block text-center text-sm underline transition hover:text-[color:var(--accent-strong)]"
                    style="color: var(--text-muted);">
                    No account? Register here!
                </a>
            </x-slot>
        @endif

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="flash-toast mb-4 text-sm font-medium" data-auto-dismiss="5000" style="color: var(--success);">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="copy-muted ms-2 text-sm">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm underline transition hover:text-[color:var(--accent-strong)]" href="{{ route('password.request') }}" style="color: var(--text-muted);">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>

        </form>
    </x-authentication-card>
</x-guest-layout>
