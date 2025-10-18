@isset($content)
    {{ $content }}
@else
    <div class="{{ $column }}">
        <label for="{{ $attributes->get('for', Str::slug(strtolower($label))) }}" class="form-label bootstrap-style">
            {{ $label }}
            @if($attributes->has('required')) <span class="text-danger">*</span> @endif
        </label>
        <div class="input-group">
            <span class="input-group-text bootstrap-color">
                <i class="{{ $icon }}"></i>
            </span>

            {{ $slot }}

            @isset($feedback)
                {{ $feedback }}
            @elseif($error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endisset
        </div>
    </div>
@endisset
