<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            @include('admin.components.filters.filter-search', compact('placeholderSearch', 'modelSearch'))

            {{ $slot }}

            @include('admin.components.filters.filter-reset', compact('resetAction'))
        </div>
    </div>
</div>
