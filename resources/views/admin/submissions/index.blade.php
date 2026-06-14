@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Submissions') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Quiz Submissions') }}</li>
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

{{-- Filter Bar --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="row g-3 mb-0 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">{{ __('Search Quizzes') }}</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Quiz name...">
            </div>
            <div class="col-md-3">
                <label for="language" class="form-label">{{ __('Language') }}</label>
                <select class="form-select" id="language" name="language">
                    <option value="">{{ __('All Languages') }}</option>
                    <option value="python" {{ request('language') === 'python' ? 'selected' : '' }}>Python</option>
                    <option value="java" {{ request('language') === 'java' ? 'selected' : '' }}>Java</option>
                    <option value="javascript" {{ request('language') === 'javascript' ? 'selected' : '' }}>JavaScript</option>
                    <option value="c" {{ request('language') === 'c' ? 'selected' : '' }}>C</option>
                    <option value="cpp" {{ request('language') === 'cpp' ? 'selected' : '' }}>C++</option>
                    <option value="php" {{ request('language') === 'php' ? 'selected' : '' }}>PHP</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-1"><i class="bx bx-search"></i> {{ __('Filter') }}</button>
                <a href="{{ route('admin.submissions.index') }}" class="btn btn-light"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

@if ($quizzes->isEmpty())
    <div class="card radius-10">
        <div class="card-body text-center py-5 text-muted">
            <i class="bx bx-task font-48 d-block mb-2"></i>
            <p class="mb-0">{{ __('No quiz submissions found.') }}</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach ($quizzes as $quiz)
            @php
                $stats = $quiz->sub_stats;
                $totalPts = $quiz->items->sum('points');
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card border h-100 shadow-sm">
                    <div class="card-body">
                        {{-- Quiz Name & Language --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="mb-0 text-primary">
                                <i class="bx bx-brain"></i> {{ $quiz->name }}
                            </h5>
                            <span class="badge bg-dark text-white">
                                {{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language ?? '') }}
                            </span>
                        </div>

                        @if ($quiz->description)
                            <p class="text-muted small mb-2">{{ Str::limit($quiz->description, 80) }}</p>
                        @endif

                        {{-- Meta info --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-light text-dark"><i class="bx bx-user"></i> {{ $stats->student_count }} students</span>
                            <span class="badge bg-light text-dark"><i class="bx bx-file"></i> {{ $stats->total }} submissions</span>
                            <span class="badge bg-light text-dark"><i class="bx bx-code-block"></i> {{ $quiz->items_count }} challenges</span>
                            <span class="badge bg-light text-dark"><i class="bx bx-star"></i> {{ $totalPts }} pts</span>
                        </div>

                        {{-- Status breakdown bars --}}
                        <div class="mb-3">
                            @php
                                $total = max($stats->total, 1);
                                $pctPassed   = round(($stats->passed_count / $total) * 100);
                                $pctFailed   = round(($stats->failed_count / $total) * 100);
                                $pctGraded   = round(($stats->graded_count / $total) * 100);
                                $pctPending  = round(($stats->pending_count / $total) * 100);
                            @endphp
                            <div class="progress" style="height: 8px;" title="Passed: {{ $stats->passed_count }}, Failed: {{ $stats->failed_count }}, Graded: {{ $stats->graded_count }}, Pending: {{ $stats->pending_count }}">
                                @if ($pctPassed > 0)
                                    <div class="progress-bar bg-success" style="width: {{ $pctPassed }}%"></div>
                                @endif
                                @if ($pctFailed > 0)
                                    <div class="progress-bar bg-danger" style="width: {{ $pctFailed }}%"></div>
                                @endif
                                @if ($pctGraded > 0)
                                    <div class="progress-bar bg-info" style="width: {{ $pctGraded }}%"></div>
                                @endif
                                @if ($pctPending > 0)
                                    <div class="progress-bar bg-warning" style="width: {{ $pctPending }}%"></div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">
                                    @if ($stats->passed_count > 0)<span class="text-success"><i class="bx bx-check-circle"></i> {{ $stats->passed_count }}</span>@endif
                                    @if ($stats->failed_count > 0)<span class="text-danger ms-2"><i class="bx bx-x-circle"></i> {{ $stats->failed_count }}</span>@endif
                                    @if ($stats->graded_count > 0)<span class="text-info ms-2"><i class="bx bx-check"></i> {{ $stats->graded_count }}</span>@endif
                                    @if ($stats->pending_count > 0)<span class="text-warning ms-2"><i class="bx bx-time-five"></i> {{ $stats->pending_count }}</span>@endif
                                </small>
                            </div>
                        </div>

                        {{-- Created date --}}
                        <small class="text-muted d-block mb-3">
                            <i class="bx bx-calendar"></i> Created {{ $quiz->created_at->format('M d, Y') }}
                            @if ($quiz->creator)
                                 &middot; by {{ $quiz->creator->name }}
                            @endif
                        </small>

                        {{-- View Students Button --}}
                        <a href="{{ route('admin.submissions.quiz-students', $quiz->id) }}" class="btn btn-primary w-100">
                            <i class="bx bx-group"></i> View Students
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4 mb-4">
        {{ $quizzes->links() }}
    </div>
@endif
@endsection