@php
    $background = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger'];
    $colSpan = 12 / (count($data) ?: 1);
@endphp
<div class="row mb-2">
    @foreach($data as $index => $statistic)
        <div class="col-md-{{ $colSpan }} mb-3">
            <div class="card {{ $background[$index] }} bootstrap text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 bootstrap">{{ $statistic['title'] ?? '' }}</h6>
                            <h2 class="card-title mb-0 bootstrap">{{ $statistic['value'] ?? '' }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="{{ $statistic['icon'] ?? '' }} fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    {{-- <div class="col-md-3 mb-3">
        <div class="card stat-card bg-success bootstrap text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 bootstrap">Active Users</h6>
                        <h2 class="card-title mb-0 bootstrap">987</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-warning bootstrap text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 bootstrap">Pending</h6>
                        <h2 class="card-title mb-0 bootstrap">45</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-danger bootstrap text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 bootstrap">Inactive</h6>
                        <h2 class="card-title mb-0 bootstrap">202</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
