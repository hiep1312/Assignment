@push('scripts')
    @vite('resources/js/image-picker.js')
@endpush
@use('Illuminate\Support\Facades\Storage')
@use('App\Enums\DefaultImage')
<div class="modal fade image-picker-modal" id="{{ $id }}" tabindex="-1" wire:ignore.self>
    <x-livewire-admin::loading-spinner id="loading-uploader" wire:key="loading-uploader" wire:loading.flex wire:target="photos" />

    @error('photos.*')
        <div class="error-overlay">
            <div class="error-modal">
                <button class="close-btn" onclick="this.closest('.error-overlay').classList.add('d-none')">
                    <i class="fas fa-times"></i>
                </button>

                <div class="error-icon-container">
                    <i class="fas fa-exclamation-circle"></i>
                </div>

                <h2 class="error-title">Upload Failed</h2>

                <p class="error-description">Sorry, your images could not be uploaded. Please check and try again.</p>

                <div class="error-details">
                    <strong>Reason:</strong>
                    {{ $message }}
                </div>

                <div class="error-buttons">
                    <button class="btn-retry" wire:click="saveUploadedPhotos">
                        <i class="fas fa-redo-alt" style="margin-right: 8px;"></i>Try Again
                    </button>
                    <button class="btn-close-modal" onclick="this.closest('.error-overlay').classList.add('d-none')">Close</button>
                </div>
            </div>
        </div>
    @endif

    @isset($modalConfirmInfo)
        <div :class="`modal-confirm-warning-overlay ${$wire.modalConfirmInfo && 'show'}`" id="confirmWarningImageModal" wire:key="confirm-warning-image-modal">
            <div class="modal-confirm-warning-dialog">
                <div class="modal-confirm-warning-content">
                    <div class="modal-confirm-warning-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>

                    <h2 class="modal-confirm-warning-title">{{ $modalConfirmInfo['title'] }}</h2>

                    <p class="modal-confirm-warning-message">{{ $modalConfirmInfo['message'] }}</p>

                    <div class="modal-confirm-warning-buttons">
                        <button class="modal-confirm-warning-btn-confirm" wire:click="deleteImages({{ $modalConfirmInfo['id'] ?? '' }})">
                            {{ $modalConfirmInfo['confirmLabel'] ?? 'Confirm' }}
                        </button>
                        <button class="modal-confirm-warning-btn-cancel" x-on:click="$wire.modalConfirmInfo = null">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endisset

    <div class="modal-dialog bootstrap-padding">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images me-2"></i>
                    Select Images From Gallery
                </h5>
                <button type="button" class="bootstrap btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="upload-section">
                    <div class="upload-drop-zone" id="dropZoneModal">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h5>Drag & Drop Images Here</h5>
                        <p class="text-muted">or click the button below to select images</p>
                        <label for="multipleImageInput" class="btn btn-upload">
                            <i class="fas fa-folder-open me-2"></i>
                            Select Images
                        </label>
                        <input type="file" id="multipleImageInput" accept="image/*" wire:model.change="photos" multiple class="d-none">
                        <small class="text-muted d-block mt-3">
                            Supported: JPG, JPEG, PNG, BMP, GIF, WebP (Max 10MB per image)
                        </small>
                    </div>
                </div>

                <div class="modal-search-section">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" id="searchImageInput" placeholder="Search images..." wire:model.live.debounce.300ms="searchImage">
                    </div>
                </div>

                <div class="gallery-section d-block" id="gallerySectionModal">
                    <div class="gallery-header">
                        <h5>
                            <i class="fas fa-images me-2"></i>
                            Image Gallery (<span id="imageCount">{{ $images->total() }}</span>)
                        </h5>
                        <div class="d-flex gap-2" x-data="{ isAllSelected: false }"
                            x-effect="isAllSelected = @json(array_map(fn($image) => $image->id, $images->items())).every(imageId => $wire.selectedImageIds.includes(imageId))">
                            <button :class="`btn ${isAllSelected ? 'btn-primary' : 'btn-outline-primary'} bootstrap-focus`" :title="isAllSelected ? `Deselect All` : `Select All`"
                                x-on:click="toggleSelectAllImagePicker(isAllSelected ? true : false)">
                                <i :class="`fas ${isAllSelected ? 'fa-square' : 'fa-check-square'} me-1`"></i>
                                <span x-text="isAllSelected ? `Deselect All` : `Select All`"></span>
                            </button>
                            <button class="btn btn-outline-danger bootstrap bootstrap-focus" :title="$wire.selectedImageIds.length ? `Delete Images` : `Delete All Images`"
                                wire:key="delete-images" wire:click="$set('modalConfirmInfo', {
                                    title: $wire.selectedImageIds.length ? `Delete Images` : `Delete All Images`,
                                    message: $wire.selectedImageIds.length
                                        ? `Are you sure you want to delete these ${$wire.selectedImageIds.length} images? They can be restored later.`
                                        : `Are you sure you want to delete all images? They can be restored later.`,
                                    confirmLabel: 'Confirm Delete'
                                })">
                                <i class="fas fa-trash-alt me-1"></i>
                                <span x-text="$wire.selectedImageIds.length ? `Delete Images` : `Delete All Images`"></span>
                            </button>
                        </div>
                    </div>
                    <div class="image-grid" id="imageGridModal">
                        @forelse($images as $image)
                            @php
                                $imagePath = $image->image_url;
                                $imageExists = Storage::disk('public')->exists($imagePath);
                                $imageSrc = $imageExists ? Storage::disk('public')->url($imagePath) : DefaultImage::NOT_FOUND->value;
                                $imageName = basename($imagePath);
                                $imageSizeBytes = $imageExists ? Storage::disk('public')->size($imagePath) : 0;
                                $imageLastModified = $imageExists ? Storage::disk('public')->lastModified($imagePath) : 0;
                                $imageMimeType = $imageExists ? Storage::disk('public')->mimeType($imagePath) : 'N/A';
                            @endphp
                            <div :class="{ 'image-card': true, 'selected': $wire.selectedImageIds.includes({{ $image->id }}) }" wire:key="image-picker-{{ $image->id }}"
                                x-on:click="$wire.selectedImageIds.includes({{ $image->id }}) ? ($wire.selectedImageIds = $wire.selectedImageIds.filter(id => id !== {{ $image->id }})) : $wire.selectedImageIds.push({{ $image->id }})">
                                <div class="checkbox-overlay">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="image-wrapper">
                                    <img src="{{ $imageSrc }}" alt="Uploaded image">
                                    <div class="image-overlay">
                                        <div class="image-actions">
                                            <button class="action-btn view-btn" wire:click.stop="$set('viewingImageId', {{ $image->id }})" title="View details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a class="action-btn download-btn" x-on:click.stop href="{{ $imageSrc }}" download="{{ $imageName }}" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="action-btn delete-btn" title="Delete" wire:click.stop="$set('modalConfirmInfo', {
                                                    title: 'Delete Image',
                                                    message: 'Are you sure you want to delete this image #{{ $image->id }}? It can be restored later.',
                                                    confirmLabel: 'Confirm Delete',
                                                    id: {{ $image->id }}
                                                })">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="image-info-section">
                                    <div class="image-name" title="{{ $imageName }}">{{ $imageName }}</div>
                                    <div class="image-meta">
                                        <span>{{ formatFileSize($imageSizeBytes) }}</span>
                                        <span>{{ date('M d, Y', $imageLastModified) }}</span>
                                        <span>{{ $imageMimeType }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="gallery-empty-state text-dark" style="grid-column: 1 / -1">
                                <div class="empty-state-icon">
                                    <i class="fa-solid fa-image-circle-xmark"></i>
                                </div>
                                <div class="empty-state-title">No images yet</div>
                                <div class="empty-state-text">Your gallery is empty. Upload your first image to get started.</div>
                            </div>
                        @endforelse
                    </div>

                    @if($images->hasPages())
                        <div class="card-footer bg-white custom-pagination" style="padding: 1.5rem 0 0;">
                            {{ $images->links() }}
                        </div>
                    @endif
                </div>
            </div>

            @isset($viewingImageId)
                @php
                    $imagePathViewed = $images->where('id', $viewingImageId)->first()?->image_url;
                    $imageExistsViewed = Storage::disk('public')->exists($imagePathViewed);
                    $imageSrcViewed = $imageExistsViewed ? Storage::disk('public')->url($imagePathViewed) : DefaultImage::NOT_FOUND->value;
                    $imageNameViewed = basename($imagePathViewed);
                    $imageSizeBytesViewed = $imageExistsViewed ? Storage::disk('public')->size($imagePathViewed) : 0;
                    $imageLastModifiedViewed = $imageExistsViewed ? Storage::disk('public')->lastModified($imagePathViewed) : 0;
                    $imageMimeTypeViewed = $imageExistsViewed ? Storage::disk('public')->mimeType($imagePathViewed) : 'N/A';
                @endphp
                <x-livewire-admin::image-viewer title="Image Details" title-icon="fas fa-info-circle"
                    ::class="`image-detail-modal ${$wire.viewingImageId && 'show'}`" wire:key="image-detail-modal" id="imageDetailModal">
                    <x-slot:button-close x-on:click="$wire.viewingImageId = null"></x-slot:button-close>

                    <x-slot:img id="detailImage" src="{{ $imageSrcViewed }}" alt="Image Details #{{ $viewingImageId }}"></x-slot:img>

                    <div class="image-detail-row gap-2">
                        <strong>
                            <i class="fas fa-file-signature"></i>
                            <span class="text-nowrap">File name:</span>
                        </strong>
                        <span class="text-end text-break" id="detailFileName">{{ Str::limit($imageNameViewed, 100) }}</span>
                    </div>
                    <div class="image-detail-row">
                        <strong>
                            <i class="fas fa-hdd"></i>
                            <span class="text-nowrap">File size:</span>
                        </strong>
                        <span id="detailFileSize">{{ formatFileSize($imageSizeBytesViewed) }}</span>
                    </div>
                    <div class="image-detail-row">
                        <strong>
                            <i class="fas fa-calendar-alt"></i>
                            <span class="text-nowrap">Last modified:</span>
                        </strong>
                        <span id="detailFileSize">{{ date('M d, Y', $imageLastModifiedViewed) }}</span>
                    </div>
                    <div class="image-detail-row">
                        <strong>
                            <i class="fas fa-file-code"></i>
                            <span class="text-nowrap">Mime type:</span>
                        </strong>
                        <span id="detailFileSize">{{ $imageMimeTypeViewed }}</span>
                    </div>

                    <x-slot:actions>
                        <a class="image-btn image-btn-success" href="{{ $imageSrcViewed }}" download="{{ $imageNameViewed }}" title="Download">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <button type="button" class="image-btn image-btn-secondary" x-on:click="$wire.viewingImageId = null" title="Close">
                            Close
                        </button>
                    </x-slot:actions>
                </x-livewire-admin::image-viewer>
            @endif

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary bootstrap" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success bootstrap" data-bs-dismiss="modal" wire:click="dispatchSelectedImages" :disabled="!$wire.selectedImageIds.length">Select Image</button>
            </div>
        </div>
    </div>
</div>
@script
<script>
    const imagePickerEl = document.querySelector("#{{ $id }}");
    imagePickerEl.addEventListener('show.bs.modal', (event) => {
        const extraData = event.relatedTarget.dataset.extraData;
        extraData && $wire.$set(
            'extraData',
            JSON.parse(/^\d+|true|false|null$/.test(extraData) ? extraData : `"${extraData}"`),
            true
        );
    });

    imagePickerEl.addEventListener('hidden.bs.modal', function() {
        $wire.extraData !== null && $wire.$set('extraData', null, true);
    });
</script>
@endscript
