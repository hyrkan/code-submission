@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Dashboard') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Section Performance Analytics') }}</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Filters --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-3 mb-0 align-items-end">
            <div class="col-md-3">
                <label for="year_id" class="form-label"><i class="bx bx-calendar"></i> {{ __('Year') }}</label>
                <select class="form-select" id="year_id" name="year_id">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" {{ $yearId == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="section_id" class="form-label"><i class="bx bx-layer"></i> {{ __('Section') }}</label>
                <select class="form-select" id="section_id" name="section_id">
                    <option value="">{{ __('All Sections') }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" data-year-id="{{ $section->year_id }}" {{ $sectionId == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary me-1"><i class="bx bx-filter"></i> {{ __('Apply Filters') }}</button>
                <a href="{{ route('dashboard') }}" class="btn btn-light"><i class="bx bx-reset"></i> {{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Active Quizzes</p>
                        <h4 class="my-1 text-primary">{{ $activeQuizzes }}</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bx-brain'></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">{{ $sectionId ? 'Students in Section' : ($yearId ? 'Students in Year' : 'Total Students') }}</p>
                        <h4 class="my-1 text-success">{{ $totalStudents }}</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bx-group'></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Submissions</p>
                        <h4 class="my-1 text-warning">{{ $totalSubmissions }}</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bx-task'></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (empty($sectionPerformance))
    <div class="card radius-10">
        <div class="card-body text-center py-5 text-muted">
            <i class="bx bx-bar-chart-alt-2 font-48 d-block mb-2"></i>
            <p class="mb-0">No section data available for the selected filters.</p>
        </div>
    </div>
@else

    {{-- Section Comparison Chart --}}
    <div class="card radius-10 mb-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="bx bx-bar-chart text-primary"></i> Section Performance Comparison</h5>
            <div style="position: relative; height: 320px;">
                <canvas id="sectionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Section Performance Cards --}}
    <div class="row g-4 mb-4">
        @foreach ($sectionPerformance as $sp)
            @php
                $section = $sp['section'];
                $scoreColor = $sp['avg_score_pct'] >= 70 ? 'success' : ($sp['avg_score_pct'] >= 50 ? 'warning' : 'danger');
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card border shadow-sm h-100">
                    <div class="card-body">
                        {{-- Section Header --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="mb-0 text-primary">
                                <i class="bx bx-layer"></i> {{ $section->name }}
                            </h5>
                            <span class="badge bg-{{ $scoreColor }} text-white fs-6">{{ $sp['avg_score_pct'] }}%</span>
                        </div>
                        <small class="text-muted d-block mb-3">{{ $section->year->name ?? 'N/A' }} &middot; {{ $sp['student_count'] }} students</small>

                        {{-- Quick Stats --}}
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <h6 class="text-{{ $scoreColor }} mb-0">{{ $sp['avg_score_pct'] }}%</h6>
                                <small class="text-muted">Avg Score</small>
                            </div>
                            <div class="col-4">
                                <h6 class="text-success mb-0">{{ $sp['pass_rate'] }}%</h6>
                                <small class="text-muted">Pass Rate</small>
                            </div>
                            <div class="col-4">
                                <h6 class="text-dark mb-0">{{ $sp['total_quizzes'] }}</h6>
                                <small class="text-muted">Quizzes</small>
                            </div>
                        </div>

                        {{-- Status Bar --}}
                        @if ($sp['total_submissions'] > 0)
                            @php
                                $total = $sp['total_submissions'];
                                $pctPassed = round(($sp['passed_count'] / $total) * 100);
                                $pctFailed = round(($sp['failed_count'] / $total) * 100);
                                $pctGraded = round((($sp['graded_count'] ?? 0) / $total) * 100);
                                $pctPending = round(($sp['pending_count'] / $total) * 100);
                            @endphp
                            <div class="progress mb-2" style="height: 8px;">
                                @if ($pctPassed > 0)<div class="progress-bar bg-success" style="width: {{ $pctPassed }}%"></div>@endif
                                @if ($pctFailed > 0)<div class="progress-bar bg-danger" style="width: {{ $pctFailed }}%"></div>@endif
                                @if ($pctGraded > 0)<div class="progress-bar bg-info" style="width: {{ $pctGraded }}%"></div>@endif
                                @if ($pctPending > 0)<div class="progress-bar bg-warning" style="width: {{ $pctPending }}%"></div>@endif
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    @if ($sp['passed_count'] > 0)<span class="text-success"><i class="bx bx-check-circle"></i> {{ $sp['passed_count'] }}</span>@endif
                                    @if ($sp['failed_count'] > 0)<span class="text-danger ms-2"><i class="bx bx-x-circle"></i> {{ $sp['failed_count'] }}</span>@endif
                                    @if (($sp['graded_count'] ?? 0) > 0)<span class="text-info ms-2"><i class="bx bx-check"></i> {{ $sp['graded_count'] }}</span>@endif
                                    @if ($sp['pending_count'] > 0)<span class="text-warning ms-2"><i class="bx bx-time-five"></i> {{ $sp['pending_count'] }}</span>@endif
                                </small>
                                <small class="text-muted">{{ $sp['total_submissions'] }} total</small>
                            </div>
                        @else
                            <div class="text-center py-2 text-muted">
                                <small><i class="bx bx-info-circle"></i> No submissions yet</small>
                            </div>
                        @endif

                        {{-- Quiz Breakdown (expandable) --}}
                        @if (!empty($sp['quiz_breakdown']))
                            <div class="mt-3">
                                <a class="btn btn-sm btn-outline-secondary w-100" data-bs-toggle="collapse" href="#quizBreakdown{{ $loop->index }}" role="button" aria-expanded="false">
                                    <i class="bx bx-chevron-down"></i> Quiz Breakdown ({{ count($sp['quiz_breakdown']) }})
                                </a>
                                <div class="collapse mt-2" id="quizBreakdown{{ $loop->index }}">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Quiz</th>
                                                    <th class="text-center">Avg %</th>
                                                    <th class="text-center">Pass %</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sp['quiz_breakdown'] as $qb)
                                                    <tr>
                                                        <td>
                                                            <small>{{ Str::limit($qb['quiz_name'], 25) }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            <small class="fw-bold text-{{ $qb['pct_score'] >= 70 ? 'success' : ($qb['pct_score'] >= 50 ? 'warning' : 'danger') }}">{{ $qb['pct_score'] }}%</small>
                                                        </td>
                                                        <td class="text-center">
                                                            <small>{{ $qb['pass_rate'] }}%</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Section Rankings Table --}}
    <div class="card radius-10 mb-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="bx bx-trophy text-warning"></i> Section Rankings</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rank</th>
                            <th>Section</th>
                            <th>Year</th>
                            <th class="text-center">Students</th>
                            <th class="text-center">Avg Score</th>
                            <th class="text-center">Pass Rate</th>
                            <th class="text-center">Passed</th>
                            <th class="text-center">Failed</th>
                            <th class="text-center">Quizzes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sectionPerformance as $i => $sp)
                            @php
                                $section = $sp['section'];
                                $scoreColor = $sp['avg_score_pct'] >= 70 ? 'success' : ($sp['avg_score_pct'] >= 50 ? 'warning' : 'danger');
                            @endphp
                            <tr>
                                <td>
                                    @if ($i === 0 && $sp['avg_score_pct'] > 0)
                                        <span class="badge bg-warning text-dark"><i class="bx bx-medal"></i> #{{ $i + 1 }}</span>
                                    @elseif ($i === 1 && $sp['avg_score_pct'] > 0)
                                        <span class="badge bg-secondary text-white"><i class="bx bx-medal"></i> #{{ $i + 1 }}</span>
                                    @elseif ($i === 2 && $sp['avg_score_pct'] > 0)
                                        <span class="badge bg-dark text-white"><i class="bx bx-medal"></i> #{{ $i + 1 }}</span>
                                    @else
                                        <span class="text-muted">#{{ $i + 1 }}</span>
                                    @endif
                                </td>
                                <td><strong>{{ $section->name }}</strong></td>
                                <td><small class="text-muted">{{ $section->year->name ?? 'N/A' }}</small></td>
                                <td class="text-center">{{ $sp['student_count'] }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <div class="progress" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-{{ $scoreColor }}" style="width: {{ $sp['avg_score_pct'] }}%"></div>
                                        </div>
                                        <small class="fw-bold text-{{ $scoreColor }}">{{ $sp['avg_score_pct'] }}%</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-{{ $sp['pass_rate'] >= 70 ? 'success' : ($sp['pass_rate'] >= 50 ? 'warning' : 'danger') }}">{{ $sp['pass_rate'] }}%</span>
                                </td>
                                <td class="text-center text-success"><strong>{{ $sp['passed_count'] }}</strong></td>
                                <td class="text-center text-danger"><strong>{{ $sp['failed_count'] }}</strong></td>
                                <td class="text-center"><span class="badge bg-secondary text-white">{{ $sp['total_quizzes'] }}</span></td>
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
@if (!empty($sectionPerformance))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sectionData = @json($sectionPerformance);
    const labels = sectionData.map(s => s.section.name);
    const avgScores = sectionData.map(s => s.avg_score_pct);
    const passRates = sectionData.map(s => s.pass_rate);

    const barColors = avgScores.map(s => s >= 70 ? 'rgba(25, 135, 84, 0.8)' : (s >= 50 ? 'rgba(255, 193, 7, 0.8)' : 'rgba(220, 53, 69, 0.8)'));

    const ctx = document.getElementById('sectionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Avg Score %',
                    data: avgScores,
                    backgroundColor: barColors,
                    borderColor: barColors.map(c => c.replace('0.8', '1')),
                    borderWidth: 1,
                    borderRadius: 6,
                },
                {
                    label: 'Pass Rate %',
                    data: passRates,
                    backgroundColor: 'rgba(79, 70, 229, 0.5)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Percentage' },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                },
                x: {
                    title: { display: true, text: 'Section' },
                    grid: { display: false },
                },
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': ' + ctx.parsed.y + '%';
                        }
                    }
                }
            }
        },
        plugins: [{
            afterDraw: function(chart) {
                const ctx = chart.ctx;
                const yAxis = chart.scales.y;
                const xAxis = chart.scales.x;

                // 70% threshold line
                const y70 = yAxis.getPixelForValue(70);
                ctx.save();
                ctx.strokeStyle = 'rgba(25, 135, 84, 0.4)';
                ctx.lineWidth = 1;
                ctx.setLineDash([6, 4]);
                ctx.beginPath();
                ctx.moveTo(xAxis.left, y70);
                ctx.lineTo(xAxis.right, y70);
                ctx.stroke();
                ctx.restore();

                // 50% threshold line
                const y50 = yAxis.getPixelForValue(50);
                ctx.save();
                ctx.strokeStyle = 'rgba(220, 53, 69, 0.4)';
                ctx.lineWidth = 1;
                ctx.setLineDash([6, 4]);
                ctx.beginPath();
                ctx.moveTo(xAxis.left, y50);
                ctx.lineTo(xAxis.right, y50);
                ctx.stroke();
                ctx.restore();
            }
        }]
    });
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    var yearSelect = document.getElementById('year_id');
    var sectionSelect = document.getElementById('section_id');
    var allSectionOptions = [];

    for (var i = 0; i < sectionSelect.options.length; i++) {
        allSectionOptions.push({
            value: sectionSelect.options[i].value,
            text: sectionSelect.options[i].text,
            yearId: sectionSelect.options[i].getAttribute('data-year-id')
        });
    }

    yearSelect.addEventListener('change', function () {
        var selectedYearId = this.value;
        var currentVal = sectionSelect.value;

        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        for (var j = 0; j < allSectionOptions.length; j++) {
            var opt = allSectionOptions[j];
            if (!selectedYearId || opt.yearId === selectedYearId) {
                var newOpt = document.createElement('option');
                newOpt.value = opt.value;
                newOpt.text = opt.text;
                newOpt.setAttribute('data-year-id', opt.yearId);
                sectionSelect.appendChild(newOpt);
            }
        }

        sectionSelect.value = currentVal;
        if (sectionSelect.value !== currentVal) {
            sectionSelect.value = '';
        }
    });

    if (yearSelect.value) {
        yearSelect.dispatchEvent(new Event('change'));
        var currentSection = '{{ $sectionId }}';
        if (currentSection) {
            sectionSelect.value = currentSection;
        }
    }
});
</script>
@endpush