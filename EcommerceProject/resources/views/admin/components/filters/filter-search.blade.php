<div class="{{ $columnSearch }}">
    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" class="form-control" placeholder="{{ $placeholderSearch }}" wire:model.live.debounce.300ms="{{ $modelSearch }}">
    </div>
</div>
