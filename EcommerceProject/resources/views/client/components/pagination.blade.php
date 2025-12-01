@push('styles')
    @vite('resources/css/pagination.css')
@endpush
<nav {{ $attributes->merge(['aria-label' => 'Page navigation']) }}>
    <ul class="pagination justify-content-center">
        <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1">‹</a>
        </li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
        <li class="page-item"><a class="page-link" href="#">4</a></li>
        <li class="page-item"><a class="page-link" href="#">5</a></li>
        <li class="page-item">
            <a class="page-link" href="#">›</a>
        </li>
    </ul>
</nav>
