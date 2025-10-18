@use('App\Enums\UserRole')
@use('Illuminate\Http\UploadedFile')
@use('App\Livewire\Admin\Components\FormPanel\ImageUploader')
@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Edit User" btn-link="{{ route('admin.users.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <x-livewire::form-panel :isFormNormal="false" id="user-edit-form" action="update">
        <x-livewire::form-panel.image-uploader :isMultiple="false" :type="ImageUploader::TYPE_AVATAR" label="Profile Avatar" labelIcon="fa-solid fa-image-user">
            @php $previewImage = match(true){
                $avatar instanceof UploadedFile && str_starts_with($avatar?->getMimeType() ?? '', 'image') => $avatar->temporaryUrl(),
                is_string($avatar) => asset("storage/{$avatar}"),
                default => DefaultImage::getDefaultPath(ImageUploader::TYPE_AVATAR)
            } @endphp
            <x-slot:image :src="$previewImage" alt="Avatar Preview"></x-slot:image>

            <x-slot:input wire:model.live="avatar"></x-slot:input>

            <x-slot:feedback>
                @error('avatar')
                    <div class="invalid-feedback mt-3 d-block text-center">
                        {{ $message }}
                    </div>
                @enderror
            </x-slot:feedback>
        </x-livewire::form-panel.image-uploader>

        <hr class="my-4">

        <x-livewire::form-panel.group x-data="{ showPassword: false }" title="Account Information" icon="fas fa-user-circle">
            <x-livewire::form-panel.group.input-group label="Username" icon="fas fa-at" for="username" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('username') is-invalid @enderror" id="username"
                    wire:model="username" placeholder="Enter username">
                <x-slot:feedback>
                    @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Email Address" icon="fas fa-envelope" for="email" column="col-md-6" required>
                <input type="email" class="form-control custom-radius-end @error('email') is-invalid @enderror" id="email"
                    value="{{ $email }}" placeholder="Enter email" readonly>
                <x-slot:feedback>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="First Name" icon="fas fa-user" for="first_name" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('first_name') is-invalid @enderror" id="first_name"
                    wire:model="first_name" placeholder="Enter first name">
                <x-slot:feedback>
                    @error('first_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Last Name" icon="fas fa-user" for="last_name" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('last_name') is-invalid @enderror" id="last_name"
                    wire:model="last_name" placeholder="Enter last name">
                <x-slot:feedback>
                    @error('last_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Date of Birth" icon="fas fa-calendar-alt" for="birthday" column="col-md-6">
                <input type="date" class="form-control custom-radius-end @error('birthday') is-invalid @enderror" id="birthday"
                    wire:model="birthday">
                <x-slot:feedback>
                    @error('birthday')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="User Role" icon="fas fa-user-tag" for="role" column="col-md-6" required>
                <select class="form-select custom-radius-end @error('role') is-invalid @enderror" id="role"
                    wire:model="role">
                    <option value="user">User</option>
                    <option value="admin">Administrator</option>
                </select>
                <x-slot:feedback>
                    @error('role')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>
        </x-livewire::form-panel.group>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary bootstrap-focus me-2" wire:click="resetForm">
                <i class="fas fa-redo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Update User
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>
</div>
