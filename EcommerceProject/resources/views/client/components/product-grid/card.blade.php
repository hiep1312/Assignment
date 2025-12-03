@assets
    @vite('resources/css/product-card.css')
@endassets

<div class="product-card">
    <div class="product-image-container">
        <img {{ $img->attributes->merge(['class' => 'product-image']) }}>
        <div class="product-overlay">
            <button {{ $addToCartButton->attributes->merge(['class' => 'btn btn-primary btn-lg w-100 add-to-cart']) }}>
                <i class="fas fa-shopping-cart me-2"></i>{{ $addToCartButton }}
            </button>
            <button {{ $viewDetailsButton->attributes->merge(['class' => 'btn btn-light w-100']) }}>
                <i class="fas fa-eye me-2"></i>{{ $viewDetailsButton }}
            </button>
        </div>
        @if($discountPercent)
            <span class="badge bg-danger position-absolute top-0 end-0 m-2">{{ $discountPercent }}%</span>
        @endif

        @if($isNew)
            <span class="badge bg-info position-absolute top-0 start-0 m-2">New</span>
        @endif
    </div>
    <div class="product-info">
        <h6 class="product-title">{{ $title }}</h6>
        <div class="product-rating mb-2">
            @php
                $reviewScore = round($avgRating * 2) / 2;
                $fullStars = floor($reviewScore);
                $hasHalfStar = ($reviewScore - $fullStars) === 0.5;
                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
            @endphp
            {!!
                str_repeat('<i class="fas fa-star text-warning"></i>', $fullStars) .
                ($hasHalfStar ? '<i class="fas fa-star-half-alt text-warning"></i>' : '') .
                str_repeat('<i class="fas fa-star text-muted"></i>', $emptyStars)
            !!}
            <span class="rating-text ms-2">({{ $totalReviews }} reviews)</span>
        </div>

        <div class="product-price">
            <span class="price-current text-primary fw-bold">{{ number_format($price, 0, '.', '.') }}đ</span>

            @isset($originalPrice)
                <span class="price-original text-muted ms-2"><s>{{ number_format($originalPrice, 0, '.', '.') }}đ</s></span>
            @endisset
        </div>

        <div class="d-flex flex-wrap-reverse justify-content-between align-items-center gap-2">
            @if($stockQuantity)
                <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>{{ formatNumberCompact($stockQuantity) }} items in stock</p>
            @else
                <p class="product-stock-status text-danger mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Sold out</p>
            @endif

            <span class="product-sold">
                <i class="fas fa-shopping-bag"></i>{{ formatNumberCompact($soldCount) }} sold
            </span>
        </div>
    </div>
</div>
