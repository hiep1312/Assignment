@assets
    @vite('resources/css/filter-section.css')
@endassets

<div {{ $attributes }}>
    <label class="form-label fw-bold mb-3">
        <i class="{{ $icon }} me-2 text-primary"></i>{{ $title }}
    </label>
    <div {{ $container->attributes }}>
        {{ $container }}
    </div>
</div>
