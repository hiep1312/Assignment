@use('App\Enums\OrderStatus')
<div class="detail-color timeline">
    @if($this->order)
        <div class="timeline-item completed">
            <div class="timeline-marker">
                <i class="fas fa-check"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-title">New Order</div>
                <div class="timeline-time">
                    <i class="fas fa-clock"></i>
                    {{ $this->order->created_at->format('m/d/Y H:i A') }}
                </div>
                <span class="timeline-status">Completed</span>
                <button type="button" class="timeline-btn timeline-btn-confirm" disabled>
                    <i class="fas fa-check-circle"></i> Confirmed
                </button>
            </div>
        </div>

        <div class="timeline-item {{ $this->getOrderTimelineStatus($this->order->confirmed_at) }}">
            <div class="timeline-marker">
                @if($this->order->confirmed_at)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-badge-check"></i>
                @endif
            </div>
            <div class="timeline-content">
                <div class="timeline-title">Order Confirmed</div>
                <div class="timeline-time">
                    <i class="fas fa-clock"></i>
                    @if($this->order->confirmed_at)
                        {{ $this->order->confirmed_at->format('m/d/Y H:i A') }}
                    @else
                        Pending confirmation
                    @endif
                </div>
                <span class="timeline-status">{{ ucfirst($this->getOrderTimelineStatus($this->order->confirmed_at)) }}</span>
                <button type="button" class="timeline-btn timeline-btn-confirm" wire:click="confirmOrderTimelineStep({{ OrderStatus::CONFIRMED->value }})"
                    @disabled($this->isOrderTimelineDisabled($this->order->confirmed_at, OrderStatus::CONFIRMED->value))>
                    {!! $this->getOrderTimelineBtnLabel($this->order->confirmed_at, "Order") !!}
                </button>
            </div>
        </div>

        <div class="timeline-item {{ $this->getOrderTimelineStatus($this->order->processing_at) }}">
            <div class="timeline-marker">
                @if($this->order->processing_at)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-spinner fa-spin"></i>
                @endif
            </div>
            <div class="timeline-content">
                <div class="timeline-title">Processing Order</div>
                <div class="timeline-time">
                    <i class="fas fa-clock"></i>
                    @if($this->order->processing_at)
                        {{ $this->order->processing_at->format('m/d/Y H:i A') }}
                    @else
                        Awaiting processing
                    @endif
                </div>
                <span class="timeline-status">{{ ucfirst($this->getOrderTimelineStatus($this->order->processing_at)) }}</span>
                <button type="button" class="timeline-btn timeline-btn-confirm" wire:click="confirmOrderTimelineStep({{ OrderStatus::PROCESSING->value }})"
                    @disabled($this->isOrderTimelineDisabled($this->order->processing_at, OrderStatus::PROCESSING->value))>
                    {!! $this->getOrderTimelineBtnLabel($this->order->processing_at, "Processing") !!}
                </button>
            </div>
        </div>

        <div class="timeline-item {{ $this->getOrderTimelineStatus($this->order->shipped_at) }}">
            <div class="timeline-marker">
                @if($this->order->shipped_at)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-shipping-fast"></i>
                @endif
            </div>
            <div class="timeline-content">
                <div class="timeline-title">Shipped</div>
                <div class="timeline-time">
                    <i class="fas fa-clock"></i>
                    @if($this->order->shipped_at)
                        {{ $this->order->shipped_at->format('m/d/Y H:i A') }}
                    @else
                        Not shipped yet
                    @endif
                </div>
                <span class="timeline-status">{{ ucfirst($this->getOrderTimelineStatus($this->order->shipped_at)) }}</span>
                <button type="button" class="timeline-btn timeline-btn-confirm" wire:click="confirmOrderTimelineStep({{ OrderStatus::SHIPPED->value }})"
                    @disabled($this->isOrderTimelineDisabled($this->order->shipped_at, OrderStatus::SHIPPED->value))>
                    {!! $this->getOrderTimelineBtnLabel($this->order->shipped_at, "Shipment") !!}
                </button>
            </div>
        </div>

        <div class="timeline-item {{ $this->getOrderTimelineStatus($this->order->delivered_at) }}">
            <div class="timeline-marker">
                @if($this->order->delivered_at)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-truck"></i>
                @endif
            </div>
            <div class="timeline-content">
                <div class="timeline-title">Delivered</div>
                <div class="timeline-time">
                    <i class="fas fa-clock"></i>
                    @if($this->order->delivered_at)
                        {{ $this->order->delivered_at->format('m/d/Y H:i A') }}
                    @else
                        Awaiting delivery
                    @endif
                </div>
                <span class="timeline-status">{{ ucfirst($this->getOrderTimelineStatus($this->order->delivered_at)) }}</span>
                <button type="button" class="timeline-btn timeline-btn-confirm" wire:click="confirmOrderTimelineStep({{ OrderStatus::DELIVERED->value }})"
                    @disabled($this->isOrderTimelineDisabled($this->order->delivered_at, OrderStatus::DELIVERED->value))>
                    {!! $this->getOrderTimelineBtnLabel($this->order->delivered_at, "Delivery") !!}
                </button>
            </div>
        </div>

        @php
            $isOrderSuccess = ($this->order->isFinalized && $this->order->completed_at);
            $isOrderFailed = ($this->order->isFinalized && !$this->order->completed_at) || $this->order->isCancelled;
            $isOrderPending = !$isOrderSuccess && !$isOrderFailed;
        @endphp
        <div @class([
            "timeline-item",
            "pending" => $isOrderPending,
            "completed" => $isOrderSuccess,
            "failed" => $isOrderFailed
        ])>
            <div class="timeline-marker">
                @switch(true)
                    @case($isOrderSuccess) <i class="fas fa-check"></i> @break
                    @case($isOrderFailed) <i class="fas fa-exclamation-triangle"></i> @break
                    @default <i class="fas fa-box-open"></i>
                @endswitch
            </div>
            <div class="timeline-content">
                @php
                    $timelineTitle = $isOrderFailed ? "Failed" : "Completed";
                    $timelineTime = $this->order->isFinalized
                        ? ($this->order->completed_at?->format('m/d/Y H:i A') ?? $this->order->cancelled_at?->format('m/d/Y H:i A'))
                        : "Pending completion";
                    $timelineStatus = $this->order->isFinalized
                        ? $timelineTitle
                        : ($isOrderFailed ? "Failed" : "Pending");
                @endphp
                <div class="timeline-title">{{ $timelineTitle }}</div>
                <div class="timeline-time">
                    <i class="fas fa-clock"></i>
                    {{ $timelineTime }}
                </div>
                <span class="timeline-status">{{ $timelineStatus }}</span>
                @if($this->order->isFinalized)
                    <button type="button" class="timeline-btn timeline-btn-{{ $isOrderSuccess ? "confirm" : "failed" }}" disabled>
                        <i class="fas {{ $isOrderSuccess ? "fa-check" : "fa-times-circle" }}"></i> Confirmed
                    </button>
                @else
                    <button type="button" class="timeline-btn timeline-btn-confirm" wire:click="resolveOrder({{ OrderStatus::COMPLETED->value }})"
                        @disabled(!$this->order->isFinal)>
                        <i class="fas fa-check"></i> Confirm Completion
                    </button>
                    <button type="button" class="timeline-btn timeline-btn-failed ms-1" wire:click="resolveOrder({{ OrderStatus::FAILED->value }})"
                        @disabled(!$this->order->isFinal)>
                        <i class="fas fa-times-circle"></i> Confirm Failure
                    </button>
                @endif
            </div>
        </div>

        @if($this->order->isCancelled)
            <div class="timeline-item failed">
                <div class="timeline-marker">
                    <i class="fas fa-times"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title">Order Cancelled</div>
                    <div class="timeline-time">
                        <i class="fas fa-clock"></i>
                        {{ $this->order->cancelled_at->format('m/d/Y H:i A') }}
                    </div>
                    <span class="timeline-status">Cancelled</span>
                    <button type="button" class="timeline-btn timeline-btn-failed" disabled>
                        <i class="fas fa-ban"></i> Cancelled by {{ $this->order->status === OrderStatus::BUYER_CANCEL->value ? 'Customer' : 'Admin' }}
                    </button>
                </div>
            </div>
        @endif
    @endif
</div>
