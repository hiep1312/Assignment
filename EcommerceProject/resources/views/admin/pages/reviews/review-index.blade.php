@use('App\Enums\DefaultImage')
@use('Illuminate\Pagination\LengthAwarePaginator')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Review List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Reviews List" />

    <x-livewire::filter-bar placeholderSearch="Search reviews..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="rating">
                <option value="">All Ratings</option>
                <option value="5">5 Stars⭐</option>
                <option value="4">4 Stars⭐</option>
                <option value="3">3 Stars⭐</option>
                <option value="2">2 Stars⭐</option>
                <option value="1">1 Star⭐</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="productId">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->title }}</option>
                @endforeach
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Review Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Restore Reviews` : `Restore All Reviews`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Restore Reviews` : `Restore All Reviews`" data-type="question"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedRecordIds.length} reviews? They will be moved back to the active reviews list.`
                        : `Are you sure you want to restore all reviews? They will be moved back to the active reviews list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="review.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Restore Reviews` : `Restore All Reviews`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Permanently Delete Reviews` : `Permanently Delete All Reviews`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Permanently Delete Reviews` : `Permanently Delete All Reviews`" data-type="warning"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedRecordIds.length} reviews? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all reviews? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="review.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Permanently Delete Reviews` : `Permanently Delete All Reviews`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Reviews" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-check-circle me-1"></i>
                    Active Reviews
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Reviews"
                    x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Reviews" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedRecordIds.length} reviews? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="review.deleted" wire:key="delete">
                    <i class="fas fa-times-circle me-1"></i>
                    Remove Reviews
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Reviews"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Reviews
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
                        <th>Status</th>
                        <th class="text-nowrap">Average Rating</th>
                        <th>Reviews</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="text-center" wire:key="product-{{ $product->id }}">
                            <td>
                                <button class="collapse-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReviews{{ $product->id }}"
                                    aria-expanded="false" aria-controls="collapseReviews{{ $product->id }}" wire:key="collapse-reviews-{{ $product->id }}" wire:ignore.self>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </td>
                            <td style="min-width: 230px;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($product->mainImage?->image_url ?? DefaultImage::PRODUCT->value)) }}"
                                        class="rounded me-2" width="50" height="50" alt="Product Image" style="object-fit: cover;">
                                    <div class="text-start">
                                        <div class="fw-bold">{{ Str::limit($product->title, 30, '...') }}</div>
                                        <small class="text-muted">Product ID: #{{ $product->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="min-width: 270px;">
                                <small class="text-muted d-block text-wrap lh-base">{{ Str::limit($product->description, 100, '...') }}</small>
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
                                @php
                                    $reviewScore = round($product->reviews_avg_rating * 2) / 2;
                                    $fullStars = floor($reviewScore);
                                    $hasHalfStar = ($reviewScore - $fullStars) === 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                @endphp
                                <div class="rating-stars text-nowrap">
                                    {!!
                                        str_repeat('<i class="fas fa-star"></i>', $fullStars) .
                                        ($hasHalfStar ? '<i class="fas fa-star-half-alt"></i>' : '') .
                                        str_repeat('<i class="far fa-star"></i>', $emptyStars)
                                    !!}
                                </div>
                                <span class="text-muted">Score: {{ $reviewScore }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-label-{{ $product->reviews_count ? 'primary' : 'secondary' }}">
                                    <i class="fas fa-award"></i>
                                    {{ $product->reviews_count }}
                                </span>
                            </td>
                        </tr>
                        <x-livewire::expandable-row id="collapseReviews{{ $product->id }}" title="Product Reviews" icon="fas fa-comments" wire:key="collapse-reviews-{{ $product->id }}">
                            <div class="card-body p-0 table-responsive shadow-sm" style="border-radius: 0.5rem 0.5rem 0 0;">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Select</th>
                                            <th>User</th>
                                            <th>Rating</th>
                                            <th>Review Content</th>
                                            <th class="text-nowrap">Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-light">
                                        @php
                                            $reviewPageName = 'reviewsPage';
                                            $currentReviewPage = LengthAwarePaginator::resolveCurrentPage($reviewPageName);
                                            $paginatedReviews = new LengthAwarePaginator(
                                                items: $product->reviews->forPage($currentReviewPage, 5),
                                                total: $product->reviews->count(),
                                                perPage: 5,
                                                currentPage: $currentReviewPage,
                                                options: [
                                                    'pageName' => $reviewPageName
                                                ]
                                            );
                                        @endphp
                                        @foreach($paginatedReviews as $review)
                                            <tr class="text-center" wire:key="review-{{ $review->id }}">
                                                <td>
                                                    <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                                        value="{{ $review->id }}" onclick="updateSelectAllState()">
                                                </td>
                                                <td style="min-width: 230px;">
                                                    @php $user = $review->user; @endphp
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('storage/' . ($user->avatar ?? DefaultImage::AVATAR->value)) }}"
                                                            class="rounded-circle me-2" width="40" height="40" alt="User Avatar">
                                                        <div class="text-start">
                                                            <div class="fw-bold">
                                                                {{ Str::limit($user->name, 20, '...') }}
                                                                @if($isTrashed)
                                                                    <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted">ID: #{{ $review->id }}</small>
                                                            <small class="text-muted d-block">User ID: #{{ $user->id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="rating-stars text-nowrap">
                                                        {!! str_repeat('<i class="fas fa-star"></i>', $review->rating) . str_repeat('<i class="far fa-star"></i>', 5 - $review->rating) !!}
                                                    </div>
                                                    <span class="text-muted">Score: {{ $review->rating }}</span>
                                                </td>
                                                <td style="min-width: 250px;">
                                                    <small class="text-muted d-block text-wrap lh-base">{{ Str::limit($review->content ?? 'No review content provided', 90, '...') }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $review->created_at->format('m/d/Y') }}</span>
                                                    <small class="text-muted d-block">{{ $review->created_at->format('H:i A') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($isTrashed)
                                                            <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                                                data-title="Restore Review" data-type="question" data-message="Are you sure you want to restore this review #{{ $review->id }}? The review will be moved back to the active reviews list."
                                                                data-confirm-label="Confirm Restore" data-event-name="review.restored" data-event-data="{{ $review->id }}">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                                                data-title="Permanently Delete Review" data-type="warning" data-message="Are you sure you want to permanently delete this review #{{ $review->id }}? This action cannot be undone."
                                                                data-confirm-label="Confirm Delete" data-event-name="review.forceDeleted" data-event-data="{{ $review->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-outline-info btn-action bootstrap-focus" title="View"
                                                                data-bs-toggle="modal" data-bs-target="#reviewPreview" wire:click="$set('selectedReviewId', {{ $review->id }})">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                                                data-title="Remove Review" data-type="warning" data-message="Are you sure you want to remove this review #{{ $review->id }}? The review can be restored later."
                                                                data-confirm-label="Confirm Delete" data-event-name="review.deleted" data-event-data="{{ $review->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($paginatedReviews->hasPages())
                                <div class="card-footer bg-white custom-pagination shadow-sm" style="padding: 1.2rem 1.5rem;">
                                    {{ $paginatedReviews->onEachSide(1)->links(data: ['scrollTo' => "collapseReviews{$product->id}"]) }}
                                </div>
                            @endif
                        </x-livewire::expandable-row>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No reviews found</h5>
                                    <p class="text-muted">There are no reviews or matching search results at the moment.</p>
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

    <x-livewire::content-preview title="Review Preview" icon="fas fa-star" id="reviewPreview" class-header="bootstrap-style bootstrap-border-bottom" modal-size="modal-normal">
        @if($selectedReviewId && $selectedReview)
            <div class="user-info">
                @php $user = $selectedReview->user; @endphp
                <div class="user-avatar">
                    <img src="{{ asset('storage/' . ($user->avatar ?? DefaultImage::AVATAR->value)) }}"
                        class="rounded-circle me-2" alt="User Avatar" style="width: 100%; height: 100%">
                </div>
                <div class="user-info-details">
                    <h6>{{ $user->name }}</h6>
                    <p>{{ $user->email }}</p>
                </div>
            </div>

            <div class="review-content">
                <div class="mb-3">
                    <div class="d-inline-block rating-stars text-nowrap">
                        {!! str_repeat('<i class="fas fa-star"></i>', $selectedReview->rating) . str_repeat('<i class="far fa-star"></i>', 5 - $selectedReview->rating) !!}
                    </div>
                    <span class="ms-1" style="font-size: 1.1rem;">{{ $selectedReview->rating }} / 5 Stars</span>
                </div>
                <p style="margin-bottom: 0.6rem;"><strong>Review Content:</strong></p>
                <p class="mb-0">{{ $selectedReview->content ?? 'No review content provided' }}</p>
                <p class="text-muted mt-3 mb-0"><i class="fas fa-calendar me-1"></i>Review Date: {{ $selectedReview->created_at->format('m/d/Y H:i A') }}</p>
            </div>
        @else
            <div class="text-center mt-2">
                <div class="dots">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
                <span class="loading-text-dots">Loading</span>
            </div>
        @endif
    </x-livewire::content-preview>
</div>
@script
<script>
    const reviewPreview = document.querySelector('#reviewPreview');

    reviewPreview.addEventListener('hide.bs.modal', () => $wire.$set('selectedReviewId', null, true));
</script>
@endscript
