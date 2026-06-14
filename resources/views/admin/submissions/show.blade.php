@extends('main_layout.master')

@push('styles')
<style>
    .ai-feedback-card {
        background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
        border-radius: 1rem;
        padding: 1.75rem;
        color: #e0e0ff;
        position: relative;
        overflow: hidden;
    }
    .ai-feedback-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at top right, rgba(99,102,241,0.15), transparent 60%);
        pointer-events: none;
    }
    .ai-feedback-card .ai-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(99,102,241,0.25);
        border: 1px solid rgba(99,102,241,0.4);
        border-radius: 2rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        color: #a5b4fc;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
    }
    .ai-feedback-card h5 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 1.25rem;
    }
    .ai-feedback-body {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        padding: 1.25rem;
        font-size: 0.875rem;
        line-height: 1.75;
        white-space: pre-wrap;
        word-break: break-word;
        color: #d1d5db;
        max-height: 500px;
        overflow-y: auto;
    }
    .ai-feedback-body::-webkit-scrollbar { width: 5px; }
    .ai-feedback-body::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); border-radius: 3px; }
    .ai-score-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border-radius: 2rem;
        padding: 0.35rem 1rem;
        font-weight: 700;
        font-size: 0.9rem;
        margin-top: 1rem;
    }
    .ai-score-chip.failed-chip {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    .ai-score-chip.graded-chip {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .ai-score-chip.submitted-chip {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .code-display {
        background: #1e1e1e;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .code-display pre {
        margin: 0;
        padding: 1.25rem;
        color: #d4d4d4;
        font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
        font-size: 0.85rem;
        overflow-x: auto;
        max-height: 500px;
        overflow-y: auto;
    }
    .code-display pre::-webkit-scrollbar { width: 5px; height: 5px; }
    .code-display pre::-webkit-scrollbar-thumb { background: #555; border-radius: 3px; }
    .info-grid dt {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        font-weight: 600;
    }
    .info-grid dd {
        font-weight: 600;
        margin-bottom: 0;
    }
    .status-badge-lg {
        font-size: 0.875rem;
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
    }
    .no-ai-feedback {
        text-align: center;
        padding: 2rem;
        color: rgba(255,255,255,0.4);
    }
    .no-ai-feedback i { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Submissions') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.submissions.index') }}">{{ __('Quiz Submissions') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Submission #' . $submission->id) }}</li>
            </ol>
        </nav>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2">
        <div class="d-flex align-items-center">
            <div class="font-35 text-white"><i class="bx bxs-check-circle"></i></div>
            <div class="ms-3">
                <h6 class="mb-0 text-white">{{ __('Success') }}</h6>
                <div class="text-white">{{ session('success') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">

    {{-- LEFT COLUMN: Submission details + AI analysis + Code --}}
    <div class="col-lg-8">

        {{-- Student & Quiz Info --}}
        <div class="card border-top border-0 border-4 border-primary mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="mb-0 text-primary"><i class="bx bx-code-block"></i> Submission Details</h5>
                    @php
                        $statusMap = [
                            'passed'    => ['bg-success', 'Passed'],
                            'failed'    => ['bg-danger',  'Failed'],
                            'graded'    => ['bg-info',    'Graded'],
                            'submitted' => ['bg-warning text-dark', 'Pending Review'],
                        ];
                        [$statusClass, $statusLabel] = $statusMap[$submission->status] ?? ['bg-secondary', ucfirst($submission->status)];
                    @endphp
                    <span class="badge {{ $statusClass }} status-badge-lg">{{ $statusLabel }}</span>
                </div>
                <hr>
                <dl class="row info-grid mb-0">
                    <div class="col-md-4 mb-3">
                        <dt>Student</dt>
                        <dd>{{ $submission->student->first_name }} {{ $submission->student->last_name }}</dd>
                        <small class="text-muted">{{ $submission->student->student_number ?? 'N/A' }}</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <dt>Quiz</dt>
                        <dd>{{ $submission->quiz->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="col-md-4 mb-3">
                        <dt>Challenge</dt>
                        <dd>{{ $submission->quizItem->title ?? 'N/A' }}</dd>
                        @if ($submission->quizItem)
                            <small class="text-muted">{{ ucfirst($submission->quizItem->difficulty) }} &middot; {{ $submission->quizItem->points }} pts</small>
                        @endif
                    </div>
                    <div class="col-md-4 mb-0">
                        <dt>Language</dt>
                        <dd><span class="badge bg-dark text-white">{{ $submission->language === 'cpp' ? 'C++' : ucfirst($submission->language) }}</span></dd>
                    </div>
                    <div class="col-md-4 mb-0">
                        <dt>Year / Section</dt>
                        <dd>{{ $submission->student->year->name ?? 'N/A' }} – {{ $submission->student->section->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="col-md-4 mb-0">
                        <dt>Submitted At</dt>
                        <dd>{{ $submission->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- AI Analysis / Assessment --}}
        <div class="ai-feedback-card mb-4">
            <div class="ai-badge">
                <i class="bx bx-brain"></i> AI Code Analysis
            </div>
            <h5><i class="bx bx-analyse"></i> Assessment &amp; Feedback</h5>

            @if ($submission->feedback)
                <div class="ai-feedback-body" id="aiFeedbackBody">{{ $submission->feedback }}</div>

                @if ($submission->score !== null)
                    @php
                        $chipClass = match($submission->status) {
                            'passed'  => 'ai-score-chip',
                            'failed'  => 'ai-score-chip failed-chip',
                            'graded'  => 'ai-score-chip graded-chip',
                            default   => 'ai-score-chip submitted-chip',
                        };
                        $maxPts = $submission->quizItem->points ?? '?';
                    @endphp
                    <div class="d-flex align-items-center gap-2 mt-3 flex-wrap">
                        <span class="{{ $chipClass }}">
                            <i class="bx bx-star"></i>
                            AI Score: {{ $submission->score }} / {{ $maxPts }} pts
                        </span>
                        @if ($submission->status === 'passed')
                            <span class="badge bg-success"><i class="bx bx-check-circle"></i> Passed threshold</span>
                        @elseif ($submission->status === 'failed')
                            <span class="badge bg-danger"><i class="bx bx-x-circle"></i> Below threshold</span>
                        @endif
                    </div>
                @endif
            @else
                <div class="no-ai-feedback">
                    <i class="bx bx-hourglass"></i>
                    <p class="mb-0" style="color:rgba(255,255,255,0.5);">
                        No AI analysis yet. This submission may not have been processed,
                        or the AI provider was unavailable at submission time.
                    </p>
                </div>
            @endif
        </div>

        {{-- Challenge Description --}}
        @if ($submission->quizItem && $submission->quizItem->description)
        <div class="card border-top border-0 border-4 border-success mb-4">
            <div class="card-body p-4">
                <h6 class="text-success mb-2"><i class="bx bx-info-circle"></i> Challenge Description</h6>
                <p class="mb-0">{!! nl2br(e($submission->quizItem->description)) !!}</p>
                @if ($submission->quizItem->expected_output)
                    <div class="mt-3">
                        <small class="text-muted d-block fw-bold mb-1">Expected Output</small>
                        <pre class="bg-light p-2 rounded mb-0"><code>{{ $submission->quizItem->expected_output }}</code></pre>
                    </div>
                @endif
                @if ($submission->quizItem->grading_criteria)
                    <div class="mt-3">
                        <small class="text-muted d-block fw-bold mb-1">Grading Criteria</small>
                        <p class="mb-0 small">{!! nl2br(e($submission->quizItem->grading_criteria)) !!}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Submitted Code --}}
        <div class="card border-top border-0 border-4 border-info mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="text-info mb-0"><i class="bx bx-code"></i> Submitted Code</h6>
                    <span class="badge bg-dark text-white">{{ $submission->language === 'cpp' ? 'C++' : ucfirst($submission->language) }}</span>
                </div>
                <div class="code-display">
                    <pre><code>{{ $submission->code }}</code></pre>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN: Manual grading override --}}
    <div class="col-lg-4">

        {{-- Manual Grade Override --}}
        <div class="card border-top border-0 border-4 border-warning mb-4 sticky-top" style="top: 80px;">
            <div class="card-body p-4">
                <h5 class="text-warning mb-1"><i class="bx bx-edit"></i> Grade Override</h5>
                <p class="text-muted small mb-4">Override the AI-assigned score or status if needed.</p>

                <form method="POST" action="{{ route('admin.submissions.update', $submission->id) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="submitted" {{ old('status', $submission->status) === 'submitted' ? 'selected' : '' }}>Submitted (Pending)</option>
                            <option value="graded"    {{ old('status', $submission->status) === 'graded'    ? 'selected' : '' }}>Graded</option>
                            <option value="passed"    {{ old('status', $submission->status) === 'passed'    ? 'selected' : '' }}>Passed</option>
                            <option value="failed"    {{ old('status', $submission->status) === 'failed'    ? 'selected' : '' }}>Failed</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="score" class="form-label fw-semibold">
                            Score
                            <span class="text-muted fw-normal">(max {{ $submission->quizItem->points ?? 0 }} pts)</span>
                        </label>
                        <input type="number"
                               class="form-control @error('score') is-invalid @enderror"
                               id="score" name="score"
                               value="{{ old('score', $submission->score) }}"
                               min="0"
                               max="{{ $submission->quizItem->points ?? 0 }}">
                        @error('score')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="feedback" class="form-label fw-semibold">Additional Notes</label>
                        <textarea class="form-control @error('feedback') is-invalid @enderror"
                                  id="feedback" name="feedback" rows="4"
                                  placeholder="Add instructor notes or override the AI feedback...">{{ old('feedback', $submission->feedback) }}</textarea>
                        @error('feedback')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small class="text-muted">Leave blank to keep the AI-generated analysis.</small>
                    </div>

                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bx bx-save"></i> Save Grade
                    </button>
                </form>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="card mb-4">
            <div class="card-body p-4">
                <h6 class="mb-3"><i class="bx bx-link"></i> Quick Links</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> All Submissions
                    </a>
                    @if ($submission->quiz)
                        <a href="{{ route('quizzes.show', $submission->quiz->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-brain"></i> View Quiz
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection