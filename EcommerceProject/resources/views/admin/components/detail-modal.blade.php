<div class="modal fade detail-modal" id="{{ $attributes->get('id', 'detailModal') }}" wire:key="{{ $attributes->get('wire:key', 'detail-modal') }}"
    tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $attributes->get('id-heading', 'detailModalLabel') }}">
                    <i class="{{ $icon }}"></i> {{ $title }}
                </h5>
                <button type="button" class="btn-close bootstrap" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <ul class="nav nav-tabs detail-modal-nav-tabs" role="tablist">
                    {{ $tabs }}
                </ul>

                <div class="tab-content detail-modal-tab">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
@script
<script>
    window.cleanupDetailModal = new Function();

    window.initDetailModal = function(){
        const modalEl = document.getElementById(@json($attributes->get('id', 'detailModal')));
        const listenerHide = function(event){
            $wire && $wire.$set(@json($activeRecordVariable), null, true);
        }

        modalEl.addEventListener('hide.bs.modal', listenerHide);

        window.cleanupDetailModal = function(){
            modalEl.removeEventListener('hide.bs.modal', listenerHide);
        }
    }

    document.addEventListener('livewire:initialized', initDetailModal);
    Livewire.hook('morphed', function() {
        if(document.getElementById(@json($attributes->get('id', 'detailModal')))){
            cleanupDetailModal();
            initDetailModal();
        }
    });
</script>
@endscript
