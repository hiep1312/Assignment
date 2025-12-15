@assets
    @vite('resources/css/cart.css')
@endassets

@use('App\Enums\DefaultImage')
@script
<script>
    const PageController = {
        __proto__: window.BasePageController,
        _traits: [window.Fetchable],

        _internal: {
            batchedItemUpdates: {

            }
        },

        init() {
            this.checkAuthStatus();
            super.init();
        },

        async checkAuthStatus() {
            try {
                if(!window.getCookie('auth_token')) {
                    throw { status: 401 };
                }

                const response = await window.http.get(@js(route('api.auth.me')));

                $wire.isGuest = false;
                $wire.currentUser = response.data.user;
                $wire.$refresh();

            }catch(error) {
                if(error.status === 401) {
                    $wire.$set('isGuest', true);
                    window.setCookie('auth_token', null, { expires: 0 });

                }else {
                    PageController.showError(500);
                }
            }
        },

        fetchData: async () => {
            try {
                let { data: axiosCartData } = await window.http.get(@js(route('api.cart.show')), {
                    params: PageController._buildApiParams.cartQueryParams()
                });

                const isExpired = new Date(axiosCartData.expires_at) < new Date();
                if(isExpired) {
                    ({ data: axiosCartData } = await window.http.post(@js(route('api.carts.refresh')), {}, {
                        params: PageController._buildApiParams.cartQueryParams()
                    }));
                }

                $wire.cartData = axiosCartData.data;
                $wire.cartItems = axiosCartData.data.items || [];
                $wire.isDataLoading = false;
                $wire.$refresh();

                return axiosCartData;

            }catch(axiosError) {
                const message = axiosError.response?.data?.message ?? axiosError.message;
                console.error("Failed to fetch cart: ", message);

                if(axiosError.status === 404) {
                    $wire.isDataLoading = false;
                    $wire.$refresh();
                }else {
                    PageController.showError(500);
                }
            }
        },

        _buildApiParams: {
            cartQueryParams: () => ({
                include: 'items.productVariant.product,items.productVariant.inventory',
                aggregate: 'count:items'
            })
        },

        events: {
            'cart-item:delete': (event) => {

            },

            'cart-item:update': (event) => {
                PageController._internal.batchedItemUpdates[event.detail.item_id] = event.detail.quantity;
                clearTimeout(window.cartUpdateTimer);

                window.cartUpdateTimer = setTimeout(async () => {
                    const batchedUpdates = PageController._internal.batchedItemUpdates;
                    PageController._internal.batchedItemUpdates = {};

                    const cartItems = Object.entries(batchedUpdates).map(([itemId, quantity]) => ({
                        item_id: parseInt(itemId),
                        quantity: quantity
                    }));

                    if(cartItems.length === 0) return;

                    try {
                        const { data: axiosCartData } = await window.http.put(@js(route('api.cart.update')), { cart_items: cartItems });

                        $wire.cartData = axiosCartData.data;
                        $wire.cartItems = axiosCartData.data.items || [];
                        $wire.$refresh();

                        window.showToast({
                            title: 'Cart Updated',
                            message: 'Your cart has been updated successfully.',
                            type: 'success',
                            duration: 12,
                            time: new Date().toISOString(),
                            icon: 'fas fa-check-circle',
                            animation: 'slideInRight'
                        });

                    }catch(axiosError) {
                        const message = axiosError.response?.data?.message ?? axiosError.message;

                        console.error("Failed to update cart: ", message);
                        window.showToast({
                            title: 'Cart Update Failed',
                            message,
                            type: 'danger',
                            duration: 60,
                            time: new Date().toISOString(),
                            icon: 'fas fa-exclamation-circle',
                            animation: 'slideInRight'
                        });
                    }
                }, 2000);
            }
        },
    };

    PageController.init();
</script>
@endscript

<div class="container-xl my-4" id="main-component" style="padding: 12px;"
    x-data="{
        selectedItems: [],

        get selectedCount() {
            return this.selectedItems.length;
        },

        get totalItems() {
            return $wire.cartItems.length || 0;
        },

        toggleSelectAll() {
            this.selectedItems = this.selectedCount !== this.totalItems ? $wire.cartItems.map(item => item.id) : [];
        },
    }">
    <livewire:client.components.toast wire:key="toast-container">
    <livewire:client.components.confirm-modal wire:key="confirm-modal">

    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> My Shopping Cart</h1>
    </div>

    <x-livewire-client::alert type="warning" title="Sign In Required" icon="fas fa-sign-in-alt" wire:cloak wire:transition
        wire:show="isGuest && !$wire.isDataLoading" wire:key="guest-alert">
        You need to be signed in to continue with the checkout process.
        Please <a href="{{ route('login') }}" class="alert-link fw-bold">sign in</a> or
        <a href="{{ route('register') }}" class="alert-link fw-bold">create an account</a> to proceed with your purchase.
    </x-livewire-client::alert>

    <x-livewire-client::alert type="info" title="Your Cart is Empty" icon="fas fa-inbox" wire:cloak wire:transition
        wire:show="cartItems.length === 0 && !$wire.isDataLoading" wire:key="empty-cart-alert">
        Your shopping cart doesn’t have any items yet.
        Start exploring our books and add your favorites to your cart.
        <a href="{{ route('client.products.index') }}" class="alert-link fw-bold">Start shopping now</a>
    </x-livewire-client::alert>

    <div class="cart-container">
        <div class="cart-toolbar">
            <div class="cart-toolbar__actions">
                @if($isDataLoading)
                    <div class="placeholder-glow d-flex align-items-center gap-2" wire:key="cart-toolbar-placeholder">
                        <input type="checkbox" class="placeholder cart-toolbar__checkbox form-check-input">
                        <label class="placeholder cart-toolbar__checkbox-label" style="width: 80px; height: 20px;"></label>
                        <button class="placeholder cart-toolbar__button" style="width: 140px; height: 38px;"></button>
                    </div>
                @else
                    <input type="checkbox" id="selectAll" class="cart-toolbar__checkbox form-check-input" wire:key="cart-toolbar-checkbox"
                        :checked="selectedCount === totalItems && totalItems > 0" x-on:change="toggleSelectAll">
                    <label for="selectAll" class="cart-toolbar__checkbox-label" wire:key="cart-toolbar-checkbox-label">Select All</label>
                    <button class="cart-toolbar__button" :disabled="totalItems === 0" wire:key="cart-toolbar-button" onclick="showConfirmModal(this)"
                        :data-title="selectedCount ? 'Delete Selected Items' : 'Clear Shopping Cart'" data-type="warning"
                        :data-message="selectedCount
                            ? 'Are you sure you want to remove the selected items from your cart?'
                            : 'Are you sure you want to clear your entire shopping cart? All items will be removed.'"
                        :data-confirm-label="selectedCount ? 'Delete' : 'Clear Cart'" data-event-name="cart-item:delete"
                        :data-event-data="selectedCount ? JSON.stringify(selectedItems) : 'all'">
                        <i class="fas fa-trash-alt"></i> <span x-text="selectedCount === 0 ? 'Clear Cart' : 'Delete Selected'"></span>
                    </button>
                @endif
            </div>
            <div class="cart-toolbar__stats">
                @if($isDataLoading)
                    <span class="placeholder" style="width: 125px; height: 21px;" wire:key="cart-toolbar-stats-placeholder"></span>
                @else
                    <span x-text="selectedCount" wire:key="cart-toolbar-stats-selected"></span> / <span x-text="totalItems" wire:key="cart-toolbar-stats-total"></span> items selected
                @endif
            </div>
        </div>

        <div class="cart-layout">
            <div class="cart-items">
                <div class="table-responsive">
                    <table class="cart-items__table">
                        <thead class="cart-items__thead">
                            <tr>
                                <th class="cart-items__th">Select</th>
                                <th class="cart-items__th">Product</th>
                                <th class="cart-items__th">Quantity</th>
                                <th class="cart-items__th">Total Price</th>
                                <th class="cart-items__th">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($isDataLoading)
                                @for($i = 0; $i < 3; $i++)
                                    <tr class="cart-item placeholder-glow" wire:key="cart-item-placeholder-{{ $i }}">
                                        <td class="cart-item__td text-center">
                                            <input type="checkbox" class="placeholder cart-item__checkbox form-check-input">
                                        </td>
                                        <td class="cart-item__td" style="min-width: 300px;">
                                            <div class="cart-item__info">
                                                <div class="cart-item__image placeholder" style="background-color: currentColor;"></div>
                                                <div class="cart-item__details">
                                                    <div class="placeholder cart-item__name" style="width: 180px; height: 20px;"></div>
                                                    <div class="placeholder cart-item__variant" style="width: 100px; height: 18px;"></div>
                                                    <div class="cart-item__price">
                                                        <span class="placeholder cart-item__price-original" style="width: 80px; height: 21px;"></span>
                                                        <span class="placeholder cart-item__price-discount" style="width: 80px; height: 21px;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart-item__td text-center">
                                            <span class="placeholder pdp-quantity-control" style="width: 115px; height: 40px; background-color: currentColor;"></span>
                                        </td>
                                        <td class="cart-item__td text-center">
                                            <span class="placeholder cart-item__total" style="width: 100px; height: 24px;"></span>
                                        </td>
                                        <td class="cart-item__td text-center">
                                            <span class="placeholder cart-item__delete" style="width: 36px; height: 36px;"></span>
                                        </td>
                                    </tr>
                                @endfor
                            @else
                                @forelse($cartItems as $item)
                                    @php
                                        $productVariant = $item['product_variant'] ?? [];
                                        $product = $productVariant['product'] ?? null;
                                    @endphp
                                    <tr class="cart-item" wire:key="cart-item-{{ $item['id'] }}" x-data="{
                                        selectedQuantity: {{ $item['quantity'] ?? 0 }},

                                        get minPurchasable() {
                                            return @json(
                                                isset($productVariant['inventory']['stock'])
                                                    ? ($productVariant['inventory']['stock'] ? 1 : 0)
                                                    : $item['quantity']
                                            );
                                        },

                                        get maxPurchasable() {
                                            return @json(
                                                isset($productVariant['inventory']['stock'])
                                                    ? ((int) $productVariant['inventory']['stock'])
                                                    : $item['quantity']
                                            );
                                        },

                                        init() {
                                            this.$watch('selectedQuantity', (value, oldValue) => {
                                                if(value === '') return;

                                                let quantity = isNaN(value) ? 1 : parseInt(value);
                                                quantity = Math.max(this.minPurchasable, Math.min(quantity, this.maxPurchasable));

                                                if(quantity !== oldValue) {
                                                    this.selectedQuantity = quantity;

                                                    document.dispatchEvent(new CustomEvent('cart-item:update', {
                                                        detail: {
                                                            item_id: @json($item['id']),
                                                            quantity
                                                        }
                                                    }));
                                                }

                                            });
                                        }
                                    }">
                                        <td class="cart-item__td text-center">
                                            <input type="checkbox" class="cart-item__checkbox form-check-input" x-model="selectedItems" value="{{ $item['id'] }}">
                                        </td>
                                        <td class="cart-item__td" style="min-width: 300px;">

                                            <div class="cart-item__info">
                                                <div class="cart-item__image">
                                                    <img src="{{ asset('storage/' . ($product['main_image']['image_url'] ?? DefaultImage::PRODUCT->value)) }}" alt="Product image of {{ $product['title'] ?? 'Unavailable' }}">
                                                </div>
                                                <div class="cart-item__details">
                                                    <div class="cart-item__name">{{ $product['title'] ?? 'Unavailable' }}</div>
                                                    <div class="cart-item__variant">Variant: {{ $productVariant['name'] ?? 'N/A' }}</div>
                                                    <div class="cart-item__price">
                                                        <span class="cart-item__price-original text-nowrap">{{ number_format((int) $item['price'], 0, '.', '.') }}đ</span>
                                                        @if($productVariant && (int) $productVariant['price'] > (int) $item['price'])
                                                            <span class="cart-item__price-discount text-nowrap">{{ number_format((int) $productVariant['price'], 0, '.', '.') }}₫</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart-item__td text-center">
                                            <div class="pdp-quantity-control">
                                                <button x-on:click="selectedQuantity = Math.max(minPurchasable, Number.isNaN(selectedQuantity) ? 0 : (selectedQuantity - 1))"  class="pdp-qty-btn pdp-qty-minus"><i class="fas fa-minus"></i></button>
                                                <input type="number" id="quantity-{{ $item['id'] }}" x-model="selectedQuantity" :min="minPurchasable" :max="maxPurchasable" x-model="selectedQuantity" class="pdp-qty-input" aria-label="Quantity to purchase">
                                                <button x-on:click="selectedQuantity = Math.min(Number.isNaN(selectedQuantity) ? minPurchasable : (selectedQuantity + 1), maxPurchasable)" class="pdp-qty-btn pdp-qty-plus"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </td>
                                        <td class="cart-item__td text-center">
                                            <div class="cart-item__total"><span x-text="new Intl.NumberFormat('vi-VN').format(selectedQuantity * @json((int) $item['price']))"></span>đ</div>
                                        </td>
                                        <td class="cart-item__td" style="text-align: center;">
                                            <button type="button" class="cart-item__delete" title="Remove item" onclick="showConfirmModal(this)"
                                                data-title="Remove Item from Cart" data-type="warning" data-message="Are you sure you want to remove this item from your cart? This action cannot be undone."
                                                data-confirm-label="Remove" data-event-name="cart-item:delete" data-event-data="{{ $item['id'] }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="cart-item" wire:key="cart-empty">
                                        <td colspan="5">
                                            <div class="cart-items__empty">
                                                <div class="cart-items__empty-icon">
                                                    <i class="fas fa-inbox"></i>
                                                </div>
                                                <div class="cart-items__empty-text">Your cart is empty</div>
                                                <p class="text-muted mb-0" style=" font-size: 0.9rem;">Add some products to start shopping</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cart-summary">
                <h2 class="cart-summary__title">Order Summary</h2>
                @if($isDataLoading)
                    <div class="placeholder-glow">
                        <div class="cart-summary__row">
                            <span class="placeholder" style="width: 62px; height: 21px;"></span>
                            <span class="placeholder cart-summary__amount" style="width: 90px; height: 21px;"></span>
                        </div>
                        <div class="cart-summary__row">
                            <span class="placeholder" style="width: 65px; height: 20px;"></span>
                            <span class="placeholder cart-summary__amount" style="width: 75px; height: 20px;"></span>
                        </div>
                        <div class="cart-summary__row">
                            <span class="placeholder" style="width: 90px; height: 20px;"></span>
                            <span class="placeholder cart-summary__amount" style="width: 60px; height: 20px;"></span>
                        </div>
                        <div class="cart-summary__row cart-summary__row--total">
                            <span class="placeholder" style="width: 60px; height: 24px;"></span>
                            <span class="placeholder cart-summary__amount" style="width: 120px; height: 24px;"></span>
                        </div>
                        <span class="placeholder w-100 cart-summary__button" style="height: 50px;"></span>
                        <span class="placeholder w-100 cart-summary__button cart-summary__button--secondary" style="height: 50px;"></span>
                    </div>
                @else
                    <div class="cart-summary__row">
                        <span>Subtotal:</span>
                        <span class="cart-summary__amount" id="subtotal">1.098.000 ₫</span>
                    </div>

                    <div class="cart-summary__row">
                        <span>Discount:</span>
                        <span class="cart-summary__amount" id="discount">-100.000 ₫</span>
                    </div>

                    <div class="cart-summary__row">
                        <span>Shipping Fee:</span>
                        <span class="cart-summary__amount" id="shipping">30.000đ</span>
                    </div>

                    <div class="cart-summary__row cart-summary__row--total">
                        <span>Total:</span>
                        <span id="total" class="cart-summary__amount">998.000 ₫</span>
                    </div>

                    <label class="cart-summary__select-label" for="paymentMode">
                        <i class="fas fa-cog"></i> Payment Mode
                    </label>

                    <select id="paymentMode" class="form-select mb-3"
                        style="border-radius: var(--border-radius-input-group); padding-top: .55rem; padding-bottom: .55rem;">
                        <option value="selected">Pay for selected items</option>
                        <option value="all">Pay for all items</option>
                    </select>

                    <button class="cart-summary__button">
                        <i class="fas fa-check-circle"></i> Proceed to Checkout
                    </button>

                    <a href="{{ route('client.products.index') }}" class="cart-summary__button cart-summary__button--secondary d-block text-center">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                @endif

                <div class="cart-summary__disclaimer">
                    <i class="fas fa-shield-alt"></i> Secure payment protected
                </div>
            </div>
        </div>

        <div class="cart-footer">
            <a href="{{ route('client.products.index') }}" class="cart-footer__link">
                <i class="fas fa-arrow-left"></i> Back to Store
            </a>
        </div>
    </div>
</div>
