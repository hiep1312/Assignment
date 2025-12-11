@assets
    @vite('resources/js/scSortable.js')
@endassets

@use('App\Livewire\Admin\Components\FormPanel\ImageUploader')
@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component" x-data="{ isEditing: false, resetVariantModal: () => {
        $wire.$set('activeVariantData', [], true);
    } }">
    <livewire:admin.components.confirm-modal>

    <x-livewire-admin::management-header title="Add New Product" btn-link="{{ route('admin.products.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <livewire:admin.components.gallery-manager wire:key="gallery-picker" id="galleryPickerModal" />

    <x-livewire-admin::data-selector title="Select Categories" id="dataSelectorCategory" resetProperty="selectedCategoryIds" wire:key="data-selector-category">
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
    </x-livewire-admin::data-selector>

    <x-livewire-admin::form-panel :isFormNormal="false" id="product-create-form" action="store">
        <x-livewire-admin::form-panel.image-uploader :isMultiple="true" :type="ImageUploader::TYPE_PRODUCT" label="Product Image" labelIcon="fas fa-camera-retro">
            <x-slot:upload-button data-bs-toggle="modal" data-bs-target="#galleryPickerModal"></x-slot:upload-button>

            <x-slot:gallery-uploader wire:sc-sortable="images" wire:sc-model.live.debounce.300ms="image_ids" wire:ignore.self wire:key="gallery-uploader-{{ count($images) }}"></x-slot:gallery-uploader>

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
        </x-livewire-admin::form-panel.image-uploader>

        <hr class="my-4">

        <x-livewire-admin::form-panel.group title="Product Information" icon="fas fa-box">
            <x-livewire-admin::form-panel.group.input-group label="Title" icon="fas fa-tag" for="title" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('title') is-invalid @enderror" id="title"
                    wire:model.blur="title" placeholder="Enter product title">
                <x-slot:feedback>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Slug" icon="fas fa-link" for="slug" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('slug') is-invalid @enderror" id="slug"
                    wire:model="slug" placeholder="Enter product slug">
                <x-slot:feedback>
                    @error('slug')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Description" icon="fas fa-align-left" for="description" column="col-md-12">
                <textarea class="form-control custom-radius-end @error('description') is-invalid @enderror" id="description"
                    wire:model="description" placeholder="Enter description" rows="5"></textarea>
                <x-slot:feedback>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Status" :icon="$status == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off'" for="status" column="col-md-6" required>
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
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Categories" icon="fas fa-folder" for="categories" column="col-md-6">
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
            </x-livewire-admin::form-panel.group.input-group>
        </x-livewire-admin::form-panel.group>

        <hr class="mt-4 mb-3">

        <x-livewire-admin::form-panel.group title="Product Variants" icon="fas fa-cubes" :hasTitleAction="true">
            <x-slot:button-action type="button" class="btn btn-success bootstrap" icon="fas fa-plus"
                data-bs-toggle="modal" data-bs-target="#variantModal" wire:click="addVariant" x-on:click="isEditing = false">Add Variant</x-slot:button-action>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Variant Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($variants as $keyId => $variant)
                            @php $variant = (object) $variant @endphp
                            <tr class="text-center" wire:key="variant-{{ $keyId }}">
                                <td>
                                    <div class="fw-bold">
                                        {{ Str::limit($variant->name, 30, '...') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ number_format($variant->discount ?? $variant->price, 0, '.', '.') }}đ</div>
                                    @if($variant->discount)
                                        <small class="text-muted d-block mt-1">
                                            Original: {{ number_format($variant->price, 0, ',', '.') }}đ
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($variant->stock > 0)
                                        <span class="text-success">
                                            <i class="fas fa-box"></i>
                                            {{ number_format($variant->stock, 0, '.', '.') }}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Sold out
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bootstrap-color
                                        @switch($variant->status)
                                            @case(1) bg-success @break
                                            @case(0) bg-secondary @break
                                        @endswitch
                                    ">
                                        @switch($variant->status)
                                            @case(1) active @break
                                            @case(0) inactive @break
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning btn-action bootstrap-focus" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#variantModal" wire:click="editVariant('{{ $keyId }}')" x-on:click="isEditing = true">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Delete Variant" data-type="warning" data-message="Are you sure you want to delete this variant #{{ $keyId }}?"
                                            data-confirm-label="Confirm Delete" data-event-name="variant.removed" data-event-data='@json($keyId)'>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-state-row">
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state" style="padding: 0">
                                        <i class="fas fa-boxes fa-2x text-muted mb-3"></i>
                                        <h5 class="text-muted">No variants found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-livewire-admin::form-panel.group>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary bootstrap-focus me-2" wire:click="resetForm">
                <i class="fas fa-redo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Create Product
            </button>
        </x-slot:actions>
    </x-livewire-admin::form-panel>

    <div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="isEditing ? 'Edit Variant' : 'Add New Variant'"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" x-on:click="resetVariantModal"></button>
                </div>
                <div class="modal-body">
                    @if($activeVariantData)
                        <div class="row g-3" wire:key="variant-{{ $activeVariantData['keyId'] ?? count($variants) }}">
                            <x-livewire-admin::form-panel.group.input-group label="Variant Name" icon="fas fa-tag" for="name" column="col-lg-6" required>
                                <input type="text" class="form-control custom-radius-end @error("activeVariantData.name") is-invalid @enderror" id="name"
                                    wire:model="activeVariantData.name" placeholder="Enter variant name">
                                <x-slot:feedback>
                                    @error("activeVariantData.name")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </x-slot:feedback>
                            </x-livewire-admin::form-panel.group.input-group>

                            <x-livewire-admin::form-panel.group.input-group label="SKU" icon="fas fa-barcode" for="sku" column="col-lg-6" required>
                                <input type="text" class="form-control @error("activeVariantData.sku") is-invalid @enderror" id="sku"
                                    wire:model="activeVariantData.sku" placeholder="Enter unique SKU code">
                                <button type="button" class="btn btn-outline-warning custom-radius-end bootstrap-hover bootstrap-focus"
                                    style="padding: 0.4375rem 0.6rem" wire:click="$set('activeVariantData.sku', '{{ strtoupper(Str::random(12)) }}')">Generate SKU</button>
                                <x-slot:feedback>
                                    @error("activeVariantData.sku")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </x-slot:feedback>
                            </x-livewire-admin::form-panel.group.input-group>

                            <x-livewire-admin::form-panel.group.input-group label="Price" icon="fas fa-dollar-sign" for="price" column="col-lg-6" required>
                                <input type="number" class="form-control custom-radius-end @error("activeVariantData.price") is-invalid @enderror" id="price"
                                    wire:model="activeVariantData.price" placeholder="Enter price" min="0" step="1">
                                <x-slot:feedback>
                                    @error("activeVariantData.price")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </x-slot:feedback>
                            </x-livewire-admin::form-panel.group.input-group>

                            <x-livewire-admin::form-panel.group.input-group label="Discounted Price" icon="fas fa-percent" for="discount" column="col-lg-6">
                                <input type="number" class="form-control custom-radius-end @error("activeVariantData.discount") is-invalid @enderror" id="discount"
                                    wire:model="activeVariantData.discount" placeholder="Enter discounted price" min="0" step="1">
                                <x-slot:feedback>
                                    @error("activeVariantData.discount")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </x-slot:feedback>
                            </x-livewire-admin::form-panel.group.input-group>

                            <x-livewire-admin::form-panel.group.input-group label="Stock Quantity" icon="fas fa-boxes" for="stock" column="col-lg-6" required>
                                <input type="number" class="form-control custom-radius-end @error("activeVariantData.stock") is-invalid @enderror" id="stock"
                                    wire:model="activeVariantData.stock" placeholder="Enter stock quantity" min="0" step="1">
                                <x-slot:feedback>
                                    @error("activeVariantData.stock")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </x-slot:feedback>
                            </x-livewire-admin::form-panel.group.input-group>

                            <x-livewire-admin::form-panel.group.input-group label="Status" :icon="$activeVariantData['status'] == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off'" for="status" column="col-md-6" required>
                                <select class="form-select custom-radius-end @error("activeVariantData.status") is-invalid @enderror" id="status"
                                    wire:model.change="activeVariantData.status" wire:key="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <x-slot:feedback>
                                    @error("activeVariantData.status")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </x-slot:feedback>
                            </x-livewire-admin::form-panel.group.input-group>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="dots">
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                            <span class="loading-text-dots">Loading</span>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" x-on:click="resetVariantModal">Cancel</button>
                    <button type="button" class="btn btn-primary" x-on:click="$wire.handleVariantModal(isEditing)" x-text="isEditing ? 'Update Variant' : 'Create Variant'"></button>
                </div>
            </div>
        </div>
    </div>
</div>
