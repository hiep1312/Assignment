@assets
    @vite('resources/css/toast.css')
@endassets

@php
    $toastClass = match($type) {
        'success' => 'toast-success',
        'danger' => 'toast-error',
        'warning' => 'toast-warning',
        'info' => 'toast-info',
        'primary' => 'toast-primary',
        'purple' => 'toast-purple',
        'pink' => 'toast-pink',
        'teal' => 'toast-teal',
        'orange' => 'toast-orange',
        'dark' => 'toast-dark',
        'light' => 'toast-light',
    };

    $animationClass = match($animation) {
        'slideInLeft' => 'animation-left',
        'slideInRight' => 'animation-right',
        'slideInTop' => 'animation-top',
        'bounce' => 'animation-bounce',
        'pulse' => 'animation-pulse',
        'fadeIn' => 'animation-fade',
        default => '',
    };

    $icon = $icon ?: match($type) {
        'success' => 'fas fa-check-circle',
        'danger' => 'fas fa-times-circle',
        'warning' => 'fas fa-triangle-exclamation',
        'info' => 'fas fa-info-circle',
        'primary' => 'fas fa-rocket',
        'purple' => 'fas fa-gem',
        'pink' => 'fas fa-heart',
        'teal' => 'fas fa-leaf',
        'orange' => 'fas fa-fire',
        'dark' => 'fas fa-moon',
        'light' => 'far fa-bell',
    };
@endphp

<div class="toast {{ $show ? 'show' : 'hide' }} {{ $toastClass }} {{ $animationClass }}" id="{{ $toastId }}" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
        <div class="icon-box">
            <i class="{{ $icon }}"></i>
        </div>
        <div class="header-content">
            <strong>{{ $title }}</strong>
            <span class="time"></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        {{ $message }}
    </div>
</div>
@script
<script>
    window.cleanupToast = new Function();

    window.initLiveToast = function() {
        window.cleanupToast();

        const toastEl = document.getElementById($wire.toastId);
        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        const duration = $wire.duration;
        const toastTimeEl = toastEl.querySelector('.time');
        const updateToastTime = function(){
            toastTimeEl.textContent = humanizeTimeDifference(new Date($wire.time));
        }

        updateToastTime();
        const toastTimeInterval = setInterval(updateToastTime, 1000);

        if($wire.show){
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

    $wire.show && initLiveToast();
</script>
@endscript
