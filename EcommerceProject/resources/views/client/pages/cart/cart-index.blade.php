@assets
    @vite('resources/css/cart.css')
@endassets

<div class="container-xl my-4" id="main-component" style="padding: 12px;">
    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> My Shopping Cart</h1>
    </div>

    <div class="cart-container">
        <div class="cart-toolbar">
            <div class="cart-toolbar__actions">
                <input type="checkbox" id="selectAll" class="cart-toolbar__checkbox form-check-input">
                <label for="selectAll" class="cart-toolbar__checkbox-label">Select All</label>
                <button class="cart-toolbar__button" id="deleteSelected">
                    <i class="fas fa-trash-alt"></i> Delete Selected
                </button>
            </div>
            <div class="cart-toolbar__stats">
                <span id="selectedCount">0</span> / <span id="totalItems">3</span> items selected
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
                        <tr class="cart-item">
                            <td class="cart-item__td" style="text-align: center;">
                                <input type="checkbox" class="cart-item__checkbox form-check-input" checked>
                            </td>
                            <td class="cart-item__td" style="min-width: 300px;">
                                <div class="cart-item__info">
                                    <div class="cart-item__image">
                                        <img src="https://via.placeholder.com/80?text=Product+1" alt="Áo Thun Nam">
                                    </div>
                                    <div class="cart-item__details">
                                        <div class="cart-item__name">Áo Thun Nam Premium</div>
                                        <div class="cart-item__variant">Màu: Cam</div>
                                        <div class="cart-item__price">
                                            <span class="cart-item__price-original text-nowrap">299.000 ₫</span>
                                            <span class="cart-item__price-discount text-nowrap">399.000 ₫</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="cart-item__td text-center">
                                <div class="pdp-quantity-control">
                                    <button class="pdp-qty-btn pdp-qty-minus"><i class="fas fa-minus"></i></button>
                                    <input type="number" id="quantity" class="pdp-qty-input" aria-label="Quantity to purchase">
                                    <button class="pdp-qty-btn pdp-qty-plus"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                            <td class="cart-item__td text-center">
                                <div class="cart-item__total">598.000 ₫</div>
                            </td>
                            <td class="cart-item__td" style="text-align: center;">
                                <button type="button" class="cart-item__delete" title="Xóa sản phẩm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        {{-- <tr class="cart-item" wire:key="cart-empty">
                            <td colspan="5">
                                <div class="cart-items__empty">
                                    <div class="cart-items__empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <div class="cart-items__empty-text">Your cart is empty</div>
                                    <p class="text-muted mb-0" style=" font-size: 0.9rem;">Add some products to start shopping</p>
                                </div>
                            </td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <h2 class="cart-summary__title">Order Summary</h2>

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
                    <span class="cart-summary__amount" id="shipping">0 ₫</span>
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
                <button class="cart-summary__button cart-summary__button--secondary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </button>

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
