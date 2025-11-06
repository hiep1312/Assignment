@use('App\Enums\DefaultImage')
@use('Illuminate\Pagination\LengthAwarePaginator')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Comment List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Comments List" btn-link="{{ route('admin.comments.create') }}" btn-label="Add New Comment" btn-icon="fas fa-comment" />

    <x-livewire::filter-bar placeholderSearch="Search comments..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="commentType">
                <option value="">All Comments</option>
                <option value="parent">Original Comments</option>
                <option value="reply">Replies</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="blogId">
                <option value="" wire:key="blog-filter-default">All Blogs</option>
                @foreach($blogs as $blog)
                    <option value="{{ $blog->id }}" wire:key="blog-filter-{{ $blog->id }}">{{ $blog->title }}</option>
                @endforeach
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Comment Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Restore Comments` : `Restore All Comments`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Restore Comments` : `Restore All Comments`" data-type="question"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedRecordIds.length} comments? They will be moved back to the active comments list.`
                        : `Are you sure you want to restore all comments? They will be moved back to the active comments list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="comment.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Restore Comments` : `Restore All Comments`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Permanently Delete Comments` : `Permanently Delete All Comments`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Permanently Delete Comments` : `Permanently Delete All Comments`" data-type="warning"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedRecordIds.length} comments? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all comments? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="comment.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Permanently Delete Comments` : `Permanently Delete All Comments`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Comments" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-check-circle me-1"></i>
                    Active Comments
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Comments"
                    x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Comments" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedRecordIds.length} comments? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="comment.deleted" wire:key="delete">
                    <i class="fas fa-times-circle me-1"></i>
                    Remove Comments
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Comments"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Comments
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
                        <th>Blog</th>
                        <th>Content</th>
                        <th>Comments</th>
                        <th>Author</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                        <tr class="text-center" wire:key="blog-{{ $blog->id }}">
                            <td>
                                <button class="collapse-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComments{{ $blog->id }}"
                                    aria-expanded="false" aria-controls="collapseComments{{ $blog->id }}" wire:key="collapse-comments-{{ $blog->id }}" wire:ignore.self>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </td>
                            <td style="min-width: 250px;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($blog->image_url ?? DefaultImage::BLOG->value)) }}"
                                        class="me-2" width="55" height="41" alt="Blog image {{ $blog->title }}">
                                    <div class="text-start">
                                        <div class="fw-bold text-wrap lh-base">
                                            {{ Str::limit($blog->title, 40, '...') }}
                                            @if($isTrashed)
                                                <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">ID: #{{ $blog->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="min-width: 270px;">
                                <small class="text-muted d-block text-wrap lh-base">{{ Str::limit(strip_tags($blog->content), 80, '...') }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-label-{{ $blog->comments_count ? 'primary' : 'secondary' }}">
                                    <i class="fas fa-comments"></i>
                                    {{ $blog->comments_count }}
                                </span>
                            </td>
                            <td style="min-width: 250px;">
                                @php $user = $blog->author; @endphp
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($user?->avatar ?? DefaultImage::AVATAR->value)) }}"
                                        class="rounded-circle me-2" width="40" height="40" alt="Author Avatar">
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
                                <span class="badge rounded-pill
                                    @switch($blog->status)
                                        @case(0) bg-secondary @break
                                        @case(1) bg-success bootstrap-color @break
                                        @case(2) bg-warning @break
                                    @endswitch
                                ">
                                    @switch($blog->status)
                                        @case(0) Draft @break
                                        @case(1) Published @break
                                        @case(2) Archived @break
                                    @endswitch
                                </span>
                            </td>
                        </tr>
                        <x-livewire::expandable-row id="collapseComments{{ $blog->id }}" title="Blog Comments" icon="fas fa-comment-dots" wire:key="collapse-comments-{{ $blog->id }}">
                            <div class="card-body p-0 table-responsive shadow-sm" style="border-radius: 0.5rem 0.5rem 0 0;">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Select</th>
                                            <th>User</th>
                                            <th>Comment Content</th>
                                            <th>Type</th>
                                            <th class="text-nowrap">Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-light">
                                        @php
                                            $commentPageName = 'commentsPage';
                                            $currentCommentPage = LengthAwarePaginator::resolveCurrentPage($commentPageName);
                                            $paginatedComments = new LengthAwarePaginator(
                                                items: $blog->comments->forPage($currentCommentPage, 5),
                                                total: $blog->comments->count(),
                                                perPage: 5,
                                                currentPage: $currentCommentPage,
                                                options: [
                                                    'pageName' => $commentPageName
                                                ]
                                            );
                                        @endphp
                                        @foreach($paginatedComments as $comment)
                                            <tr class="text-center" wire:key="comment-{{ $comment->id }}">
                                                <td>
                                                    <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                                        value="{{ $comment->id }}" onclick="updateSelectAllState()">
                                                </td>
                                                <td style="min-width: 230px;">
                                                    @php $user = $comment->user; @endphp
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
                                                            <small class="text-muted">Comment ID: #{{ $comment->id }}</small>
                                                            <small class="text-muted d-block">User ID: #{{ $user->id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="min-width: 250px;">
                                                    <small class="text-muted d-block text-wrap lh-base">{{ Str::limit($comment->content, 100, '...') }}</small>
                                                </td>
                                                <td>
                                                    @if($comment->parent_id)
                                                        <span class="badge bg-label-info">
                                                            <i class="fas fa-reply"></i> Reply
                                                        </span>
                                                        @if($comment->reply_to)
                                                            <small class="text-muted d-block mt-1">to #{{ $comment->reply_to }}</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-label-primary">
                                                            <i class="fas fa-comment"></i> Original
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span>{{ $comment->created_at->format('m/d/Y') }}</span>
                                                    <small class="text-muted d-block">{{ $comment->created_at->format('H:i A') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($isTrashed)
                                                            <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                                                data-title="Restore Comment" data-type="question" data-message="Are you sure you want to restore this comment #{{ $comment->id }}? The comment will be moved back to the active comments list."
                                                                data-confirm-label="Confirm Restore" data-event-name="comment.restored" data-event-data="{{ $comment->id }}">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                                                data-title="Permanently Delete Comment" data-type="warning" data-message="Are you sure you want to permanently delete this comment #{{ $comment->id }}? This action cannot be undone."
                                                                data-confirm-label="Confirm Delete" data-event-name="comment.forceDeleted" data-event-data="{{ $comment->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @else
                                                            @if($comment->user_id === auth()->id())
                                                                <a href="{{ route('admin.comments.edit', $comment->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endif
                                                            <button type="button" class="btn btn-outline-info btn-action bootstrap-focus" title="View"
                                                                data-bs-toggle="modal" data-bs-target="#commentPreview" wire:click="$set('selectedCommentId', {{ $comment->id }})">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                                                data-title="Remove Comment" data-type="warning" data-message="Are you sure you want to remove this comment #{{ $comment->id }}? The comment can be restored later."
                                                                data-confirm-label="Confirm Delete" data-event-name="comment.deleted" data-event-data="{{ $comment->id }}">
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
                            @if($paginatedComments->hasPages())
                                <div class="card-footer bg-white custom-pagination shadow-sm" style="padding: 1.2rem 1.5rem;">
                                    {{ $paginatedComments->onEachSide(1)->links(data: ['scrollTo' => "collapseComments{$blog->id}"]) }}
                                </div>
                            @endif
                        </x-livewire::expandable-row>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No comments found</h5>
                                    <p class="text-muted">There are no comments or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($blogs->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $blogs->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>

    <x-livewire::content-preview title="Comment Preview" icon="fas fa-comment" id="commentPreview" class-header="bootstrap-style bootstrap-border-bottom" modal-size="modal-lg">
        @if($selectedCommentId && $selectedComment)
            @if($selectedComment->parent)
                @php
                    $parentComment = $selectedComment->parent;
                    $parentUser = $parentComment->user;
                @endphp
                <x-livewire::comment-item>
                    <x-slot:img src="{{ asset('storage/' . ($parentUser->avatar ?? DefaultImage::AVATAR->value)) }}"
                        class="rounded-circle" alt="User Avatar" style="width: 100%; height: 100%"></x-slot:img>

                    <x-slot:author>
                        <span class="author-name">{{ Str::limit($parentUser->name, 30, '...') }}</span>
                        <span class="author-username">{{ '@' . Str::limit($parentUser->username, 25, '...') }}</span>
                    </x-slot:author>

                    <x-slot:time>{{ $parentComment->created_at->diffForHumans() }}</x-slot:time>

                    {{ $parentComment->content }}
                </x-livewire::comment-item>
            @endif

            @if($selectedComment->parent && $selectedComment->replyTo && $selectedComment->parent_id !== $selectedComment->reply_to)
                <x-livewire::comment-item :is-placeholder="true" />

                @php
                    $replyComment = $selectedComment->replyTo;
                    $replyUser = $replyComment->user;
                @endphp
                <x-livewire::comment-item class="reply">
                    <x-slot:img src="{{ asset('storage/' . ($replyUser->avatar ?? DefaultImage::AVATAR->value)) }}"
                        class="rounded-circle" alt="User Avatar" style="width: 100%; height: 100%"></x-slot:img>

                    <x-slot:author>
                        <span class="author-name">{{ Str::limit($replyUser->name, 30, '...') }}</span>
                        <span class="author-username">{{ '@' . Str::limit($replyUser->username, 25, '...') }}</span>
                    </x-slot:author>

                    <x-slot:time>{{ $replyComment->created_at->diffForHumans() }}</x-slot:time>

                    {{ $replyComment->content }}
                </x-livewire::comment-item>
            @endif

            @php
                $user = $selectedComment->user;
            @endphp
            <x-livewire::comment-item @class([
                "viewing",
                "reply" => $selectedComment->parent
            ])>
                <x-slot:img src="{{ asset('storage/' . ($user->avatar ?? DefaultImage::AVATAR->value)) }}"
                    class="rounded-circle" alt="User Avatar" style="width: 100%; height: 100%"></x-slot:img>

                <x-slot:author>
                    <span class="author-name">{{ Str::limit($user->name, 30, '...') }}</span>
                    <span class="author-username">{{ '@' . Str::limit($user->username, 25, '...') }}</span>
                </x-slot:author>

                <x-slot:time>{{ $selectedComment->created_at->diffForHumans() }}</x-slot:time>

                @if($selectedComment->replyTo)
                    <x-slot:reply-to>
                        <i class="fas fa-arrow-left"></i> Replying to <span class="reply-to-username text-break">{{ '@' . Str::limit($selectedComment->replyTo->user->username, 30, '...') }}</span>
                    </x-slot:reply-to>
                @endif

                {{ $selectedComment->content }}
            </x-livewire::comment-item>
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
    const commentPreview = document.querySelector('#commentPreview');

    commentPreview.addEventListener('hide.bs.modal', () => $wire.$set('selectedCommentId', null, true));
</script>
@endscript
