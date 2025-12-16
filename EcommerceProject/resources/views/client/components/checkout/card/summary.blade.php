@assets
    @vite('resources/css/checkout-summary.css')
@endassets

<div {{ $attributes }}>
    <div class="cop-summary-row">
        <span class="cop-summary-label">Subtotal (<span id="totalItems">4</span> items)</span>
        <span class="cop-summary-value" id="subtotalAmount">1.890.000₫</span>
    </div>

    <div class="cop-summary-row">
        <span class="cop-summary-label">
            <i class="fas fa-truck me-1"></i> Shipping Fee
        </span>
        <span class="cop-summary-value" id="shippingAmount">30.000₫</span>
    </div>

    <div class="cop-summary-row">
        <span class="cop-summary-label">
            <i class="fas fa-tag me-1"></i> Discount
        </span>
        <span class="cop-summary-value" style="color: var(--cop-success);" id="discountAmount">-209.000₫</span>
    </div>

    <div class="cop-summary-total cop-summary-row">
        <span class="cop-summary-label">Total</span>
        <span class="cop-summary-value" id="totalAmount">1.711.000₫</span>
    </div>

    <div class="cop-summary-security">
        <p>
            <i class="fas fa-shield-alt"></i>
            <strong>Secure Checkout:</strong> Your payment and personal information are fully protected.
        </p>
    </div>
</div>
