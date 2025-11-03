@php
    $background = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger'];
    $colSpan = 12 / (count($dataStats) ?: 1);
@endphp
<div class="row mb-2">
    @foreach($dataStats as $index => $statistic)
        <div class="col-md-{{ $colSpan === 12 ? $colSpan : 6 }} col-lg-{{ $colSpan }} mb-3">
            <div class="card {{ $background[$index] }} bootstrap-color text-white overflow-x-auto">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 bootstrap-style">{{ $statistic['title'] ?? '' }}</h6>
                            <h2 class="card-title mb-0 bootstrap-style">{{ $statistic['value'] ?? '' }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="{{ $statistic['icon'] ?? '' }} fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
