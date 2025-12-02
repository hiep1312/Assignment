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
                with_product: true
            })
        },

        events: {
            "pagination:changed": (event) => {
                if(event.detail.page !== $wire.pagination.current_page) return;

                $wire.isCardLoading = true;
                $wire.$refresh();
                PageController.fetchData();
            }
        },

        registerEvents: () => {
            for(const [eventName, handler] of Object.entries(PageController.customEvents)) {
                document.addEventListener(eventName, handler);
            }

            /* Register default events and ensure cleanup on page unload */
            window.addEventListener('beforeunload', PageController.unregisterEvents);
        },

        unregisterEvents: () => {
            for(const [eventName, handler] of Object.entries(PageController.customEvents)) {
                document.removeEventListener(eventName, handler);
            }
        }
    };

    PageController.init();
</script>
@endscript

<div class="container-xl" id="main-component">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <x-livewire-client::product-filter>
            </x-livewire-client::product-filter>
        </div>

        <div class="col-lg-9">
            <div class="top-bar mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
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
</div>
