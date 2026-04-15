<div class="flex min-h-screen flex-col items-center justify-center pt-6 sm:pt-0">
    <div class="mb-6">
        {{ $logo }}
    </div>

    <div class="auth-card w-full overflow-hidden px-6 py-6 sm:max-w-md">
        {{ $slot }}
    </div>

    @isset($after)
        <div class="mt-4 w-full sm:max-w-md">
            {{ $after }}
        </div>
    @endisset
</div>
