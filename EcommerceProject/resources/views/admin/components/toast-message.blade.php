<div class="bs-toast toast toast-wrapper toast-animation fade {{ $show ? 'show' : 'hide' }} bg-{{ $type }}" id="{{ $attributes->get('id', 'toast-container') }}"
    role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false" wire:key="{{ "{$attributes->get('id', 'toast-container')}-{$time}" }}"
    data-time="{{ $time }}" data-show="{{ $show }}">
    <div class="toast-header toast-animation">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">{{ $title }}</div>
        <small class="toast-time">now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body toast-animation">
        {{ $slot }}
    </div>
    <div class="progress-bar-toast toast-animation" style="animation-duration: {{ $duration ?? PHP_INT_MAX }}s"></div>
</div>
@script
<script>
    window.cleanupToast = new Function();

    window.initLiveToast = function(){
        const toastEl = document.getElementById(@json($attributes->get('id', 'toast-container')));
        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        const duration = @json($duration);
        const toastTimeEl = toastEl.querySelector('.toast-time');
        const updateToastTime = function(){
            toastTimeEl.textContent = humanizeTimeDifference(new Date(toastEl.dataset.time));
        }

        updateToastTime();
        const toastTimeInterval = setInterval(updateToastTime, 1000);

        if(Boolean(toastEl.dataset.show) && duration !== null){
            const timeout = setTimeout(() => {
                toast.hide();
                cleanupToast();
            }, parseInt(duration) * 1000);

            window.cleanupToast = function(){
                clearTimeout(timeout);
                clearInterval(toastTimeInterval);
            }
        }
    }

    document.addEventListener('livewire:initialized', initLiveToast);
    Livewire.hook('morphed', function() {
        if(document.getElementById(@json($attributes->get('id', 'toast-container')))){
            cleanupToast();
            initLiveToast();
        }
    });
</script>
@endscript
