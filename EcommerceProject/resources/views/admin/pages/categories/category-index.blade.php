@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Category List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Category List" btn-link="{{ route('admin.categories.create') }}" btn-label="Add New Category" btn-icon="fas fa-folder-plus" />

    <x-livewire::filter-bar placeholderSearch="Search categories..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="createdBy">
                <option value="">All Creators</option>
                @foreach($creators as $creator)
                    <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="categoryGroup">
                <option value="">All Groups</option>
                <option value="product">Product</option>
                <option value="blog">Blog</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Category Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Restore Categories` : `Restore All Categories`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Restore Categories` : `Restore All Categories`" data-type="question"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedRecordIds.length} categories? They will be moved back to the active categories list.`
                        : `Are you sure you want to restore all categories? They will be moved back to the active categories list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="category.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Restore Categories` : `Restore All Categories`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Permanently Delete Categories` : `Permanently Delete All Categories`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Permanently Delete Categories` : `Permanently Delete All Categories`" data-type="warning"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedRecordIds.length} categories? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all categories? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="category.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Permanently Delete Categories` : `Permanently Delete All Categories`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Categories" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-folder-open me-1"></i>
                    Active Categories
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Categories"
                    x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Categories" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedRecordIds.length} categories? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="category.deleted" wire:key="delete">
                    <i class="fas fa-folder-minus me-1"></i>
                    Remove Categories
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Categories"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Categories
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
                        <th>Category</th>
                        <th>Linked Items</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="text-center" wire:key="category-{{ $category->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $category->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td style="min-width: 250px;">
                                <div class="text-start">
                                    <div class="fw-bold text-wrap lh-base">
                                        {{ Str::limit($category->name, 100, '...') }}
                                        @if($isTrashed)
                                            <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                <i class="fas fa-trash-alt"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted">ID: #{{ $category->id }}</small>
                                </div>
                            </td>
                            <td>
                                @php $linkedCount = $category->products_count + $category->blogs_count @endphp
                                <span class="badge rounded-pill bg-label-{{ $linkedCount ? 'primary' : 'secondary' }}">
                                    <i class="fas fa-link"></i>
                                    {{ $category->products_count + $category->blogs_count }}
                                </span>
                            </td>
                            <td style="min-width: 250px;">
                                @php $user = $category->creator; @endphp
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($user?->avatar ?? DefaultImage::AVATAR->value)) }}"
                                        class="rounded-circle me-2" width="40" height="40" alt="User Avatar">
                                    <div class="text-start">
                                        <div class="fw-bold">{{ Str::limit($user?->name ?? 'Unknown User', 30, '...') }}</div>
                                        @if($user)
                                            <small class="text-muted d-block text-nowrap">
                                                <i class="fas fa-envelope me-1"></i>{{ Str::limit($user?->email, 25, '...') }}
                                            </small>
                                        @else
                                            <small class="text-danger d-block">
                                                <i class="fas fa-times-circle me-1"></i>User deleted or not found
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>{{ $category->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $category->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($isTrashed)
                                        <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                            data-title="Restore Category" data-type="question" data-message="Are you sure you want to restore this category #{{ $category->id }}? The category will be moved back to the active categories list."
                                            data-confirm-label="Confirm Restore" data-event-name="category.restored" data-event-data="{{ $category->id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                            data-title="Permanently Delete Category" data-type="warning" data-message="Are you sure you want to permanently delete this category #{{ $category->id }}? This action cannot be undone."
                                            data-confirm-label="Confirm Delete" data-event-name="category.forceDeleted" data-event-data="{{ $category->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Remove Category" data-type="warning" data-message="Are you sure you want to remove this category #{{ $category->id }}? The category can be restored later."
                                            data-confirm-label="Confirm Delete" data-event-name="category.deleted" data-event-data="{{ $category->id }}">
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
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No categories found</h5>
                                    <p class="text-muted">There are no categories or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($categories->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $categories->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
