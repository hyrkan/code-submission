@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Analytics') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">{{ __('Students') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $student->first_name }} {{ $student->last_name }} – Analytics</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Student Header --}}
<div class="card border-top border-0 border-4 border-primary mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 56px; height: 56px; font-size: 1.5rem; font-weight: 700;">
                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="mb-0">{{ $student->first_name }} {{ $student->last_name }}</h4>
                    <span class="badge bg-dark text-white">{{ $student->student_number ?? 'N/A' }}</span>
                    <span class="text-muted">{{ $student->year->name ?? '' }} – {{ $student->section->name ?? '' }}</span>
                    <span class="text-muted">&middot; {{ $student->user->email ?? '' }}</span>
                </div>
            </div>
            <div>
                @php
                    $trendIcon = match($trend) {
                        'improving' => 'bx-trending-up text-success',
                        'declining' => 'bx-trending-down text-danger',
                        default     => 'bx-minus text-secondary',
                    };
                    $trendLabel = ucfirst($trend);
                    $trendColor = match($trend) {
                        'improving' => 'success',
                        'declining' => 'danger',
                        default     => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $trendColor }} text-white fs-6 py-2 px-3">
                    <i class="bx {{ $trendIcon }}"></i> {{ $trendLabel }}
                </span>
            </div>
        </div>
    </div>
</div>

@if ($totalQuizzes === 0)
    <div class="card radius-10">
        <div class="card-body text-center py-5 text-muted">
            <i class="bx bx-bar-chart-alt-2 font-48 d-block mb-2"></i>
            <p class="mb-0">No quiz submissions found for this student.</p>
        </div>
    </div>
@else

    {{-- Summary Stats Cards --}}
    <div class="row row-cols-2 row-cols-md-5 g-3 mb-4">
        <div class="col">
            <div class="card border-start border-0 border-4 border-primary h-100">
                <div class="card-body text-center py-3">
                    <h3 class="text-primary mb-0">{{ $totalQuizzes }}</h3>
                    <small class="text-muted">Quizzes Taken</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-info h-100">
                <div class="card-body text-center py-3">
                    <h3 class="text-info mb-0">{{ $totalSubmissions }}</h3>
                    <small class="text-muted">Total Submissions</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-{{ $overallAvgScore >= 70 ? 'success' : ($overallAvgScore >= 50 ? 'warning' : 'danger') }} h-100">
                <div class="card-body text-center py-3">
                    <h3 class="text-{{ $overallAvgScore >= 70 ? 'success' : ($overallAvgScore >= 50 ? 'warning' : 'danger') }} mb-0">{{ $overallAvgScore }}%</h3>
                    <small class="text-muted">Avg Score</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-success h-100">
                <div class="card-body text-center py-3">
                    <h3 class="text-success mb-0">{{ $passRate }}%</h3>
                    <small class="text-muted">Pass Rate</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-{{ $trend === 'improving' ? 'success' : ($trend === 'declining' ? 'danger' : 'secondary') }} h-100">
                <div class="card-body text-center py-3">
                    <h3 class="text-{{ $trend === 'improving' ? 'success' : ($trend === 'declining' ? 'danger' : 'secondary') }} mb-0">
                        <i class="bx {{ $trendIcon }}"></i>
                    </h3>
                    <small class="text-muted">{{ $trendLabel }} Trend</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT: Score Trend Chart --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bx bx-line-chart text-primary"></i> Score Trend Over Time</h5>
                    <div style="position: relative; height: 350px;">
                        <canvas id="scoreTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Difficulty Breakdown --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bx bx-bar-chart text-info"></i> Difficulty Breakdown</h5>

                    @foreach (['easy', 'medium', 'hard'] as $diff)
                        @php
                            $ds = $difficultyStats[$diff];
                            $passRateD = $ds['total'] > 0 ? round(($ds['passed'] / $ds['total']) * 100) : 0;
                            $diffColor = $diff === 'easy' ? 'success' : ($diff === 'medium' ? 'warning' : 'danger');
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-{{ $diffColor }} text-white">{{ ucfirst($diff) }}</span>
                                <small class="text-muted">{{ $ds['passed'] }}/{{ $ds['total'] }} passed</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $diffColor }}" style="width: {{ $passRateD }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Pass rate: {{ $passRateD }}%</small>
                                <small class="text-muted">Avg: {{ $ds['avg_score'] }} pts</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Quiz Performance Table --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="bx bx-table text-primary"></i> Quiz Performance History</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('Quiz') }}</th>
                            <th>{{ __('Language') }}</th>
                            <th class="text-center">{{ __('Score') }}</th>
                            <th class="text-center">{{ __('Percentage') }}</th>
                            <th class="text-center">{{ __('Passed') }}</th>
                            <th class="text-center">{{ __('Failed') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Trend') }}</th>
                            <th>{{ __('Submitted') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quizPerformance as $i => $qp)
                            @php
                                $statusMap = [
                                    'passed'  => ['bg-success', 'Passed'],
                                    'failed'  => ['bg-danger',  'Failed'],
                                    'pending' => ['bg-warning text-dark', 'Pending'],
                                ];
                                [$sc, $sl] = $statusMap[$qp['status']] ?? ['bg-secondary', ucfirst($qp['status'])];

                                // Calculate trend vs previous quiz
                                $quizTrend = '—';
                                $quizTrendIcon = 'bx-minus text-secondary';
                                if ($i > 0) {
                                    $prevPct = $quizPerformance[$i - 1]['pct_score'];
                                    $diff = $qp['pct_score'] - $prevPct;
                                    if ($diff > 5) {
                                        $quizTrend = '+' . round($diff, 1) . '%';
                                        $quizTrendIcon = 'bx-trending-up text-success';
                                    } elseif ($diff < -5) {
                                        $quizTrend = round($diff, 1) . '%';
                                        $quizTrendIcon = 'bx-trending-down text-danger';
                                    } else {
                                        $quizTrend = '±' . round(abs($diff), 1) . '%';
                                        $quizTrendIcon = 'bx-minus text-secondary';
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.submissions.quiz-students', $qp['quiz_id']) }}" class="text-decoration-none">
                                        <strong>{{ $qp['quiz_name'] }}</strong>
                                    </a>
                                </td>
                                <td><span class="badge bg-dark text-white">{{ $qp['language'] === 'cpp' ? 'C++' : ucfirst($qp['language'] ?? '') }}</span></td>
                                <td class="text-center"><strong>{{ $qp['total_score'] }}</strong> <span class="text-muted">/ {{ $qp['max_score'] }}</span></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; min-width: 60px;">
                                            <div class="progress-bar bg-{{ $qp['pct_score'] >= 70 ? 'success' : ($qp['pct_score'] >= 50 ? 'warning' : 'danger') }}" style="width: {{ $qp['pct_score'] }}%"></div>
                                        </div>
                                        <small class="fw-bold">{{ $qp['pct_score'] }}%</small>
                                    </div>
                                </td>
                                <td class="text-center text-success"><strong>{{ $qp['passed_count'] }}</strong></td>
                                <td class="text-center text-danger"><strong>{{ $qp['failed_count'] }}</strong></td>
                                <td class="text-center"><span class="badge {{ $sc }}">{{ $sl }}</span></td>
                                <td class="text-center">
                                    @if ($i > 0)
                                        <i class="bx {{ $quizTrendIcon }}" style="font-size: 1.2rem;" title="{{ $quizTrend }}"></i>
                                        <small class="d-block">{{ $quizTrend }}</small>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td><small>{{ $qp['submitted_at']?->format('M d, Y') ?? 'N/A' }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@if ($totalQuizzes > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = @json(array_map(fn($qp) => $qp['quiz_name'], $quizPerformance));
    const scores = @json(array_column($quizPerformance, 'pct_score'));
    const dates  = @json(array_map(fn($qp) => $qp['submitted_at']?->format('M d') ?? '', $quizPerformance));

    // Color each point based on pass/fail
    const pointColors = scores.map(s => s >= 70 ? '#198754' : (s >= 50 ? '#ffc107' : '#dc3545'));

    const ctx = document.getElementById('scoreTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.map((l, i) => l + (dates[i] ? '\n' + dates[i] : '')),
            datasets: [{
                label: 'Score %',
                data: scores,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointRadius: 6,
                pointHoverRadius: 9,
                pointBackgroundColor: pointColors,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Score %' },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                },
                x: {
                    title: { display: true, text: 'Quiz' },
                    grid: { display: false },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        font: { size: 11 },
                    },
                },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return 'Score: ' + ctx.parsed.y + '%';
                        }
                    }
                },
                annotation: undefined,
            }
        },
        plugins: [{
            // Draw threshold lines
            afterDraw: function(chart) {
                const ctx = chart.ctx;
                const yAxis = chart.scales.y;
                const xAxis = chart.scales.x;

                // 70% line (pass threshold)
                const y70 = yAxis.getPixelForValue(70);
                ctx.save();
                ctx.strokeStyle = 'rgba(25, 135, 84, 0.5)';
                ctx.lineWidth = 1;
                ctx.setLineDash([6, 4]);
                ctx.beginPath();
                ctx.moveTo(xAxis.left, y70);
                ctx.lineTo(xAxis.right, y70);
                ctx.stroke();
                ctx.fillStyle = 'rgba(25, 135, 84, 0.7)';
                ctx.font = '11px sans-serif';
                ctx.fillText('70% (Good)', xAxis.right - 70, y70 - 5);
                ctx.restore();

                // 50% line (failing threshold)
                const y50 = yAxis.getPixelForValue(50);
                ctx.save();
                ctx.strokeStyle = 'rgba(220, 53, 69, 0.5)';
                ctx.lineWidth = 1;
                ctx.setLineDash([6, 4]);
                ctx.beginPath();
                ctx.moveTo(xAxis.left, y50);
                ctx.lineTo(xAxis.right, y50);
                ctx.stroke();
                ctx.fillStyle = 'rgba(220, 53, 69, 0.7)';
                ctx.fillText('50% (Fail)', xAxis.right - 70, y50 - 5);
                ctx.restore();
            }
        }]
    });
});
</script>
@endif
@endpush