@unless($isDetailFilter)
    <div class="card mb-4">
        <div class="card-body">
@endunless
            <div class="row g-3 {{ $attributes->get('class') }}">
                @include('admin.components.filters.filter-search', [
                    'columnSearch' => $attributes->get('columnSearch', 'col-md-4'),
                    'placeholderSearch' => $attributes->get('placeholderSearch', 'Search data...'),
                    'modelSearch' => $attributes->get('modelSearch', 'search'),
                ])

                {{ $slot }}

                @include('admin.components.filters.filter-reset', [
                    'columnReset' => $attributes->get('columnReset', 'col-md-2'),
                    'resetAction' => $attributes->get('resetAction', 'resetFilters'),
                ])
            </div>
@unless($isDetailFilter)
        </div>
    </div>
@endunless

