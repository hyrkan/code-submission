@extends('main_layout.master')

@push('styles')
<style>
    /* Score ring */
    .score-ring-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: 120px;
        height: 120px;
    }
    .score-ring-wrapper svg {
        transform: rotate(-90deg);
    }
    .score-ring-wrapper .ring-bg {
        fill: none;
        stroke: #e9ecef;
        stroke-width: 8;
    }
    .score-ring-wrapper .ring-fill {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
        transition: stroke-dashoffset 1s ease;
    }
    .score-ring-text {
        position: absolute;
        text-align: center;
    }
    .score-ring-text .score-number {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
    }
    .score-ring-text .score-sub {
        font-size: 0.7rem;
        color: #6c757d;
    }

    /* Results quiz info header */
    .results-quiz-title { font-weight: 700; color: #1a1a2e; }
    .results-quiz-desc { color: #6c757d; font-size: 0.9rem; }
    .results-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
        color: #495057;
    }

    /* Challenge result cards */
    .challenge-result-card {
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .challenge-result-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .challenge-result-header {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
    }
    .challenge-result-header:hover {
        background: #f8f9fa;
    }
    .challenge-result-header .challenge-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .challenge-result-header .challenge-number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        color: #fff;
    }
    .challenge-result-body {
        display: none;
        padding: 0;
        background: #fafbfc;
    }
    .challenge-result-body.open {
        display: block;
    }

    /* Score badge in header */
    .score-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Feedback section */
    .feedback-section {
        padding: 1.25rem;
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
    }
    .feedback-section h6 {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
    }
    .feedback-content {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        font-size: 0.9rem;
        line-height: 1.7;
        color: #333;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .feedback-content strong,
    .feedback-content b {
        color: #1a1a2e;
    }

    /* Code section */
    .code-section {
        padding: 1.25rem;
        background: #fff;
    }
    .code-section h6 {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
    }
    .code-block {
        background: #1e1e1e;
        color: #d4d4d4;
        border-radius: 0.5rem;
        padding: 1rem;
        font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
        font-size: 0.85rem;
        line-height: 1.6;
        overflow-x: auto;
        white-space: pre;
        max-height: 500px;
        overflow-y: auto;
    }

    /* Status colors */
    .status-passed { color: #198754; }
    .status-failed { color: #dc3545; }
    .status-graded { color: #0d6efd; }
    .status-submitted { color: #6c757d; }

    .bg-passed { background-color: #d1fae5 !important; }
    .bg-failed { background-color: #fee2e2 !important; }
    .bg-graded { background-color: #dbeafe !important; }
    .bg-submitted { background-color: #f3f4f6 !important; }

    /* Difficulty badges */
    .difficulty-easy   { color: #198754; font-weight: 600; }
    .difficulty-medium { color: #ffc107; font-weight: 600; }
    .difficulty-hard   { color: #dc3545; font-weight: 600; }

    /* Expand/collapse chevron */
    .chevron-icon {
        transition: transform 0.2s;
        font-size: 1.25rem;
        color: #6c757d;
    }
    .chevron-icon.rotated {
        transform: rotate(180deg);
    }

    /* No feedback placeholder */
    .no-feedback {
        padding: 2rem;
        text-align: center;
        color: #6c757d;
    }
    .no-feedback i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    /* Percentage color classes */
    .pct-excellent { color: #198754; }
    .pct-good { color: #0d6efd; }
    .pct-average { color: #ffc107; }
    .pct-poor { color: #dc3545; }
</style>
@endpush

@section('content')
{{-- Back link --}}
<div class="mb-3">
    <a href="{{ route('student.dashboard') }}" class="text-decoration-none text-muted">
        <i class="bx bx-arrow-back"></i> Back to Dashboard
    </a>
</div>

{{-- ── Results Header ───────────────────────────────────── --}}
@php
    $circumference = 2 * 3.14159 * 42;
    $offset = $circumference - ($percentage / 100) * $circumference;
    $ringColor = $percentage >= 75 ? '#198754' : ($percentage >= 50 ? '#ffc107' : ($percentage >= 25 ? '#fd7e14' : '#dc3545'));
@endphp

<div class="card radius-10 border-start border-0 border-4 {{ $percentage >= 75 ? 'border-success' : ($percentage >= 50 ? 'border-warning' : 'border-danger') }} mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-3 text-center mb-3 mb-md-0">
                <div class="score-ring-wrapper mx-auto">
                    <svg width="120" height="120" viewBox="0 0 100 100">
                        <circle class="ring-bg" cx="50" cy="50" r="42"/>
                        <circle class="ring-fill" cx="50" cy="50" r="42"
                            stroke="{{ $ringColor }}"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $offset }}"/>
                    </svg>
                    <div class="score-ring-text">
                        <div class="score-number" style="color: {{ $ringColor }}">{{ $percentage }}%</div>
                        <div class="score-sub">Overall</div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <h5 class="results-quiz-title mb-0">
                        <i class="bx bx-bar-chart-alt-2 text-primary me-1"></i> {{ $quiz->name }}
                    </h5>
                    <span class="badge {{ $percentage >= 75 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }} text-white fs-6">
                        {{ $totalScore }}/{{ $totalPoints }} pts
                    </span>
                </div>
                @if ($quiz->description)
                    <p class="results-quiz-desc mb-2">{{ $quiz->description }}</p>
                @endif
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="results-meta-badge">
                        <i class="bx bx-code"></i> {{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language) }}
                    </span>
                    <span class="results-meta-badge">
                        <i class="bx bx-check-circle"></i> {{ $submittedCount }}/{{ $totalItems }} Submitted
                    </span>
                    <span class="results-meta-badge">
                        <i class="bx bx-trophy"></i> {{ $submissions->where('status', 'passed')->count() }} Passed
                    </span>
                    @if ($quiz->year)
                        <span class="results-meta-badge">
                            <i class="bx bx-calendar"></i> {{ $quiz->year->name }}
                        </span>
                    @endif
                    @if ($quiz->section)
                        <span class="results-meta-badge">
                            <i class="bx bx-layer"></i> {{ $quiz->section->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Score Summary Card ───────────────────────────────── --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0"><i class="bx bx-bar-chart-alt-2 me-1 text-primary"></i> Score Summary</h6>
            <span class="badge {{ $percentage >= 75 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }} text-white fs-6">
                {{ $totalScore }} / {{ $totalPoints }} pts
            </span>
        </div>
        <div class="progress" style="height: 12px;">
            <div class="progress-bar {{ $percentage >= 75 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}"
                 role="progressbar"
                 style="width: {{ $percentage }}%;"
                 aria-valuenow="{{ $percentage }}"
                 aria-valuemin="0"
                 aria-valuemax="100">
            </div>
        </div>
        <div class="d-flex justify-content-between mt-2">
            <small class="text-muted">0%</small>
            <small class="text-muted fw-semibold">{{ $percentage }}%</small>
            <small class="text-muted">100%</small>
        </div>
    </div>
</div>

{{-- ── Challenge Results ────────────────────────────────── --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <h6 class="mb-0"><i class="bx bx-code-block me-1 text-primary"></i> Challenge Results</h6>
            <span class="badge bg-primary text-white ms-2">{{ $quiz->items->count() }} challenges</span>
        </div>

        @foreach ($quiz->items as $item)
            @php
                $submission = $submissions->get($item->id);
                $itemScore = $submission->score ?? null;
                $itemStatus = $submission->status ?? 'not_submitted';
                $statusColor = match($itemStatus) {
                    'passed' => 'success',
                    'failed' => 'danger',
                    'graded' => 'primary',
                    default => 'secondary',
                };
                $bgClass = 'bg-' . $itemStatus;
                $diffColor = $item->difficulty === 'easy' ? 'success' : ($item->difficulty === 'medium' ? 'warning' : 'danger');
            @endphp

            <div class="challenge-result-card" id="challenge-card-{{ $loop->index }}">
                <div class="challenge-result-header" onclick="toggleChallenge({{ $loop->index }})">
                    <div class="challenge-info">
                        <div class="challenge-number bg-{{ $diffColor }}">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $item->title }}</h6>
                            <small class="text-muted">
                                <span class="difficulty-{{ $item->difficulty }}">{{ ucfirst($item->difficulty) }}</span>
                                &middot; {{ $item->points }} pts
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if ($submission)
                            <span class="score-badge bg-{{ $statusColor }} text-white">
                                @if ($itemScore !== null)
                                    {{ $itemScore }}/{{ $item->points }} pts
                                @else
                                    {{ ucfirst($itemStatus) }}
                                @endif
                            </span>
                            <span class="badge bg-{{ $statusColor }} text-white">
                                @if ($itemStatus === 'passed')
                                    <i class="bx bx-check"></i> Passed
                                @elseif ($itemStatus === 'failed')
                                    <i class="bx bx-x"></i> Failed
                                @elseif ($itemStatus === 'graded')
                                    <i class="bx bx-check-shield"></i> Graded
                                @else
                                    <i class="bx bx-time-five"></i> Submitted
                                @endif
                            </span>
                        @else
                            <span class="badge bg-secondary text-white">Not Submitted</span>
                        @endif
                        <i class="bx bx-chevron-down chevron-icon" id="chevron-{{ $loop->index }}"></i>
                    </div>
                </div>

                <div class="challenge-result-body" id="challenge-body-{{ $loop->index }}">
                    @if ($submission)
                        {{-- Feedback / Code Review --}}
                        <div class="feedback-section">
                            <h6><i class="bx bx-message-detail text-primary"></i> AI Code Review & Assessment</h6>
                            @if ($submission->feedback)
                                <div class="feedback-content">{!! nl2br(e($submission->feedback)) !!}</div>
                            @else
                                <div class="no-feedback">
                                    <i class="bx bx-loader-alt bx-spin text-muted"></i>
                                    <p class="mb-0">Your code is being analyzed by AI. Check back soon for your detailed review.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Submitted Code --}}
                        <div class="code-section">
                            <h6>
                                <i class="bx bx-code text-primary"></i> Submitted Code
                                <span class="badge bg-dark text-white ms-2" style="font-size: 0.7rem;">
                                    {{ $submission->language === 'cpp' ? 'C++' : ucfirst($submission->language) }}
                                </span>
                                @if ($submission->submitted_at)
                                    <span class="text-muted ms-2" style="font-size: 0.75rem; font-weight: 400;">
                                        Submitted {{ $submission->submitted_at->format('M d, Y h:i A') }}
                                    </span>
                                @endif
                            </h6>
                            <div class="code-block">{{ $submission->code }}</div>
                        </div>
                    @else
                        <div class="no-feedback">
                            <i class="bx bx-info-circle text-muted"></i>
                            <p class="mb-0">No submission found for this challenge.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── Back to Dashboard ───────────────────────────────── --}}
<div class="text-center mb-4">
    <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg px-5">
        <i class="bx bx-home"></i> Back to Dashboard
    </a>
</div>
@endsection

@push('scripts')
<script>
function toggleChallenge(index) {
    var body = document.getElementById('challenge-body-' + index);
    var chevron = document.getElementById('chevron-' + index);
    if (body) {
        body.classList.toggle('open');
    }
    if (chevron) {
        chevron.classList.toggle('rotated');
    }
}

// Auto-open first challenge on page load
document.addEventListener('DOMContentLoaded', function () {
    toggleChallenge(0);
});
</script>
@endpush