@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Blog List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Blog List" btn-link="{{ route('admin.blogs.create') }}" btn-label="Add New Blog" btn-icon="fas fa-plus-circle" />

    <x-livewire::filter-bar placeholderSearch="Search blogs..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="status">
                <option value="">All Status</option>
                <option value="0">Draft</option>
                <option value="1">Published</option>
                <option value="2">Archived</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="authorId">
                <option value="">All Authors</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Blog Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Restore Blogs` : `Restore All Blogs`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Restore Blogs` : `Restore All Blogs`" data-type="question"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedRecordIds.length} blogs? They will be moved back to the active blogs list.`
                        : `Are you sure you want to restore all blogs? They will be moved back to the active blogs list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="blog.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Restore Blogs` : `Restore All Blogs`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedRecordIds.length ? `Permanently Delete Blogs` : `Permanently Delete All Blogs`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedRecordIds.length ? `Permanently Delete Blogs` : `Permanently Delete All Blogs`" data-type="warning"
                    x-bind:data-message="$wire.selectedRecordIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedRecordIds.length} blogs? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all blogs? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="blog.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedRecordIds.length ? `Permanently Delete Blogs` : `Permanently Delete All Blogs`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Blogs" wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-check-circle me-1"></i>
                    Active Blogs
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Blogs"
                    x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Blogs" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedRecordIds.length} blogs? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="blog.deleted" wire:key="delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    Remove Blogs
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Blogs"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Blogs
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
                        <th>Comments</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                        <tr class="text-center" wire:key="blog-{{ $blog->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $blog->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td style="min-width: 250px;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($blog->image_url ?? DefaultImage::BLOG->value)) }}"
                                        class="me-2" width="55" height="41" alt="Blog image {{ $blog->title }}">
                                    <div class="text-start">
                                        <div class="fw-bold text-wrap lh-base">
                                            {{ Str::limit("$blog->title", 60, '...') }}
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
                            <td>
                                <span>{{ $blog->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $blog->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($isTrashed)
                                        <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                            data-title="Restore Blog" data-type="question" data-message="Are you sure you want to restore this blog #{{ $blog->id }}? The blog will be moved back to the active blogs list."
                                            data-confirm-label="Confirm Restore" data-event-name="blog.restored" data-event-data="{{ $blog->id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                            data-title="Permanently Delete Blog" data-type="warning" data-message="Are you sure you want to permanently delete this blog #{{ $blog->id }}? This action cannot be undone."
                                            data-confirm-label="Confirm Delete" data-event-name="blog.forceDeleted" data-event-data="{{ $blog->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-info btn-action bootstrap-focus" title="View"
                                            data-bs-toggle="modal" data-bs-target="#blogPreview" wire:click="$set('selectedBlogId', {{ $blog->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Remove Blog" data-type="warning" data-message="Are you sure you want to remove this blog #{{ $blog->id }}? The blog can be restored later."
                                            data-confirm-label="Confirm Delete" data-event-name="blog.deleted" data-event-data="{{ $blog->id }}">
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
                                    <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No blogs found</h5>
                                    <p class="text-muted">There are no blogs or matching search results at the moment.</p>
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

    <x-livewire::content-preview title="Blog Preview" icon="fas fa-blog" id="blogPreview" class-header="bootstrap-style bootstrap-border-bottom">
        @if($selectedBlogId && ($selectedBlog = $blogs->firstWhere('id', $selectedBlogId)))
            <h3 class="fw-bold mb-2">{{ $selectedBlog->title }}</h3>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <p class="text-muted m-0">
                    <i class="fas fa-user"></i> <strong>{{ $selectedBlog->author?->name ?? 'Unknown User' }}</strong>
                </p>
                <p class="text-muted m-0">
                    <i class="fas fa-calendar-alt"></i> <strong>{{ $selectedBlog->created_at->format('m/d/Y') }}</strong>
                </p>
            </div>
            <hr class="my-3">
            <div class="ck-content" style="overflow-x: auto;">
                {!! $selectedBlog->content !!}
            </div>
            <div class="tags-section">
                <strong>Tags:</strong>
                @foreach ($selectedBlog->categories as $category)
                    <span class="tag-item"><i class="fas fa-bookmark"></i> {{ $category->name }}</span>
                @endforeach
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
    const blogPreview = document.querySelector('#blogPreview');

    blogPreview.addEventListener('hide.bs.modal', () => $wire.$set('selectedBlogId', null, true));
</script>
@endscript
