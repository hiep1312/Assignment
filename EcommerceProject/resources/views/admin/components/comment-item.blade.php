@if($isPlaceholder)
    <div class="comment reply placeholder-glow">
        <div class="comment-header">
            <div class="comment-avatar placeholder"></div>
            <div class="comment-meta">
                <div class="comment-author">
                    <span class="author-name"><span class="d-inline-block placeholder" style="width: 100px;"></span></span>
                    <span class="author-username"><span class="d-inline-block placeholder" style="width: 70px;"></span></span>
                </div>
                <div class="comment-time">
                    <i class="fas fa-clock"></i>
                    <span class="d-inline-block placeholder" style="width: 75px;"></span>
                </div>
            </div>
        </div>
        @isset($replyTo)
            <div class="reply-to">
                <span class="d-inline-block placeholder" style="width: 13px;"></span>
                <span class="d-inline-block placeholder" style="width: 86px;"></span>
                <span class="d-inline-block placeholder" style="width: 70px;"></span>
            </div>
        @endisset
        <div class="comment-body">
            <span class="d-inline-block placeholder w-100 text-muted" style="height: 55px;"></span>
        </div>
        @isset($actions)
            <div class="comment-actions">
                <button class="comment-action">
                    <span class="d-inline-block placeholder" style="width: 55px; height: 15px;"></span>
                </button>
                <button class="comment-action">
                    <span class="d-inline-block placeholder" style="width: 55px; height: 15px;"></span>
                </button>
            </div>
        @endisset
    </div>
@else
    <div {{ $attributes->merge(['class' => 'comment']) }}>
        <div class="comment-header">
            <div class="comment-avatar">
                <img {{ $img->attributes }}>
            </div>
            <div class="comment-meta">
                <div class="comment-author">
                    {{ $author }}
                </div>
                <div class="comment-time">
                    <i class="fas fa-clock"></i>
                    {{ $time }}
                </div>
            </div>
        </div>
        @isset($replyTo)
            <div class="reply-to">
                {{ $replyTo }}
            </div>
        @endisset
        <div class="comment-body">
            {{ $slot }}
        </div>
        @isset($actions)
            <div class="comment-actions">
                {{ $actions }}
            </div>
        @endisset
    </div>
@endif
