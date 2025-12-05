@assets
    @vite('resources/css/empty-state-client.css')
@endassets

<div {{ $attributes->merge(['class' => 'no-data-placeholder']) }}>
    <div class="no-data-content">
        <i class="{{ $icon }}"></i>
        <h4>{{ $title }}</h4>
        <p>{{ $slot }}</p>
    </div>
</div>
