<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="margin-bottom: 5px">{{ $title }}</h1>
    @if($btnLink && $btnLabel)
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ $btnLink }}" class="{{ $btnClass }}">
                <i class="{{ $btnIcon }} me-2"></i>
                {{ $btnLabel }}
            </a>
        </div>
    @endif
</div>
