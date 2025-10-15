<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $caption }}</h5>
            <div class="d-flex flex-wrap-reverse gap-2 justify-content-end justify-content-md-center">
                {{ $actions }}
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        {{ $slot }}
    </div>
    {{ $pagination }}
</div>
