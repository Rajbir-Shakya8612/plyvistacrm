@extends('layouts.salesperson')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-muted">Here's your performance overview for today.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">Today's Leads</div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-person-plus text-primary"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $todayLeads ?? 0 }}</h3>
                    <div class="small mt-2">
                        @if(($leadChange ?? 0) > 0)
                            <span class="text-success">
                                <i class="bi bi-arrow-up"></i> {{ $leadChange }}%
                            </span>
                        @elseif(($leadChange ?? 0) < 0)
                            <span class="text-danger">
                                <i class="bi bi-arrow-down"></i> {{ abs($leadChange) }}%
                            </span>
                        @else
                            <span class="text-muted">No change</span>
                        @endif
                        <span class="text-muted">vs yesterday</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">Today's Sales</div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-cart-check text-success"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">₹{{ number_format($todaySales ?? 0) }}</h3>
                    <div class="small mt-2">
                        @if(($salesChange ?? 0) > 0)
                            <span class="text-success">
                                <i class="bi bi-arrow-up"></i> {{ $salesChange }}%
                            </span>
                        @elseif(($salesChange ?? 0) < 0)
                            <span class="text-danger">
                                <i class="bi bi-arrow-down"></i> {{ abs($salesChange) }}%
                            </span>
                        @else
                            <span class="text-muted">No change</span>
                        @endif
                        <span class="text-muted">vs yesterday</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">Target Progress</div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-bullseye text-warning"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $targetProgress ?? 0 }}%</h3>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $targetProgress ?? 0 }}%"></div>
                    </div>
                    <div class="small mt-2 text-muted">
                        ₹{{ number_format($currentSales ?? 0) }} / ₹{{ number_format($targetAmount ?? 0) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">Conversion Rate</div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-graph-up text-info"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $conversionRate ?? 0 }}%</h3>
                    <div class="small mt-2">
                        @if(($conversionChange ?? 0) > 0)
                            <span class="text-success">
                                <i class="bi bi-arrow-up"></i> {{ $conversionChange }}%
                            </span>
                        @elseif(($conversionChange ?? 0) < 0)
                            <span class="text-danger">
                                <i class="bi bi-arrow-down"></i> {{ abs($conversionChange) }}%
                            </span>
                        @else
                            <span class="text-muted">No change</span>
                        @endif
                        <span class="text-muted">vs last month</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Performance Overview</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Lead Sources</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="leadSourcesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @forelse($activities ?? [] as $activity)
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $activity->description }}</h6>
                                    <div class="text-muted small">
                                        @if($activity->type === 'lead')
                                            <i class="bi bi-person-plus text-primary"></i>
                                        @elseif($activity->type === 'sale')
                                            <i class="bi bi-cart-check text-success"></i>
                                        @elseif($activity->type === 'attendance')
                                            <i class="bi bi-calendar-check text-info"></i>
                                        @endif
                                        {{ $activity->details }}
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar2-x mb-3" style="font-size: 2rem;"></i>
                            <p class="mb-0">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Today's Schedule</h5>
                </div>
                <div class="card-body">
                    @forelse($schedule ?? [] as $event)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-calendar-event text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $event->title }}</h6>
                                <div class="text-muted small">
                                    <i class="bi bi-clock"></i> {{ $event->start_time->format('h:i A') }}
                                    @if($event->location)
                                        <span class="ms-2">
                                            <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar2-x mb-3" style="font-size: 2rem;"></i>
                            <p class="mb-0">No events scheduled for today</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($performanceData->labels ?? []) !!},
            datasets: [
                {
                    label: 'Leads',
                    data: {!! json_encode($performanceData->leads ?? []) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: '#0d6efd20',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Sales',
                    data: {!! json_encode($performanceData->sales ?? []) !!},
                    borderColor: '#198754',
                    backgroundColor: '#19875420',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Lead Sources Chart
    const leadSourcesCtx = document.getElementById('leadSourcesChart').getContext('2d');
    new Chart(leadSourcesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($leadSourcesData->labels ?? []) !!},
            datasets: [{
                data: {!! json_encode($leadSourcesData->values ?? []) !!},
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush 