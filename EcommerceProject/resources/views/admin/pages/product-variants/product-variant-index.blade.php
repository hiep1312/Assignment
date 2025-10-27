<div>
    @teleport('#main-component')
        <livewire:admin.components.confirm-modal id="confirmModalDetail" wire:key="confirm-modal-detail">
    @endteleport

    <x-livewire::filter-bar placeholderSearch="Search variants..." columnSearch="col-md-6 col-lg-4" modelSearch="search"
        columnReset="col-md-3 col-lg-2" resetAction="resetFilters" :isDetailFilter="true">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="status">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="stock">
                <option value="">All Stock</option>
                <option value="1">In Stock</option>
                <option value="0">Out of Stock</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Product Variant Records" class="mt-3" :isDetailFilter="true">
        <x-slot:actions>
            <a href="{{ route('admin.products.variants.create', $product_id) }}" class="btn btn-primary">
                <i class="fas fa-layer-group me-1"></i>
                Add New Variant
            </a>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Variants" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-check-circle me-1"></i>
                    Active Variants
                </button>
            @else
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Deleted Variants" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Variants
                </button>
            @endif
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>Variant Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $variant)
                        <tr class="text-center" wire:key="variant-{{ $variant->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="text-start">
                                        <div class="fw-bold">
                                            {{ Str::limit($variant->name, 30, '...') }}
                                            @if($isTrashed)
                                                <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">ID: #{{ $variant->id }}</small>
                                    </div>
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
                                @if($variant->inventory->stock > 0)
                                    <span class="text-success">
                                        <i class="fas fa-box"></i>
                                        {{ number_format($variant->inventory->stock, 0, '.', '.') }}
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
                                <span>{{ $variant->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $variant->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($isTrashed)
                                        <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                            data-title="Restore Variant" data-type="question" data-message="Are you sure you want to restore this variant #{{ $variant->id }}? The variant will be moved back to the active variants list."
                                            data-confirm-label="Confirm Restore" data-event-name="variant.restored" data-event-data="{{ $variant->id }}" data-id="confirmModalDetail">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                            data-title="Permanently Delete Variant" data-type="warning" data-message="Are you sure you want to permanently delete this variant #{{ $variant->id }}? This action cannot be undone."
                                            data-confirm-label="Confirm Delete" data-event-name="variant.forceDeleted" data-event-data="{{ $variant->id }}" data-id="confirmModalDetail">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.products.variants.edit', [$product_id, $variant->id]) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Remove Variant" data-type="warning" data-message="Are you sure you want to remove this variant #{{ $variant->id }}? The variant can be restored later."
                                            data-confirm-label="Confirm Delete" data-event-name="variant.deleted" data-event-data="{{ $variant->id }}" data-id="confirmModalDetail">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state" style="padding: 0">
                                    <i class="fas fa-boxes fa-2x text-muted mb-3"></i>
                                    <h5 class="text-muted">No variants found</h5>
                                    <p class="text-muted">There are no product variants or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($variants->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $variants->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
