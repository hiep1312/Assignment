@assets
    @vite('resources/css/cart.css')
@endassets

@script
<script>
    const PageController = {
        __proto__: window.BasePageController,
        _traits: [window.Fetchable],

        _internal: {
            guestTokenKey: 'cart_guest_token',
            userAuthKey: 'auth_token',
            cartDataKey: 'cart_data'
        },

        init() {
            this.checkAuthStatus();
            super.init();
        },

        async checkAuthStatus() {
            try {
                const response = await window.http.get(@js(route('api.auth.me')));

                if(response.data) {
                    $wire.$set('isGuest', false);
                    $wire.$set('currentUser', response.data.data);
                }

            }catch(axiosError) {
                if(axiosError.status === 401) {
                    $wire.$set('isGuest', true);

                    document.dispatchEvent(new CustomEvent('auth:unauthorized', {
                        detail: { message: 'Please log in to continue with the checkout.' }
                    }));

                }else {
                    PageController.showError(500);
                }
            }
        },

        fetchData: async () => {
            try {
                const createResponse = await window.http.post(@js(route('api.cart.store')));

                const newCart = createResponse.data.data;
                localStorage.setItem(CartController._internal.cartDataKey, newCart.id);

                if (newCart.guest_token) {
                    localStorage.setItem(CartController._internal.guestTokenKey, newCart.guest_token);
                }

                const response = await window.http.get(
                    @js(route('api.cart.show')),
                    {
                        headers,
                        params: {
                            include: 'items.variant.product.mainImage,items.variant.inventory'
                        }
                    }
                );

                const cartData = response.data.data;

                $wire.cartItems = cartData.items || [];
                $wire.cartSummary = {
                    subtotal: cartData.subtotal || 0,
                    discount: cartData.discount || 0,
                    shipping: cartData.shipping || 0,
                    total: cartData.total || 0
                };
                $wire.isDataLoading = false;
                $wire.$refresh();

                return cartData;
            } catch (error) {
                const message = error.response?.data?.message ?? error.message;
                console.error("Failed to fetch cart: ", message);

                if (error.response?.status === 404) {
                    localStorage.removeItem(CartController._internal.cartDataKey);
                    return CartController.fetchData();
                }

                CartController.showError(500);
            }
        },

        async updateQuantity(itemId, quantity) {
            if (quantity < 1) return;

            try {
                const guestToken = localStorage.getItem(CartController._internal.guestTokenKey);
                const authToken = localStorage.getItem(CartController._internal.userAuthKey);

                let headers = {};
                if (authToken) {
                    headers['Authorization'] = `Bearer ${authToken}`;
                } else if (guestToken) {
                    headers['X-Guest-Token'] = guestToken;
                }

                await window.http.put(
                    @js(route('api.items.update', ':id')).replace(':id', itemId),
                    { quantity },
                    { headers }
                );

                await CartController.refreshData();
            } catch (error) {
                console.error("Failed to update quantity:", error);
                document.dispatchEvent(new CustomEvent('cart:error', {
                    detail: { message: 'Failed to update quantity. Please try again.' }
                }));
            }
        },

        async deleteItem(itemId) {
            try {
                const guestToken = localStorage.getItem(CartController._internal.guestTokenKey);
                const authToken = localStorage.getItem(CartController._internal.userAuthKey);

                let headers = {};
                if (authToken) {
                    headers['Authorization'] = `Bearer ${authToken}`;
                } else if (guestToken) {
                    headers['X-Guest-Token'] = guestToken;
                }

                await window.http.delete(
                    @js(route('api.items.destroy', ':id')).replace(':id', itemId),
                    { headers }
                );

                await CartController.refreshData();
            } catch (error) {
                console.error("Failed to delete item:", error);
                document.dispatchEvent(new CustomEvent('cart:error', {
                    detail: { message: 'Failed to delete item. Please try again.' }
                }));
            }
        },

        async deleteSelected(itemIds) {
            if (!itemIds || itemIds.length === 0) return;

            try {
                const guestToken = localStorage.getItem(CartController._internal.guestTokenKey);
                const authToken = localStorage.getItem(CartController._internal.userAuthKey);
                const cartId = localStorage.getItem(CartController._internal.cartDataKey);

                let headers = {};
                if (authToken) {
                    headers['Authorization'] = `Bearer ${authToken}`;
                } else if (guestToken) {
                    headers['X-Guest-Token'] = guestToken;
                }

                await window.http.delete(
                    @js(route('api.carts.items.delete', ':cart')).replace(':cart', cartId),
                    {
                        headers,
                        data: { item_ids: itemIds }
                    }
                );

                await CartController.refreshData();
            } catch (error) {
                console.error("Failed to delete selected items:", error);
                document.dispatchEvent(new CustomEvent('cart:error', {
                    detail: { message: 'Failed to delete selected items. Please try again.' }
                }));
            }
        },

        events: {
            "cart:updateQuantity": async (event) => {
                const { itemId, quantity } = event.detail;
                await CartController.updateQuantity(itemId, quantity);
            },

            "cart:deleteItem": async (event) => {
                const { itemId } = event.detail;
                await CartController.deleteItem(itemId);
            },

            "cart:deleteSelected": async (event) => {
                const { itemIds } = event.detail;
                await CartController.deleteSelected(itemIds);
            },

            "cart:checkout": (event) => {
                const isGuest = $wire.isGuest;

                if (isGuest) {
                    $wire.$set('showCheckoutWarning', true);
                    document.dispatchEvent(new CustomEvent('auth:required', {
                        detail: { message: 'Please login to proceed with checkout.' }
                    }));
                } else {
                    window.location.href = @js(route('client.checkout.index'));
                }
            }
        }
    };

    CartController.init();
</script>
@endscript

<div class="container-xl my-4" id="main-component" style="padding: 12px;">
    <x-livewire-client::alert type="danger" title="Error" icon="fas fa-exclamation-circle"
        x-data="{ showAlert: false, message: '' }"
        x-init="document.addEventListener('cart:error', event => { showAlert = true; message = event.detail.message; })"
        x-show="showAlert"
        wire:transition
        style="margin-bottom: 15px;"
        wire:key="cart-error-alert">
        <span x-text="message"></span>
        <x-slot:btn-close @click="showAlert = false"></x-slot:btn-close>
    </x-livewire-client::alert>

    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> My Shopping Cart</h1>
    </div>

    <div class="cart-container" x-data="{
        selectedItems: [],
        selectAll: false,

        toggleAll() {
            if (this.selectAll) {
                this.selectedItems = $wire.cartItems.map(item => item.id);
            } else {
                this.selectedItems = [];
            }
        },

        toggleItem(itemId) {
            const index = this.selectedItems.indexOf(itemId);
            if (index > -1) {
                this.selectedItems.splice(index, 1);
            } else {
                this.selectedItems.push(itemId);
            }
            this.selectAll = this.selectedItems.length === $wire.cartItems.length;
        },

        deleteSelected() {
            if (this.selectedItems.length === 0) {
                alert('Please select items to delete');
                return;
            }

            if (confirm(`Delete ${this.selectedItems.length} selected item(s)?`)) {
                document.dispatchEvent(new CustomEvent('cart:deleteSelected', {
                    detail: { itemIds: this.selectedItems }
                }));
                this.selectedItems = [];
                this.selectAll = false;
            }
        }
    }">
        <div class="cart-toolbar">
            <div class="cart-toolbar__actions">
                <input type="checkbox"
                    id="selectAll"
                    class="cart-toolbar__checkbox form-check-input"
                    x-model="selectAll"
                    @change="toggleAll()">
                <label for="selectAll" class="cart-toolbar__checkbox-label">Select All</label>
                <button class="cart-toolbar__button" @click="deleteSelected()">
                    <i class="fas fa-trash-alt"></i> Delete Selected
                </button>
            </div>
            <div class="cart-toolbar__stats">
                <span x-text="selectedItems.length"></span> / <span x-text="$wire.cartItems?.length ?? 0"></span> items selected
            </div>
        </div>

        <div class="cart-layout">
            <div class="cart-items">
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
                        <template x-if="$wire.isDataLoading">
                            @for($i = 0; $i < 3; $i++)
                            <tr class="cart-item placeholder-glow">
                                <td class="cart-item__td" style="text-align: center;">
                                    <span class="placeholder" style="width: 20px; height: 20px; display: inline-block;"></span>
                                </td>
                                <td class="cart-item__td" style="min-width: 300px;">
                                    <div class="cart-item__info">
                                        <div class="cart-item__image">
                                            <span class="placeholder" style="width: 80px; height: 80px; display: block;"></span>
                                        </div>
                                        <div class="cart-item__details">
                                            <div class="cart-item__name placeholder col-8"></div>
                                            <div class="cart-item__variant placeholder col-6"></div>
                                            <div class="cart-item__price">
                                                <span class="placeholder col-4"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="cart-item__td text-center">
                                    <span class="placeholder" style="width: 100px; height: 35px; display: inline-block;"></span>
                                </td>
                                <td class="cart-item__td text-center">
                                    <span class="placeholder col-6"></span>
                                </td>
                                <td class="cart-item__td" style="text-align: center;">
                                    <span class="placeholder" style="width: 30px; height: 30px; display: inline-block;"></span>
                                </td>
                            </tr>
                            @endfor
                        </template>

                        <template x-if="!$wire.isDataLoading && $wire.cartItems?.length > 0">
                            <template x-for="item in $wire.cartItems" :key="item.id">
                                <tr class="cart-item">
                                    <td class="cart-item__td" style="text-align: center;">
                                        <input type="checkbox"
                                            class="cart-item__checkbox form-check-input"
                                            :checked="selectedItems.includes(item.id)"
                                            @change="toggleItem(item.id)">
                                    </td>
                                    <td class="cart-item__td" style="min-width: 300px;">
                                        <div class="cart-item__info">
                                            <div class="cart-item__image">
                                                <img :src="item.variant?.product?.main_image?.image_url
                                                    ? '/storage/' + item.variant.product.main_image.image_url
                                                    : 'https://via.placeholder.com/80?text=No+Image'"
                                                    :alt="item.variant?.product?.title">
                                            </div>
                                            <div class="cart-item__details">
                                                <div class="cart-item__name" x-text="item.variant?.product?.title"></div>
                                                <div class="cart-item__variant" x-text="'Variant: ' + item.variant?.name"></div>
                                                <div class="cart-item__price">
                                                    <span class="cart-item__price-original text-nowrap"
                                                        x-text="new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price)"></span>
                                                    <template x-if="item.variant?.discount">
                                                        <span class="cart-item__price-discount text-nowrap"
                                                            x-text="new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.variant.price)"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart-item__td text-center">
                                        <div class="pdp-quantity-control"
                                            x-data="{ quantity: item.quantity }"
                                            x-init="$watch('quantity', value => {
                                                if (value > 0 && value !== item.quantity) {
                                                    document.dispatchEvent(new CustomEvent('cart:updateQuantity', {
                                                        detail: { itemId: item.id, quantity: parseInt(value) }
                                                    }));
                                                }
                                            })">
                                            <button class="pdp-qty-btn pdp-qty-minus"
                                                @click="if (quantity > 1) quantity--">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number"
                                                class="pdp-qty-input"
                                                x-model.number="quantity"
                                                :max="item.variant?.inventory?.stock ?? 999"
                                                min="1"
                                                aria-label="Quantity to purchase">
                                            <button class="pdp-qty-btn pdp-qty-plus"
                                                @click="if (quantity < (item.variant?.inventory?.stock ?? 999)) quantity++">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="cart-item__td text-center">
                                        <div class="cart-item__total"
                                            x-text="new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price * item.quantity)"></div>
                                    </td>
                                    <td class="cart-item__td" style="text-align: center;">
                                        <button type="button"
                                            class="cart-item__delete"
                                            title="Xóa sản phẩm"
                                            @click="if (confirm('Delete this item?')) {
                                                document.dispatchEvent(new CustomEvent('cart:deleteItem', {
                                                    detail: { itemId: item.id }
                                                }));
                                            }">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </template>

                        <template x-if="!$wire.isDataLoading && (!$wire.cartItems || $wire.cartItems.length === 0)">
                            <tr class="cart-item">
                                <td colspan="5">
                                    <div class="cart-items__empty">
                                        <div class="cart-items__empty-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <div class="cart-items__empty-text">Your cart is empty</div>
                                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Add some products to start shopping</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <h2 class="cart-summary__title">Order Summary</h2>

                <div class="cart-summary__row" :class="$wire.isDataLoading && 'placeholder-glow'">
                    <span>Subtotal:</span>
                    <span class="cart-summary__amount"
                        :class="$wire.isDataLoading && 'placeholder col-4'"
                        x-text="$wire.isDataLoading ? '' : new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format($wire.cartSummary?.subtotal ?? 0)"></span>
                </div>

                <div class="cart-summary__row" :class="$wire.isDataLoading && 'placeholder-glow'">
                    <span>Discount:</span>
                    <span class="cart-summary__amount"
                        :class="$wire.isDataLoading && 'placeholder col-4'"
                        x-text="$wire.isDataLoading ? '' : '-' + new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format($wire.cartSummary?.discount ?? 0)"></span>
                </div>

                <div class="cart-summary__row" :class="$wire.isDataLoading && 'placeholder-glow'">
                    <span>Shipping Fee:</span>
                    <span class="cart-summary__amount"
                        :class="$wire.isDataLoading && 'placeholder col-4'"
                        x-text="$wire.isDataLoading ? '' : new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format($wire.cartSummary?.shipping ?? 0)"></span>
                </div>

                <div class="cart-summary__row cart-summary__row--total" :class="$wire.isDataLoading && 'placeholder-glow'">
                    <span>Total:</span>
                    <span class="cart-summary__amount"
                        :class="$wire.isDataLoading && 'placeholder col-5'"
                        x-text="$wire.isDataLoading ? '' : new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format($wire.cartSummary?.total ?? 0)"></span>
                </div>

                <x-livewire-client::alert type="warning" title="Login Required" icon="fas fa-exclamation-triangle"
                    x-data="{ showAlert: false, message: '' }"
                    x-init="document.addEventListener('auth:unauthorized', event => { showAlert = true; message = event.detail.message; })"
                    x-show="showAlert" wire:transition wire:key="auth-warning-alert">

                    <span x-text="message"></span>

                    <div class="mt-2">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </div>

                    <x-slot:btn-close @click="showAlert = false"></x-slot:btn-close>
                </x-livewire-client::alert>

                <template x-if="!$wire.isGuest" wire:key="cart-checkout">
                    <div>
                        <label class="cart-summary__select-label" for="paymentMode">
                            <i class="fas fa-cog"></i> Payment Mode
                        </label>
                        <select id="paymentMode" class="form-select mb-3" style="border-radius: var(--border-radius-input-group); padding-top: .55rem; padding-bottom: .55rem;">
                            <option value="selected">Pay for selected items</option>
                            <option value="all">Pay for all items</option>
                        </select>

                        <button class="cart-summary__button"
                            @click="document.dispatchEvent(new Event('cart:checkout'))"
                            :disabled="$wire.isDataLoading || !$wire.cartItems || $wire.cartItems.length === 0">
                            <i class="fas fa-check-circle"></i> Proceed to Checkout
                        </button>
                    </div>
                </template>

                <a class="cart-summary__button cart-summary__button--secondary" href="{{ route('client.products.index') }}">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>

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
