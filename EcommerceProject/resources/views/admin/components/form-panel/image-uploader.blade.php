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
                <label @if($showCameraIcon) for="{{ $idInput }}" @else wire:click="$set('{{ $input->attributes->whereStartsWith('wire:model')->first() }}', null, true)" @endif
                    @class(["upload-btn", "trash-background" => !$showCameraIcon])>

                    <i @class([
                        'fas',
                        'fa-camera' => $showCameraIcon,
                        'fa-trash' => !$showCameraIcon,
                        'fa-2x' => $type === ImageUploader::TYPE_BANNER
                    ])></i>
                </label>
            </div>

            @if($type === ImageUploader::TYPE_BANNER)
                @isset($action)
                    {{ $action }}
                @else
                    <button {{ $btnDelete->attributes->merge(['class' => 'uploader delete-btn']) }}>
                        <i class="fas fa-trash"></i>
                    </button>
                    <button {{ $btnView->attributes->merge(['class' => 'uploader view-btn']) }}>
                        <i class="fas fa-eye"></i>
                    </button>
                @endisset
            @endif

            <input {{ $input->attributes->merge(['type' => 'file', 'id' => $idInput, 'accept' => 'image/*', 'hidden' => true]) }}>

            {{ $feedback ?? '' }}
        </div>
    </div>
@endif

