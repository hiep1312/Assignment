@assets
    @vite('resources/css/product-filter.css')
@endassets

<div class="filter-card">
    <div class="mb-4">
        <label class="form-label fw-bold mb-3">
            <i class="fas fa-eye me-2 text-primary"></i>Tìm Kiếm Sản Phẩm
        </label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-warning"></i>
            </span>
            <input type="text" class="form-control border-start-0" placeholder="Tên sản phẩm...">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold mb-3">
            <i class="fas fa-list me-2 text-primary"></i>Danh Mục
        </label>
        <div class="category-list">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cat1" checked>
                <label class="form-check-label" for="cat1">
                    Tất Cả Sản Phẩm <span class="badge bg-secondary ms-2 text-truncate" style="max-width: 60px;">124000000000000000000000000</span>
                </label>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold mb-3">
            <i class="fas fa-dollar-sign me-2 text-primary"></i>Khoảng Giá
        </label>
        <div class="price-range-container">
            <input type="range" class="price-slider" id="minPrice" min="0" max="10000000" value="0" step="100000">
            <input type="range" class="price-slider" id="maxPrice" min="0" max="10000000" value="10000000" step="100000">
        </div>
        <div class="d-flex gap-2 mt-3">
            <div class="flex-grow-1">
                <label class="form-label small" for="minPriceInput">Từ</label>
                <input type="text" class="form-control form-control-sm" id="minPriceInput" value="0" readonly>
            </div>
            <div class="flex-grow-1">
                <label class="form-label small" for="maxPriceInput">Đến</label>
                <input type="text" class="form-control form-control-sm" id="maxPriceInput" value="10.000.000" readonly>
            </div>
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

    <div class="mb-4">
        <label class="form-label fw-bold mb-3">
            <i class="fas fa-tag me-2 text-primary"></i>Trạng Thái
        </label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="inStock" checked>
            <label class="form-check-label" for="inStock">
                <span class="badge badge-status bg-success me-2">Còn Hàng</span>
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="new">
            <label class="form-check-label" for="new">
                <span class="badge badge-status bg-primary me-2">Hàng Mới</span>
            </label>
        </div>
    </div>

    <button class="btn btn-outline-secondary filter-btn w-100" type="button">
        <i class="fas fa-redo me-2"></i>Đặt Lại
    </button>
</div>
