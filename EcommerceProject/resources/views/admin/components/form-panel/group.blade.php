<div class="mb-4">
    <h5 class="mb-3">
        <i class="{{ $icon }} text-primary me-2"></i>
        {{ $title }}
    </h5>

    <div class="row g-3" @if($attributes->has('x-data')) x-data="{{ $attributes->get('x-data') }}" @endif>
        {{ $slot }}
    </div>
</div>
