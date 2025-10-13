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
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-icon">
                    <i class="{{ $typeIcon }}"></i>
                </div>
                <h4 class="modal-title mb-3">{{ $title }}</h4>
                @if($message)
                    <p class="modal-text">
                        {{ $message }}
                    </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn {{ $typeBtn }}" @if($confirmAction) wire:click="{{ $confirmAction }}" @else data-bs-dismiss="modal" @endif>{{ $confirmLabel }}</button>
            </div>
        </div>
    </div>
</div>
