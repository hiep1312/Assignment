<div {{ $attributes }}>
    <div class="image-modal-dialog">
        <div class="image-modal-content">
            <div class="image-modal-header">
                <h5 class="image-modal-title">
                    <i class="{{ $titleIcon }}"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="image-btn-close" title="Close" @isset($buttonClose) {{ $buttonClose->attributes }} @endisset>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="image-modal-body">
                <img {{ $img->attributes }} class="image-detail-image">
                <div class="image-detail-info">
                    {{ $slot }}
                </div>
            </div>

            <div class="image-modal-footer">
                {{ $actions }}
            </div>
        </div>
    </div>
</div>
