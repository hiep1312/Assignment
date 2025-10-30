<div class="detail-color">
    <div class="info-grid">
        <div class="info-section shipping">
            <div class="section-title">
                <i class="fas fa-user"></i>
                Recipient Information
            </div>
            <div class="info-item">
                <span class="info-label">Recipient Name:</span>
                <span class="info-value">{{ $shipping->recipient_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Phone Number:</span>
                <span class="info-value">{{ $shipping->phone }}</span>
            </div>
        </div>

        <div class="info-section shipping">
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i>
                Shipping Address
            </div>
            <div class="info-item">
                <span class="info-label">Province/City:</span>
                <span class="info-value">{{ $shipping->province }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">District:</span>
                <span class="info-value">{{ $shipping->district }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Ward:</span>
                <span class="info-value">{{ $shipping->ward }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Street:</span>
                <span class="info-value @unless($shipping->street) text-muted @endunless">{{ $shipping->street ?? 'Not provided' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Postal Code:</span>
                <span class="info-value @unless($shipping->postal_code) text-muted @endunless">{{ $shipping->postal_code ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="full-width-section">
        <div class="section-title">
            <i class="fas fa-sticky-note"></i>
            Delivery Note
        </div>
        <div class="note-text @unless($shipping->note) text-muted @endunless">
            {{ $shipping->note ?? 'No delivery notes' }}
        </div>
    </div>
</div>
