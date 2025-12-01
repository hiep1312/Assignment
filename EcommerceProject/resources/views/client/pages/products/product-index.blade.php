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

@script
<script>
    window.pagination = null;

    const PageController = {
        init: async () => {
            try {
                const response = await window.http.get(@js(route('api.products.index')), {
                    params: {
                        aggregate: 'count:reviews, avg:reviews.rating',
                    }
                });

                const axiosData = response.data;

                pagination = {
                    current_page: axiosData.current_page,
                    last_page: axiosData.last_page,
                    links: axiosData.links,
                    next_page_url: axiosData.next_page_url,
                    per_page: axiosData.per_page,
                    prev_page_url: axiosData.prev_page_url,
                    total: axiosData.total
                };

                $wire.set('products', axiosData.data, true);
            } catch(axiosError) {
                const message = axiosError.response.data?.message ?? axiosError.message;

                console.error("Failed to fetch: ", message);
            }
        },
    };

    PageController.init();
</script>
@endscript

<div class="container-xl" id="main-component">
    <div class="row">
        <div class="col-lg-3 mb-4">
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
        </div>

        <div class="col-lg-9">
            <div class="top-bar mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-list me-2"></i>Hiển thị <strong>12</strong> trên <strong>124</strong> sản
                        phẩm
                    </p>
                </div>
                <div class="sort-container">
                    <label for="sortBy" class="form-label me-2 mb-0">Sắp xếp:</label>
                    <select id="sortBy" class="form-select form-select-sm">
                        <option value="">Mặc Định</option>
                        <option value="price-asc">Giá: Thấp Đến Cao</option>
                        <option value="price-desc">Giá: Cao Đến Thấp</option>
                        <option value="rating">Đánh Giá Cao Nhất</option>
                        <option value="newest">Mới Nhất</option>
                        <option value="popular">Phổ Biến Nhất</option>
                    </select>
                </div>
            </div>

            <x-livewire-client::product-grid>
                {{-- @forelse()

                @empty
                    <div class="no-data-placeholder" id="noDataPlaceholder" style="display: none;">
                        <div class="no-data-content">
                            <i class="fas fa-search"></i>
                            <h4>Không tìm thấy sản phẩm</h4>
                            <p>Xin lỗi, không có sản phẩm nào phù hợp với tiêu chí tìm kiếm của bạn.</p>
                            <button class="btn btn-primary mt-3">
                                <i class="fas fa-redo me-2"></i>Đặt Lại Bộ Lọc
                            </button>
                        </div>
                    </div>
                @endforelse --}}
            </x-livewire-client::product-grid>

            <x-livewire-client::pagination class="mt-4" />
        </div>
    </div>
</div>
