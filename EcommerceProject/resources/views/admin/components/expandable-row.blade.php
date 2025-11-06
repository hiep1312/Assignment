<tr class="collapse fade" id="{{ $id }}" style="background-color: #f8f8f8" wire:key="{{ $attributes->get('wire:key', $id) }}" wire:ignore.self>
    <td colspan="6" class="p-0">
        <div class="review-section">
            <h5><i class="{{ $icon }} me-1"></i> {{ $title }}</h5>
            {{ $slot }}
        </div>
    </td>
</tr>
