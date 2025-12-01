<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire-admin::management-header title="Edit Variant" btn-link="{{ route('admin.products.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <x-livewire-admin::data-selector title="Select Product Variant" id="dataSelectorVariant" resetProperty="selectedVariantId" wire:key="data-selector-variant">
        <x-slot:input type="text" placeholder="Search variants..." wire:model.live.debounce.300ms="searchVariants"></x-slot:input>

        @forelse($availableVariants as $variants)
            <div class="checkbox-item" onclick="this.querySelector('input').click()" wire:key="variant-{{ $variants->id }}">
                <div class="checkbox-wrapper">
                    <input type="radio" value="{{ $variants->id }}" wire:model.change="selectedVariantId">
                    <span class="checkmark"></span>
                </div>
                <label class="checkbox-label">{{ $variants->name }}</label>
            </div>
        @empty
            <div class="empty-state-selection">No existing variants found.</div>
        @endforelse

        <x-slot:button-confirm wire:click="selectVariant" :disabled="!$selectedVariantId">Choose Variant</x-slot:button-confirm>
    </x-livewire-admin::data-selector>

    <x-livewire-admin::form-panel :isFormNormal="false" id="product-variant-edit-form" action="update">
        <x-livewire-admin::form-panel.group title="Variant Information" icon="fas fa-box-open">
            <x-livewire-admin::form-panel.group.input-group label="Variant Name" icon="fas fa-tag" for="name" column="col-md-6" required>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    wire:model.blur="name" placeholder="Enter variant name">
                <button type="button" class="btn btn-outline-secondary custom-radius-end bootstrap-hover bootstrap-focus"
                    style="padding: 0.4375rem 0.6rem" data-bs-toggle="modal" data-bs-target="#dataSelectorVariant">Select variant</button>
                <x-slot:feedback>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="SKU" icon="fas fa-barcode" for="sku" column="col-md-6" required>
                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                    wire:model="sku" placeholder="Enter unique SKU code">
                <button type="button" class="btn btn-outline-warning custom-radius-end bootstrap-hover bootstrap-focus"
                    style="padding: 0.4375rem 0.6rem" wire:click="$set('sku', '{{ strtoupper(Str::random(12)) }}')">Generate SKU</button>
                <x-slot:feedback>
                    @error('sku')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Price" icon="fas fa-dollar-sign" for="price" column="col-md-6" required>
                <input type="number" class="form-control custom-radius-end @error('price') is-invalid @enderror" id="price"
                    wire:model="price" placeholder="Enter price" min="0" step="1">
                <x-slot:feedback>
                    @error('price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Discounted Price" icon="fas fa-percent" for="discount" column="col-md-6">
                <input type="number" class="form-control custom-radius-end @error('discount') is-invalid @enderror" id="discount"
                    wire:model="discount" placeholder="Enter discounted price" min="0" step="1">
                <x-slot:feedback>
                    @error('discount')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Stock Quantity" icon="fas fa-boxes" for="stock" column="col-md-6" required>
                <input type="number" class="form-control custom-radius-end @error('stock') is-invalid @enderror" id="stock"
                    wire:model="stock" placeholder="Enter stock quantity" min="0" step="1">
                <x-slot:feedback>
                    @error('stock')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire-admin::form-panel.group.input-group>

            <x-livewire-admin::form-panel.group.input-group label="Status" :icon="$status == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off'" for="status" column="col-md-6" required>
                <select class="form-select custom-radius-end @error('status') is-invalid @enderror" id="status"
                    wire:model.change="status" wire:key="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <x-slot:feedback>
                    @error('status')
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
                <i class="fas fa-save me-2"></i>
                Update Variant
            </button>
        </x-slot:actions>
    </x-livewire-admin::form-panel>
</div>
