@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Students') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('All Students') }}</li>
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

<div class="card radius-10">
    <div class="card-body">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3 mb-4 align-items-end" id="filterForm">
            <div class="col-md-3">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, student #, course...">
            </div>
            <div class="col-md-3">
                <label for="year_id" class="form-label">{{ __('Year') }}</label>
                <select class="form-select" id="year_id" name="year_id">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
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
                <a href="{{ route('admin.students.index') }}" class="btn btn-light"><i class="bx bx-reset"></i></a>
            </div>
        </form>

        <!-- Summary -->
        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-primary text-white">{{ $students->total() }} {{ __('students found') }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Student Number') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Course') }}</th>
                        <th>{{ __('Year') }}</th>
                        <th>{{ __('Section') }}</th>
                        <th>{{ __('Joined') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                            <td><span class="badge bg-dark text-white">{{ $student->student_number ?? 'N/A' }}</span></td>
                            <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                            <td>{{ $student->user->email ?? 'N/A' }}</td>
                            <td>{{ $student->course ?? 'N/A' }}</td>
                            <td>{{ $student->year->name ?? 'N/A' }}</td>
                            <td>{{ $student->section->name ?? 'N/A' }}</td>
                            <td>{{ $student->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.students.analytics', $student->id) }}" class="btn btn-sm btn-outline-primary" title="View Analytics">
                                    <i class="bx bx-bar-chart-alt-2"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">{{ __('No students found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var yearSelect = document.getElementById('year_id');
    var sectionSelect = document.getElementById('section_id');
    var allSectionOptions = [];

    // Store all section options
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

        // Filter sections based on selected year
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

        // Restore selection if still valid
        sectionSelect.value = currentVal;
        if (sectionSelect.value !== currentVal) {
            sectionSelect.value = '';
        }
    });

    // Trigger change on load if a year is pre-selected
    if (yearSelect.value) {
        yearSelect.dispatchEvent(new Event('change'));
        // Re-apply the section filter value after filtering
        var currentSection = '{{ request("section_id") }}';
        if (currentSection) {
            sectionSelect.value = currentSection;
        }
    }
});
</script>
@endpush