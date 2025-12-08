@assets
    @vite('resources/css/review-card.css')
@endassets

<div {{ $attributes->class(['pdp-review-card']) }}>
    <div class="pdp-review-user">
        <img {{ $avatar->attributes->class(['pdp-review-avatar']) }}>
        <div class="pdp-review-user-info">
            <h6 class="pdp-review-user-name">{{ $name }}</h6>
            <div class="pdp-review-stars">
                @php
                    [, $fullStars, $halfStar, $emptyStars] = parseRatingStars($score);
                @endphp
                {!!
                    str_repeat('<i class="fas fa-star pdp-star-filled"></i>', $fullStars) .
                    str_repeat('<i class="fas fa-star-half-alt pdp-star-filled"></i>', $halfStar) .
                    str_repeat('<i class="fas fa-star pdp-star-empty"></i>', $emptyStars)
                !!}
            </div>
        </div>
        <span class="pdp-review-time">{{ $time }}</span>
    </div>
    <p class="pdp-review-text">{{ $slot }}</p>
    <div class="pdp-review-actions">
        <button {{ $helpfulButton->attributes->class(['pdp-review-helpful-btn']) }}>
            <i class="fas fa-thumbs-up"></i> {{ $helpfulButton }}
        </button>
        <button {{ $unhelpfulButton->attributes->class(['pdp-review-unhelpful-btn']) }}>
            <i class="fas fa-thumbs-down"></i> {{ $unhelpfulButton }}
        </button>

        @isset($deleteButton)
            <button {{ $deleteButton->attributes->class(['pdp-review-delete-btn']) }}>
                <i class="fas fa-trash-alt"></i> {{ $deleteButton }}
            </button>
        @endisset
    </div>
</div>
