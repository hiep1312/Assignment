@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Edit Comment" btn-link="{{ route('admin.comments.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <x-livewire::form-panel :isFormNormal="false" id="comment-edit-form" action="update">
        <x-livewire::form-panel.group title="Comment Information" icon="fas fa-comment-dots">
            <x-livewire::form-panel.group.input-group label="Blog" icon="fas fa-file-alt" for="blog" column="col-md-6" required>
                <input type="text" class="form-control" id="blog"
                    value="{{ $blog_title ?? '' }}" placeholder="Choose blog" readonly>
                <button type="button" class="btn btn-outline-secondary custom-radius-end bootstrap-hover bootstrap-focus"
                    style="padding: 0.4375rem 0.6rem" disabled>Select blog</button>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Parent Comment" icon="fas fa-comment-dots" for="parent_id" column="col-md-6">
                <select class="form-select custom-radius-end @error('parent_id') is-invalid @enderror" id="parent_id"
                    :value="$wire.parent_id" disabled>
                    <option value="" wire:key="parent-comment-default">Root Comment</option>
                    @if($parentCommentSelected)
                        <option wire:key="parent-comment-{{ $parentCommentSelected->id }}"
                            value="{{ $parentCommentSelected->id }}">{{ Str::limit($parentCommentSelected->user->name, 17, '...') }}: {{ Str::limit($parentCommentSelected->content, 45, '...') }}</option>
                    @endif
                </select>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Reply To Comment" icon="fas fa-reply" for="reply_to" column="col-12">
                <select class="form-select custom-radius-end @error('reply_to') is-invalid @enderror" id="reply_to"
                    :value="$wire.reply_to" disabled>
                    <option value="" wire:key="reply-to-default">Reply to Parent Comment</option>
                    @if($replyCommentSelected)
                        <option wire:key="reply-to-{{ $replyCommentSelected->id }}"
                            value="{{ $replyCommentSelected->id }}">{{ Str::limit($replyCommentSelected->user->name, 30, '...') }}: {{ Str::limit($replyCommentSelected->content, 60, '...') }}</option>
                    @endif
                </select>
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
                <i class="fas fa-save me-2"></i>
                Update Comment
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>
</div>
