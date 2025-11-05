@assets
    @vite('resources/js/editor-handler.js')
@endassets
@use('App\Livewire\Admin\Components\FormPanel\ImageUploader')
@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <x-livewire::management-header title="Edit Blog" btn-link="{{ route('admin.blogs.index') }}" btn-label="Back to List"
        btn-icon="fas fa-arrow-left" btn-class="btn btn-outline-secondary bootstrap-focus" />

    <livewire:admin.components.gallery-manager wire:key="gallery-picker" id="galleryPickerModal" />

    <x-livewire::data-selector title="Select Categories" id="dataSelectorCategory" resetProperty="selectedCategoryIds" wire:key="data-selector-category">
        <x-slot:input type="text" placeholder="Search categories..." wire:model.live.debounce.300ms="searchCategories"></x-slot:input>

        @forelse($categories as $category)
            <div class="checkbox-item" onclick="this.querySelector('input').click()" wire:key="category-{{ $category->id }}">
                <div class="checkbox-wrapper">
                    <input type="checkbox" value="{{ $category->id }}" wire:model.change="selectedCategoryIds">
                    <span class="checkmark"></span>
                </div>
                <label class="checkbox-label">{{ $category->name }}</label>
            </div>
        @empty
            <div class="empty-state-selection">No existing categories found.</div>
        @endforelse

        <x-slot:button-confirm wire:click="selectCategories" :disabled="!count($selectedCategoryIds)">Choose Categories</x-slot:button-confirm>
    </x-livewire::data-selector>

    <x-livewire::form-panel :isFormNormal="false" id="blog-edit-form" action="update">
        <x-livewire::form-panel.image-uploader :isMultiple="false" :type="ImageUploader::TYPE_BLOG" label="Blog Thumbnail" labelIcon="fa-solid fa-image">
            @php $previewImage = is_int($thumbnail_id) ? asset("storage/{$thumbnail->image_url}") : DefaultImage::getDefaultPath(ImageUploader::TYPE_BLOG) @endphp
            <x-slot:image :src="$previewImage" alt="Blog Thumbnail Preview"></x-slot:image>

            <x-slot:upload-button data-bs-toggle="modal" data-bs-target="#galleryPickerModal" data-extra-data="true" wire:model.live="thumbnail_id"></x-slot:upload-button>

            <x-slot:feedback>
                @error('image_id')
                    <div class="invalid-feedback mt-3 d-block text-center">
                        {{ $message }}
                    </div>
                @enderror
            </x-slot:feedback>
        </x-livewire::form-panel.image-uploader>

        <hr class="my-4">

        <x-livewire::form-panel.group title="Blog Information" icon="fas fa-blog">
            <x-livewire::form-panel.group.input-group label="Title" icon="fas fa-heading" for="title" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('title') is-invalid @enderror" id="title"
                    wire:model.blur="title" placeholder="Enter blog title">
                <x-slot:feedback>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Slug" icon="fas fa-link" for="slug" column="col-md-6" required>
                <input type="text" class="form-control custom-radius-end @error('slug') is-invalid @enderror" id="slug"
                    wire:model="slug" placeholder="Enter blog slug">
                <x-slot:feedback>
                    @error('slug')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Status" :icon="$status == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off'" for="status" column="col-md-6" required>
                <select class="form-select custom-radius-end @error('status') is-invalid @enderror" id="status"
                    wire:model.change="status">
                    <option value="0">Draft</option>
                    <option value="1">Published</option>
                    <option value="2">Archived</option>
                </select>
                <x-slot:feedback>
                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>

            <x-livewire::form-panel.group.input-group label="Categories" icon="fas fa-folder" for="categories" column="col-md-6">
                <input type="text" class="form-control @error('category_ids') is-invalid @enderror" id="categories"
                    value="{{ implode(', ', $category_names) }}" placeholder="Choose categories" readonly>
                <button type="button" class="btn btn-outline-secondary custom-radius-end bootstrap-hover bootstrap-focus"
                    style="padding: 0.4375rem 0.6rem" data-bs-toggle="modal" data-bs-target="#dataSelectorCategory"
                    x-on:click="$wire.selectedCategoryIds = $wire.category_ids">Select categories</button>
                <x-slot:feedback>
                    @error('category_ids')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </x-slot:feedback>
            </x-livewire::form-panel.group.input-group>
        </x-livewire::form-panel.group>

        <hr class="my-4">

        <div class="mb-4 @error('content') is-invalid @enderror">
            <h5 class="mb-3"><i class="fas fa-file-alt text-primary me-2"></i>Blog Content</h5>

            <div wire:ignore wire:key="blog-content">
                <textarea class="form-control" id="ckeditor" data-model="content" data-label="Content Editor"
                    data-placeholder="Enter blog content" rows="10">{{ $content }}</textarea>
            </div>

            @error('content')
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
                        <strong>Content Guidelines</strong>
                    </h5>
                    <p class="mb-2">Tips for creating engaging blog content:</p>

                    <ul class="ps-3 m-0" style="color: #566a7f;">
                        <li class="mb-2">Use headings (H2, H3) to structure your content</li>
                        <li class="mb-2">Add images to make your blog visually appealing</li>
                        <li class="mb-2">Keep paragraphs short and easy to read</li>
                        <li class="mb-2">Include relevant keywords for SEO optimization</li>
                        <li class="mb-2">Save as "Draft" to preview before publishing</li>
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
                <i class="fas fa-save me-2"></i>
                Update Blog
            </button>
        </x-slot:actions>
    </x-livewire::form-panel>
</div>
