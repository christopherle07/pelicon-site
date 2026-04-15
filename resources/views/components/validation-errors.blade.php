@if ($errors->any())
    <div {{ $attributes }}>
        <div class="font-medium" style="color: var(--danger);">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm" style="color: var(--danger);">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
