@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Quizzes') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('All Quizzes') }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('quizzes.create') }}" class="btn btn-primary radius-30 px-4"><i class="bx bx-plus"></i>{{ __('Create Quiz') }}</a>
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
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('quizzes.index') }}" class="row g-3 mb-4 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">{{ __('Search Quiz') }}</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by quiz name...">
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">{{ __('Date From') }}</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">{{ __('Date To') }}</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-1"><i class="bx bx-search"></i> {{ __('Filter') }}</button>
                <a href="{{ route('quizzes.index') }}" class="btn btn-light"><i class="bx bx-reset"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="quizzesTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Quiz Name') }}</th>
                        <th>{{ __('Language') }}</th>
                        <th>{{ __('Year') }}</th>
                        <th>{{ __('Section') }}</th>
                        <th>{{ __('Time Limit') }}</th>
                        <th>{{ __('Points') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created By') }}</th>
                        <th>{{ __('Date Created') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quizzes as $quiz)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $quiz->name }}</strong>
                                @if ($quiz->description)
                                    <br><small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-dark text-white">{{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language) }}</span></td>
                            <td>{{ $quiz->year->name ?? __('N/A') }}</td>
                            <td>{{ $quiz->section->name ?? __('N/A') }}</td>
                            <td>{{ $quiz->time_limit ? $quiz->time_limit . ' min' : __('N/A') }}</td>
                            <td><span class="badge bg-info text-white">{{ $quiz->total_points }}</span></td>
                            <td>
                                @if ($quiz->is_published)
                                    <span class="badge bg-success text-white">{{ __('Published') }}</span>
                                @else
                                    <span class="badge bg-secondary text-white">{{ __('Draft') }}</span>
                                @endif
                            </td>
                            <td>{{ $quiz->creator->name ?? __('N/A') }}</td>
                            <td>{{ $quiz->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('quizzes.show', $quiz->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bx bx-show m-0"></i></a>
                                    <a href="{{ route('quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-outline-info" title="Edit"><i class="bx bx-edit-alt m-0"></i></a>
                                    <form action="{{ route('quizzes.toggle-archive', $quiz->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if ($quiz->is_archived)
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Unarchive"><i class="bx bx-archive-out m-0"></i></button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive"><i class="bx bx-archive m-0"></i></button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">{{ __('No quizzes found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#quizzesTable').DataTable({
            paging: true,
            searching: false,
            info: true,
            ordering: true,
            order: [],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                emptyTable: "{{ __('No quizzes found.') }}",
                info: "{{ __('Showing _START_ to _END_ of _TOTAL_ quizzes') }}",
                infoEmpty: "{{ __('Showing 0 to 0 of 0 quizzes') }}",
                infoFiltered: "{{ __('(filtered from _MAX_ total quizzes)') }}",
                lengthMenu: "{{ __('Show _MENU_ quizzes') }}",
                zeroRecords: "{{ __('No matching quizzes found') }}"
            }
        });
    });
</script>
@endpush