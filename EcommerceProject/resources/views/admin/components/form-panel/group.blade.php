<div class="mb-4">
    @if($hasTitleAction)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">
                <i class="{{ $icon }} text-primary me-2"></i>
                {{ $title }}
            </h5>
            <button {{ $buttonAction->attributes }}>
                @if($buttonAction->attributes->has('icon')) <i class="{{ $buttonAction->attributes->get('icon') }}"></i> @endif
                {{ $buttonAction }}
            </button>
        </div>
    @else
        <h5 class="mb-3">
            <i class="{{ $icon }} text-primary me-2"></i>
            {{ $title }}
        </h5>
    @endif

    <div {{ $attributes->merge(['class' => $hasTitleAction ? '' : 'row g-3']) }}>
        {{ $slot }}
    </div>
</div>
