@assets
    <style>
        .pdp-reviews-list {
            margin-top: 1.75rem;
        }
    </style>
@endassets

<div {{ $attributes->class(['pdp-reviews-list']) }}>
    {{ $slot }}
</div>
