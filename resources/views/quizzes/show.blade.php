@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Quizzes') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">{{ __('All Quizzes') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $quiz->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Quiz Details Card -->
<div class="card border-top border-0 border-4 border-primary mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-primary">{{ $quiz->name }}</h4>
            <div class="d-flex gap-2">
                @if ($quiz->is_published)
                    <span class="badge bg-success text-white fs-6">{{ __('Published') }}</span>
                @else
                    <span class="badge bg-secondary text-white fs-6">{{ __('Draft') }}</span>
                @endif
                @if ($quiz->is_archived)
                    <span class="badge bg-warning text-dark fs-6">{{ __('Archived') }}</span>
                @endif
            </div>
        </div>
        @if ($quiz->description)
            <p class="text-muted mb-3">{{ $quiz->description }}</p>
        @endif
        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Year</small>
                <strong>{{ $quiz->year->name ?? 'All Years' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Section</small>
                <strong>{{ $quiz->section->name ?? 'All Sections' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Language</small>
                <strong><span class="badge bg-dark text-white">{{ ucfirst($quiz->language === 'cpp' ? 'C++' : $quiz->language) }}</span></strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Time Limit</small>
                <strong>{{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No limit' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Total Points</small>
                <strong><span class="badge bg-info text-white">{{ $quiz->total_points }}</span></strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Created By</small>
                <strong>{{ $quiz->creator->name ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Date Created</small>
                <strong>{{ $quiz->created_at->format('M d, Y h:i A') }}</strong>
            </div>
            @if ($quiz->scheduled_at)
            <div class="col-md-3">
                <small class="text-muted d-block">Scheduled</small>
                <strong>{{ $quiz->scheduled_at->format('M d, Y h:i A') }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Coding Challenges -->
<div class="card border-top border-0 border-4 border-success mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <i class="bx bx-code-block me-2 font-22 text-success"></i>
            <h5 class="mb-0 text-success">{{ __('Coding Challenges') }} ({{ $quiz->items->count() }})</h5>
        </div>
        <hr>

        @forelse ($quiz->items as $item)
            <div class="card border mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0 text-primary">
                            <i class="bx bx-code-block"></i> Challenge #{{ $loop->iteration }}: {{ $item->title }}
                        </h6>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-{{ $item->difficulty === 'easy' ? 'success' : ($item->difficulty === 'medium' ? 'warning' : 'danger') }} text-white">{{ ucfirst($item->difficulty) }}</span>
                            <span class="badge bg-info text-white">{{ $item->points }} pts</span>
                        </div>
                    </div>

                    @if ($item->description)
                        <p class="mb-2">{{ $item->description }}</p>
                    @endif

                    <div class="row g-2 mb-2">
                        @if ($item->sample_input)
                        <div class="col-md-4">
                            <small class="text-muted d-block">Sample Input</small>
                            <pre class="bg-light p-2 rounded mb-0"><code>{{ $item->sample_input }}</code></pre>
                        </div>
                        @endif
                        @if ($item->sample_output)
                        <div class="col-md-4">
                            <small class="text-muted d-block">Sample Output</small>
                            <pre class="bg-light p-2 rounded mb-0"><code>{{ $item->sample_output }}</code></pre>
                        </div>
                        @endif
                        @if ($item->expected_output)
                        <div class="col-md-4">
                            <small class="text-muted d-block">Expected Output</small>
                            <pre class="bg-light p-2 rounded mb-0"><code>{{ $item->expected_output }}</code></pre>
                        </div>
                        @endif
                    </div>

                    @if ($item->coding_standards)
                    <div class="mb-2">
                        <small class="text-muted d-block"><i class="bx bx-check-shield"></i> Coding Standards & Guidelines</small>
                        <p class="mb-0 ps-3">{{ $item->coding_standards }}</p>
                    </div>
                    @endif

                    @if ($item->grading_criteria)
                    <div class="mb-0">
                        <small class="text-muted d-block"><i class="bx bx-star"></i> Grading Criteria</small>
                        <p class="mb-0 ps-3">{{ $item->grading_criteria }}</p>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bx bx-code-block font-48 d-block mb-2"></i>
                <p class="mb-0">{{ __('No challenges in this quiz.') }}</p>
            </div>
        @endforelse
    </div>
</div>

<div class="d-flex gap-2 mb-4">
    <a href="{{ route('quizzes.edit', $quiz->id) }}" class="btn btn-primary px-4"><i class="bx bx-edit-alt"></i> {{ __('Edit Quiz') }}</a>
    <form action="{{ route('quizzes.toggle-publish', $quiz->id) }}" method="POST">
        @csrf
        @method('PATCH')
        @if ($quiz->is_published)
            <button type="submit" class="btn btn-warning px-4"><i class="bx bx-hide"></i> {{ __('Unpublish') }}</button>
        @else
            <button type="submit" class="btn btn-success px-4"><i class="bx bx-check-circle"></i> {{ __('Publish') }}</button>
        @endif
    </form>
    <a href="{{ route('quizzes.index') }}" class="btn btn-light px-4"><i class="bx bx-arrow-back"></i> {{ __('Back to Quizzes') }}</a>
</div>
@endsection