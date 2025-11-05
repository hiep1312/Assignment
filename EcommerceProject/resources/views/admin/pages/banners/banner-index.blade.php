@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal wire:key="confirm-modal">

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Banner List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Banner List" btn-link="{{ route('admin.banners.create') }}" btn-label="Add New Banner" btn-icon="fas fa-image" />

    <x-livewire::filter-bar placeholderSearch="Search banners..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="status">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select" wire:model.change="sortOrder">
                <option value="">Default Order (Created Date)</option>
                <option value="asc">Position: Low to High</option>
                <option value="desc">Position: High to Low</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Banner Records">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Delete Banners"
                x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                data-title="Delete Banners" data-type="warning" x-bind:data-message="`Are you sure you want to delete these ${$wire.selectedRecordIds.length} banners? This action cannot be undone.`"
                data-confirm-label="Confirm Delete" data-event-name="banner.deleted" wire:key="delete">
                <i class="fas fa-trash-alt me-1"></i>
                Delete Banners
            </button>
            <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Reorder Positions" onclick="confirmModalAction(this)"
                data-title="Reorder Banner Positions" data-type="question" data-message="Are you sure you want to reorder all banner positions? All positions will be reorganized sequentially starting from 1."
                data-confirm-label="Confirm Reorder" data-event-name="banner.reordered" wire:key="reorder">
                <i class="fas fa-sort-numeric-down me-1"></i>
                Reorder Positions
            </button>
        </x-slot:actions>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="toggleAll" onclick="toggleSelectAll(this)" data-state="0">
                        </th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Link URL</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                        <tr class="text-center" wire:key="banner-{{ $banner->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $banner->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <div class="banner-image" data-bs-toggle="modal" data-bs-target="#bannerPreview" data-banner-id="{{ $banner->id }}">
                                        <img src="{{ asset('storage/' . ($banner->image_url ?? DefaultImage::BANNER->value)) }}"
                                            alt="Banner image {{ $banner->title }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0;">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-start">
                                    <div class="fw-bold text-wrap lh-base">
                                        {{ Str::limit($banner->title ?? 'Untitled', 30, '...') }}
                                    </div>
                                    <small class="text-muted">ID: #{{ $banner->id }}</small>
                                </div>
                            </td>
                            <td>
                                @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary bootstrap-focus"
                                        title="Open link: {{ $banner->link_url }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-circle bg-label-secondary">{{ $banner->position }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bootstrap-color
                                    @switch($banner->status)
                                        @case(1) bg-success @break
                                        @case(2) bg-secondary @break
                                    @endswitch
                                ">
                                    @switch($banner->status)
                                        @case(1) Active @break
                                        @case(2) Inactive @break
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span>{{ $banner->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $banner->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-{{ $banner->status == 1 ? 'secondary' : 'success' }} btn-action bootstrap-focus"
                                        title="{{ $banner->status === 1 ? 'Deactivate' : 'Activate' }}"
                                        wire:click="switchStatus({{ $banner->id }})">
                                        <i class="fas fa-{{ $banner->status === 1 ? 'toggle-off' : 'toggle-on' }}"></i>
                                    </button>
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                        data-title="Delete Banner" data-type="warning" data-message="Are you sure you want to permanently delete this banner #{{ $banner->id }}? This action cannot be undone."
                                        data-confirm-label="Confirm Delete" data-event-name="banner.deleted" data-event-data="{{ $banner->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-image-landscape fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No banners found</h5>
                                    <p class="text-muted">There are no banners or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($banners->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $banners->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>

    <x-livewire::content-preview title="Banner Preview" icon="fas fa-images" id="bannerPreview">
        <div id="slideshowBanner" class="carousel slide">
            <div class="carousel-inner">
                @foreach($banners as $banner)
                    <div class="carousel-item" wire:key="carousel-item-{{ $banner->id }}" data-banner-id="{{ $banner->id }}" wire:ignore.self>
                        <img src="{{ asset('storage/' . ($banner->image_url ?? DefaultImage::BANNER->value)) }}"
                            class="d-block w-100" alt="Banner image {{ $banner->title }}"
                            style="object-fit: cover; aspect-ratio: 16 / 8;">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>{{ $banner->title }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev carousel-control-bootstrap-opacity" type="button" data-bs-target="#slideshowBanner"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next carousel-control-bootstrap-opacity" type="button" data-bs-target="#slideshowBanner"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <x-slot:script>
            <script>
                const bannerPreview = document.querySelector('#bannerPreview');
                bannerPreview.addEventListener('show.bs.modal', function(event) {
                    const idActive = event.relatedTarget.getAttribute('data-banner-id');
                    const framePreview = event.target;

                    const itemActive = framePreview.querySelector(`[data-banner-id="${idActive}"]`);
                    itemActive.classList.add('active');
                });

                bannerPreview.addEventListener('hidden.bs.modal', function(event) {
                    const framePreview = event.target;

                    const itemActive = framePreview.querySelector('.active');
                    itemActive.classList.remove('active');
                });
            </script>
        </x-slot:script>
    </x-livewire::content-preview>
</div>
