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

<div class="card radius-10 mb-4">
    <div class="card-body">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="row g-3 mb-0 align-items-end" id="filterForm">
            <div class="col-md-2">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Student or quiz...">
            </div>
            <div class="col-md-2">
                <label for="quiz_id" class="form-label">{{ __('Quiz') }}</label>
                <select class="form-select" id="quiz_id" name="quiz_id">
                    <option value="">{{ __('All Quizzes') }}</option>
                    @foreach ($quizzes as $quiz)
                        <option value="{{ $quiz->id }}" {{ request('quiz_id') == $quiz->id ? 'selected' : '' }}>{{ $quiz->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="year_id" class="form-label">{{ __('Year') }}</label>
                <select class="form-select" id="year_id" name="year_id">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="section_id" class="form-label">{{ __('Section') }}</label>
                <select class="form-select" id="section_id" name="section_id">
                    <option value="">{{ __('All Sections') }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" data-year-id="{{ $section->year_id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select class="form-select" id="status" name="status">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Graded</option>
                    <option value="passed" {{ request('status') === 'passed' ? 'selected' : '' }}>Passed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-1"><i class="bx bx-search"></i> {{ __('Filter') }}</button>
                <a href="{{ route('admin.submissions.index') }}" class="btn btn-light"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

@php
    $groupedSubmissions = $submissions->groupBy('quiz_id');
@endphp

@if ($groupedSubmissions->isEmpty())
    <div class="card radius-10">
        <div class="card-body text-center py-5 text-muted">
            <i class="bx bx-task font-48 d-block mb-2"></i>
            <p class="mb-0">{{ __('No submissions found.') }}</p>
        </div>
    </div>
@else
    @foreach ($groupedSubmissions as $quizId => $quizSubmissions)
        @php
            $quiz = $quizSubmissions->first()->quiz;
            $totalSubmissions = $quizSubmissions->count();
            $passedCount = $quizSubmissions->where('status', 'passed')->count();
            $failedCount = $quizSubmissions->where('status', 'failed')->count();
            $gradedCount = $quizSubmissions->where('status', 'graded')->count();
            $submittedCount = $quizSubmissions->where('status', 'submitted')->count();
        @endphp

        <div class="card border-top border-0 border-4 border-primary mb-4">
            <div class="card-body p-4">
                <!-- Quiz Header -->
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="mb-0 text-primary">
                            <i class="bx bx-brain"></i> {{ $quiz->name ?? 'Unknown Quiz' }}
                        </h5>
                        <span class="badge bg-dark text-white">
                            {{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language ?? '') }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white">{{ $totalSubmissions }} submissions</span>
                        @if ($submittedCount > 0)
                            <span class="badge bg-warning text-dark">{{ $submittedCount }} pending</span>
                        @endif
                        @if ($gradedCount > 0)
                            <span class="badge bg-info text-white">{{ $gradedCount }} graded</span>
                        @endif
                        @if ($passedCount > 0)
                            <span class="badge bg-success text-white">{{ $passedCount }} passed</span>
                        @endif
                        @if ($failedCount > 0)
                            <span class="badge bg-danger text-white">{{ $failedCount }} failed</span>
                        @endif
                    </div>
                </div>
                @if ($quiz->description)
                    <p class="text-muted small mb-3">{{ Str::limit($quiz->description, 120) }}</p>
                @endif

                <!-- Submissions grouped by Challenge -->
                @php
                    $byChallenge = $quizSubmissions->groupBy('quiz_item_id');
                @endphp

                @foreach ($byChallenge as $itemId => $itemSubmissions)
                    @php
                        $item = $itemSubmissions->first()->quizItem;
                    @endphp

                    <div class="card border mb-3">
                        <div class="card-header bg-light py-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0">
                                    <i class="bx bx-code-block text-success"></i>
                                    {{ $item->title ?? 'Unknown Challenge' }}
                                    @if ($item)
                                        <span class="badge bg-{{ $item->difficulty === 'easy' ? 'success' : ($item->difficulty === 'medium' ? 'warning' : 'danger') }} text-white ms-2" style="font-size: 0.7rem;">{{ ucfirst($item->difficulty) }}</span>
                                        <span class="badge bg-info text-white ms-1" style="font-size: 0.7rem;">{{ $item->points }} pts</span>
                                    @endif
                                </h6>
                                <span class="badge bg-secondary text-white">{{ $itemSubmissions->count() }} submissions</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Student') }}</th>
                                            <th>{{ __('Student #') }}</th>
                                            <th>{{ __('Year / Section') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Score') }}</th>
                                            <th>{{ __('Submitted At') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemSubmissions as $submission)
                                            <tr>
                                                <td>
                                                    <strong>{{ $submission->student->first_name }} {{ $submission->student->last_name }}</strong>
                                                </td>
                                                <td><span class="badge bg-dark text-white">{{ $submission->student->student_number ?? 'N/A' }}</span></td>
                                                <td>
                                                    <small>{{ $submission->student->year->name ?? 'N/A' }} - {{ $submission->student->section->name ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    @if ($submission->status === 'passed')
                                                        <span class="badge bg-success text-white">Passed</span>
                                                    @elseif ($submission->status === 'failed')
                                                        <span class="badge bg-danger text-white">Failed</span>
                                                    @elseif ($submission->status === 'graded')
                                                        <span class="badge bg-info text-white">Graded</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Submitted</span>
                                                    @endif
                                                </td>
                                                <td>{{ $submission->score ?? '—' }}</td>
                                                <td>{{ $submission->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.submissions.show', $submission->id) }}" class="btn btn-sm btn-outline-primary" title="View & Grade">
                                                        <i class="bx bx-show m-0"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-center mb-4">
        {{ $submissions->links() }}
    </div>
@endif
@endsection

@push('scripts')
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
        var currentSection = '{{ request("section_id") }}';
        if (currentSection) {
            sectionSelect.value = currentSection;
        }
    }
});
</script>
@endpush