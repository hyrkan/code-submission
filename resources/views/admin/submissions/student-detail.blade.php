@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Submissions') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.submissions.index') }}">{{ __('Quiz Submissions') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.submissions.quiz-students', $quiz->id) }}">{{ $quiz->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $student->first_name }} {{ $student->last_name }}</li>
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

{{-- Student & Quiz Summary --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-top border-0 border-4 border-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1 text-primary"><i class="bx bx-user"></i> {{ $student->first_name }} {{ $student->last_name }}</h4>
                        <p class="text-muted mb-0">
                            <span class="badge bg-dark text-white">{{ $student->student_number ?? 'N/A' }}</span>
                            &middot; {{ $student->year->name ?? 'N/A' }} – {{ $student->section->name ?? 'N/A' }}
                            &middot; {{ $student->user->email ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.submissions.quiz-students', $quiz->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back"></i> Back to Students
                        </a>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    @php
                        $total = $submissions->count();
                        $passed = $submissions->where('status', 'passed')->count();
                        $failed = $submissions->where('status', 'failed')->count();
                        $graded = $submissions->where('status', 'graded')->count();
                        $pending = $submissions->where('status', 'submitted')->count();
                        $totalScore = $submissions->sum('score');
                        $maxScore = $quiz->items->sum('points');
                    @endphp
                    <div class="col">
                        <h4 class="text-primary mb-0">{{ $total }}</h4>
                        <small class="text-muted">Submitted</small>
                    </div>
                    <div class="col">
                        <h4 class="text-success mb-0">{{ $passed }}</h4>
                        <small class="text-muted">Passed</small>
                    </div>
                    <div class="col">
                        <h4 class="text-danger mb-0">{{ $failed }}</h4>
                        <small class="text-muted">Failed</small>
                    </div>
                    <div class="col">
                        <h4 class="text-info mb-0">{{ $graded }}</h4>
                        <small class="text-muted">Graded</small>
                    </div>
                    <div class="col">
                        <h4 class="text-warning mb-0">{{ $pending }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col border-start">
                        <h4 class="text-dark mb-0">{{ $totalScore }} <small class="text-muted">/ {{ $maxScore }}</small></h4>
                        <small class="text-muted">Total Score</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-top border-0 border-4 border-info h-100">
            <div class="card-body">
                <h5 class="text-info mb-2"><i class="bx bx-brain"></i> {{ $quiz->name }}</h5>
                <span class="badge bg-dark text-white">{{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language ?? '') }}</span>
                <span class="badge bg-secondary text-white">{{ $quiz->items->count() }} challenges</span>
                <span class="badge bg-info text-white">{{ $maxScore }} pts</span>
                @if ($quiz->description)
                    <p class="text-muted small mt-2 mb-0">{{ Str::limit($quiz->description, 120) }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Challenge Submissions --}}
@if ($submissions->isEmpty())
    <div class="card radius-10">
        <div class="card-body text-center py-5 text-muted">
            <i class="bx bx-code-block font-48 d-block mb-2"></i>
            <p class="mb-0">{{ __('No submissions found for this student.') }}</p>
        </div>
    </div>
@else
    @foreach ($submissions as $index => $submission)
        @php
            $item = $submission->quizItem;
            $statusMap = [
                'passed'    => ['bg-success', 'Passed'],
                'failed'    => ['bg-danger',  'Failed'],
                'graded'    => ['bg-info',    'Graded'],
                'submitted' => ['bg-warning text-dark', 'Pending'],
            ];
            [$statusClass, $statusLabel] = $statusMap[$submission->status] ?? ['bg-secondary', ucfirst($submission->status)];
        @endphp

        <div class="card border-top border-0 border-4 border-{{ $submission->status === 'passed' ? 'success' : ($submission->status === 'failed' ? 'danger' : ($submission->status === 'graded' ? 'info' : 'warning')) }} mb-4">
            <div class="card-body p-4">
                {{-- Challenge Header --}}
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="mb-0">
                            <i class="bx bx-code-block text-primary"></i>
                            Challenge #{{ $index + 1 }}: {{ $item->title ?? 'Unknown' }}
                        </h5>
                        @if ($item)
                            <span class="badge bg-{{ $item->difficulty === 'easy' ? 'success' : ($item->difficulty === 'medium' ? 'warning' : 'danger') }} text-white">
                                {{ ucfirst($item->difficulty) }}
                            </span>
                            <span class="badge bg-info text-white">{{ $item->points }} pts</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $statusClass }} status-badge-lg">{{ $statusLabel }}</span>
                        @if ($submission->score !== null)
                            <span class="badge bg-dark text-white" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="bx bx-star"></i> {{ $submission->score }} / {{ $item->points ?? '?' }} pts
                            </span>
                        @endif
                        <a href="{{ route('admin.submissions.show', $submission->id) }}" class="btn btn-sm btn-outline-primary" title="View Details & Grade">
                            <i class="bx bx-expand-alt"></i> Details
                        </a>
                    </div>
                </div>

                @if ($item && $item->description)
                    <div class="mb-3">
                        <small class="text-muted fw-bold d-block mb-1"><i class="bx bx-info-circle"></i> Challenge Description</small>
                        <p class="text-muted small mb-0">{{ Str::limit($item->description, 200) }}</p>
                    </div>
                @endif

                {{-- AI Feedback --}}
                @if ($submission->feedback)
                    <div class="mb-3">
                        <small class="text-muted fw-bold d-block mb-1"><i class="bx bx-brain"></i> AI Feedback</small>
                        <div class="bg-light rounded p-3" style="max-height: 200px; overflow-y: auto; font-size: 0.85rem; line-height: 1.6; white-space: pre-wrap;">{{ Str::limit($submission->feedback, 500) }}</div>
                    </div>
                @endif

                {{-- Submitted Code --}}
                <div class="mb-2">
                    <small class="text-muted fw-bold d-block mb-1"><i class="bx bx-code"></i> Submitted Code</small>
                    <div class="code-display" style="background: #1e1e1e; border-radius: 0.5rem; overflow: hidden;">
                        <pre style="margin: 0; padding: 1rem; color: #d4d4d4; font-family: 'Consolas', monospace; font-size: 0.85rem; max-height: 250px; overflow: auto;"><code>{{ $submission->code }}</code></pre>
                    </div>
                </div>

                <small class="text-muted">
                    <i class="bx bx-time"></i> Submitted: {{ $submission->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}
                    &middot; Language: {{ $submission->language === 'cpp' ? 'C++' : ucfirst($submission->language) }}
                </small>
            </div>
        </div>
    @endforeach
@endif
@endsection