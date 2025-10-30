@use('App\Enums\PaymentMethod')
<div class="detail-color">
    <div class="info-grid">
        <div class="info-section payment">
            <div class="section-title">
                <i class="fas fa-credit-card"></i>
                Payment Method
            </div>
            <div class="info-item">
                <span class="info-label">Method:</span>
                <span class="info-value">
                    @switch($payment->method)
                        @case(PaymentMethod::CASH) <i class="fas fa-wallet"></i> Cash @break
                        @case(PaymentMethod::BANK_TRANSFER) <i class="fas fa-university"></i> Bank Transfer @break
                        @case(PaymentMethod::CREDIT_CARD) <i class="fas fa-credit-card-alt"></i> Credit Card @break
                    @endswitch
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-badge
                        @switch($payment->status)
                            @case(0) status-pending @break
                            @case(1) status-paid @break
                            @case(2) status-failed @break
                        @endswitch
                    ">
                        @switch($payment->status)
                            @case(0) <i class="fas fa-clock"></i> Pending @break
                            @case(1) <i class="fas fa-check-circle"></i> Paid @break
                            @case(2) <i class="fas fa-times-circle"></i> Failed @break
                        @endswitch
                    </span>
                </span>
            </div>
        </div>

        <div class="info-section payment">
            <div class="section-title">
                <i class="fas fa-receipt"></i>
                Payment Details
            </div>
            <div class="info-item">
                <span class="info-label">Amount:</span>
                <span class="info-value">{{ number_format($payment->amount, 0, '.', '.') }}đ</span>
            </div>
            <div class="info-item">
                <span class="info-label">Transaction ID:</span>
                <span class="info-value @unless($payment->transaction_id) text-muted @endunless">{{ $payment->transaction_id ?? 'Not available' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Payment Date:</span>
                <span class="info-value @unless($payment->paid_at) text-muted @endunless">{{ $payment->paid_at ? $payment->paid_at->format('m/d/Y h:i A') : 'Not paid yet' }}</span>
            </div>
        </div>
    </div>

    <div class="full-width-section">
        <div class="section-title">
            <i class="fas fa-info-circle"></i>
            Transaction Information
        </div>
        <div class="json-viewer">
            <pre>{!! formatJsonToHtml($payment->transaction_data) ?? 'No transaction details available' !!}</pre>
        </div>
    </div>
</div>
