<div class="modal fade" id="{{ $attributes->get('id', 'contentPreviewModal') }}" tabindex="-1" aria-hidden="true" {{ $attributes }}>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bootstrap-style bg-primary bootstrap-color">
                <h5 class="modal-title" style="color: #FFFFFF">
                    <i class="{{ $icon }}"></i> {{ $title }}
                </h5>
                <button type="button" class="btn-close btn-close-white bootstrap" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body markdown-body" style="padding: 1rem 1.5rem;">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
