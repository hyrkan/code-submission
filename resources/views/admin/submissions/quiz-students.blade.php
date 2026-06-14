@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Submissions') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.submissions.index') }}">{{ __('Quiz Submissions') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $quiz->name }}</li>
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

{{-- Quiz Summary Card --}}
<div class="card border-top border-0 border-4 border-primary mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="mb-1 text-primary"><i class="bx bx-brain"></i> {{ $quiz->name }}</h4>
                @if ($quiz->description)
                    <p class="text-muted mb-0">{{ Str::limit($quiz->description, 150) }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-dark text-white fs-6">{{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language ?? '') }}</span>
                <span class="badge bg-info text-white fs-6"><i class="bx bx-user"></i> {{ $students->total() }} students</span>
                <span class="badge bg-light text-dark fs-6"><i class="bx bx-code-block"></i> {{ $quiz->items->count() }} challenges</span>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card radius-10 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.submissions.quiz-students', $quiz->id) }}" class="row g-3 mb-0 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">{{ __('Search Student') }}</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name or student #...">
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
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-1"><i class="bx bx-search"></i> {{ __('Filter') }}</button>
                <a href="{{ route('admin.submissions.quiz-students', $quiz->id) }}" class="btn btn-light"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

@if ($students->isEmpty())
    <div class="card radius-10">
        <div class="card-body text-center py-5 text-muted">
            <i class="bx bx-user font-48 d-block mb-2"></i>
            <p class="mb-0">{{ __('No students found for this quiz.') }}</p>
        </div>
    </div>
@else
    <div class="card radius-10">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Student') }}</th>
                            <th>{{ __('Student #') }}</th>
                            <th>{{ __('Year / Section') }}</th>
                            <th class="text-center">{{ __('Challenges') }}</th>
                            <th class="text-center">{{ __('Total Score') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php
                                $stats = $student->sub_stats;
                                $maxScore = $quiz->items->sum('points');
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-dark text-white">{{ $student->student_number ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <small>{{ $student->year->name ?? 'N/A' }} – {{ $student->section->name ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary text-white">{{ $stats->total }} / {{ $quiz->items->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $stats->total_score ?? 0 }}</strong> <span class="text-muted">/ {{ $maxScore }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'passed'    => ['bg-success', 'Passed'],
                                            'failed'    => ['bg-danger',  'Failed'],
                                            'graded'    => ['bg-info',    'Graded'],
                                            'submitted' => ['bg-warning text-dark', 'Pending'],
                                        ];
                                        [$sc, $sl] = $statusMap[$student->overall_status] ?? ['bg-secondary', ucfirst($student->overall_status)];
                                    @endphp
                                    <span class="badge {{ $sc }}">{{ $sl }}</span>
                                    @if ($stats->passed_count > 0)
                                        <br><small class="text-success"><i class="bx bx-check-circle"></i> {{ $stats->passed_count }} passed</small>
                                    @endif
                                    @if ($stats->failed_count > 0)
                                        <br><small class="text-danger"><i class="bx bx-x-circle"></i> {{ $stats->failed_count }} failed</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.submissions.student-detail', [$quiz->id, $student->id]) }}" class="btn btn-sm btn-outline-primary" title="View Submissions">
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

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4 mb-4">
        {{ $students->links() }}
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