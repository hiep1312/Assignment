@php $id = $attributes->get('id', 'bannerPreview'); @endphp
<div class="modal fade" id="{{ $id }}" tabindex="-1" wire:ignore.self wire:key="{{ $id }}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header boostrap-padding">
                <h5 class="modal-title">
                    <i class="{{ $icon }} me-2"></i>{{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body boostrap-padding-top">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{ $script ?? '' }}
@endpush
