<div {{ $attributes->merge(['class' => 'form-section']) }}>
    <div class="form-section-title">
        <i class="{{ $icon }}"></i> {{ $title }}
    </div>
    {{ $slot }}
</div>
