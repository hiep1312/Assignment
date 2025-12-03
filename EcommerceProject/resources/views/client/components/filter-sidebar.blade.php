@assets
    @vite('resources/css/filter-sidebar.css')
@endassets

<div {{ $attributes->merge(['class' => 'filter-card']) }}>
    {{ $slot }}
</div>
