@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Add New Comment" btn-link="{{ route('admin.comments.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <x-livewire::data-selector title="Select Blog" id="dataSelectorBlog" resetProperty="selectedBlogId" wire:key="data-selector-blog">
        <x-slot:input type="text" placeholder="Search blogs..." wire:model.live.debounce.300ms="searchBlogs"></x-slot:input>

        @forelse($blogs as $blog)
            <div class="checkbox-item" onclick="this.querySelector('input').click()" wire:key="blog-{{ $blog->id }}">
                <div class="checkbox-wrapper">
                    <input type="radio" value="{{ $blog->id }}" wire:model.change="selectedBlogId">
                    <span class="checkmark"></span>
                </div>
                <label class="checkbox-label">{{ $blog->title }}</label>
            </div>
        @empty
            <div class="empty-state-selection">No existing blogs found.</div>
        @endforelse

        <x-slot:button-confirm wire:click="selectBlog" :disabled="!$selectedBlogId">Choose Blog</x-slot:button-confirm>
    </x-livewire::data-selector>

    <x-livewire::form-panel :isFormNormal="false" id="comment-create-form" action="store">
        <x-livewire::form-panel.group title="Comment Information" icon="fas fa-comment-dots">
            <x-livewire::form-panel.group.input-group label="Blog" icon="fas fa-file-alt" for="blog" column="col-md-6" required>
                <input type="text" class="form-control @error('blog_id') is-invalid @enderror" id="blog"
                    value="{{ $blog_title ?? '' }}" placeholder="Choose blog" readonly>
                <button type="button" class="btn btn-outline-secondary custom-radius-end bootstrap-hover bootstrap-focus"
                    style="padding: 0.4375rem 0.6rem" data-bs-toggle="modal" data-bs-target="#dataSelectorBlog"
                    x-on:click="$wire.selectedBlogId = $wire.blog_id">Select blog</button>
                <x-slot:feedback>
                    @error('blog_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Parent Comment" icon="fas fa-comment-dots" for="parent_id" column="col-md-6">
                <select class="form-select custom-radius-end @error('parent_id') is-invalid @enderror" id="parent_id"
                    wire:model.change="parent_id">
                    <option value="" wire:key="parent-comment-default">Root Comment</option>
                    @foreach($parentComments as $comment)
                        <option value="{{ $comment->id }}" wire:key="parent-comment-{{ $comment->id }}">{{ Str::limit($comment->user->name, 17, '...') }}: {{ Str::limit($comment->content, 45, '...') }}</option>
                    @endforeach
                </select>
                <x-slot:feedback>
                    @error('parent_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Reply To Comment" icon="fas fa-reply" for="reply_to" column="col-12">
                <select class="form-select custom-radius-end @error('reply_to') is-invalid @enderror" id="reply_to"
                    wire:model.change="reply_to">
                    <option value="" wire:key="reply-to-default">Reply to Parent Comment</option>
                    @foreach($replyComments as $comment)
                        <option value="{{ $comment->id }}" wire:key="reply-to-{{ $comment->id }}">{{ Str::limit($comment->user->name, 30, '...') }}: {{ Str::limit($comment->content, 60, '...') }}</option>
                    @endforeach
                </select>
                <x-slot:feedback>
                    @error('reply_to')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            @if($parent_id && $parentCommentSelected)
                <div class="col-12" style="margin-top: 20px;" wire:key="parent-comment-{{ $parentCommentSelected->id }}">
                    @php
                        $parentUser = $parentCommentSelected->user;
                    @endphp
                    <x-livewire::comment-item style="margin-bottom: 0" wire:key="frame-parent-comment-{{ $parentCommentSelected->id }}">
                        <x-slot:img src="{{ asset('storage/' . ($parentUser->avatar ?? DefaultImage::AVATAR->value)) }}"
                            class="rounded-circle" alt="User Avatar" style="width: 100%; height: 100%"></x-slot:img>

                        <x-slot:author>
                            <span class="author-name">{{ Str::limit($parentUser->name, 35, '...') }}</span>
                            <span class="author-username">{{ '@' . Str::limit($parentUser->username, 30, '...') }}</span>
                        </x-slot:author>

                        <x-slot:time>{{ $parentCommentSelected->created_at->diffForHumans() }}</x-slot:time>

                        {{ $parentCommentSelected->content }}
                    </x-livewire::comment-item>
                </div>

                @if($reply_to && $replyCommentSelected)
                    @php
                        $replyUser = $replyCommentSelected->user;
                    @endphp
                    <x-livewire::comment-item class="reply" style="margin-top: 20px; margin-bottom: 0" wire:key="frame-reply-comment-{{ $replyCommentSelected->id }}">
                        <x-slot:img src="{{ asset('storage/' . ($replyUser->avatar ?? DefaultImage::AVATAR->value)) }}"
                            class="rounded-circle" alt="User Avatar" style="width: 100%; height: 100%"></x-slot:img>

                        <x-slot:author>
                            <span class="author-name">{{ Str::limit($replyUser->name, 30, '...') }}</span>
                            <span class="author-username">{{ '@' . Str::limit($replyUser->username, 25, '...') }}</span>
                        </x-slot:author>

                        <x-slot:time>{{ $replyCommentSelected->created_at->diffForHumans() }}</x-slot:time>

                        {{ $replyCommentSelected->content }}
                    </x-livewire::comment-item>
                @endif
            @endif

            <x-livewire::form-panel.group.input-group label="Comment Content" icon="fas fa-edit" for="content" column="col-md-12" required>
                <textarea class="form-control custom-radius-end @error('content') is-invalid @enderror" id="content"
                    wire:model="content" placeholder="Enter comment content" rows="5" maxlength="500"></textarea>
                <x-slot:feedback>
                    @error('content')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>
            <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle"></i> Maximum 500 characters
                <span class="float-end" x-text="`${$wire.content.length}/500`"></span>
            </small>
        </x-livewire::form-panel.group>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary bootstrap-focus me-2" wire:click="resetForm">
                <i class="fas fa-redo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-comment-medical me-2"></i>
                Create Comment
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>
</div>
