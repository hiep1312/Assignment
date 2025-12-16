@assets
    @vite('resources/css/checkout-header.css')
@endassets
@use('App\Livewire\Client\Components\Checkout\Header')
<div {{ $attributes->class(['cop-header']) }}>
    <h1 class="cop-header-title">
        <i class="{{ $icon }}"></i>
        {{ $title }}
    </h1>
    <div @isset($breadcrumb) {{ $breadcrumb->attributes->class(['cop-breadcrumb']) }} @else class="cop-breadcrumb" @endisset>
        <span class="cop-breadcrumb-item">
            <i class="fas fa-shopping-cart"></i>
            Cart
        </span>
        <span class="cop-breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
        <span class="cop-breadcrumb-item @if($activeStep === Header::STEP_PREVIEW) active @endif">
            <i class="fas fa-clipboard-list"></i>
            Preview
        </span>
        <span class="cop-breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
        <span class="cop-breadcrumb-item">
            <i class="fas fa-credit-card"></i>
            Checkout
        </span>
    </div>
</div>
