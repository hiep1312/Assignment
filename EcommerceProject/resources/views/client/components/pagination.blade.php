@assets('styles')
    @vite('resources/css/pagination.css')
@endassets
@if($isPlaceholder)
    <div {{ $attributes->merge(['class' => 'placeholder-glow']) }}>
        <ul class="pagination justify-content-center">
            <span class="d-inline-block placeholder" style="width: 31px; height: 38px; border-radius: 5px;"></span>
            <span class="d-inline-block placeholder" style="width: 35px; height: 38px; border-radius: 5px;"></span>
            <span class="d-inline-block placeholder" style="width: 35px; height: 38px; border-radius: 5px;"></span>
            <span class="d-inline-block placeholder" style="width: 35px; height: 38px; border-radius: 5px;"></span>
            <span class="d-inline-block placeholder" style="width: 31px; height: 38px; border-radius: 5px;"></span>
        </ul>
    </div>
@else
    <nav {{ $attributes->merge(['aria-label' => 'Page navigation']) }}>
        <ul class="pagination justify-content-center">
            @php
                $links = $pagination['links'];
                $lastIndex = count($links) - 1;
            @endphp
            @foreach($links as $index => $link)
                @if($index === 0)
                    <li @class([
                        "page-item",
                        "disabled" => is_null($link['page'])
                    ])>
                        <a class="page-link" href="javascript:void(0);" onclick="handlePageChange(@js($link['page']))" tabindex="-1">‹</a>
                    </li>
                @elseif($index === $lastIndex)
                    <li @class([
                        "page-item",
                        "disabled" => is_null($link['page'])
                    ])>
                        <a class="page-link" href="javascript:void(0);" onclick="handlePageChange(@js($link['page']))" tabindex="-1">›</a>
                    </li>
                @else
                    <li @class([
                        "page-item",
                        "disabled" => !isset($link['page']),
                        'active' => $link['active']
                    ])>
                        <a class="page-link" href="javascript:void(0);" onclick="handlePageChange(@js($link['page']))">{{ $link['label'] }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
@endif
@script
<script>
    window.handlePageChange = function(page){
        if(page === null) return;

        const params = new URLSearchParams(window.location.search);
        params.set('page', page);

        const newUrl = window.location.pathname + '?' + params.toString();
        history.pushState({ page }, '', newUrl);

        document.dispatchEvent(new CustomEvent('pagination:changed', { detail: { page } }));
    }
</script>
@endscript
