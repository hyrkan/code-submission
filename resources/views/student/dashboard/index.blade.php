@extends('main_layout.master')

@section('content')
@if (session('error'))
    <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2 mb-3">
        <div class="d-flex align-items-center">
            <div class="font-35 text-white"><i class="bx bxs-message-square-x"></i></div>
            <div class="ms-3">
                <h6 class="mb-0 text-white">{{ __('Error') }}</h6>
                <div class="text-white">{{ session('error') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2 mb-3">
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

{{-- Summary Cards --}}
<div class="row row-cols-1 row-cols-md-3 mb-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Active Quizzes</p>
                        <h4 class="my-1 text-primary">{{ $activeQuizzes->count() }}</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bx-brain'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Overdue</p>
                        <h4 class="my-1 text-danger">{{ $overdueQuizzes->count() }}</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bx-time-five'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Completed</p>
                        <h4 class="my-1 text-success">{{ $completedQuizzes->count() }}</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bx-check-circle'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Active Quizzes --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h6 class="mb-0"><i class="bx bx-brain me-1 text-primary"></i> Active Quizzes</h6>
            </div>
            <span class="badge bg-primary text-white ms-2">{{ $activeQuizzes->count() }}</span>
        </div>

        @if ($activeQuizzes->count() > 0)
            <div class="row g-3">
                @foreach ($activeQuizzes as $quiz)
                    <div class="col-md-6 col-xl-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-primary">{{ $quiz->name }}</h6>
                                    <span class="badge bg-info text-white">{{ $quiz->total_points }} pts</span>
                                </div>
                                @if ($quiz->description)
                                    <p class="text-muted small mb-2">{{ Str::limit($quiz->description, 80) }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @if ($quiz->year)
                                        <span class="badge bg-light text-dark"><i class="bx bx-calendar"></i> {{ $quiz->year->name }}</span>
                                    @endif
                                    @if ($quiz->section)
                                        <span class="badge bg-light text-dark"><i class="bx bx-layer"></i> {{ $quiz->section->name }}</span>
                                    @endif
                                    @if ($quiz->time_limit)
                                        <span class="badge bg-light text-dark"><i class="bx bx-time"></i> {{ $quiz->time_limit }} min</span>
                                    @endif
                                    <span class="badge bg-dark text-white"><i class="bx bx-code"></i> {{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language) }}</span>
                                    <span class="badge bg-light text-dark"><i class="bx bx-code-block"></i> {{ $quiz->items->count() }} challenges</span>
                                </div>
                                <small class="text-muted d-block mb-2">
                                    Created by {{ $quiz->creator->name ?? 'N/A' }} &middot; {{ $quiz->created_at->format('M d, Y') }}
                                </small>
                                @if ($quiz->scheduled_at)
                                    <small class="text-primary d-block mb-2">
                                        <i class="bx bx-time"></i> Due: {{ $quiz->scheduled_at->format('M d, Y h:i A') }}
                                    </small>
                                @endif
                                <div class="d-flex gap-2 mt-2">
                                    @foreach ($quiz->items->take(3) as $item)
                                        <span class="badge bg-{{ $item->difficulty === 'easy' ? 'success' : ($item->difficulty === 'medium' ? 'warning' : 'danger') }} text-white">{{ ucfirst($item->difficulty) }}</span>
                                    @endforeach
                                    @if ($quiz->items->count() > 3)
                                        <span class="badge bg-secondary text-white">+{{ $quiz->items->count() - 3 }} more</span>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('student.quizzes.take', $quiz->id) }}" class="btn btn-sm btn-primary w-100">
                                        <i class="bx bx-code-block"></i> Take Quiz
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bx bx-brain font-48 d-block mb-2"></i>
                <p class="mb-0">No active quizzes available right now.</p>
            </div>
        @endif
    </div>
</div>

{{-- Overdue Quizzes --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h6 class="mb-0"><i class="bx bx-time-five me-1 text-danger"></i> Overdue - Not Taken</h6>
            </div>
            <span class="badge bg-danger text-white ms-2">{{ $overdueQuizzes->count() }}</span>
        </div>

        @if ($overdueQuizzes->count() > 0)
            <div class="row g-3">
                @foreach ($overdueQuizzes as $quiz)
                    <div class="col-md-6 col-xl-4">
                        <div class="card border border-danger h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-danger">{{ $quiz->name }}</h6>
                                    <span class="badge bg-info text-white">{{ $quiz->total_points }} pts</span>
                                </div>
                                @if ($quiz->description)
                                    <p class="text-muted small mb-2">{{ Str::limit($quiz->description, 80) }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @if ($quiz->year)
                                        <span class="badge bg-light text-dark"><i class="bx bx-calendar"></i> {{ $quiz->year->name }}</span>
                                    @endif
                                    @if ($quiz->section)
                                        <span class="badge bg-light text-dark"><i class="bx bx-layer"></i> {{ $quiz->section->name }}</span>
                                    @endif
                                    @if ($quiz->time_limit)
                                        <span class="badge bg-light text-dark"><i class="bx bx-time"></i> {{ $quiz->time_limit }} min</span>
                                    @endif
                                    <span class="badge bg-dark text-white"><i class="bx bx-code"></i> {{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language) }}</span>
                                    <span class="badge bg-light text-dark"><i class="bx bx-code-block"></i> {{ $quiz->items->count() }} challenges</span>
                                </div>
                                @if ($quiz->scheduled_at)
                                    <small class="text-danger d-block mb-2">
                                        <i class="bx bx-time"></i> Was due: {{ $quiz->scheduled_at->format('M d, Y h:i A') }}
                                    </small>
                                @endif
                                <div class="d-flex gap-2 mt-2">
                                    @foreach ($quiz->items->take(3) as $item)
                                        <span class="badge bg-{{ $item->difficulty === 'easy' ? 'success' : ($item->difficulty === 'medium' ? 'warning' : 'danger') }} text-white">{{ ucfirst($item->difficulty) }}</span>
                                    @endforeach
                                    @if ($quiz->items->count() > 3)
                                        <span class="badge bg-secondary text-white">+{{ $quiz->items->count() - 3 }} more</span>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('student.quizzes.take', $quiz->id) }}" class="btn btn-sm btn-danger w-100">
                                        <i class="bx bx-code-block"></i> Take Quiz (Overdue)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bx bx-check-circle font-48 d-block mb-2"></i>
                <p class="mb-0">No overdue quizzes. You're all caught up!</p>
            </div>
        @endif
    </div>
</div>

{{-- Completed Quizzes --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h6 class="mb-0"><i class="bx bx-check-circle me-1 text-success"></i> Completed Quizzes</h6>
            </div>
            <span class="badge bg-success text-white ms-2">{{ $completedQuizzes->count() }}</span>
        </div>

        @if ($completedQuizzes->count() > 0)
            <div class="row g-3">
                @foreach ($completedQuizzes as $quiz)
                    <div class="col-md-6 col-xl-4">
                        <div class="card border border-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-success">{{ $quiz->name }}</h6>
                                    <span class="badge bg-success text-white"><i class="bx bx-check"></i> Taken</span>
                                </div>
                                @if ($quiz->description)
                                    <p class="text-muted small mb-2">{{ Str::limit($quiz->description, 80) }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @if ($quiz->year)
                                        <span class="badge bg-light text-dark"><i class="bx bx-calendar"></i> {{ $quiz->year->name }}</span>
                                    @endif
                                    @if ($quiz->section)
                                        <span class="badge bg-light text-dark"><i class="bx bx-layer"></i> {{ $quiz->section->name }}</span>
                                    @endif
                                    @if ($quiz->time_limit)
                                        <span class="badge bg-light text-dark"><i class="bx bx-time"></i> {{ $quiz->time_limit }} min</span>
                                    @endif
                                    <span class="badge bg-dark text-white"><i class="bx bx-code"></i> {{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language) }}</span>
                                    <span class="badge bg-light text-dark"><i class="bx bx-code-block"></i> {{ $quiz->items->count() }} challenges</span>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('student.quizzes.results', $quiz->id) }}" class="btn btn-sm btn-success w-100">
                                        <i class="bx bx-bar-chart-alt-2"></i> View Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bx bx-brain font-48 d-block mb-2"></i>
                <p class="mb-0">You haven't completed any quizzes yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection