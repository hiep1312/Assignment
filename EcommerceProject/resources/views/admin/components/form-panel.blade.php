<div class="card shadow-sm">
    <div class="card-body p-4">
        <form id="{{ $id }}" @if($isFormNormal) action="{{ $action }}" enctype="{{ $enctype }}" @else wire:submit.prevent="{{ $action }}" @endif novalidate>
            @if($isFormNormal)
                @csrf
                @method($method)
            @endif
            {{ $slot }}

            <hr class="my-4">

            <div class="form-actions d-flex justify-content-center justify-content-sm-between align-items-center flex-wrap-reverse gap-2">
                <div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Fields marked with <span class="text-danger">*</span> are required
                    </small>
                </div>
                <div>
                    {{ $actions }}
                </div>
            </div>
        </form>
    </div>
</div>
