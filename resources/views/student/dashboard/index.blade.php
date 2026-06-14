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

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">My Courses</p>
                        <h4 class="my-1 text-info">3</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-book'></i>
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
                        <p class="mb-0 text-secondary">Upcoming Exams</p>
                        <h4 class="my-1 text-danger">2</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bxs-edit'></i>
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
                        <p class="mb-0 text-secondary">Completed Quizzes</p>
                        <h4 class="my-1 text-success">15</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-check-circle'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Quizzes -->
<div class="card radius-10">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h6 class="mb-0"><i class="bx bx-brain me-1 text-primary"></i> Available Quizzes</h6>
            </div>
            <span class="badge bg-primary text-white ms-2">{{ $quizzes->count() }}</span>
        </div>

        @if ($quizzes->count() > 0)
            <div class="row g-3">
                @foreach ($quizzes as $quiz)
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
                                    <small class="text-warning d-block mb-2">
                                        <i class="bx bx-time"></i> Scheduled: {{ $quiz->scheduled_at->format('M d, Y h:i A') }}
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
                <p class="mb-0">No quizzes available for your year and section yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
