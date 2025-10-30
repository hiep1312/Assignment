@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    <x-livewire::loading-spinner id="image-loading" wire:key="image-loading" wire:loading.flex wire:target="photos, singlePhoto" />

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Image List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @elseif(session()->has('image-updated-success'))
        <x-livewire::toast-message title="Image Updated" type="primary" time="{{ session('image-updated-success')[1] }}" :show="true" :duration="8">
            {{ session('image-updated-success')[0] }}
        </x-livewire::toast-message>
    @elseif($errors->has('singlePhoto') || session()->has('image-updated-fail'))
        <x-livewire::toast-message title="Update Failed" type="danger" time="{{ session('image-updated-fail')[1] ?? now()->toISOString() }}" :show="true" :duration="8">
            {{ session('image-updated-fail')[0] ?? $errors->first('singlePhoto') }}
        </x-livewire::toast-message>
        @php $errors->forget('singlePhoto'); @endphp
    @endif

    <x-livewire::management-header title="Image List" btn-for-input="multipleImageInput" btn-label="Add New Image" btn-icon="fas fa-images" />

    <input type="file" id="multipleImageInput" accept="image/*" wire:model.change="photos" multiple class="d-none">

    @error('photos.*')
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-circle me-3 mt-1" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-2">Upload Failed</h5>
                    <p class="m-0">{{ $message }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror

    <x-livewire::filter-bar placeholderSearch="Search images..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <input type="datetime-local" class="form-control" wire:model.change="datetimeFrom" title="Created Datetime From">
        </div>
        <div class="col-md-3">
            <input type="datetime-local" class="form-control" wire:model.change="datetimeTo" title="Created Datetime To">
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Image Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Restore Images` : `Restore All Images`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Restore Images` : `Restore All Images`" data-type="question"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedRecordIds.length} images? They will be moved back to the active images list.`
                        : `Are you sure you want to restore all images? They will be moved back to the active images list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="image.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Restore Images` : `Restore All Images`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Permanently Delete Images` : `Permanently Delete All Images`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Permanently Delete Images` : `Permanently Delete All Images`" data-type="warning"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedRecordIds.length} images? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all images? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="image.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Permanently Delete Images` : `Permanently Delete All Images`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Images" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-image me-1"></i>
                    Active Images
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Images"
                    x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Images" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedRecordIds.length} images? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="image.deleted" wire:key="delete">
                    <i class="fas fa-times-circle me-1"></i>
                    Remove Images
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Images"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Images
                </button>
            @endif
        </x-slot:actions>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="toggleAll" onclick="toggleSelectAll(this)" data-state="0">
                        </th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($images as $image)
                        <tr class="text-center" wire:key="image-{{ $image->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $image->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <div class="image-common">
                                        <img src="{{ asset('storage/' . ($image->image_url ?? DefaultImage::NOT_FOUND->value)) }}?v={{ $image->updated_at->timestamp }}"
                                            alt="Website Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0;">
                                    </div>
                                </div>
                            </td>
                            <td style="min-width: 200px;">
                                <div class="text-start position-relative">
                                    <code class="d-block fw-bold text-break" style="color: inherit">
                                        {{ Str::limit(basename($image->image_url), 50, '...') }}
                                        @if($isTrashed)
                                            <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                <i class="fas fa-trash-alt"></i>
                                            </span>
                                        @endif
                                        <button class="btn btn-sm btn-outline-secondary bootstrap-focus ms-1" style="padding: 0.15rem 0.25rem; font-size: 0.75rem;"
                                            title="Copy Link" onclick="copyToClipboard('{{ asset('storage/' . ($image->image_url ?? DefaultImage::NOT_FOUND->value)) }}', this)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </code>
                                    <small class="text-muted">ID: #{{ $image->id }}</small>
                                </div>
                            </td>
                            <td>
                                <span>{{ $image->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $image->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <span>{{ $image->updated_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $image->updated_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($isTrashed)
                                        <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                            data-title="Restore Image" data-type="question" data-message="Are you sure you want to restore this image #{{ $image->id }}? The image will be moved back to the active images list."
                                            data-confirm-label="Confirm Restore" data-event-name="image.restored" data-event-data="{{ $image->id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                            data-title="Permanently Delete Image" data-type="warning" data-message="Are you sure you want to permanently delete this image #{{ $image->id }}? This action cannot be undone."
                                            data-confirm-label="Confirm Delete" data-event-name="image.forceDeleted" data-event-data="{{ $image->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" wire:click="$set('viewingImageId', {{ $image->id }})" class="btn btn-outline-info btn-action bootstrap-focus" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <input type="file" id="updateImageInput" accept="image/*" wire:model.change="singlePhoto" class="d-none">
                                        <label for="updateImageInput" x-on:click="$wire.set('updatingImageId', {{ $image->id }})" class="btn btn-outline-warning btn-action" title="Update Image">
                                            <i class="fas fa-sync-alt"></i>
                                        </label>
                                        <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Remove Image" data-type="warning" data-message="Are you sure you want to remove this image #{{ $image->id }}? The image can be restored later."
                                            data-confirm-label="Confirm Delete" data-event-name="image.deleted" data-event-data="{{ $image->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No images found</h5>
                                    <p class="text-muted">There are no images or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($images->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $images->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>

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
        <x-livewire::image-viewer title="Image Details" title-icon="fas fa-info-circle"
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
        </x-livewire::image-viewer>
    @endif
</div>
