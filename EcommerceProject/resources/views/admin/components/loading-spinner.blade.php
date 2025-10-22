@push('styles')
    @vite('resources/css/loading.css')
@endpush
<div {{ $attributes->merge(['class' => 'loading-overlay']) }}>
    <div class="loader">
        <div class="box box-1">
            <div class="side-left"></div>
            <div class="side-right"></div>
            <div class="side-top"></div>
        </div>
        <div class="box box-2">
            <div class="side-left"></div>
            <div class="side-right"></div>
            <div class="side-top"></div>
        </div>
        <div class="box box-3">
            <div class="side-left"></div>
            <div class="side-right"></div>
            <div class="side-top"></div>
        </div>
        <div class="box box-4">
            <div class="side-left"></div>
            <div class="side-right"></div>
            <div class="side-top"></div>
        </div>
    </div>
    <div class="loading-text">Loading...</div>
</div>
