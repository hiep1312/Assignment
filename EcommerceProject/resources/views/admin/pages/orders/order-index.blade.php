@use('App\Enums\PaymentMethod')
@use('App\Enums\DefaultImage')
@use('App\Enums\OrderStatus')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal wire:key="confirm-modal-order-{{ $recordDetail }}">

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Order List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @elseif(session()->has('timeline-updated'))
        <x-livewire::toast-message title="{{ session('timeline-updated')[0] }}" type="primary" time="{{ session('timeline-updated')[2] }}" :show="true" :duration="8" id="timeline-updated-container">
            {{ session('timeline-updated')[1] }}
        </x-livewire::toast-message>
        @php session()->forget('timeline-updated'); @endphp
    @endif

    <x-livewire::management-header title="Order List" />

    <x-livewire::stats-overview :data-stats="$statistic" />

    <x-livewire::detail-modal activeRecordVariable="recordDetail" title="Order Details" icon="fas fa-shopping-cart" id="orderDetailModal" wire:key="order-detail">
        <x-slot:tabs>
            <li class="nav-item" role="presentation">
                <button class="nav-link active bootstrap-style" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline-content"
                    type="button" role="tab" aria-controls="timeline-content" aria-selected="true"
                    wire:key="tab-order-timeline-detail-{{ $recordDetail }}" wire:ignore.self>
                    <i class="fas fa-clock"></i> Timeline
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bootstrap-style" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes-content"
                    type="button" role="tab" aria-controls="notes-content" aria-selected="false"
                    wire:key="tab-order-notes-detail-{{ $recordDetail }}" wire:ignore.self>
                    <i class="fas fa-comments"></i> Notes & Cancellation
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bootstrap-style" id="items-tab" data-bs-toggle="tab" data-bs-target="#items-content"
                    type="button" role="tab" aria-controls="items-content" aria-selected="false"
                    wire:key="tab-order-items-detail-{{ $recordDetail }}" wire:ignore.self>
                    <i class="fas fa-list"></i> Items
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bootstrap-style" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping-content"
                    type="button" role="tab" aria-controls="shipping-content" aria-selected="false"
                    wire:key="tab-order-shipping-detail-{{ $recordDetail }}" wire:ignore.self>
                    <i class="fas fa-truck"></i> Shipping Address
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bootstrap-style" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment-content"
                    type="button" role="tab" aria-controls="payment-content" aria-selected="false"
                    wire:key="tab-payment-detail-{{ $recordDetail }}" wire:ignore.self>
                    <i class="fas fa-credit-card"></i> Payment
                </button>
            </li>
        </x-slot:tabs>

        <div class="tab-pane fade show active" id="timeline-content" role="tabpanel" aria-labelledby="timeline-tab"
            wire:key="order-timeline-detail-{{ $recordDetail }}" wire:ignore.self>
            @if($recordDetail)
                <livewire:admin.orders.order-timeline :order="$recordDetail" wire:key="order-timeline-{{ $recordDetail }}-{{ $lockReloadDetail }}" />
            @endif
        </div>
        <div class="tab-pane fade" id="notes-content" role="tabpanel" aria-labelledby="notes-tab"
            wire:key="order-note-detail-{{ $recordDetail }}" wire:ignore.self>
            @if($recordDetail)
                <livewire:admin.orders.order-notes :order="$recordDetail" wire:key="order-notes-{{ $recordDetail }}-{{ $lockReloadDetail }}" />
            @endif
        </div>
        <div class="tab-pane fade" id="items-content" role="tabpanel" aria-labelledby="items-tab"
            wire:key="order-items-detail-{{ $recordDetail }}" wire:ignore.self>
            @if($recordDetail)
                <livewire:admin.order-items.order-item-index :order="$recordDetail" wire:key="order-items-{{ $recordDetail }}-{{ $lockReloadDetail }}" />
            @endif
        </div>
        <div class="tab-pane fade" id="shipping-content" role="tabpanel" aria-labelledby="shipping-tab"
            wire:key="order-shipping-detail-{{ $recordDetail }}" wire:ignore.self>
            @if($recordDetail)
                <livewire:admin.order-shippings.order-shipping-detail :order="$recordDetail" wire:key="order-shipping-{{ $recordDetail }}-{{ $lockReloadDetail }}" />
            @endif
        </div>
        <div class="tab-pane fade" id="payment-content" role="tabpanel" aria-labelledby="payment-tab"
            wire:key="payment-detail-{{ $recordDetail }}" wire:ignore.self>
            @if($recordDetail)
                <livewire:admin.payments.payment-detail :order="$recordDetail" wire:key="payment-{{ $recordDetail }}-{{ $lockReloadDetail }}" />
            @endif
        </div>
    </x-livewire::detail-modal>

    <x-livewire::filter-bar placeholderSearch="Search orders..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="status">
                <option value="">All Status</option>
                <option value="1">New</option>
                <option value="2">Confirmed</option>
                <option value="3">Processing</option>
                <option value="4">Shipped</option>
                <option value="5">Delivered</option>
                <option value="6">Completed</option>
                <option value="7">Failed</option>
                <option value="8">Buyer Cancel</option>
                <option value="9">Admin Cancel</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="paymentMethod">
                <option value="">All Payment Methods</option>
                <option value="{{ PaymentMethod::CASH->value }}">Cash</option>
                <option value="{{ PaymentMethod::BANK_TRANSFER->value }}">Bank Transfer</option>
                <option value="{{ PaymentMethod::CREDIT_CARD->value }}">Credit Card</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Order Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Restore Orders` : `Restore All Orders`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Restore Orders` : `Restore All Orders`" data-type="question"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedRecordIds.length} orders? They will be moved back to the active orders list.`
                        : `Are you sure you want to restore all orders? They will be moved back to the active orders list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="order.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Restore Orders` : `Restore All Orders`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Permanently Delete Orders` : `Permanently Delete All Orders`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Permanently Delete Orders` : `Permanently Delete All Orders`" data-type="warning"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedRecordIds.length} orders? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all orders? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="order.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Permanently Delete Orders` : `Permanently Delete All Orders`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Orders" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-check-circle me-1"></i>
                    Active Orders
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Orders"
                    x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Orders" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedRecordIds.length} orders? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="order.deleted" wire:key="delete">
                    <i class="fas fa-times-circle me-1"></i>
                    Remove Orders
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Orders"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Orders
                </button>
            @endif
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="toggleAll" onclick="toggleSelectAll(this)" data-state="0">
                        </th>
                        <th>Order Information</th>
                        <th>Products</th>
                        <th>Total Amount</th>
                        <th>Customer Note</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="text-center" wire:key="order-{{ $order->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $order->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td style="min-width: 250px;">
                                @php $user = $order->user; @endphp
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($user->avatar ?? DefaultImage::AVATAR->value)) }}"
                                        class="rounded-circle me-2" width="40" height="40" alt="User Avatar">
                                    <div class="text-start">
                                        <div class="fw-bold">
                                            {{ Str::limit($user->name, 20, '...') }}
                                            @if($isTrashed)
                                                <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block text-nowrap">
                                            <i class="fas fa-envelope me-1"></i>{{ Str::limit($user->email, 19, '...') }}
                                        </small>
                                        <small class="text-muted">Code: {{ Str::limit($order->order_code, 25, '...') }}</small>
                                        <button class="btn btn-sm btn-outline-secondary bootstrap-focus ms-1" style="padding: 0.15rem 0.25rem; font-size: 0.75rem;"
                                            title="Copy Code" onclick="copyToClipboard('{{ $order->order_code }}', this)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @php $items = $order->items; @endphp
                                <div class="d-flex flex-column gap-2">
                                    @foreach($items->take(2) as $item)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1">
                                                <div class="text-truncate fw-semibold" style="font-size: 0.85rem;">
                                                    {{ Str::limit($item->productVariant->product->title, 19, '...') }}
                                                </div>
                                                <small class="text-muted">
                                                    <span class="badge bg-label-secondary" style="font-size: 0.7rem;">x{{ $item->quantity }}</span>
                                                    <span class="ms-1">{{ number_format($item->price, 0, '.', '.') }}đ</span>
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($items->count() > 2)
                                        <small class="text-primary">
                                            <i class="fas fa-plus-circle"></i> {{ $items->count() - 2 }} more item(s)
                                        </small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold text-success bootstrap-color">
                                    {{ number_format($order->total_amount, 0, '.', '.') }}đ
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-shipping-fast me-1"></i>{{ number_format($order->shipping_fee, 0, '.', '.') }}đ
                                </small>
                            </td>

                            <td>
                                <p class="text-wrap lh-base mb-0 @unless($order->customer_note) text-muted @endunless"
                                    style="width: 150px">{{ Str::limit($order->customer_note ?? 'No note', 60, '...') }}</p>
                            </td>

                            <td>
                                @php $payment = $order->payment; @endphp
                                <span class="badge rounded-pill bootstrap-color
                                    @switch($order->status)
                                        @case(1) @case(4) bg-info @break
                                        @case(2) bg-primary @break
                                        @case(3) bg-warning @break
                                        @case(5) @case(6) bg-success @break
                                        @case(7) bg-danger @break
                                        @case(8) bg-secondary @break
                                        @case(9) bg-dark @break
                                    @endswitch
                                ">
                                    @switch($order->status)
                                        @case(1) New @break
                                        @case(2) Confirmed @break
                                        @case(3) Processing @break
                                        @case(4) Shipped @break
                                        @case(5) Delivered @break
                                        @case(6) Completed @break
                                        @case(7) Failed @break
                                        @case(8) Buyer Cancel @break
                                        @case(9) Admin Cancel @break
                                    @endswitch
                                </span>
                                <span class="text-muted d-block mt-1 text-nowrap">
                                    @switch($payment->method)
                                        @case(PaymentMethod::CASH) <i class="fas fa-wallet"></i> Cash @break
                                        @case(PaymentMethod::BANK_TRANSFER) <i class="fas fa-university"></i> Bank Transfer @break
                                        @case(PaymentMethod::CREDIT_CARD) <i class="fas fa-credit-card-alt"></i> Credit Card @break
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span>{{ $order->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $order->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($isTrashed)
                                        <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                            data-title="Restore Order" data-type="question" data-message="Are you sure you want to restore this order {{ $order->order_code }}? The order will be moved back to the active orders list."
                                            data-confirm-label="Confirm Restore" data-event-name="order.restored" data-event-data="{{ $order->id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                            data-title="Permanently Delete Order" data-type="warning" data-message="Are you sure you want to permanently delete this order {{ $order->order_code }}? This action cannot be undone."
                                            data-confirm-label="Confirm Delete" data-event-name="order.forceDeleted" data-event-data="{{ $order->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-info btn-action bootstrap-focus" title="View Details"
                                            data-bs-toggle="modal" data-bs-target="#orderDetailModal" wire:click="$set('recordDetail', {{ $order->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Remove Order" data-type="warning" data-message="Are you sure you want to remove this order {{ $order->order_code }}? The order can be restored later."
                                            data-confirm-label="Confirm Delete" data-event-name="order.deleted" data-event-data="{{ $order->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No orders found</h5>
                                    <p class="text-muted">There are no orders or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:pagination>
            @if($orders->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $orders->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
