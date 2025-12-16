@assets
    @vite('resources/css/review-modal.css')
@endassets

<div {{ $attributes->merge(['class' => 'modal fade', 'tabindex' => '-1']) }}>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content pdp-modal-content">
            <div class="modal-header pdp-modal-header">
                <h5 class="modal-title"><i class="fas fa-pen-fancy"></i> {{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pdp-modal-body">
                <form {{ $form->attributes }}>
                    {{ $alert }}

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-star pdp-icon-label"></i> Star rating <span class="text-danger">*</span></label>
                        <div class="pdp-rating-input">
                            <button {{ $starButton->attributes->merge(['type' => 'button', 'class' => 'pdp-star-input']) }} data-rating="1"><i class="fas fa-star"></i></button>
                            <button {{ $starButton->attributes->merge(['type' => 'button', 'class' => 'pdp-star-input']) }} data-rating="2"><i class="fas fa-star"></i></button>
                            <button {{ $starButton->attributes->merge(['type' => 'button', 'class' => 'pdp-star-input']) }} data-rating="3"><i class="fas fa-star"></i></button>
                            <button {{ $starButton->attributes->merge(['type' => 'button', 'class' => 'pdp-star-input']) }} data-rating="4"><i class="fas fa-star"></i></button>
                            <button {{ $starButton->attributes->merge(['type' => 'button', 'class' => 'pdp-star-input']) }} data-rating="5"><i class="fas fa-star"></i></button>
                        </div>
                        @isset($starMessage)
                            {{ $starMessage }}
                        @endisset
                    </div>
                    <div class="mb-3">
                        <label for="reviewContent" class="form-label"><i class="fas fa-pen-fancy pdp-icon-label"></i> Review content</label>
                        <textarea {{ $textarea->attributes->merge(['class' => 'form-control pdp-form-textarea', 'id' => 'reviewContent', 'rows' => 5, 'placeholder' => 'Share your experience...']) }}></textarea>
                        @isset($contentMessage)
                            {{ $contentMessage }}
                        @endisset
                    </div>
                </form>
            </div>
            @if($showActions)
                <div class="modal-footer pdp-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button {{ $submitButton->attributes->merge(['class' => 'btn pdp-btn-submit-review']) }}>{{ $submitButton }}</button>
                </div>
            @endif
        </div>
    </div>
</div>
