@use('App\Enums\OrderStatus')
<div class="detail-color">
    @teleport('#main-component')
        <livewire:admin.components.confirm-modal id="confirmModalDetail" wire:key="confirm-modal-detail">
    @endteleport

    <div class="notes-form-container">
        <div class="notes-section customer-notes">
            <div class="notes-section-title">
                <i class="fas fa-comment-dots"></i> Customer Notes
            </div>
            <div class="notes-display @if($this->order->customer_note) has-content @endif" id="customerNotes" wire:key="customer-notes">
                @if($this->order->customer_note)
                    {{ $this->order->customer_note }}
                @else
                    <em style="color: #6c757d;">No notes from customer</em>
                @endif
            </div>
        </div>

        <div class="notes-section admin-notes">
            <div class="notes-section-title">
                <i class="fas fa-clipboard-list"></i> Admin Notes
            </div>
            @if($this->order->allowAdminNote())
                <form id="adminNoteForm" wire:key="admin-note-form">
                    <div class="cancel-form-group">
                        <textarea id="adminNotesText" placeholder="Enter admin notes (maximum 500 characters)..." wire:model="admin_note"
                            maxlength="500" rows="5" wire:key="admin-notes-text"></textarea>
                    </div>
                    <div class="cancel-button-group" wire:key="admin-note-button-group">
                        <button type="button" class="btn-confirm-note" wire:click="saveAdminNote">
                            <i class="fas fa-times-circle"></i> Confirm
                        </button>
                        <button type="reset" class="btn-cancel-reset">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </form>
            @else
                <div class="notes-display @if($this->order->admin_note) has-content @endif" id="adminNotes" wire:key="customer-notes">
                    @if($this->order->admin_note)
                        {{ $this->order->admin_note }}
                    @else
                        <em style="color: #6c757d;">No notes from admin</em>
                    @endif
                </div>
            @endif

            @if(session()->has('admin-note-saved'))
                <div id="adminNoteSuccess" class="note-success-badge">
                    <i class="fas fa-check-circle"></i> Note has been saved
                </div>
            @endif
        </div>

        <div class="notes-section cancel-section">
            <div class="notes-section-title">
                <i class="fas fa-ban"></i> Cancel Order
            </div>

            @if($this->order->allowCancel())
                <div class="cancel-warning" wire:key="cancel-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="cancel-warning-text">
                        <strong>Warning:</strong> Canceling an order is an irreversible action. Please be sure before proceeding.
                    </div>
                </div>

                <form id="cancelOrderForm" wire:key="cancel-order-form">
                    <div class="cancel-form-group">
                        <label for="cancelDetails">
                            <i class="fas fa-pen"></i> Cancellation Reason Details
                        </label>
                        <textarea id="cancelDetails" placeholder="Enter detailed reason for order cancellation..." wire:model="cancel_reason"
                            maxlength="255" rows="5" wire:key="cancel-details"></textarea>
                    </div>

                    <div class="cancel-button-group" wire:key="cancel-order-button-group">
                        <button type="button" class="btn-cancel-order" onclick="confirmModalAction(this)"
                            data-title="Cancel Order" data-type="warning" data-message="Are you sure you want to cancel this order {{ $this->order->order_code }}? This action cannot be undone."
                            data-confirm-label="Confirm Cancellation" data-event-name="order.cancelled" data-event-data="{{ $this->order->id }}" data-id="confirmModalDetail">
                            <i class="fas fa-times-circle"></i> Cancel Order
                        </button>
                        <button type="reset" class="btn-cancel-reset">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </form>
            @else
                <div class="notes-display @if($this->order->cancel_reason) has-content @endif" id="cancelReason" wire:key="customer-notes">
                    @if($this->order->cancel_reason)
                        {{ $this->order->cancel_reason }}
                    @else
                        <em style="color: #6c757d;">No cancellation reason provided</em>
                    @endif
                </div>
            @endif

            @if(session()->has('cancel-success'))
                <div class="cancel-success-message" id="cancelSuccessMessage">
                    <i class="fas fa-check-circle"></i> Order has been successfully canceled!
                </div>
            @endif
        </div>
    </div>
</div>

