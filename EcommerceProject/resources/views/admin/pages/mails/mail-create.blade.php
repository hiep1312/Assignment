@use('App\Helpers\MailTemplateHelper')
@use('App\Services\MarkdownService')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Add New Mail" btn-link="{{ route('admin.mails.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <x-livewire::form-panel :isFormNormal="false" id="mail-create-form" action="store">
        <x-livewire::form-panel.group title="Mail Information" icon="fas fa-envelope">
            <x-livewire::form-panel.group.input-group label="Subject" icon="fas fa-heading" for="subject" column="col-md-6">
                <input type="text" class="form-control custom-radius-end @error('subject') is-invalid @enderror" id="subject"
                    wire:model="subject" placeholder="Enter subject">
                <x-slot:feedback>
                    @error('subject')
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
                    <option value="1">Order Success</option>
                    <option value="2">Order Failed</option>
                    <option value="3">Shipping Update</option>
                    <option value="4">Forgot Password</option>
                    <option value="5">Register Success</option>
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

        <hr class="mt-4 mb-3">

        <x-livewire::form-panel.group title="Email Body" icon="fas fa-file-alt" :hasTitleAction="true">
            <x-slot:button-action type="button" class="btn btn-success bootstrap" icon="fas fa-envelope-open-text"
                data-bs-toggle="modal" data-bs-target="#mailPreviewModal">Preview</x-slot:button-action>

            <x-livewire::form-panel.group.input-group icon="fas fa-align-left" for="body" column="col-12" required>
                <textarea class="form-control custom-radius-end @error('body') is-invalid @enderror" id="body"
                    wire:model.live.debounce.500ms="body" placeholder="Enter email body" rows="5"></textarea>
                <x-slot:feedback>
                    @error('body')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>
        </x-livewire::form-panel.group>

        <div class="alert alert-info border-2 border-info text-dark mt-4" role="alert"
            style="font-family: var(--bs-font-sans-serif-origin);">
            <div class="d-flex align-items-start">
                <i class="fas fa-lightbulb me-2 mt-1" style="font-size: 1.2rem;"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2" style="font-size: 1.05rem;">
                        <strong>Email Variables</strong>
                    </h5>
                    <p class="mb-2">Use the following variables in your email content to automatically insert the corresponding information:</p>

                    <ul class="ps-3 m-0" style="color: #566a7f;">
                        @foreach (MailTemplateHelper::getMailPlaceholdersWithDescription($type) as ['placeholder' => $placeholder, 'description' => $description])
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
                <i class="fas fa-paper-plane me-2"></i>
                Create Mail
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>

    <x-livewire::content-preview title="Mail Preview" icon="fas fa-envelope" id="mailPreviewModal" wire:ignore.self>
        @if($body)
            {!! MarkdownService::instance()->text($body) !!}
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-exclamation-triangle me-1"></i>
                No email body content available.
            </div>
        @endif
    </x-livewire::content-preview>
</div>
