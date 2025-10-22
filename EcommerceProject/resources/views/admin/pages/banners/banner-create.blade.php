@use('App\Enums\UserRole')
@use('App\Livewire\Admin\Components\FormPanel\ImageUploader')
@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Add New Banner" btn-link="{{ route('admin.banners.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <livewire:admin.components.gallery-manager wire:key="gallery-picker" id="galleryPickerModal" />

    <x-livewire::form-panel :isFormNormal="false" id="banner-create-form" action="store">
        <x-livewire::form-panel.image-uploader :isMultiple="false" :type="ImageUploader::TYPE_BANNER" label="Banner Image" labelIcon="fa-solid fa-image-landscape">
            @php $previewImage = is_int($image_id) ? asset("storage/{$image->image_url}") : DefaultImage::getDefaultPath(ImageUploader::TYPE_BANNER) @endphp
            <x-slot:image :src="$previewImage" alt="Banner Preview"></x-slot:image>

            <x-slot:upload-button data-bs-toggle="modal" data-bs-target="#galleryPickerModal" wire:model.live="image_id"></x-slot:upload-button>

            <x-slot:feedback>
                @error('image_id')
                    <div class="invalid-feedback mt-3 d-block text-center">
                        {{ $message }}
                    </div>
                @enderror
            </x-slot:feedback>
        </x-livewire::form-panel.image-uploader>

        <hr class="my-4">

        <x-livewire::form-panel.group title="Banner Information" icon="fas fa-rectangle-ad">
            <x-livewire::form-panel.group.input-group label="Title" icon="fas fa-heading" for="title" column="col-12">
                <input type="text" class="form-control custom-radius-end @error('title') is-invalid @enderror" id="title"
                    wire:model="title" placeholder="Enter banner title">
                <x-slot:feedback>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Link URL" icon="fas fa-link" for="link_url" column="col-md-6" required>
                <input type="url" class="form-control custom-radius-end @error('link_url') is-invalid @enderror" id="link_url"
                    wire:model="link_url" placeholder="Enter link url">
                <x-slot:feedback>
                    @error('link_url')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Status" :icon="$status == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off'" for="status" column="col-md-6" required>
                <select class="form-select custom-radius-end @error('status') is-invalid @enderror" id="status"
                    wire:model.change="status" wire:key="status">
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <x-slot:feedback>
                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <div class="col-12">
                <label for="position" class="form-label bootstrap-style">Display Order <span class="text-danger">*</span></label>
                <div class="priority-display @error('position') is-invalid @enderror" id="position">
                    @foreach(range(1, 100) as $priorityIndex)
                        @php
                            $isUsed = in_array($priorityIndex, $usedPositions);
                            $isActivePosition = $priorityIndex === $position;
                        @endphp
                        <button type="button" @class([
                            "priority-item",
                            "used" => $isUsed,
                            "available" => !$isUsed && !$isActivePosition,
                            "current" => !$isUsed && $isActivePosition
                        ]) wire:click="$set('position', {{ $priorityIndex }})">{{ $priorityIndex }}</button>
                    @endforeach
                </div>
                @error('position')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </x-livewire::form-panel.group>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary bootstrap-focus me-2" wire:click="resetForm">
                <i class="fas fa-redo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Create Banner
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>
</div>
