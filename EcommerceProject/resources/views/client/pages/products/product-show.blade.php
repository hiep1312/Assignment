@assets
    @vite('resources/css/product-show.css')
@endassets

@use('App\Enums\DefaultImage')
@script
<script>
    const PageController = {
        __proto__: window.BasePageController,

        _internal: {
            localStorageKeys: ['reviewPage'],
        },

        init() {
            /* Clear local storage */
            for(const key of PageController._internal.localStorageKeys) {
                localStorage.removeItem(key);
            }

            super.init();
        },

        fetchData: async () => {
            try {
                const productResponse = await window.http.get(@js(route('api.products.show', $routeSlug)), { params: PageController._buildApiParams.productQueryParams() });

                const { data: axiosProductData } = productResponse;

                $wire.currentProduct = axiosProductData.data;
                $wire.isDataLoading = false;
                $wire.$refresh();

                return axiosProductData;
            }catch(axiosError) {
                const message = axiosError.response?.data?.message ?? axiosError.message;

                console.error("Failed to fetch: ", message);
            }
        },

        _buildApiParams: {
            productQueryParams: () => ({
                include: 'variants.inventory, images',
                aggregate: 'count:reviews, avg:reviews.rating, sum:inventories.sold_number'
            }),

            reviewQueryParams: () => {
                const apiParams = {};
                const allowedFields = {
                    page: () => localStorage.getItem('reviewPage'),
                    with_rating_stats: () => !$wire.isReviewsLoaded,
                    with_can_review: () => !$wire.isReviewsLoaded
                };

                for(const [key, getter] of Object.entries(allowedFields)) {
                    const value = getter();

                    if(Boolean(value)) {
                        apiParams[key] = value;
                    }
                }

                return {
                    include: 'user',
                    per_page: 5,
                    ...apiParams
                };
            }
        },

        events: {
            "reviews:load": async (event) => {
                if(!$wire.isReviewsLoaded) {
                    try {
                        const reviewResponse = await window.http.get(window.reviewsApiUrl, { params: PageController._buildApiParams.reviewQueryParams() });

                        const { data: axiosReviewData } = reviewResponse;

                        $wire.reviewsData = axiosReviewData.data;
                        $wire.ratingDistribution = axiosReviewData.rating_distribution;
                        $wire.canReview = axiosReviewData.can_review;
                        $wire.isReviewsLoaded = true;
                        localStorage.setItem('reviewPage', axiosReviewData.current_page);
                        $wire.$refresh();

                    }catch(axiosError) {
                        const message = axiosError.response?.data?.message ?? axiosError.message;

                        console.error("Failed to fetch: ", message);
                    }
                }
            },
        }
    };

    PageController.init();
</script>
@endscript
<div class="container-xl my-4" id="main-component" style="padding: 12px;">
    <div class="row px-1 px-lg-0 g-4" x-data="{
            selectedVariant: $wire.$entangle('selectedVariant'),
            activeImage: '{{ asset('storage/' . ($currentProduct['main_image']['image_url'] ?? DefaultImage::PRODUCT->value)) }}',
            selectedQuantity: null,
            minPurchasable(){
                return this.selectedVariant.inventory?.stock ? 1 : 0;
            },
            init() {
                this.$watch('selectedVariant', (value, oldValue) => {
                    if(typeof value === 'object' && (this.selectedQuantity === null || value.id !== oldValue.id)) {
                        this.selectedQuantity = this.minPurchasable();
                    }
                });

                this.$watch('selectedQuantity', value => {
                    if(value === '') return;

                    let quantity = isNaN(value) ? 1 : parseInt(value);
                    quantity = Math.max(this.minPurchasable(), Math.min(quantity, parseInt(this.selectedVariant.inventory?.stock) || 1));

                    if(quantity !== this.selectedQuantity) {
                        this.selectedQuantity = quantity;
                    }
                });
            }
        }">
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
            <div class="pdp-gallery-container">
                <div class="pdp-main-image-wrapper mb-3">
                    @if($isDataLoading)
                        <div class="placeholder-glow pdp-main-image" wire:key="main-image-placeholder"><span class="placeholder" style="width: 100%; height: 100%"></span></div>
                    @else
                        <img id="mainImage" :src="activeImage" wire:key="main-image" alt="Main product image" class="pdp-main-image">

                        @if(now()->diffInDays($currentProduct['created_at'], true) <= 7)
                            <span class="badge bg-info position-absolute top-0 start-0 m-3" wire:key="new-badge">New</span>
                        @endif

                        <span class="badge bg-danger position-absolute top-0 end-0 m-3" x-show="selectedVariant.discount" wire:key="discount-badge"
                            x-text="`-${(((selectedVariant.price - (selectedVariant.discount ?? 0)) / selectedVariant.price) * 100).toFixed(0)}%`"></span>
                    @endif
                </div>

                <div class="pdp-thumbnails-wrapper-scroll">
                    <div class="pdp-thumbnails-scroll-container">
                        @if($isDataLoading)
                            @for($i = 0; $i < 5; $i++)
                                <div class="pdp-thumbnail-item placeholder-glow" wire:key="thumbnail-placeholder-{{ $i }}">
                                    <span @class([
                                        "placeholder",
                                        "pdp-thumbnail",
                                        "pdp-thumbnail-active" => $i === 0
                                    ])></span>
                                </div>
                            @endfor
                        @else
                            @foreach($currentProduct['images'] ?? [] as $image)
                                <div class="pdp-thumbnail-item" wire:key="thumbnail-{{ $image['id'] }}">
                                    <img src="{{ asset('storage/' . ($image['image_url'] ?? DefaultImage::PRODUCT->value)) }}" alt="Product image"
                                        :class="`pdp-thumbnail ${activeImage === $el.src ? 'pdp-thumbnail-active' : ''}`" x-on:click="activeImage = $el.src">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
            <div class="pdp-product-info placeholder-glow">
                <nav class="pdp-breadcrumb mb-3" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);"><i class="fas fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('client.products.index') }}">Books</a></li>
                        <li class="breadcrumb-item active">Book Details</li>
                    </ol>
                </nav>

                @if($isDataLoading)
                    <h1 class="pdp-title mb-2" wire:key="product-title-placeholder"><span class="placeholder d-inline-block col-12 col-sm-10"></span></h1>
                    <div class="pdp-rating-section d-flex align-items-center gap-3 mb-3" wire:key="product-rating-placeholder">
                        <div class="pdp-stars d-flex gap-1" style="height: 18px;">
                            <span class="placeholder pdp-star" style="width: 17px; height: 16px;"></span>
                            <span class="placeholder pdp-star" style="width: 17px; height: 16px;"></span>
                            <span class="placeholder pdp-star" style="width: 17px; height: 16px;"></span>
                            <span class="placeholder pdp-star" style="width: 17px; height: 16px;"></span>
                            <span class="placeholder pdp-star" style="width: 17px; height: 16px;"></span>
                        </div>
                        <span class="placeholder pdp-rating-score" style="width: 132px; height: 18px;"></span>
                        <span class="placeholder pdp-sold-count" style="width: 103px; height: 18px;"></span>
                    </div>

                    <div class="pdp-price-section mb-4 p-3 rounded-3" wire:key="product-price-placeholder">
                        <div class="d-flex flex-wrap align-items-center gap-3 pdp-price-row">
                            <span class="pdp-price-current placeholder" style="width: 165px; height: 46px"></span>
                            <span class="pdp-price-original placeholder" style="width: 96px; height: 28px"></span>
                            <span class="pdp-discount-percent placeholder" style="width: 54px; height: 30px"></span>
                        </div>
                        <p class="pdp-price-note mb-0 small mt-2"><i class="fas fa-gift"></i> <span class="placeholder" style="width: 128px; height: 17px"></span></p>
                        <p class="pdp-stock-info mb-0 small mt-2">
                            <i class="fas fa-box"></i> <span class="placeholder" style="width: 200px; height: 17px"></span>
                        </p>
                    </div>

                    <div class="pdp-variants-section mb-3" wire:key="product-variants-placeholder">
                        <label class="pdp-variant-label mb-2">
                            <i class="fas fa-cube"></i> <span class="placeholder" style="width: 180px; height: 20px"></span>
                        </label>
                        <div class="d-flex flex-wrap gap-2 mb-1">
                            <button class="pdp-size-btn pdp-size-btn-active placeholder" style="width: 100px; height: 40px"></button>
                            <button class="pdp-size-btn placeholder" style="width: 100px; height: 40px"></button>
                            <button class="pdp-size-btn placeholder" style="width: 100px; height: 40px"></button>
                            <button class="pdp-size-btn placeholder" style="width: 100px; height: 40px"></button>
                        </div>
                        <p class="pdp-selected-variant mt-2 mb-0">Selected variant: <strong class="placeholder" style="width: 100px; height: 16px"></strong></p>
                    </div>
                @else
                    <h1 class="pdp-title mb-2" wire:key="product-title">{{ $currentProduct['title'] }}</h1>
                    <div class="pdp-rating-section d-flex align-items-center gap-3 mb-3" wire:key="product-rating">
                        @php
                            $avgRating = (float) $currentProduct['reviews_avg_rating'] ?? 0;
                            [, $fullStars, $halfStar, $emptyStars] = parseRatingStars($avgRating);
                        @endphp
                        <div class="pdp-stars">
                            {!!
                                str_repeat('<span class="pdp-star"><i class="fas fa-star"></i></span>', $fullStars) .
                                str_repeat('<span class="pdp-star"><i class="fas fa-star-half-alt"></i></span>', $halfStar) .
                                str_repeat('<span class="pdp-star-empty"><i class="fas fa-star"></i></span>', $emptyStars)
                            !!}
                        </div>
                        <span class="pdp-rating-score">{{ number_format($avgRating, 1) }}/5 ({{ formatNumberCompact($currentProduct['reviews_count'] ?? 0) }} reviews)</span>
                        <span class="pdp-sold-count"><i class="fas fa-shopping-bag"></i> Sold {{ formatNumberCompact((int) $currentProduct['inventories_sum_sold_number'] ?? 0) }}</span>
                    </div>

                    <div class="pdp-price-section mb-4 p-3 rounded-3" wire:key="product-price">
                        <div class="d-flex flex-wrap align-items-center gap-3 pdp-price-row">
                            <span class="pdp-price-current" x-text="`${new Intl.NumberFormat('vi-VN').format(selectedVariant.discount ?? selectedVariant.price)}đ`"></span>
                            <span class="pdp-price-original" x-show="selectedVariant.discount"
                                x-text="`${new Intl.NumberFormat('vi-VN').format(selectedVariant.price)}đ`"></span>
                            <span class="pdp-discount-percent" x-show="selectedVariant.discount"
                                x-text="`-${(((selectedVariant.price - (selectedVariant.discount ?? 0)) / selectedVariant.price) * 100).toFixed(0)}%`"></span>
                        </div>
                        <p class="pdp-price-note mb-0 small mt-2">
                            <i class="fas fa-gift"></i> You save:
                            <strong>
                                <span x-text="new Intl.NumberFormat('vi-VN').format(selectedVariant.discount ? (selectedVariant.price - selectedVariant.discount) : 0)"></span>đ
                            </strong>
                        </p>
                        <p class="pdp-stock-info mb-0 small mt-2">
                            <i class="fas fa-box"></i> Only <strong><span x-text="new Intl.NumberFormat('en-US', {notation: 'compact', minimumFractionDigits: 0, maximumFractionDigits: 1}).format(selectedVariant.inventory?.stock ?? 0)"></span> items</strong> left in stock
                        </p>
                    </div>

                    @unless(empty($currentProduct['variants']))
                        <div class="pdp-variants-section mb-3" wire:key="product-variants">
                            <label class="pdp-variant-label mb-2">
                                <i class="fas fa-layer-group"></i> Choose a variant:
                            </label>
                            <div class="d-flex flex-wrap gap-2 mb-1">
                                @foreach($currentProduct['variants'] as $variant)
                                    <button :class="`pdp-size-btn ${selectedVariant.id === {{ $variant['id'] }} ? 'pdp-size-btn-active' : ''}`" {{-- @disabled(!($variant['inventory']['stock'] ?? 0)) --}}
                                        x-on:click="selectedVariant = @js($variant)">{{ $variant['name'] }}</button>
                                @endforeach
                            </div>
                            <p class="pdp-selected-variant mt-2 mb-0">Selected variant: <strong x-text="selectedVariant.name ?? 'N/A'"></strong></p>
                        </div>
                    @endunless
                @endif

                <div class="pdp-cart-section d-flex gap-3 mb-3">
                    <div class="pdp-quantity-control">
                        <button x-on:click="
                            const quantity = parseInt($el.nextElementSibling.value);
                            $el.nextElementSibling.value = Math.max(minPurchasable(), Number.isNaN(quantity) ? 0 : (quantity - 1));
                        " class="pdp-qty-btn pdp-qty-minus"><i class="fas fa-minus"></i></button>
                        <input type="number" id="quantity" x-model="selectedQuantity" :min="minPurchasable" :max="selectedVariant.inventory?.stock || 0" class="pdp-qty-input" aria-label="Quantity to purchase">
                        <button x-on:click="
                            const quantity = parseInt($el.previousElementSibling.value);
                            $el.previousElementSibling.value = Math.min(Number.isNaN(quantity) ? minPurchasable() : (quantity + 1), parseInt(selectedVariant.inventory?.stock) || 0);
                        " class="pdp-qty-btn pdp-qty-plus"><i class="fas fa-plus"></i></button>
                    </div>
                    <button class="btn pdp-btn-add-to-cart flex-grow-1"
                        :disabled="!(selectedVariant.id && selectedVariant.inventory?.stock)">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button class="btn pdp-btn-wishlist">
                        <i class="fas fa-heart"></i> <span class="pdp-wishlist-text">Wishlist</span>
                    </button>
                </div>

                <button class="btn pdp-btn-buy-now w-100 mb-4">
                    <i class="fas fa-bolt"></i> Buy Now
                </button>

                <div class="pdp-features">
                    <div class="pdp-feature wow fadeInUp" data-wow-delay="0.1s">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Free Shipping</div>
                            <div class="pdp-feature-text">From 99,000₫</div>
                        </div>
                    </div>
                    <div class="pdp-feature wow fadeInUp" data-wow-delay="0.1s">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">30-Day Returns</div>
                            <div class="pdp-feature-text">Completely Free</div>
                        </div>
                    </div>
                    <div class="pdp-feature wow fadeInUp" data-wow-delay="0.1s">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Official Warranty</div>
                            <div class="pdp-feature-text">12 Months</div>
                        </div>
                    </div>
                    <div class="pdp-feature wow fadeInUp" data-wow-delay="0.1s">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Secure Payment</div>
                            <div class="pdp-feature-text">Protected</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pdp-details-section mt-5">
        <ul class="nav pdp-nav-tabs" id="pdpTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link pdp-tab-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description-content" wire:key="description-tab" wire:ignore>
                    <i class="fas fa-file-alt"></i> Product Description
                </button>
            </li>
            <li class="nav-item" role="presentation">
                @if($isDataLoading)
                    <button class="nav-link pdp-tab-link placeholder-glow" id="reviews-tab" wire:key="reviews-tab-placeholder" disabled wire:ignore>
                        <i class="fas fa-comments"></i> Reviews (<span class="placeholder" style="width: 28px; height: 18px;"></span>)
                    </button>
                @else
                    <button class="nav-link pdp-tab-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-content" wire:key="reviews-tab" wire:ignore
                        onclick="document.dispatchEvent(new Event('reviews:load'))">
                        <i class="fas fa-comments"></i> Reviews ({{ $currentProduct['reviews_count'] ?? 0 }})
                    </button>
                @endif
            </li>
        </ul>

        <div class="tab-content pdp-tab-content">
            <div class="tab-pane fade show active" id="description-content" role="tabpanel" wire:key="description-content" wire:ignore.self>
                <div class="pdp-description-content">
                    <h3 class="mb-3">Detailed Description</h3>
                    @if($isDataLoading)
                        <div class="placeholder-glow">
                            <p class="placeholder col-12"></p>
                            <p class="placeholder col-12"></p>
                            <p class="placeholder col-12"></p>
                            <p class="placeholder col-8"></p>
                        </div>
                    @else
                        <p>{{ $currentProduct['description'] ?? 'The detailed description of this book is currently unavailable.' }}</p>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade" id="reviews-content" role="tabpanel" wire:key="reviews-content" wire:ignore.self>
                <x-livewire-client::review-section header-class="mb-4" :is-placeholder="!$isReviewsLoaded"
                    :avg-rating="(float) ($currentProduct['reviews_avg_rating'] ?? 0)" :total-reviews="$currentProduct['reviews_count'] ?? 0"
                    :star-counts="$ratingDistribution">
                    <x-slot:action-button data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="fas fa-pen"></i> Write a review
                    </x-slot:action-button>

                    <x-livewire-client::review-section.review-list>
                        <div class="pdp-review-card mb-3">
                            <div class="pdp-review-user">
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqRd_vJ-aLY2GDN3jgEsernIPtTxXMpK_GWhQBWjeGS5_tdMuKF7JVr34&s" class="pdp-review-avatar" alt="Avatar">
                                <div class="pdp-review-user-info">
                                    <h6 class="pdp-review-user-name">Nguyễn Văn A</h6>
                                    <div class="pdp-review-stars">
                                        <i class="fas fa-star pdp-star-filled"></i>
                                        <i class="fas fa-star pdp-star-filled"></i>
                                        <i class="fas fa-star pdp-star-filled"></i>
                                        <i class="fas fa-star pdp-star-filled"></i>
                                        <i class="fas fa-star pdp-star-filled"></i>
                                    </div>
                                </div>
                                <span class="pdp-review-time">2 tuần trước</span>
                            </div>
                            <p class="pdp-review-text">Áo sơ mi này chất lượng thật sự tuyệt vời. Cotton mềm mại, không khó chịu khi mặc. Khâu may chắc chắn, màu sắc bền. Giao hàng nhanh và đóng gói cần thận. Rất hài lòng với mua sắm này!</p>
                            <div class="pdp-review-actions">
                                <button class="pdp-review-helpful-btn">
                                    <i class="fas fa-thumbs-up"></i> Helpful
                                </button>
                                <button class="pdp-review-unhelpful-btn">
                                    <i class="fas fa-thumbs-down"></i> Not helpful
                                </button>

                                <button class="pdp-review-delete-btn" onclick="deleteReview(this)">
                                    <i class="fas fa-trash-alt"></i> Delete review
                                </button>
                            </div>
                        </div>
                    </x-livewire-client::review-section.review-list>

                    <div class="text-center mt-4">
                        <button class="pdp-btn-load-more">
                            <i class="fas fa-chevron-down"></i> Xem thêm đánh giá
                        </button>
                    </div>
                </x-livewire-client::review-section>
            </div>
        </div>
    </div>

    <div class="pdp-related-section mt-4">
        <h3 class="mb-3"><i class="fas fa-cubes"></i> Sản phẩm liên quan</h3>
        <x-livewire-client::empty-state icon="fas fa-cube" title="Không có sản phẩm liên quan">
            Quay lại trang chủ để khám phá những sản phẩm khác
        </x-livewire-client::empty-state>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content pdp-modal-content">
                <div class="modal-header pdp-modal-header">
                    <h5 class="modal-title"><i class="fas fa-pen-fancy"></i> Viết đánh giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pdp-modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-star pdp-icon-label"></i> Đánh giá sao</label>
                            <div class="pdp-rating-input">
                                <button type="button" class="pdp-star-input" data-rating="1"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="2"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="3"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="4"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="5"><i class="fas fa-star"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reviewTitle" class="form-label"><i class="fas fa-heading pdp-icon-label"></i> Tiêu đề đánh giá</label>
                            <input type="text" class="form-control pdp-form-input" id="reviewTitle" placeholder="Nhập tiêu đề đánh giá">
                        </div>
                        <div class="mb-3">
                            <label for="reviewContent" class="form-label"><i class="fas fa-pen-fancy pdp-icon-label"></i> Nội dung đánh giá</label>
                            <textarea class="form-control pdp-form-textarea" id="reviewContent" rows="5" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="verifiedPurchase">
                            <label class="form-check-label" for="verifiedPurchase">
                                <i class="fas fa-check"></i> Tôi đã mua sản phẩm này
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer pdp-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn pdp-btn-submit-review">Gửi đánh giá</button>
                </div>
            </div>
        </div>
    </div>
</div>
