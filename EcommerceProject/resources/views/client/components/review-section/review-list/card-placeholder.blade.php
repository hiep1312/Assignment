@assets
    @vite('resources/css/review-card.css')
@endassets

<div {{ $attributes->class(['pdp-review-card', 'placeholder-glow']) }}>
    <div class="pdp-review-user">
        <span class="pdp-review-avatar placeholder" style="background-color: currentColor;"></span>
        <div class="pdp-review-user-info">
            <h6 class="pdp-review-user-name"><span class="placeholder" style="width: 130px;"></span></h6>
            <div class="pdp-review-stars">
                <span class="placeholder pdp-star-filled" style="width: 15px; margin-right: 2px;"></span>
                <span class="placeholder pdp-star-filled" style="width: 15px; margin-right: 2px;"></span>
                <span class="placeholder pdp-star-filled" style="width: 15px; margin-right: 2px;"></span>
                <span class="placeholder pdp-star-filled" style="width: 15px; margin-right: 2px;"></span>
                <span class="placeholder pdp-star-filled" style="width: 15px; margin-right: 2px;"></span>
            </div>
        </div>
        <span class="pdp-review-time placeholder" style="width: 82px;"></span>
    </div>
    <p class="pdp-review-text">
        <span class="placeholder col-12"></span>
        <span class="placeholder col-10"></span>
    </p>
    <div class="pdp-review-actions">
        <button class="pdp-review-helpful-btn placeholder" style="width: 80px; background-color: currentColor; height: 19px"></button>
        <button class="pdp-review-unhelpful-btn placeholder" style="width: 100px; background-color: currentColor; height: 19px"></button>

        @if($showDeleteButton)
            <button class="pdp-review-delete-btn placeholder" style="width: 115px; background-color: currentColor; height: 19px"></button>
        @endif
    </div>
</div>
