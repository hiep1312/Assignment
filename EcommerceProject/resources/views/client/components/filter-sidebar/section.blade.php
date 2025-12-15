@assets
    <style>
        .form-label {
            color: var(--color-black-product);
            font-size: 0.95rem;
        }

        .text-primary {
            color: var(--color-primary-product) !important;
        }
    </style>
@endassets

<div {{ $attributes }}>
    <label class="form-label fw-bold mb-3">
        <i class="{{ $icon }} me-2 text-primary"></i>{{ $title }}
    </label>
    <div {{ $container->attributes }}>
        {{ $container }}
    </div>
</div>
