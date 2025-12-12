@assets
    @vite('resources/css/confirm-modal-client.css')
@endassets
@php
    $modalClass = match($type){
        'success' => 'cp-modal-success',
        'warning' => 'cp-modal-warning',
        'danger' => 'cp-modal-danger',
        'info' => 'cp-modal-info',
        'question' => 'cp-modal-question',
        'modern' => 'cp-modal-modern',
        'light' => 'cp-modal-minimal',
        'glass' => 'cp-modal-glass',
    };

    $typeIcon = match($type){
        'success' => 'fas fa-check',
        'warning' => 'fas fa-exclamation-triangle',
        'danger' => 'fas fa-times',
        'info' => 'fas fa-info',
        'question' => 'fas fa-question',
        'modern' => 'fas fa-sparkles',
        'light' => 'fas fa-circle-notch',
        'glass' => 'fas fa-gem',
    };
@endphp
<div class="modal fade cp-modal {{ $modalClass }}" id="{{ $id }}" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" style="z-index: 1100;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="cp-icon-wrapper">
                    <i class="{{ $typeIcon }}"></i>
                </div>
                <h5 class="cp-title">{{ $title }}</h5>
                @if($message)
                    <p class="cp-message">
                        {{ $message }}
                    </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="cp-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="cp-btn-confirm" wire:click="confirmAction" data-bs-dismiss="modal">{{ $confirmLabel }}</button>
            </div>
        </div>
    </div>
</div>
@script
<script>
    window.syncModal = function(isOpen = null){
        const modal = bootstrap.Modal.getOrCreateInstance("#{{ $id }}");
        const condition = typeof isOpen === 'boolean' ? isOpen : @json($realtimeOpen);

        condition ? modal.show() : modal.hide();
    }

    syncModal();
</script>
@endscript
