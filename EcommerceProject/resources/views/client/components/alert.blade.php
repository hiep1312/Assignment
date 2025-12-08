@php
    [$backgroundColor, $textColor] = match($type) {
        'danger' => ['#f8d7da', '#721c24'],
        'success' => ['#d4edda', '#155724'],
        'warning' => ['#fff3cd', '#856404'],
        'info' => ['#d1ecf1', '#0c5460'],
        'secondary' => ['#e2e3e5', '#383d41'],
        'primary' => ['#cfe2ff', '#084298'],
        'dark' => ['#d3d3d4', '#141619'],
        'light' => ['#fefefe', '#636464'],
        'purple' => ['#e9d5ff', '#6f42c1'],
    };

    $icon = $icon ?: match($type) {
        'danger' => 'fas fa-times-circle',
        'success' => 'fas fa-check-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info', 'secondary' => 'fas fa-info-circle',
        'primary' => 'fas fa-bullhorn',
        'dark' => 'fas fa-exclamation-circle',
        'light' => 'far fa-bell',
        'purple' => 'fas fa-gem',
    };
@endphp

<div {{ $attributes->merge([
    'class' => 'alert alert-dismissible fade show border-0 rounded-3',
    'style' => "background-color: {$backgroundColor}; color: {$textColor};",
    'role' => 'alert'
]) }}>
    <div class="d-flex align-items-start">
        <div class="flex-shrink-0 me-2">
            <i class="{{ $icon }}" style="color: {{ $textColor }}; font-size: 18px;"></i>
        </div>

        <div class="flex-grow-1">
            @if($title)
                <strong class="d-block mb-1">{{ $title }}</strong>
            @endif

            <span style="font-size: 14px;">{{ $slot }}</span>
        </div>

        @isset($btnClose)
            <button {{ $btnClose->attributes->merge([
                'type' => 'button',
                'class' => 'btn-close',
                'aria-label' => 'Close',
                'style' => "font-size: 14px; box-shadow: none !important;"
            ]) }}></button>
        @endisset
    </div>
</div>
