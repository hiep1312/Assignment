@assets
    @vite('resources/css/product-show.css')
@endassets

<div class="container-xl my-4" id="main-component" style="padding: 12px;">
    <div class="row px-1 px-lg-0 g-4">
        <div class="col-lg-6">
            <div class="pdp-gallery-container">
                <div class="pdp-main-image-wrapper mb-3">
                    <img id="mainImage" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqRd_vJ-aLY2GDN3jgEsernIPtTxXMpK_GWhQBWjeGS5_tdMuKF7JVr34&s" alt="Ảnh sản phẩm" class="pdp-main-image">
                    <span class="badge bg-danger position-absolute top-0 end-0 m-3">-30%</span>
                    <span class="badge bg-info position-absolute top-0 start-0 m-3">Mới</span>
                </div>

                <div class="pdp-thumbnails-wrapper">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqRd_vJ-aLY2GDN3jgEsernIPtTxXMpK_GWhQBWjeGS5_tdMuKF7JVr34&s" class="pdp-thumbnail pdp-thumbnail-active" onclick="changeImage(this)">
                        </div>
                        <div class="col-6 col-md-3">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqRd_vJ-aLY2GDN3jgEsernIPtTxXMpK_GWhQBWjeGS5_tdMuKF7JVr34&s" class="pdp-thumbnail" onclick="changeImage(this)">
                        </div>
                        <div class="col-6 col-md-3">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqRd_vJ-aLY2GDN3jgEsernIPtTxXMpK_GWhQBWjeGS5_tdMuKF7JVr34&s" class="pdp-thumbnail" onclick="changeImage(this)">
                        </div>
                        <div class="col-6 col-md-3">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqRd_vJ-aLY2GDN3jgEsernIPtTxXMpK_GWhQBWjeGS5_tdMuKF7JVr34&s" class="pdp-thumbnail" onclick="changeImage(this)">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="pdp-product-info">
                <nav class="pdp-breadcrumb mb-3" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="#">Điện tử</a></li>
                        <li class="breadcrumb-item active">Sản phẩm</li>
                    </ol>
                </nav>

                <h1 class="pdp-title mb-2">Tai nghe không dây chất lượng cao</h1>

                <div class="pdp-rating-section d-flex align-items-center gap-3 mb-3">
                    <div class="pdp-stars">
                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                        <span class="pdp-star"><i class="fas fa-star"></i></span>
                        <span class="pdp-star"><i class="fas fa-star-half-alt"></i></span>
                    </div>
                    <span class="pdp-rating-score">4.5/5 (128 reviews)</span>
                    <span class="pdp-sold-count"><i class="fas fa-check-circle"></i> Đã bán 1.2k</span>
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
                            <div class="pdp-feature-title">Giao hàng miễn phí</div>
                            <div class="pdp-feature-text">Từ 99,000₫</div>
                        </div>
                    </div>
                    <div class="pdp-feature">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Đổi trả trong 30 ngày</div>
                            <div class="pdp-feature-text">Miễn phí hoàn toàn</div>
                        </div>
                    </div>
                    <div class="pdp-feature">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Bảo hành chính hãng</div>
                            <div class="pdp-feature-text">12 tháng</div>
                        </div>
                    </div>
                    <div class="pdp-feature">
                        <div class="pdp-feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="pdp-feature-content">
                            <div class="pdp-feature-title">Thanh toán an toàn</div>
                            <div class="pdp-feature-text">Được bảo vệ</div>
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
                        <div class="row align-items-center">
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
                                <div class="pdp-rating-breakdown-new">
                                    <div class="pdp-rating-row-new">
                                        <span class="pdp-rating-label">5<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar-new">
                                            <div class="pdp-progress-new" style="width: 65%"></div>
                                        </div>
                                        <span class="pdp-rating-count">83</span>
                                    </div>
                                    <div class="pdp-rating-row-new">
                                        <span class="pdp-rating-label">4<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar-new">
                                            <div class="pdp-progress-new" style="width: 20%"></div>
                                        </div>
                                        <span class="pdp-rating-count">26</span>
                                    </div>
                                    <div class="pdp-rating-row-new">
                                        <span class="pdp-rating-label">3<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar-new">
                                            <div class="pdp-progress-new" style="width: 10%"></div>
                                        </div>
                                        <span class="pdp-rating-count">13</span>
                                    </div>
                                    <div class="pdp-rating-row-new">
                                        <span class="pdp-rating-label">2<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar-new">
                                            <div class="pdp-progress-new" style="width: 3%"></div>
                                        </div>
                                        <span class="pdp-rating-count">4</span>
                                    </div>
                                    <div class="pdp-rating-row-new">
                                        <span class="pdp-rating-label">1<i class="fas fa-star pdp-star-icon-small"></i></span>
                                        <div class="pdp-progress-bar-new">
                                            <div class="pdp-progress-new" style="width: 2%"></div>
                                        </div>
                                        <span class="pdp-rating-count">2</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New write review button styling matching image -->
                    <button class="pdp-btn-write-review mb-4" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="fas fa-pen"></i> Viết đánh giá
                    </button>

                    <!-- Reviews List -->
                    <div class="pdp-reviews-list">
                        <!-- Review Item 1 -->
                        <div class="pdp-review-card mb-3">
                            <div class="pdp-review-header">
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
                            </div>
                            <h5 class="pdp-review-title">Sản phẩm rất tốt, chất lượng cao</h5>
                            <p class="pdp-review-text">Áo sơ mi này chất lượng thật sự tuyệt vời. Cotton mềm mại, không khó chịu khi mặc. Khâu may chắc chắn, màu sắc bền. Giao hàng nhanh và đóng gói cần thận. Rất hài lòng với mua sắm này!</p>
                            <div class="pdp-review-actions">
                                <button class="pdp-review-helpful-btn">
                                    <i class="fas fa-thumbs-up"></i> Hữu ích <span>(15)</span>
                                </button>
                                <button class="pdp-review-unhelpful-btn">
                                    <i class="fas fa-thumbs-down"></i> Không hữu ích <span>(2)</span>
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
                                    <i class="fas fa-thumbs-up"></i> Hữu ích <span>(8)</span>
                                </button>
                                <button class="pdp-review-unhelpful-btn">
                                    <i class="fas fa-thumbs-down"></i> Không hữu ích <span>(1)</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Load More -->
                    <div class="text-center mt-4">
                        <button class="pdp-btn-load-more">
                            <i class="fas fa-chevron-down"></i> Xem thêm đánh giá
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="pdp-related-section mt-5">
        <h2 class="mb-4"><i class="fas fa-cubes"></i> Sản phẩm liên quan</h2>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
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
