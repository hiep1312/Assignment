@php
$typeIcon = match($type){
    'success' => 'fas fa-check',
    'warning' => 'fas fa-exclamation-triangle',
    'error' => 'fas fa-times',
    'info' => 'fas fa-info',
    'question' => 'fas fa-question',
    'delete' => 'fas fa-trash-alt',
};

$typeBtn = match($type){
    'success', 'question' => 'btn-primary',
    'warning' => 'btn-warning',
    'error', 'delete' => 'btn-danger',
    'info' => 'btn-info',
};
@endphp
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" style="z-index: 1100;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-icon {{ $type }}">
                    <i class="{{ $typeIcon }}"></i>
                </div>
                <h3 class="modal-title text-center mb-3">{{ $title }}</h3>
                @if($message)
                    <p class="modal-text text-center">
                        {{ $message }}
                    </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn {{ $typeBtn }}" wire:click="confirmAction" data-bs-dismiss="modal">{{ $confirmLabel }}</button>
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
