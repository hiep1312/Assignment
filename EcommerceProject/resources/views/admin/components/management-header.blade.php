<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="margin-bottom: 5px">{{ $title }}</h1>
    @if($addNewUrl && $addLabel)
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ $addNewUrl }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>
                {{ $addLabel }}
            </a>
        </div>
    @endif
</div>
