@push('scripts')
    <script>
        const toast = bootstrap.Toast.getOrCreateInstance(document.getElementById(@json($attributes->get('id', 'toast-container'))));
        const duration = @json($duration);

        if(@json($show) && duration !== null){
            const timer = setInterval(() => {
                const progressEl = document.querySelector('.toast .progress-bar');
            }, 1000);

            const timeout = setTimeout(() => {
                toast.hide();

                clearInterval(timer);
                clearTimeout(timeout);
            }, parseInt(duration) * 1000);
        }
    </script>
@endpush
<div class="bs-toast toast toast-wrapper toast-animation fade {{ $show ? 'show' : 'hide' }} bg-{{ $type }}" id="{{ $attributes->get('id', 'toast-container') }}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
    <div class="toast-header toast-animation">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">{{ $title }}</div>
        <small>{{ $time }}</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body toast-animation">
        {{ $slot }}
    </div>
    <div class="progress-bar-toast toast-animation" style="animation-duration: {{ $duration ?? PHP_INT_MAX }}s"></div>
</div>
