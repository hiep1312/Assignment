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
        __proto__: window.BasePageController,
        _traits: [window.Fetchable, window.ApiPagination],

        _internal: {
            localStorageKeys: ['filter_categories', 'filter_availability', 'filter_ratings'],
            resetParams: ['search', 'page', 'per_page', 'sort_by', 'min_price', 'max_price'],
            sortOptions: ['price-asc', 'price-desc', 'newest']
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
                const [categoriesResponse, productsResponse, ratingSummaryResponse] = await Promise.all([
                    window.http.get(@js(route('api.categories.index')), { params: PageController._buildApiParams.categoryQueryParams() }),
                    window.http.get(@js(route('api.products.index')), { params: PageController._buildApiParams.productQueryParams() }),
                    window.http.get(@js(route('api.products.reviews.distribution')))
                ]);

                const { data: axiosCategoryData } = categoriesResponse;
                const { data: axiosProductData } = productsResponse;
                const { data: axiosRatingSummaryData } = ratingSummaryResponse;

                $wire.categories = axiosCategoryData.data;
                $wire.ratingStatistics = axiosRatingSummaryData.data;
                $wire.products = axiosProductData.data;
                $wire.pagination = PageController.getPagination(axiosProductData);
                $wire.priceRange = axiosProductData.price_range;
                $wire.isDataLoading = false;
                $wire.$refresh();

                return [axiosCategoryData, axiosProductData, axiosRatingSummaryData];
            }catch(axiosError) {
                const message = axiosError.response?.data?.message ?? axiosError.message;

                console.error("Failed to fetch: ", message);
                PageController.showError(500);
            }
        },

        _buildApiParams: {
            productQueryParams: () => {
                const params = window.getQueryParams();
                const apiParams = {};

                const allowedFields = {
                    search: () => params.search,
                    category: () => params.category,
                    filter_categories: () => localStorage.getItem('filter_categories'),
                    price_range: () => {
                        if(params.min_price || params.max_price) {
                            return `${params.min_price ?? $wire.priceRange?.min_price ?? 0} - ${params.max_price ?? $wire.priceRange?.max_price ?? Number.MAX_SAFE_INTEGER}`;
                        }
                    },
                    filter_availability: () => localStorage.getItem('filter_availability'),
                    filter_ids: () => {
                        const filters = {
                            bySelectedRatings: () => {
                                const selectedRatings = localStorage.getItem('filter_ratings')?.split(',') ?? [];
                                const productIds = $wire.ratingStatistics.flatMap(stat => {
                                    return selectedRatings.includes(stat.rating)
                                        ? stat.product_ids
                                        : [];
                                });

                                return productIds;
                            },
                        };

                        let uniqueProductIds = new Set();
                        for(const [name, fn] of Object.entries(filters)) {
                            uniqueProductIds = uniqueProductIds.union(new Set(fn()));
                        };

                        return [...uniqueProductIds].join(',');
                    },
                    page: () => params.page,
                    per_page: () => params.per_page,
                    sort_by: () => PageController._internal.sortOptions.includes(params.sort_by) && params.sort_by
                };

                for(const [key, getter] of Object.entries(allowedFields)) {
                    const value = getter();

                    if(Boolean(value)) {
                        apiParams[key] = value;
                    }
                }

                return {
                    aggregate: 'count:reviews, avg:reviews.rating, sum:inventories.stock, sum:inventories.sold_number',
                    include: 'primaryVariant',
                    status: 1,
                    with_price_range: 1,
                    ...apiParams
                };
            },

            categoryQueryParams: () => ({
                has_relation: 'products',
                aggregate: 'count:products',
                per_page: 30
            })
        },

        events: {
            "filter:search": (event) => {
                clearTimeout(window.searchDebounceTimer);

                window.searchDebounceTimer = setTimeout(() => {
                    const searchValue = event.detail.search?.trim();

                    if(searchValue !== undefined && searchValue !== window.getQueryParams('search')) {
                        window.setQueryParams('search', searchValue || null);
                        PageController.refreshData();
                    }
                }, 400);
            },

            "filter:categories": (event) => {
                clearTimeout(window.categoriesDebounceTimer);

                window.categoriesDebounceTimer = setTimeout(() => {
                    const selectedCategories = event.detail.categories;
                    const storedCategories = localStorage.getItem('filter_categories')?.split(',') ?? [];

                    if(
                        !(
                            selectedCategories.length === 1 && selectedCategories.includes('all') && storedCategories.length === 0
                        ) && (
                            storedCategories.length !== selectedCategories.length ||
                            selectedCategories.some(category => !storedCategories.includes(category))
                        )
                    ) {
                        if(selectedCategories.includes('all') || selectedCategories.length === 0) {
                            localStorage.removeItem('filter_categories')
                        }else {
                            localStorage.setItem('filter_categories', selectedCategories.join(','))
                        }

                        PageController.refreshData();
                    }
                }, 600);
            },

            "filter:price": (event) => {
                clearTimeout(window.priceDebounceTimer);

                window.priceDebounceTimer = setTimeout(() => {
                    const { minPrice, maxPrice } = event.detail;
                    const [minPriceParam, maxPriceParam] = window.getQueryParams(['min_price', 'max_price']);

                    if(
                        Number(minPrice) !== Number(minPriceParam ?? $wire.priceRange?.min_price ?? null) ||
                        Number(maxPrice) !== Number(maxPriceParam ?? $wire.priceRange?.max_price ?? null)
                    ) {
                        window.setQueryParams({
                            min_price: minPrice,
                            max_price: maxPrice
                        });

                        PageController.refreshData();
                    }
                }, 600);
            },

            "filter:ratings": (event) => {
                clearTimeout(window.categoriesDebounceTimer);

                window.categoriesDebounceTimer = setTimeout(() => {
                    const selectedRatings = event.detail.ratings;
                    const storedRatings = localStorage.getItem('filter_ratings')?.split(',') ?? [];

                    if(
                        !(
                            selectedRatings.length === 0 && storedRatings.length === 0
                        ) && (
                            storedRatings.length !== selectedRatings.length ||
                            selectedRatings.some(rating => !storedRatings.includes(rating))
                        )
                    ) {
                        if(selectedRatings.length === 0) {
                            localStorage.removeItem('filter_ratings')
                        }else {
                            localStorage.setItem('filter_ratings', selectedRatings.join(','))
                        }

                        PageController.refreshData();
                    }
                }, 600);
            },

            "filter:reset": () => {
                if(
                    window.getQueryParams(PageController._internal.resetParams).some(paramValue => paramValue !== null) ||
                    PageController._internal.localStorageKeys.some(key => localStorage.getItem(key) !== null)
                ) {
                    window.setQueryParams(Object.fromEntries(resetParams.map(key => [key, null])));

                    document.dispatchEvent(new Event('reset:filters'));
                    PageController.init();
                }
            },

            "filter:availability": (event) => {
                clearTimeout(window.availabilityDebounceTimer);

                window.availabilityDebounceTimer = setTimeout(() => {
                    const selectedAvailability = event.detail.availability;
                    const storedAvailability = localStorage.getItem('filter_availability')?.split(',') ?? [];

                    if(
                        !(
                            selectedAvailability.length === 0 && storedAvailability.length === 0
                        ) && (
                            storedAvailability.length !== selectedAvailability.length ||
                            selectedAvailability.some(availability => !storedAvailability.includes(availability))
                        )
                    ) {
                        if(selectedAvailability.length === 0) {
                            localStorage.removeItem('filter_availability')
                        }else {
                            localStorage.setItem('filter_availability', selectedAvailability.join(','))
                        }

                        PageController.refreshData();
                    }
                }, 600);
            },

            "filter:perPage": (event) => {
                clearTimeout(window.perPageDebounceTimer);

                window.perPageDebounceTimer = setTimeout(() => {
                    const perPage = Number(event.detail.perPage);

                    if(perPage != (window.getQueryParams('per_page') ?? 20)) {
                        window.setQueryParams({
                            per_page: perPage === 20 ? null : perPage,
                            page: null
                        });

                        PageController.refreshData();
                    }
                }, 500);
            },

            "filter:sortBy": (event) => {
                clearTimeout(window.sortByDebounceTimer);

                window.sortByDebounceTimer = setTimeout(() => {
                    const sortBy = event.detail.sortBy;
                    const isValidSortBy = PageController._internal.sortOptions.includes(sortBy);

                    if(sortBy !== window.getQueryParams('sort_by') && isValidSortBy) {
                        window.setQueryParams({
                            sort_by: sortBy,
                            page: null
                        });

                        PageController.refreshData();
                    }else {
                        window.setQueryParams('sort_by', isValidSortBy ? sortBy : null);
                    }
                }, 500);
            },

            "pagination:changed": (event) => {
                if(event.detail.page === $wire.pagination?.current_page) return;

                PageController.refreshData();
            },
        },
    };

    PageController.init();
</script>
@endscript

<div class="container-xl" id="main-component">
    <div class="row">
        <div class="col-lg-3 mb-4 wow fadeInUp" data-wow-delay="0.1s">
            <x-livewire-client::filter-sidebar>
                <x-livewire-client::filter-sidebar.section title="Search Books" icon="fas fa-eye" class="mb-4" wire:key="search-section"
                    x-data="{ search: window.getQueryParams('search') }" x-init="document.addEventListener('reset:filters', () => { search = '' })"
                    x-effect="document.dispatchEvent(new CustomEvent('filter:search', { detail: { search } }))">
                    <x-slot:container class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-warning"></i>
                        </span>
                        <input type="search" class="form-control border-start-0" x-model="search" placeholder="Book title...">
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <template x-if="!window.getQueryParams('category')">
                    <x-livewire-client::filter-sidebar.section title="Categories" icon="fas fa-list" class="mb-4" wire:key="categories-section"
                        x-data="{ checkedCategories: ['all'] }" x-init="document.addEventListener('reset:filters', () => { checkedCategories = ['all'] })"
                        x-effect="document.dispatchEvent(new CustomEvent('filter:categories', { detail: { categories: checkedCategories } }))">
                        <x-slot:container class="category-list">
                            <div class="form-check" wire:key="all-products">
                                <input class="form-check-input" type="checkbox" id="all-products" x-model="checkedCategories" value="all">
                                <label class="form-check-label" for="all-products">
                                    All Products <span class="badge bg-secondary ms-2 text-truncate" style="max-width: 60px;">{{ $pagination['total'] ?? 0 }}</span>
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
                </template>

                <x-livewire-client::filter-sidebar.section title="Price Range" icon="fas fa-dollar-sign" class="mb-4">
                    <x-slot:container x-data="{
                        priceRange: $wire.$entangle('priceRange'),
                        minValue: null,
                        maxValue: null,
                        init() {
                            document.addEventListener('reset:filters', () => {
                                this.minValue = this.priceRange?.min_price ?? 0;
                                this.maxValue = this.priceRange?.max_price ?? 0;
                            });

                            this.$watch('priceRange', value => {
                                if(Object.keys(value).length) {
                                    if(this.minValue === null && this.maxValue === null) {
                                        this.minValue = value.min_price;
                                        this.maxValue = value.max_price;
                                    }else if(value.min_price > this.minValue) {
                                        this.minValue = value.min_price;
                                    }else if(value.max_price < this.maxValue) {
                                        this.maxValue = value.max_price;
                                    }
                                }
                            });

                            this.$watch('minValue', value => {
                                if(Number(value) > Number(this.maxValue)) {
                                    this.maxValue = value;
                                }
                            });

                            this.$watch('maxValue', value => {
                                if(Number(value) < Number(this.minValue)) {
                                    this.minValue = value;
                                }
                            });
                        }
                    }" x-effect="document.dispatchEvent(new CustomEvent('filter:price', { detail: { minPrice: minValue, maxPrice: maxValue } }))">
                        <div class="price-range-container">
                            <input type="range" class="price-slider" id="minPrice" x-model="minValue" step="1000" wire:key="min-price"
                                :min="priceRange?.min_price ?? 0" :max="priceRange?.max_price ?? 0">
                            <input type="range" class="price-slider" id="maxPrice" x-model="maxValue" step="1000" wire:key="max-price"
                                :min="priceRange?.min_price ?? 0" :max="priceRange?.max_price ?? 0">
                        </div>
                        <div class="d-flex gap-2 mt-3 @unless(count($priceRange)) placeholder-glow @endunless">
                            <div class="flex-grow-1">
                                <label class="form-label small" for="minPriceInput">From</label>
                                @unless(count($priceRange))
                                    <input type="text" class="form-control form-control-sm placeholder" id="minPriceInput" readonly wire:key="min-price-input">
                                @else
                                    <input type="text" class="form-control form-control-sm" id="minPriceInput" readonly wire:key="min-price-input"
                                        :value="new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(minValue ?? priceRange?.min_price)">
                                @endunless
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label small" for="maxPriceInput">To</label>
                                @unless(count($priceRange))
                                    <input type="text" class="form-control form-control-sm placeholder" id="maxPriceInput" readonly wire:key="max-price-input">
                                @else
                                    <input type="text" class="form-control form-control-sm" id="maxPriceInput" readonly wire:key="max-price-input"
                                        :value="new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(maxValue ?? priceRange?.max_price)">
                                @endunless
                            </div>
                        </div>
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <x-livewire-client::filter-sidebar.section title="Rating" icon="fas fa-star" class="mb-4" wire:key="rating-section"
                    x-data="{ checkedRatings: [] }" x-init="document.addEventListener('reset:filters', () => { checkedRatings = [] })"
                    x-effect="document.dispatchEvent(new CustomEvent('filter:ratings', { detail: { ratings: checkedRatings } }))">
                    <x-slot:container class="rating-list">
                        @php
                            $ratingCounts = array_column($ratingStatistics, 'total_products', 'rating');
                        @endphp
                        @foreach (range(5, 1) as $ratingLevel)
                            @php
                                $filledStars = $ratingLevel;
                                $emptyStars = 5 - $ratingLevel;
                            @endphp
                            <div class="form-check @empty($ratingCounts) placeholder-glow @endempty" wire:key="rating-{{ $ratingLevel }}">
                                <input class="form-check-input" type="checkbox" x-model="checkedRatings" value="{{ $ratingLevel }}" id="rating-{{ $ratingLevel }}">
                                <label class="form-check-label" for="rating-{{ $ratingLevel }}">
                                    {!!
                                        str_repeat('<i class="fas fa-star text-warning"></i>', $filledStars) .
                                        str_repeat('<i class="fas fa-star text-muted"></i>', $emptyStars)
                                    !!}
                                    @empty($ratingCounts)
                                        <span class="badge bg-secondary ms-2 placeholder"><span class="d-inline-block" style="width: 14px;"></span></span>
                                    @else
                                        <span class="badge bg-secondary ms-2">{{ $ratingCounts[$ratingLevel] ?? 0 }}</span>
                                    @endempty
                                </label>
                            </div>
                        @endforeach
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <x-livewire-client::filter-sidebar.section title="Status" icon="fas fa-tag" class="mb-4" wire:key="status-section"
                    x-data="{ checkedStatus: [] }" x-effect="document.dispatchEvent(new CustomEvent('filter:availability', { detail: { availability: checkedStatus } }))"
                    x-init="document.addEventListener('reset:filters', () => { checkedStatus = [] })">
                    <x-slot:container>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="inStock" x-model="checkedStatus" value="in_stock">
                            <label class="form-check-label" for="inStock">
                                <span class="badge badge-status bg-success me-2">In Stock</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="new" x-model="checkedStatus" value="new_arrival">
                            <label class="form-check-label" for="new">
                                <span class="badge badge-status bg-primary me-2">New Arrival</span>
                            </label>
                        </div>
                    </x-slot:container>
                </x-livewire-client::filter-sidebar.section>

                <button class="btn btn-outline-secondary filter-btn w-100" type="button" x-on:click="document.dispatchEvent(new Event('filter:reset'))">
                    <i class="fas fa-redo me-2"></i>Reset
                </button>
            </x-livewire-client::filter-sidebar>
        </div>

        <div class="col-lg-9">
            <div class="top-bar mb-4 d-flex justify-content-center justify-content-md-between align-items-center flex-wrap gap-3 wow fadeInUp" data-wow-delay="0.1s">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-list me-2"></i>Showing <strong x-text="$wire.products.length"></strong> out of <strong x-text="$wire.pagination?.total ?? 0"></strong> products
                    </p>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end gap-3 flex-wrap align-items-center">
                    <div class="sort-container" x-data="{ perPage: window.getQueryParams('per_page') ?? 20 }"
                        x-effect="document.dispatchEvent(new CustomEvent('filter:perPage', { detail: { perPage } }))"
                        x-init="document.addEventListener('reset:filters', () => { perPage = 20 })">
                        <label for="sortBy" class="form-label me-2 mb-0">Per Page:</label>
                        <select id="sortBy" class="form-select form-select-sm" x-model="perPage"
                            style="border-radius: var(--border-radius-input-group); padding-top: .35rem; padding-bottom: .35rem;">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="sort-container" x-data="{ sortBy: window.getQueryParams('sort_by') ?? '' }"
                        x-effect="document.dispatchEvent(new CustomEvent('filter:sortBy', { detail: { sortBy } }))"
                        x-init="document.addEventListener('reset:filters', () => { sortBy = '' })">
                        <label for="sortBy" class="form-label me-2 mb-0">Sort By:</label>
                        <select id="sortBy" class="form-select form-select-sm" x-model="sortBy"
                            style="border-radius: var(--border-radius-input-group); padding-top: .35rem; padding-bottom: .35rem;">
                            <option value="">Default</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="newest">Newest</option>
                        </select>
                    </div>
                </div>
            </div>

            <x-livewire-client::product-grid>
                @if($isDataLoading)
                    @for($i = 0; $i < 12; $i++)
                        <x-livewire-client::product-grid.card-placeholder wire:key="product-placeholder-{{ $i }}"></x-livewire-client::product-grid.card-placeholder>
                    @endfor
                @else
                    @forelse($products as $product)
                        @php
                            $primaryVariant = $product['primary_variant'] ?? ['price' => 0, 'discount' => null];
                        @endphp
                        <x-livewire-client::product-grid.card :title="$product['title']" :price="$primaryVariant['discount'] ?? $primaryVariant['price']" :original-price="isset($primaryVariant['discount']) ? $primaryVariant['price'] : null"
                            :avg-rating="(float) $product['reviews_avg_rating']" :total-reviews="$product['reviews_count']" wire:key="product-{{ $product['id'] }}" :stock-quantity="(int) $product['inventories_sum_stock']" :sold-count="(int) $product['inventories_sum_sold_number']"
                            :isNew="now()->diffInDays($product['created_at'], true) <= 7" :discount-percent="isset($primaryVariant['discount']) ? floor((($primaryVariant['price'] - $primaryVariant['discount']) / $primaryVariant['price']) * 100) : 0">
                            <x-slot:img :src="asset('storage/' . ($product['main_image']['image_url'] ?? DefaultImage::PRODUCT->value))" :alt="'Product image of' . $product['title']"></x-slot:img>

                            <x-slot:add-to-cart-button>Add to Cart</x-slot:add-to-cart-button>
                            <x-slot:view-details-button href="{{ route('client.products.show', $product['slug']) }}">View Details</x-slot:view-details-button>
                        </x-livewire-client::product-grid.card>
                    @empty
                        <x-livewire-client::empty-state icon="fas fa-ghost" title="Oops! Nothing Found">
                            We couldn't find any products for your current selection. Try adjusting your filters or check back later.
                        </x-livewire-client::empty-state>
                    @endforelse
                @endif
            </x-livewire-client::product-grid>

            <x-livewire-client::pagination class="mt-4" :pagination="$pagination" />
        </div>
    </div>
    <div class="row justify-content-center align-items-center g-4 mt-4" wire:ignore>
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
