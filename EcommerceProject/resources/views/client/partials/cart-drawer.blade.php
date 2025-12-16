@assets
    @vite('resources/css/cart-drawer.css')
@endassets

<div class="shc-cart-drawer shc-show shc-collapsed" id="cartDrawer">
    <div class="shc-drawer-header">
        <h3 class="shc-drawer-title">📦 Sản phẩm vừa thêm</h3>
        <button class="shc-toggle-btn" id="toggleDrawerBtn" title="Mở rộng">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="shc-close-btn" id="closeDrawerBtn" title="Đóng">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="shc-drawer-body" id="cartDrawerBody">
        <div class="shc-cart-item">
            <div class="shc-item-image">💻</div>
            <div class="shc-item-info">
                <p class="shc-item-name">Laptop</p>
                <p class="shc-item-price">15.000.000₫</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button class="shc-remove-btn" onclick="cartState.removeItem(1)" title="Xóa">
                    <i class="fas fa-trash"></i>
                </button>
                <div class="shc-qty-control">
                    <button class="shc-qty-btn" onclick="cartState.updateQuantity(1, 1)">−</button>
                    <span class="shc-qty-display">2</span>
                    <button class="shc-qty-btn" onclick="cartState.updateQuantity(1, 3)">+</button>
                </div>
            </div>
        </div>

        <div class="shc-cart-item">
            <div class="shc-item-image">📱</div>
            <div class="shc-item-info">
                <p class="shc-item-name">Điện thoại</p>
                <p class="shc-item-price">10.000.000₫</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button class="shc-remove-btn" onclick="cartState.removeItem(2)" title="Xóa">
                    <i class="fas fa-trash"></i>
                </button>
                <div class="shc-qty-control">
                    <button class="shc-qty-btn" onclick="cartState.updateQuantity(2, 0)">−</button>
                    <span class="shc-qty-display">1</span>
                    <button class="shc-qty-btn" onclick="cartState.updateQuantity(2, 2)">+</button>
                </div>
            </div>
        </div>

        <div class="shc-cart-item">
            <div class="shc-item-image">🎧</div>
            <div class="shc-item-info">
                <p class="shc-item-name">Tai nghe</p>
                <p class="shc-item-price">2.000.000₫</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button class="shc-remove-btn" onclick="cartState.removeItem(3)" title="Xóa">
                    <i class="fas fa-trash"></i>
                </button>
                <div class="shc-qty-control">
                    <button class="shc-qty-btn" onclick="cartState.updateQuantity(3, 0)">−</button>
                    <span class="shc-qty-display">1</span>
                    <button class="shc-qty-btn" onclick="cartState.updateQuantity(3, 2)">+</button>
                </div>
            </div>
        </div>
    </div>
</div>
