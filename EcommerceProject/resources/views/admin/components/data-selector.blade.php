<div class="modal fade" tabindex="-1" id="{{ $attributes->get('id', 'frameSelectionData') }}" wire:key="{{ $attributes->get('wire:key', 'frame-selection-data') }}" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close bootstrap" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="search-box">
                    <input {{ $input->attributes }}>
                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <div class="checkbox-container">
                    <div class="checkbox-list" id="checkboxListModal">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @if($resetProperty) wire:click="$set('{{ $resetProperty }}', {{ $input->attributes->get('type', 'radio') ? 'null' : '[]' }})" @endif>Cancel</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" {{ $buttonConfirm->attributes }}>{{ $buttonConfirm }}</button>
            </div>
        </div>
    </div>
</div>
