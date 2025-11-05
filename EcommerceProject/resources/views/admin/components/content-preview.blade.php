@php $id = $attributes->get('id', 'contentPreview'); @endphp
<div class="modal fade {{ $attributes->get('class') }}" id="{{ $id }}" tabindex="-1" wire:ignore.self wire:key="{{ $id }}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable {{ $attributes->get('modal-size', 'modal-xl') }}">
        <div class="modal-content">
            <div class="modal-header bootstrap-padding {{ $attributes->get('class-header') }}">
                <h5 class="modal-title">
                    <i class="{{ $icon }} me-2"></i>{{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bootstrap-padding-top {{ $attributes->get('class-body') }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{ $script ?? '' }}
@endpush
