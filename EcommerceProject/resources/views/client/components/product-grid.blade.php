@assets
    @vite('resources/css/product-grid.css')
@endassets

<div {{ $attributes->merge(['class' => 'products-grid']) }}>
    {{ $slot }}
</div>
