@use('App\Livewire\Admin\Components\FormPanel\ImageUploader')
@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Edit Product" btn-link="{{ route('admin.products.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <livewire:admin.components.gallery-manager wire:key="gallery-picker" id="galleryPickerModal" />

    <x-livewire::data-selector title="Select Categories" id="dataSelectorCategory" resetProperty="selectedCategoryIds" wire:key="data-selector-category">
        <x-slot:input type="text" placeholder="Search categories..." wire:model.live.debounce.300ms="searchCategories"></x-slot:input>

        @forelse($categories as $category)
            <div class="checkbox-item" onclick="this.querySelector('input').click()" wire:key="category-{{ $category->id }}">
                <div class="checkbox-wrapper">
                    <input type="checkbox" value="{{ $category->id }}" wire:model.change="selectedCategoryIds">
                    <span class="checkmark"></span>
                </div>
                <label class="checkbox-label">{{ $category->name }}</label>
            </div>
        @empty
            <div class="empty-state-selection">No existing categories found.</div>
        @endforelse

        <x-slot:button-confirm wire:click="selectCategories" :disabled="!count($selectedCategoryIds)">Choose Categories</x-slot:button-confirm>
    </x-livewire::data-selector>

    <x-livewire::form-panel :isFormNormal="false" id="product-edit-form" action="update">
        <x-livewire::form-panel.image-uploader :isMultiple="true" :type="ImageUploader::TYPE_PRODUCT" label="Product Image" labelIcon="fas fa-box">
            <x-slot:upload-button data-bs-toggle="modal" data-bs-target="#galleryPickerModal"></x-slot:upload-button>

            <x-slot:gallery-uploader wire:sc-sortable="images" wire:sc-model.live.debounce.500ms="image_ids" wire:ignore.self wire:key="gallery-uploader-{{ count($images) }}"></x-slot:gallery-uploader>

            @forelse($images as $image)
                @php $isMainImage = $image->id == $mainImageId @endphp
                <div class="gallery-item" wire:key="image-{{ $image->id }}" wire:ignore.self sc-id="{{ $image->id }}">
                    <img src="{{ asset("storage/{$image->image_url}") }}" alt="Product image #{{ $image->id }}" class="gallery-image">
                    <div class="gallery-overlay">
                        @unless($isMainImage)
                            <button type="button" class="gallery-btn star" wire:click="$set('mainImageId', {{ $image->id }})">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        @endunless
                        <button type="button" class="gallery-btn delete" wire:click="removeImage({{ $image->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @if($isMainImage)
                        <div class="badge-star-image">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path>
                            </svg>
                            Main
                        </div>
                    @endif
                </div>
            @empty
                <x-slot:empty-state>No product images yet</x-slot:empty-state>
            @endforelse
        </x-livewire::form-panel.image-uploader>

        <hr class="my-4">

        <x-livewire::form-panel.group title="Product Information" icon="fas fa-box">
            <x-livewire::form-panel.group.input-group label="Title" icon="fas fa-tag" for="title" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('title') is-invalid @enderror" id="title"
                    wire:model.blur="title" placeholder="Enter product title">
                <x-slot:feedback>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Slug" icon="fas fa-link" for="slug" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('slug') is-invalid @enderror" id="slug"
                    wire:model="slug" placeholder="Enter product slug">
                <x-slot:feedback>
                    @error('slug')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Description" icon="fas fa-align-left" for="description" column="col-md-12">
                <textarea class="form-control custom-radius-end @error('description') is-invalid @enderror" id="description"
                    wire:model="description" placeholder="Enter description" rows="5"></textarea>
                <x-slot:feedback>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Status" :icon="$status == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off'" for="status" column="col-md-6" required>
                <select class="form-select custom-radius-end @error('status') is-invalid @enderror" id="status"
                    wire:model.change="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <x-slot:feedback>
                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Categories" icon="fas fa-folder" for="categories" column="col-md-6">
                <input type="text" class="form-control @error('category_ids') is-invalid @enderror" id="categories"
                    value="{{ implode(', ', $category_names) }}" placeholder="Choose categories" readonly>
                <button type="button" class="btn btn-outline-secondary custom-radius-end bootstrap-hover bootstrap-focus"
                    style="padding: 0.4375rem 0.6rem" data-bs-toggle="modal" data-bs-target="#dataSelectorCategory"
                    x-on:click="$wire.selectedCategoryIds = $wire.category_ids">Select categories</button>
                <x-slot:feedback>
                    @error('category_ids')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>
        </x-livewire::form-panel.group>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary bootstrap-focus me-2" wire:click="resetForm">
                <i class="fas fa-redo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Update Product
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>
</div>
