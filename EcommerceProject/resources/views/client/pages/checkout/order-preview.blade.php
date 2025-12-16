@assets
    @vite('resources/css/checkout.css')
@endassets
@use('App\Livewire\Client\Components\Checkout\Header')
<div class="container-xl my-4" id="main-component" style="padding: 12px;">
    <x-livewire-client::checkout.header icon="fas fa-list-alt" title="Order Preview" :active-step="Header::STEP_PREVIEW" />

    <div class="cop-container">
        <div class="row">
            <div class="col-lg-8">
                <div class="cop-card">
                    <h2 class="cop-card-title">
                        <i class="fas fa-box-open"></i>
                        Sản phẩm trong đơn hàng
                    </h2>

                    <!-- Product Item 1 -->
                    <div class="cop-product-item" data-product-id="1">
                        <img src="/placeholder.svg?height=80&width=80" alt="Áo thun nam" class="cop-product-image">
                        <div class="cop-product-info">
                            <div class="cop-product-name">Áo thun nam cao cấp cotton 100%</div>
                            <div class="cop-product-variant">
                                <i class="fas fa-palette"></i> Màu: Cam |
                                <i class="fas fa-ruler"></i> Size: L
                            </div>
                            <div class="cop-product-price-row">
                                <div class="cop-quantity-control">
                                    <span class="cop-quantity-label">Số lượng:</span>
                                    <button class="cop-quantity-btn" onclick="decreaseQuantity(1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="cop-quantity-input" value="2" min="1" max="99" data-product-id="1" onchange="updateQuantity(1, this.value)" readonly>
                                    <button class="cop-quantity-btn" onclick="increaseQuantity(1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="cop-product-prices">
                                    <div class="cop-product-unit-price">295.000₫ x <span class="cop-qty-display">2</span></div>
                                    <div class="cop-product-total-price" data-unit-price="295000">590.000₫</div>
                                    <div class="cop-product-price-original">650.000₫</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Item 2 -->
                    <div class="cop-product-item" data-product-id="2">
                        <img src="/placeholder.svg?height=80&width=80" alt="Quần jean" class="cop-product-image">
                        <div class="cop-product-info">
                            <div class="cop-product-name">Quần jean nam form slim fit</div>
                            <div class="cop-product-variant">
                                <i class="fas fa-palette"></i> Màu: Xanh đậm |
                                <i class="fas fa-ruler"></i> Size: 32
                            </div>
                            <div class="cop-product-price-row">
                                <div class="cop-quantity-control">
                                    <span class="cop-quantity-label">Số lượng:</span>
                                    <button class="cop-quantity-btn" onclick="decreaseQuantity(2)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="cop-quantity-input" value="1" min="1" max="99" data-product-id="2" onchange="updateQuantity(2, this.value)" readonly>
                                    <button class="cop-quantity-btn" onclick="increaseQuantity(2)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="cop-product-prices">
                                    <div class="cop-product-unit-price">450.000₫ x <span class="cop-qty-display">1</span></div>
                                    <div class="cop-product-total-price" data-unit-price="450000">450.000₫</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Item 3 -->
                    <div class="cop-product-item" data-product-id="3">
                        <img src="/placeholder.svg?height=80&width=80" alt="Giày thể thao" class="cop-product-image">
                        <div class="cop-product-info">
                            <div class="cop-product-name">Giày thể thao nam phong cách street style</div>
                            <div class="cop-product-variant">
                                <i class="fas fa-palette"></i> Màu: Trắng |
                                <i class="fas fa-shoe-prints"></i> Size: 42
                            </div>
                            <div class="cop-product-price-row">
                                <div class="cop-quantity-control">
                                    <span class="cop-quantity-label">Số lượng:</span>
                                    <button class="cop-quantity-btn" onclick="decreaseQuantity(3)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="cop-quantity-input" value="1" min="1" max="99" data-product-id="3" onchange="updateQuantity(3, this.value)" readonly>
                                    <button class="cop-quantity-btn" onclick="increaseQuantity(3)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="cop-product-prices">
                                    <div class="cop-product-unit-price">850.000₫ x <span class="cop-qty-display">1</span></div>
                                    <div class="cop-product-total-price" data-unit-price="850000">850.000₫</div>
                                    <div class="cop-product-price-original">999.000₫</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Note Section -->
                    <div class="cop-note-section">
                        <label class="cop-note-label">
                            <i class="fas fa-comment-dots"></i>
                            Ghi chú đơn hàng
                        </label>
                        <textarea
                            class="cop-note-textarea"
                            placeholder="Nhập ghi chú cho đơn hàng (không bắt buộc). Ví dụ: Giao hàng giờ hành chính, gọi trước khi giao..."
                            maxlength="500"
                        ></textarea>
                        <div class="cop-note-hint">
                            <i class="fas fa-info-circle"></i>
                            Tối đa 500 ký tự
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-lg-4">
                <x-livewire-client::checkout.card>
                    <x-slot:title icon="fas fa-receipt" style="margin-bottom: 8px;">Order Summary</x-slot:title>

                    <x-livewire-client::checkout.-card.-summary></x-livewire-client::checkout.-card.-summary>
                </x-livewire-client::checkout.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="cop-card">
            <div class="cop-action-section">
                <a href="cart.html" class="cop-btn cop-btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại giỏ hàng
                </a>
                <a href="checkout-address.html" class="cop-btn cop-btn-next">
                    Tiếp tục
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
