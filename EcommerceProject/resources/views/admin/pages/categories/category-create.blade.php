<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire-admin::management-header title="Add New Category" btn-link="{{ route('admin.categories.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <x-livewire-admin::form-panel :isFormNormal="false" id="category-create-form" action="store">
        <x-livewire-admin::form-panel.group title="Category Information" icon="fas fa-folder">
            <x-livewire-admin::form-panel.group.input-group label="Name" icon="fas fa-tag" for="name" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('name') is-invalid @enderror" id="name"
                    wire:model.blur="name" placeholder="Enter category name">
                <x-slot:feedback>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Slug" icon="fas fa-link" for="slug" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('slug') is-invalid @enderror" id="slug"
                    wire:model="slug" placeholder="Enter category slug">
                <x-slot:feedback>
                    @error('slug')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>
        </x-livewire-admin::form-panel.group>

        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary bootstrap-focus me-2" wire:click="resetForm">
                <i class="fas fa-redo me-2"></i>
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-folder-plus me-2"></i>
                Create Category
            </button>
        </x-slot:actions>
    </x-livewire-admin::form-panel>
</div>
