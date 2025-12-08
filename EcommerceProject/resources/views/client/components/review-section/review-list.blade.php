@assets
    @vite('resources/css/review-list.css')
@endassets

<div {{ $attributes->class(['pdp-reviews-list']) }}>
    {{ $slot }}
</div>
