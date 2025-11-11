@assets
    @vite('resources/css/products.css')
@endassets

@section('hero')
    <div class="container-fluid py-5 bg-dark hero-header mb-5">
        <div class="container text-center my-5 pt-5 pb-4">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Booking</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('template.client.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Booking</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

<div class="container-lg py-5">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="filter-card">
                <!-- Search Box -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">
                        <i class="fas fa-search me-2 text-primary"></i>Tìm Kiếm Sản Phẩm
                    </label>
                    <input type="text" class="form-control search-input" placeholder="Nhập tên sản phẩm...">
                </div>

                <!-- Categories Filter -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">
                        <i class="fas fa-list me-2 text-primary"></i>Danh Mục
                    </label>
                    <div class="category-list">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat1" checked>
                            <label class="form-check-label" for="cat1">
                                Tất Cả Sản Phẩm <span class="badge bg-secondary ms-2">124</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat2">
                            <label class="form-check-label" for="cat2">
                                Laptop <span class="badge bg-secondary ms-2">24</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat3">
                            <label class="form-check-label" for="cat3">
                                Điện Thoại <span class="badge bg-secondary ms-2">38</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat4">
                            <label class="form-check-label" for="cat4">
                                Tablet <span class="badge bg-secondary ms-2">18</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat5">
                            <label class="form-check-label" for="cat5">
                                Phụ Kiện <span class="badge bg-secondary ms-2">32</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat6">
                            <label class="form-check-label" for="cat6">
                                Âm Thanh <span class="badge bg-secondary ms-2">12</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">
                        <i class="fas fa-dollar-sign me-2 text-primary"></i>Khoảng Giá
                    </label>
                    <div class="price-range-slider">
                        <input type="range" min="0" max="50000000" value="0" class="form-range price-min" id="priceMin">
                        <input type="range" min="0" max="50000000" value="50000000" class="form-range price-max mt-2"
                            id="priceMax">
                    </div>
                    <div class="price-display mt-3">
                        <small class="text-muted">Từ: <span id="minPrice">0</span> - Đến: <span
                                id="maxPrice">50,000,000</span> ₫</small>
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">
                        <i class="fas fa-star me-2 text-primary"></i>Đánh Giá
                    </label>
                    <div class="rating-list">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rating5">
                            <label class="form-check-label" for="rating5">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="badge bg-secondary ms-2">45</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rating4">
                            <label class="form-check-label" for="rating4">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="badge bg-secondary ms-2">62</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rating3">
                            <label class="form-check-label" for="rating3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-muted"></i>
                                <i class="fas fa-star text-muted"></i>
                                <span class="badge bg-secondary ms-2">28</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">
                        <i class="fas fa-tag me-2 text-primary"></i>Trạng Thái
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="inStock" checked>
                        <label class="form-check-label" for="inStock">
                            <span class="badge bg-success me-2">Còn Hàng</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sale">
                        <label class="form-check-label" for="sale">
                            <span class="badge bg-danger me-2">Đang Giảm Giá</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="new">
                        <label class="form-check-label" for="new">
                            <span class="badge bg-primary me-2">Hàng Mới</span>
                        </label>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="d-grid gap-2">
                    <button class="btn btn-primary filter-btn" type="button">
                        <i class="fas fa-filter me-2"></i>Áp Dụng Bộ Lọc
                    </button>
                    <button class="btn btn-outline-secondary filter-btn" type="button">
                        <i class="fas fa-redo me-2"></i>Đặt Lại
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="col-lg-9">
            <!-- Top Bar -->
            <div class="top-bar mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-list me-2"></i>Hiển thị <strong>12</strong> trên <strong>124</strong> sản
                        phẩm
                    </p>
                </div>
                <div class="sort-container">
                    <label for="sortBy" class="form-label me-2 mb-0">Sắp xếp:</label>
                    <select id="sortBy" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                        <option value="">Mặc Định</option>
                        <option value="price-asc">Giá: Thấp Đến Cao</option>
                        <option value="price-desc">Giá: Cao Đến Thấp</option>
                        <option value="rating">Đánh Giá Cao Nhất</option>
                        <option value="newest">Mới Nhất</option>
                        <option value="popular">Phổ Biến Nhất</option>
                    </select>
                </div>
                <div class="view-toggle">
                    <button class="btn btn-sm btn-outline-primary active" title="Xem lưới">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" title="Xem danh sách">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <!-- Product Card 1 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=MacBook+Pro" alt="MacBook Pro 16"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-20%</span>
                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Mới</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">MacBook Pro 16 M3 Max</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="rating-text ms-2">(245 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">42,990,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>53,990,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 15
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 2 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=iPhone+15+Pro" alt="iPhone 15 Pro"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-15%</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">iPhone 15 Pro Max 256GB</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <span class="rating-text ms-2">(512 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">29,990,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>35,290,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 28
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 3 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=iPad+Air" alt="iPad Air"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Mới</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">iPad Air 11 M2 Wi-Fi</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-muted"></i>
                            <span class="rating-text ms-2">(89 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">18,990,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>21,490,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 32
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 4 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=AirPods+Pro" alt="AirPods Pro"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-25%</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">AirPods Pro 2 Gen USB-C</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="rating-text ms-2">(378 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">6,742,500 ₫</span>
                            <span class="price-original text-muted ms-2"><s>8,990,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn
                            50+ sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 5 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=Dell+XPS" alt="Dell XPS 15"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">Dell XPS 15 9530 Intel i7</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <span class="rating-text ms-2">(156 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">38,490,000 ₫</span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 8
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 6 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=Samsung+Galaxy" alt="Samsung Galaxy S24"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-10%</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">Samsung Galaxy S24 Ultra 512GB</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-muted"></i>
                            <span class="rating-text ms-2">(423 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">26,991,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>29,990,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 19
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 7 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=Sony+WH-1000" alt="Sony WH-1000XM5"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Mới</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">Sony WH-1000XM5 Headphones</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="rating-text ms-2">(567 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">8,490,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>9,990,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 35
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 8 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=Apple+Watch" alt="Apple Watch Ultra"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-30%</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">Apple Watch Ultra 2 Titanium</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <span class="rating-text ms-2">(289 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">13,993,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>19,990,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 12
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 9 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=Magic+Keyboard" alt="Magic Keyboard"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">Magic Keyboard Wireless White</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="rating-text ms-2">(134 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">2,490,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>2,990,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 41
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 10 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=USB-C+Hub" alt="USB-C Hub"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-12%</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">USB-C Hub 7-in-1 Multiport</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-muted"></i>
                            <span class="rating-text ms-2">(98 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">879,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>999,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 67
                            sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 11 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=Fast+Charger" alt="Fast Charger"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Mới</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">65W Fast Charger USB-C GaN</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="rating-text ms-2">(212 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">549,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>699,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn
                            100+ sản phẩm</p>
                    </div>
                </div>

                <!-- Product Card 12 -->
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="https://via.placeholder.com/280x280?text=Portable+SSD" alt="Portable SSD"
                            class="product-image">
                        <div class="product-overlay">
                            <button class="btn btn-primary btn-lg w-100 add-to-cart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                            </button>
                            <button class="btn btn-light w-100 mt-2">
                                <i class="fas fa-eye me-2"></i>Xem Chi Tiết
                            </button>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-18%</span>
                    </div>
                    <div class="product-info">
                        <h6 class="product-title">Samsung Portable SSD T7 Shield 2TB</h6>
                        <div class="product-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <span class="rating-text ms-2">(445 đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current text-primary fw-bold">4,590,000 ₫</span>
                            <span class="price-original text-muted ms-2"><s>5,590,000 ₫</s></span>
                        </div>
                        <p class="product-stock-status text-success mb-0"><i class="fas fa-check-circle me-1"></i>Còn 24
                            sản phẩm</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><i class="fas fa-chevron-left me-1"></i>Trước</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Tiếp<i class="fas fa-chevron-right ms-1"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
