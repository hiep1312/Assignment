@assets
    @vite('resources/css/product-card.css')
@endassets

<div class="product-card placeholder-glow">
    <div class="product-image-container">
        <div class="placeholder" style="width: 100%; aspect-ratio: 1/1; background: rgb(134, 142, 150);"></div>
    </div>
    <div class="product-info">
        <h6 class="product-title"><span class="d-inline-block placeholder col-9" style="height: 21px"></span></h6>
        <div class="product-rating" style="margin-bottom: .6rem;">
            <span class="d-inline-block placeholder" style="width: 85px; height: 18px;"></span>
            <span class="d-inline-block placeholder ms-2" style="width: 70px; height: 18px;"></span>
        </div>

        <div class="product-price" style="margin-bottom: 7px;">
            <span class="d-inline-block placeholder" style="width: 100px; height: 21px;"></span>
        </div>

        <div class="d-flex flex-wrap-reverse justify-content-between align-items-center gap-2">
            <p class="mb-0">
                <span class="d-inline-block placeholder" style="width: 120px; height: 18px;"></span>
            </p>
            <span class="d-inline-block placeholder" style="width: 70px; height: 18px;"></span>
        </div>
    </div>
</div>
