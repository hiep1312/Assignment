@use('App\Helpers\NotificationTemplateHelper')
@assets
    @vite('resources/js/editor-handler.js')
@endassets
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Add New Notification" btn-link="{{ route('admin.notifications.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <livewire:admin.components.gallery-manager wire:key="gallery-picker" id="galleryPickerModal" />

    <x-livewire::form-panel :isFormNormal="false" id="notification-create-form" action="store">
        <x-livewire::form-panel.group title="Notification Information" icon="fas fa-bell">
            <x-livewire::form-panel.group.input-group label="Title" icon="fas fa-heading" for="title" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('title') is-invalid @enderror" id="title"
                    wire:model="title" placeholder="Enter title">
                <x-slot:feedback>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Type" icon="fas fa-tag" for="type" column="col-md-6" required>
                <select class="form-select custom-radius-end @error('type') is-invalid @enderror" id="type"
                    wire:model.change="type">
                    <option value="0">Custom</option>
                    <option value="1">Order Update</option>
                    <option value="2">Payment Update</option>
                    <option value="3">Promotion</option>
                    <option value="4">Account Update</option>
                    <option value="5">System</option>
                </select>
                <x-slot:feedback>
                    @error('type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>
        </x-livewire::form-panel.group>

        <hr class="my-4">

        <div class="mb-4 @error('message') is-invalid @enderror">
            <h5 class="mb-3"><i class="fas fa-comment-alt text-primary me-2"></i>Notification Message</h5>

            <div wire:ignore wire:key="notification-message">
                <textarea class="form-control" id="ckeditor" data-model="message" data-label="Message Editor"
                    data-placeholder="Enter notification message" rows="5"></textarea>
            </div>

            @error('message')
                <div class="invalid-feedback d-block text-center" style="margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="alert alert-info border-2 border-info text-dark mt-4" role="alert"
            style="font-family: var(--bs-font-sans-serif-origin);">
            <div class="d-flex align-items-start">
                <i class="fas fa-lightbulb me-2 mt-1" style="font-size: 1.2rem;"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2" style="font-size: 1.05rem;">
                        <strong>Notification Variables</strong>
                    </h5>
                    <p class="mb-2">Use the following variables in your notification message to automatically insert the corresponding information:</p>

                    <ul class="ps-3 m-0" style="color: #566a7f;">
                        @foreach (NotificationTemplateHelper::getPlaceholdersWithDescription($type) as ['placeholder' => $placeholder, 'description' => $description])
                            <li class="mb-2"><kbd>{{ $placeholder }}</kbd> - {{ $description }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary bootstrap-focus me-2" wire:click="resetForm">
                <i class="fas fa-redo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-bell me-2"></i>
                Create Notification
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>
</div>
