@push('styles')
    @vite('resources/css/product-grid.css')
@endpush

<div {{ $attributes->merge(['class' => 'products-grid']) }}>
    {{ $slot }}
</div>