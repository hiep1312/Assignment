@assets
    @vite('resources/css/message-center.css')
    @vite('resources/js/editor-handler.js')
@endassets
@use('App\Helpers\MailTemplateHelper')
@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    @if(session()->has('mail.queued'))
        <x-livewire::toast-message title="Email Queued" type="primary" time="{{ session('mail.queued')[1] }}" :show="true" :duration="8">
            {{ session('mail.queued')[0] }}
        </x-livewire::toast-message>
    @endif

    <livewire:admin.components.gallery-manager wire:key="gallery-picker" id="galleryPickerModal" />

    <x-livewire::management-header title="Mail Center" btn-link="{{ route('admin.mails.index') }}" btn-label="Manage Templates" btn-icon="fas fa-folder-open" />

    <div class="card-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active bootstrap-style" id="send-tab" data-bs-toggle="tab" data-bs-target="#send-content"
                    type="button" wire:ignore.self>
                    <i class="fas fa-paper-plane"></i> Send Mail
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bootstrap-style" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-content"
                    type="button" wire:ignore.self>
                    <i class="fas fa-history"></i> Send History
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="send-content" role="tabpanel"
                wire:ignore.self>
                <x-livewire::form-section title="Choose Sending Method" icon="fas fa-cogs">
                    <div class="radio-group">
                        <div :class="`radio-option ${$wire.sendType === 'template' ? 'active' : ''}`" x-on:click="$wire.$set('sendType', 'template')">
                            <label class="radio-label">
                                <input type="radio" name="send_type" :checked="$wire.sendType === 'template'">
                                <i class="fas fa-certificate"></i>
                                Use Template
                            </label>
                            <p class="mb-0" style="font-size: 0.85rem;">Select from predefined email templates</p>
                        </div>
                        <div :class="`radio-option ${$wire.sendType === 'manual' ? 'active' : ''}`" x-on:click="$wire.$set('sendType', 'manual')">
                            <label class="radio-label">
                                <input type="radio" name="send_type" :checked="$wire.sendType === 'manual'">
                                <i class="fas fa-pencil-alt"></i>
                                Write Manually
                            </label>
                            <p class="mb-0" style="font-size: 0.85rem;">Compose a custom email message</p>
                        </div>
                    </div>
                </x-livewire::form-section>

                <x-livewire::form-section ::class="$wire.sendType !== 'template' ? 'd-none' : ''" title="Choose Template" icon="fas fa-envelope-open-text">
                    <div class="form-group">
                        <label for="selectedTemplate">Template Email <span class="text-danger">*</span></label>
                        <select class="form-select @error('selectedTemplate') is-invalid @enderror" wire:model="selectedTemplate" id="selectedTemplate">
                            <option value="">Select a template</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->subject ?? "No subject #{$template->id}" }}</option>
                            @endforeach
                        </select>
                        @error('selectedTemplate')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="alert alert-info bootstrap-style" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> The placeholders in the email will be automatically replaced with real recipient data.
                    </div>
                </x-livewire::form-section>

                <x-livewire::form-section ::class="$wire.sendType !== 'manual' ? 'd-none' : ''" title="Compose Email" icon="fas fa-edit">
                    <div class="form-group">
                        <label for="subject">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" placeholder="Example: Special promotion for you"
                            wire:model="subject" id="subject">
                        @error('subject')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group @error('body') is-invalid @enderror">
                        <label for="ckeditor">Email Content <span class="text-danger">*</span></label>
                        <div wire:ignore wire:key="mail-body">
                            <textarea class="form-control" id="ckeditor" data-model="body" data-label="Content Editor"
                                data-placeholder="Write your email content here..." rows="5"></textarea>
                        </div>
                        @error('body')
                            <div class="invalid-feedback d-block text-center" style="margin-top: 0.5rem;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="alert alert-info border-2 border-info text-dark mt-4" role="alert" style="font-family: var(--bs-font-sans-serif-origin);">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-lightbulb me-2 mt-1" style="font-size: 1.2rem;"></i>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading mb-2" style="font-size: 1.05rem;">
                                    <strong>Email Variables</strong>
                                </h5>
                                <p class="mb-2">Use the following variables in your email content to automatically insert the corresponding information:</p>

                                <ul class="ps-3 m-0" style="color: #566a7f;">
                                    @foreach (MailTemplateHelper::getPlaceholdersWithDescription(0) as ['placeholder' => $placeholder, 'description' => $description])
                                        <li class="mb-2"><kbd>{{ $placeholder }}</kbd> - {{ $description }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </x-livewire::form-section>

                <x-livewire::form-section title="Select Recipients" icon="fas fa-users">
                    <div class="user-selection-controls" x-data="{ allSelected: false }" x-effect="allSelected = @json($users->pluck('id')->toArray()).every(userId => $wire.selectedUsers.includes(userId))">
                        <input type="text" class="form-control user-search-input" id="user-search" wire:model.live.debounce.300ms="searchUsers"
                            placeholder="🔍 Search by name or email...">
                        <button :class="allSelected ? 'btn-unselect-all' : 'btn-select-all'"
                            x-on:click="$wire.selectedUsers = allSelected ? [] : Array.from(new Set([...$wire.selectedUsers, ...@json($users->pluck('id')->toArray())]))">
                            <span x-html="allSelected
                                ? `<i class='fas fa-square'></i> Deselect All`
                                : `<i class='fas fa-check-square'></i> Select All`
                            "></span>
                        </button>
                    </div>
                    <label>User List
                        <span class="user-counter" x-text="`${$wire.selectedUsers.length} selected`">0 selected</span>
                    </label>
                    <div class="user-selection-frame @if($errors->has('selectedUsers') || $errors->has('selectedUsers.*')) is-invalid @endif">
                        @forelse($users as $user)
                            <div class="user-item" wire:key="user-{{ $user->id }}" onclick="this.querySelector('input[type=checkbox]').click()">
                                <input type="checkbox" wire:model.change="selectedUsers" value="{{ $user->id }}" @click.stop>
                                <img src="{{ asset('storage/' . ($user->avatar ?? DefaultImage::AVATAR->value)) }}"
                                    class="rounded-circle me-2" width="40" height="40" alt="User Avatar">
                                <div class="user-info-messsage-center">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state-selection">No existing users found.</div>
                        @endforelse
                    </div>
                    @if($errors->has('selectedUsers') || $errors->has('selectedUsers.*'))
                        <div class="invalid-feedback d-block text-center" style="margin-top: 0.5rem;">
                            {{ $errors->first('selectedUsers') ?? $errors->first('selectedUsers.*') }}
                        </div>
                    @endif
                </x-livewire::form-section>

                <div class="btn-group-action">
                    <button class="btn-primary-custom" wire:click="submitEmail">
                        <i class="fas fa-paper-plane"></i> Send Email
                    </button>
                    <button class="btn-secondary-custom" wire:click="resetForm">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>

            <div class="tab-pane fade" id="history-content" role="tabpanel" wire:ignore.self>
                <livewire:admin.mails.mail-batches wire:key="mail-batches" />
            </div>
        </div>
    </div>
</div>
