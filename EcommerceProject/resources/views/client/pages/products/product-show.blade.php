@assets
    @vite('resources/css/product-show.css')
@endassets

@use('App\Enums\DefaultImage')
@script
<script>
    const PageController = {
        __proto__: window.BasePageController,

        _internal: {

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
                include: 'primaryVariant, variants.inventory, images',
                aggregate: 'count:reviews, avg:reviews.rating, sum:inventories.sold_number'
            }),
        }
    };

    PageController.init();
</script>
@endscript
<div class="container-xl my-4" id="main-component" style="padding: 12px;">
    <div class="row px-1 px-lg-0 g-4">
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
            <div class="pdp-gallery-container" x-data="{ activeImage: '{{ asset('storage/' . ($currentProduct['main_image']['image_url'] ?? DefaultImage::PRODUCT->value)) }}' }">
                <div class="pdp-main-image-wrapper mb-3">
                    @if($isDataLoading)
                        <div class="placeholder-glow pdp-main-image" wire:key="main-image-placeholder"><span class="placeholder" style="width: 100%; height: 100%"></span></div>
                    @else
                        <img id="mainImage" :src="activeImage" wire:key="main-image" alt="Main product image" class="pdp-main-image">
                        <span class="badge bg-info position-absolute top-0 start-0 m-3">New</span>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-3"></span>
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
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Điện tử</a></li>
                        <li class="breadcrumb-item active">Sản phẩm</li>
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
                    <h1 class="pdp-title mb-2">{{ $currentProduct['title'] }}</h1>

                    <div class="pdp-rating-section d-flex align-items-center gap-3 mb-3">
                        @php
                            $avgRating = (int) $currentProduct['reviews_avg_rating'] ?? 0;
                            $reviewScore = round($avgRating * 2) / 2;
                            $fullStars = floor($reviewScore);
                            $hasHalfStar = ($reviewScore - $fullStars) === 0.5;
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                        @endphp
                        <div class="pdp-stars">
                            {!!
                                str_repeat('<span class="pdp-star"><i class="fas fa-star"></i></span>', $fullStars) .
                                ($hasHalfStar ? '<span class="pdp-star"><i class="fas fa-star-half-alt"></i></span>' : '') .
                                str_repeat('<span class="pdp-star-empty"><i class="fas fa-star"></i></span>', $emptyStars)
                            !!}
                        </div>
                        <span class="pdp-rating-score">{{ number_format($avgRating, 1) }}/5 ({{ formatNumberCompact($currentProduct['reviews_count'] ?? 0) }} reviews)</span>
                        <span class="pdp-sold-count"><i class="fas fa-shopping-bag"></i> Sold {{ formatNumberCompact((int) $currentProduct['inventories_sum_sold_number'] ?? 0) }}</span>
                    </div>

                    <div class="pdp-price-section mb-4 p-3 rounded-3">
                        <div class="d-flex flex-wrap align-items-center gap-3 pdp-price-row">
                            <span class="pdp-price-current">1.299.000đ</span>
                            <span class="pdp-price-original">1.856.000đ</span>
                            <span class="pdp-discount-percent">-30%</span>
                        </div>
                        <p class="pdp-price-note mb-0 small mt-2"><i class="fas fa-gift"></i> Tiết kiệm: 557.000đ</p>
                        <p class="pdp-stock-info mb-0 small mt-2">
                            <i class="fas fa-box"></i> Còn <strong>245 sản phẩm</strong> trong kho
                        </p>
                    </div>

                    <div class="pdp-variants-section mb-3">
                        <label class="pdp-variant-label mb-2">
                            <i class="fas fa-cube"></i> Chọn dung lượng pin:
                        </label>
                        <div class="d-flex flex-wrap gap-2 mb-1">
                            <button class="pdp-size-btn pdp-size-btn-active" data-size="8 giờ">8 giờ</button>
                            <button class="pdp-size-btn" data-size="12 giờ">12 giờ</button>
                            <button class="pdp-size-btn" data-size="24 giờ">24 giờ</button>
                        </div>
                        <p class="pdp-selected-variant mt-2 mb-0">Dung lượng: <strong id="selectedSize">8 giờ</strong></p>
                    </div>
                @endif

                <div class="pdp-cart-section d-flex gap-3 mb-3">
                    <div class="pdp-quantity-control">
                        <button class="pdp-qty-btn" onclick="decreaseQty()"><i class="fas fa-minus"></i></button>
                        <input type="number" id="quantity" value="1" min="1" class="pdp-qty-input">
                        <button class="pdp-qty-btn" onclick="increaseQty()"><i class="fas fa-plus"></i></button>
                    </div>
                    <button class="btn pdp-btn-add-to-cart flex-grow-1">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                    </button>
                    <button class="btn pdp-btn-wishlist">
                        <i class="fas fa-heart"></i> <span class="pdp-wishlist-text">Yêu thích</span>
                    </button>
                </div>

                <button class="btn pdp-btn-buy-now w-100 mb-4">
                    <i class="fas fa-bolt"></i> Mua ngay
                </button>

                <div class="pdp-features">
                    <div class="pdp-feature">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Free Shipping</div>
                            <div class="pdp-feature-text">From 99,000₫</div>
                        </div>
                    </div>
                    <div class="pdp-feature">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">30-Day Returns</div>
                            <div class="pdp-feature-text">Completely Free</div>
                        </div>
                    </div>
                    <div class="pdp-feature">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Official Warranty</div>
                            <div class="pdp-feature-text">12 Months</div>
                        </div>
                    </div>
                    <div class="pdp-feature">
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
                <button class="nav-link pdp-tab-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description">
                    <i class="fas fa-file-alt"></i> Mô tả sản phẩm
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link pdp-tab-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews">
                    <i class="fas fa-comments"></i> Đánh giá (128)
                </button>
            </li>
        </ul>

        <div class="tab-content pdp-tab-content" id="pdpTabContent">
            <div class="tab-pane fade show active" id="description" role="tabpanel">
                <div class="pdp-description-content">
                    <h3 class="mb-3">Mô tả chi tiết</h3>
                    <p>Tai nghe không dây này được thiết kế với công nghệ tiên tiến, mang lại trải nghiệm âm thanh tuyệt vời. Với pin dài 8-24 giờ, bạn có thể sử dụng cả ngày mà không lo hết pin.</p>
                    <h4 class="mt-4 mb-2">Đặc điểm nổi bật:</h4>
                </div>
            </div>

            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <div class="pdp-reviews-section">
                    <div class="pdp-reviews-header mb-4">
                        <div class="row gap-3 gap-md-0 align-items-center">
                            <div class="col-md-4">
                                <div class="pdp-reviews-summary-box">
                                    <div class="pdp-average-rating-large">4.5</div>
                                    <div class="pdp-stars-large mb-2">
                                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                                        <span class="pdp-star-half"><i class="fas fa-star-half"></i></span>
                                    </div>
                                    <p class="pdp-review-count">Dựa trên <strong>128 đánh giá</strong></p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="pdp-rating-breakdown">
                                    <div class="pdp-rating-row">
                                        <span class="pdp-rating-label">5<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar">
                                            <div class="pdp-progress" style="width: 65%"></div>
                                        </div>
                                        <span class="pdp-rating-count">83</span>
                                    </div>
                                    <div class="pdp-rating-row">
                                        <span class="pdp-rating-label">4<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar">
                                            <div class="pdp-progress" style="width: 20%"></div>
                                        </div>
                                        <span class="pdp-rating-count">26</span>
                                    </div>
                                    <div class="pdp-rating-row">
                                        <span class="pdp-rating-label">3<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar">
                                            <div class="pdp-progress" style="width: 10%"></div>
                                        </div>
                                        <span class="pdp-rating-count">13</span>
                                    </div>
                                    <div class="pdp-rating-row">
                                        <span class="pdp-rating-label">2<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar">
                                            <div class="pdp-progress" style="width: 3%"></div>
                                        </div>
                                        <span class="pdp-rating-count">4</span>
                                    </div>
                                    <div class="pdp-rating-row">
                                        <span class="pdp-rating-label">1<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar">
                                            <div class="pdp-progress" style="width: 2%"></div>
                                        </div>
                                        <span class="pdp-rating-count">2</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="pdp-btn-write-review" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="fas fa-pen"></i> Viết đánh giá
                    </button>

                    <div class="pdp-reviews-list">
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
                            <h5 class="pdp-review-title">Sản phẩm rất tốt, chất lượng cao</h5>
                            <p class="pdp-review-text">Áo sơ mi này chất lượng thật sự tuyệt vời. Cotton mềm mại, không khó chịu khi mặc. Khâu may chắc chắn, màu sắc bền. Giao hàng nhanh và đóng gói cần thận. Rất hài lòng với mua sắm này!</p>
                            <div class="pdp-review-actions">
                                <button class="pdp-review-helpful-btn">
                                    <i class="fas fa-thumbs-up"></i> Hữu ích
                                </button>
                                <button class="pdp-review-unhelpful-btn">
                                    <i class="fas fa-thumbs-down"></i> Không hữu ích
                                </button>
                            </div>
                        </div>

                        <!-- Review Item 2 -->
                        <div class="pdp-review-card mb-3">
                            <div class="pdp-review-header">
                                <div class="pdp-review-user">
                                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqRd_vJ-aLY2GDN3jgEsernIPtTxXMpK_GWhQBWjeGS5_tdMuKF7JVr34&s" class="pdp-review-avatar" alt="Avatar">
                                    <div class="pdp-review-user-info">
                                        <h6 class="pdp-review-user-name">Trần Thị B</h6>
                                        <div class="pdp-review-stars">
                                            <i class="fas fa-star pdp-star-filled"></i>
                                            <i class="fas fa-star pdp-star-filled"></i>
                                            <i class="fas fa-star pdp-star-filled"></i>
                                            <i class="fas fa-star pdp-star-filled"></i>
                                            <i class="fas fa-star pdp-star-empty"></i>
                                        </div>
                                    </div>
                                    <span class="pdp-review-time">1 tháng trước</span>
                                </div>
                            </div>
                            <h5 class="pdp-review-title">Tốt nhưng hơi nhỏ</h5>
                            <p class="pdp-review-text">Áo đẹp lắm, nhưng kích thuôc hơi nhỏ so với mô tả. Tôi đã đổi size L sang XL. Cảm ơn dịch vụ đổi hàng rất nhanh.</p>
                            <div class="pdp-review-actions">
                                <button class="pdp-review-helpful-btn">
                                    <i class="fas fa-thumbs-up"></i> Hữu ích
                                </button>
                                <button class="pdp-review-unhelpful-btn">
                                    <i class="fas fa-thumbs-down"></i> Không hữu ích
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="pdp-btn-load-more">
                            <i class="fas fa-chevron-down"></i> Xem thêm đánh giá
                        </button>
                    </div>
                </div>
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
                            <label class="form-label">Đánh giá sao</label>
                            <div class="pdp-rating-input">
                                <button type="button" class="pdp-star-input" data-rating="1"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="2"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="3"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="4"><i class="fas fa-star"></i></button>
                                <button type="button" class="pdp-star-input" data-rating="5"><i class="fas fa-star"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reviewTitle" class="form-label">Tiêu đề đánh giá</label>
                            <input type="text" class="form-control pdp-form-input" id="reviewTitle" placeholder="Nhập tiêu đề đánh giá">
                        </div>
                        <div class="mb-3">
                            <label for="reviewContent" class="form-label">Nội dung đánh giá</label>
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
