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

@use('App\Enums\DefaultImage')
@script
<script>
    const PageController = {
        init: () => {
            PageController.fetchData();
            PageController.registerEvents();
        },

        fetchData: async () => {
            try {
                const [categoriesResponse, productsResponse] = await Promise.all([
                    window.http.get(@js(route('api.categories.index')), { params: PageController._buildApiParams.categoryQueryParams() }),
                    window.http.get(@js(route('api.products.index')), { params: PageController._buildApiParams.productQueryParams() })
                ]);

                const { data: axiosCategoryData } = categoriesResponse;
                const { data: axiosProductData } = productsResponse;

                $wire.categories = axiosCategoryData.data;
                $wire.products = axiosProductData.data;
                $wire.pagination = window.getPaginationFromApi(axiosProductData);
                $wire.isCardLoading = false;
                $wire.$refresh();

                return [axiosCategoryData, axiosProductData];
            }catch(axiosError) {
                const message = axiosError.response.data?.message ?? axiosError.message;

                console.error("Failed to fetch: ", message);
            }
        },

        _buildApiParams: {
            productQueryParams: () => {
                const params = new URLSearchParams(window.location.search);
                const allowedFields = ['page'];
                const apiParams = {};

                for(const field of allowedFields) {
                    if(params.has(field)) {
                        apiParams[field] = params.get(field);
                    }
                }

                return {
                    aggregate: 'count:reviews, avg:reviews.rating',
                    include: 'primaryVariant',
                    ...apiParams
                };
            },

            categoryQueryParams: () => ({
                has_relation: 'products',
                aggregate: 'count:products'
            })
        },

        events: {
            "pagination:changed": (event) => {
                if(event.detail.page === $wire.pagination?.current_page) return;

                $wire.$set('isCardLoading', true);
                PageController.fetchData();
            },

            "filter:categories": (event) => {
                const temp = () => {
                    window.history.pushState({}, '', window.location.pathname);
                }
            }
        },

        registerEvents: () => {
            for(const [eventName, handler] of Object.entries(PageController.events)) {
                document.addEventListener(eventName, handler);
            }

            /* Register default events and ensure cleanup on page unload */
            window.addEventListener('beforeunload', PageController.unregisterEvents);
        },

        unregisterEvents: () => {
            for(const [eventName, handler] of Object.entries(PageController.events)) {
                document.removeEventListener(eventName, handler);
            }
        }
    };

    PageController.init();
</script>
@endscript

<div class="container-xl" id="main-component">
    <div class="row">
        <div class="col-lg-3 mb-4 wow fadeInUp" data-wow-delay="0.1s">
            <x-livewire-client::filter-sidebar>
                <x-livewire-client::filter-sidebar.section title="Tìm Kiếm Sản Phẩm" icon="fas fa-eye" class="mb-4">
                    <x-slot:container class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-warning"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Tên sản phẩm...">
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <x-livewire-client::filter-sidebar.section title="Categories" icon="fas fa-list" class="mb-4" wire:key="categories-section"
                    x-data="{ checkedCategories: ['all'] }" x-effect="">
                    <x-slot:container class="category-list">
                        <div class="form-check" wire:key="all-products">
                            <input class="form-check-input" type="checkbox" id="all-products" x-model="checkedCategories" value="all">
                            <label class="form-check-label" for="all-products">
                                All Products <span class="badge bg-secondary ms-2 text-truncate" style="max-width: 60px;">{{ $pagination['total'] ?? 'N/A' }}</span>
                            </label>
                        </div>
                        @foreach($categories as $category)
                            <div class="form-check" wire:key="category-{{ $category['id'] }}">
                                <input class="form-check-input" type="checkbox" id="category-{{ $category['id'] }}" x-model="checkedCategories" value="{{ $category['id'] }}">
                                <label class="form-check-label" for="category-{{ $category['id'] }}">
                                    {{ $category['name'] }} <span class="badge bg-secondary ms-2 text-truncate" style="max-width: 60px;">{{ $category['products_count'] }}</span>
                                </label>
                            </div>
                        @endforeach
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <x-livewire-client::filter-sidebar.section title="Khoảng Giá" icon="fas fa-dollar-sign" class="mb-4">
                    <x-slot:container>
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
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <x-livewire-client::filter-sidebar.section title="Đánh Giá" icon="fas fa-star" class="mb-4">
                    <x-slot:container class="rating-list">
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
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <x-livewire-client::filter-sidebar.section title="Trạng Thái" icon="fas fa-tag" class="mb-4">
                    <x-slot:container>
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
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <button class="btn btn-outline-secondary filter-btn w-100" type="button">
                    <i class="fas fa-redo me-2"></i>Đặt Lại
                </button>
            </x-livewire-client::filter-sidebar>
        </div>

        <div class="col-lg-9">
            <div class="top-bar mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2 wow fadeInUp" data-wow-delay="0.1s">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-list me-2"></i>Showing <strong x-text="$wire.products.length"></strong> out of <strong x-text="$wire.pagination?.total ?? 0"></strong> products
                    </p>
                </div>
                <div class="sort-container">
                    <label for="sortBy" class="form-label me-2 mb-0">Sắp xếp:</label>
                    <select id="sortBy" class="form-select form-select-sm" style="border-radius: var(--border-radius-input-group); padding-top: .35rem; padding-bottom: .35rem;">
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
                @if($isCardLoading)
                    @for($i = 0; $i < 12; $i++)
                        <x-livewire-client::product-grid.card-placeholder wire:key="product-placeholder-{{ $i }}"></x-livewire-client::product-grid.card-placeholder>
                    @endfor
                @else
                    @forelse($products as $product)
                        @php
                            $primaryVariant = $product['primary_variant'] ?? ['price' => 0, 'discount' => null];
                            $mainImage = $product['main_image'];
                            $inventoryStats = $product['inventory_summary'] ?? ['total_stock' => 0, 'total_sold' => 0];
                        @endphp
                        <x-livewire-client::product-grid.card :title="$product['title']" :price="$primaryVariant['discount'] ?? $primaryVariant['price']" :original-price="$primaryVariant['discount']" :stock-quantity="$inventoryStats['total_stock']" :sold-count="$inventoryStats['total_sold']"
                            :avg-rating="(float) $product['reviews_avg_rating']" :total-reviews="$product['reviews_count']" wire:key="product-{{ $product['id'] }}">
                            <x-slot:img :src="asset('storage/' . (isset($mainImage['image_url']) ? $mainImage['image_url'] : DefaultImage::PRODUCT->value))" :alt="'Product image of' . $product['title']"></x-slot:img>

                            <x-slot:add-to-cart-button>Add to Cart</x-slot:add-to-cart-button>
                            <x-slot:view-details-button>View Details</x-slot:view-details-button>
                        </x-livewire-client::product-grid.card>
                    @empty
                        <div class="no-data-placeholder">
                            <div class="no-data-content">
                                <i class="fas fa-ghost"></i>
                                <h4>Oops! Nothing Found</h4>
                                <p>We couldn't find any products for your current selection. Try adjusting your filters or check back later.</p>
                            </div>
                        </div>
                    @endforelse
                @endif
            </x-livewire-client::product-grid>

            <x-livewire-client::pagination class="mt-4" :pagination="$pagination" />
        </div>
    </div>
    <div class="row justify-content-center align-items-center g-4 mt-4">
        <div class="col-lg-3 col-sm-6 wow fadeInUp rounded" data-wow-delay="0.1s">
            <div class="service-item rounded pt-3">
                <div class="p-4">
                    <i class="fa fa-3x fa-rocket text-primary mb-4"></i>
                    <h5>Fast Delivery</h5>
                    <p>We provide quick and reliable shipping to ensure timely book deliveries.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 wow fadeInUp rounded" data-wow-delay="0.3s">
            <div class="service-item rounded pt-3">
                <div class="p-4">
                    <i class="fa fa-3x fa-lock text-primary mb-4"></i>
                    <h5>Secure Payment</h5>
                    <p>Shop with confidence using our fully encrypted and protected payment system.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 wow fadeInUp rounded" data-wow-delay="0.5s">
            <div class="service-item rounded pt-3">
                <div class="p-4">
                    <i class="fa fa-3x fa-exchange-alt text-primary mb-4"></i>
                    <h5>Easy Returns</h5>
                    <p>Enjoy a simple 30-day return process designed for smooth, hassle free exchanges.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 wow fadeInUp rounded" data-wow-delay="0.7s">
            <div class="service-item rounded pt-3">
                <div class="p-4">
                    <i class="fa fa-3x fa-headset text-primary mb-4"></i>
                    <h5>24/7 Support</h5>
                    <p>Our support team is always available to assist you anytime, day or night.</p>
                </div>
            </div>
        </div>
    </div>
</div>
