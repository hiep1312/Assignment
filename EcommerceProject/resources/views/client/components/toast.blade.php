@assets
    @vite('resources/css/toast.css')
@endassets

<div x-data="{
    toastHideTimeout: null,
    toastTimeInterval: null,
    duration: $wire.$entangle('duration'),
    time: $wire.$entangle('time'),
    hideToast(toastElement) {
        toastElement.classList.remove('show');
    },

    updateTime(timeElement) {
        timeElement.textContent = humanizeTimeDifference(new Date(this.time || (this.time = Date.now())));
    }
}">
    @if(isset($title) || isset($message) || isset($type))
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
                'fadeIn' => 'animation-shake',
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

        <div class="toast show {{ $toastClass }} {{ $animationClass }}" role="alert" aria-live="assertive" aria-atomic="true"
            wire:key="toast-container-{{ $time ?? time() }}" x-init="clearTimeout(toastHideTimeout); toastHideTimeout = setTimeout(hideToast, (parseInt(duration) || 12) * 1000, $el)">
            <div class="toast-header">
                <div class="icon-box">
                    <i class="{{ $icon }}"></i>
                </div>
                <div class="header-content">
                    <strong>{{ $title }}</strong>
                    <span class="time" x-init="clearInterval(toastTimeInterval); toastTimeInterval = setInterval(() => updateTime($el), 1000); updateTime($el)"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" @click="hideToast($el.parentElement.parentElement)"></button>
            </div>
            <div class="toast-body">
                {{ $message }}
            </div>
        </div>
    @endif
</div>

