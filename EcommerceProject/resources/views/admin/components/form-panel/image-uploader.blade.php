@use('App\Livewire\Admin\Components\FormPanel\ImageUploader')
@use('App\Enums\DefaultImage')
@if($isMultiple)
    {{-- $isMultiple, $type, $label, $labelIcon --}}
@else
    @php
        $typeClass = match($type){
            ImageUploader::TYPE_AVATAR => 'avatar-user',
            ImageUploader::TYPE_BANNER => 'banner'
        };
        $idImage = $attributes->get('id-image', "{$type}Preview");
        $idInput = $attributes->get('id-input', "{$type}Input");
    @endphp
    <div id="{{ $attributes->get('id', 'frame-image-uploader') }}">
        <h5 class="mb-3">
            <i class="{{ $labelIcon }} text-primary me-2"></i>
            {{ $label }}
        </h5>
        <div class="image-uploader-wrapper {{ $typeClass }}">
            <img {{ $image->attributes->merge(['class' => 'image-uploader']) }}>

            <div class="image-uploader-overlay">
                @php
                    $src = $image->attributes->get('src', DefaultImage::getDefaultPath($type));
                    $showCameraIcon = empty($src) || Str::contains($src, DefaultImage::values());
                @endphp
                <label @if($showCameraIcon)
                            @if($type === ImageUploader::TYPE_AVATAR) for="{{ $idInput }}"
                            @else {{ $uploadButton->attributes }} @endif
                       @else
                            wire:click="$set('{{ $type === ImageUploader::TYPE_AVATAR
                                ? $input->attributes->whereStartsWith('wire:model')->first()
                                : $uploadButton->attributes->whereStartsWith('wire:model')->first()
                            }}', null)"
                       @endif
                    @class(["upload-btn", "trash-background" => !$showCameraIcon])>

                    <i @class([
                        'fas',
                        'fa-camera' => $showCameraIcon,
                        'fa-trash' => !$showCameraIcon,
                        'fa-2x' => $type === ImageUploader::TYPE_BANNER
                    ])></i>
                </label>
            </div>

            @if($type === ImageUploader::TYPE_AVATAR)
                <input {{ $input->attributes->merge(['type' => 'file', 'id' => $idInput, 'accept' => 'image/*', 'hidden' => true]) }}>
            @endif
        </div>

        {{ $feedback ?? '' }}
    </div>
@endif
