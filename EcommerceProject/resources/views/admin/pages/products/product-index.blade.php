@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal wire:key="confirm-modal-product-{{ $recordDetail }}">

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Product List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Product List" btn-link="{{ route('admin.products.create') }}" btn-label="Add New Product" btn-icon="fas fa-plus" />

    <x-livewire::stats-overview :data-stats="$statistic" />

    <x-livewire::detail-modal activeRecordVariable="recordDetail" title="Product Details" icon="fas fa-layer-group" id="productDetailModal" wire:key="product-detail">
        <x-slot:tabs>
            <li class="nav-item" role="presentation">
                <button class="nav-link active bootstrap-style" id="variants-tab" data-bs-toggle="tab" data-bs-target="#variants-content"
                    type="button" role="tab" aria-controls="variants-content" aria-selected="true"
                    wire:key="tab-product-variant-detail-{{ $recordDetail }}" wire:ignore.self>
                    <i class="fas fa-list"></i> Variants
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bootstrap-style" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-content"
                    type="button" role="tab" aria-controls="images-content" aria-selected="false"\
                    wire:key="tab-product-image-detail-{{ $recordDetail }}" wire:ignore.self>
                    <i class="fas fa-images"></i> Images
                </button>
            </li>
        </x-slot:tabs>

        <div class="tab-pane fade show active" id="variants-content" role="tabpanel" aria-labelledby="variants-tab"
            wire:key="product-variant-detail-{{ $recordDetail }}" wire:ignore.self>
            @if($recordDetail)
                <livewire:admin.product-variants.product-variant-index :product="$recordDetail" wire:key="product-variant-{{ $recordDetail }}" />
            @endif
        </div>
        <div class="tab-pane fade" id="images-content" role="tabpanel" aria-labelledby="images-tab" x-data="{}"
            wire:key="product-image-detail-{{ $recordDetail }}" wire:ignore>
            @if($recordDetail)
                @php
                    $selectedProduct = $products->firstWhere('id', $recordDetail);
                    $mainImage = $selectedProduct->mainImage ?? (object)['id' => -1, 'image_url' => null];
                @endphp
                <div class="image-gallery" x-data="{ viewingImageId: {{ $mainImage->id }}, viewingImageSrc: '{{ asset('storage/' . ($mainImage->image_url ?? DefaultImage::PRODUCT->value)) }}' }">
                    <div class="main-image-container">
                        <img id="mainImage" :src="viewingImageSrc" alt="Currently viewed image">
                    </div>

                    <div class="thumbnails-container">
                        @foreach($selectedProduct->images->prepend($mainImage) as $image)
                            @php $imageSrc = asset('storage/' . ($image->image_url ?? DefaultImage::PRODUCT->value)); @endphp
                            <div :class="`thumbnail ${viewingImageId === {{ $image->id }} && 'active'}`" wire:key="product-image-{{ $image->id }}"
                                x-on:click="viewingImageId = {{ $image->id }}; viewingImageSrc = '{{ $imageSrc }}'">
                                <img src="{{ $imageSrc }}" alt="Thumbnail image">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-livewire::detail-modal>

    <x-livewire::filter-bar placeholderSearch="Search products..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="status">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="categoryId">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Product Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Restore Products` : `Restore All Products`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Restore Products` : `Restore All Products`" data-type="question"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedRecordIds.length} products? They will be moved back to the active products list.`
                        : `Are you sure you want to restore all products? They will be moved back to the active products list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="product.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Restore Products` : `Restore All Products`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Permanently Delete Products` : `Permanently Delete All Products`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Permanently Delete Products` : `Permanently Delete All Products`" data-type="warning"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedRecordIds.length} products? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all products? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="product.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Permanently Delete Products` : `Permanently Delete All Products`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Products" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-check-circle me-1"></i>
                    Active Products
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Products"
                    x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Products" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedRecordIds.length} products? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="product.deleted" wire:key="delete">
                    <i class="fas fa-times-circle me-1"></i>
                    Remove Products
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Products"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Products
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
                        <th>Product</th>
                        <th>Description</th>
                        <th>Variants</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="text-center" wire:key="product-{{ $product->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $product->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($product->mainImage?->image_url ?? DefaultImage::PRODUCT->value)) }}"
                                        class="rounded me-2" width="50" height="50" alt="Product Image" style="object-fit: cover;">
                                    <div class="text-start">
                                        <div class="fw-bold">
                                            {{ Str::limit($product->title, 30, '...') }}
                                            @if($isTrashed)
                                                <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">ID: #{{ $product->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 301px;">
                                <small class="text-muted d-block text-wrap lh-base">{{ Str::limit($product->description, 80, '...') }}</small>
                            </td>
                            <td>
                                @if($product->variants->isNotEmpty())
                                    @php $variantsInStock = $product->variants->sum('inventory.stock'); @endphp
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-label-primary mb-1" style="text-transform: none;">
                                            <i class="fas fa-layer-group me-1"></i>{{ $product->variants_count }} variant(s)
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-box me-1"></i>{{ $variantsInStock }} in stock
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.9em;">
                                        <i class="fas fa-minus-circle"></i> No variants
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill bootstrap-color
                                    @switch($product->status)
                                        @case(1) bg-success @break
                                        @case(0) bg-secondary @break
                                    @endswitch
                                ">
                                    @switch($product->status)
                                        @case(1) active @break
                                        @case(0) inactive @break
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span>{{ $product->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $product->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($isTrashed)
                                        <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                            data-title="Restore Product" data-type="question" data-message="Are you sure you want to restore this product #{{ $product->id }}? The product will be moved back to the active products list."
                                            data-confirm-label="Confirm Restore" data-event-name="product.restored" data-event-data="{{ $product->id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                            data-title="Permanently Delete Product" data-type="warning" data-message="Are you sure you want to permanently delete this product #{{ $product->id }}? This action cannot be undone."
                                            data-confirm-label="Confirm Delete" data-event-name="product.forceDeleted" data-event-data="{{ $product->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-info btn-action bootstrap-focus" title="View Details"
                                            data-bs-toggle="modal" data-bs-target="#productDetailModal" wire:click="$set('recordDetail', {{ $product->id }})">
                                            <i class="fas fa-layer-group"></i>
                                        </button>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Remove Product" data-type="warning" data-message="Are you sure you want to remove this product #{{ $product->id }}? The product can be restored later."
                                            data-confirm-label="Confirm Delete" data-event-name="product.deleted" data-event-data="{{ $product->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No products found</h5>
                                    <p class="text-muted">There are no products or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:pagination>
            @if($products->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $products->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
