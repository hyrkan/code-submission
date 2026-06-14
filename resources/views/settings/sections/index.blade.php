@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Settings') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Sections') }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('sections.create') }}" class="btn btn-primary radius-30 px-4"><i class="bx bx-plus"></i>{{ __('Add Section') }}</a>
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

@if (session('error'))
    <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
        <div class="d-flex align-items-center">
            <div class="font-35 text-white"><i class="bx bxs-x-circle"></i></div>
            <div class="ms-3">
                <h6 class="mb-0 text-white">{{ __('Error') }}</h6>
                <div class="text-white">{{ session('error') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card radius-10">
    <div class="card-body">
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('sections.index') }}" class="row g-3 mb-4 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label">{{ __('Search Section') }}</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by section name...">
            </div>
            <div class="col-md-4">
                <label for="year_id" class="form-label">{{ __('Filter by Year') }}</label>
                <select class="form-select" id="year_id" name="year_id">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2"><i class="bx bx-search"></i> {{ __('Filter') }}</button>
                <a href="{{ route('sections.index') }}" class="btn btn-light"><i class="bx bx-reset"></i> {{ __('Reset') }}</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Year') }}</th>
                        <th>{{ __('Students') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sections as $section)
                        <tr>
                            <td><strong>{{ $section->name }}</strong></td>
                            <td>{{ $section->year->name ?? __('N/A') }}</td>
                            <td><span class="badge bg-info text-white">{{ $section->students_count }}</span></td>
                            <td>
                                @if ($section->is_archived)
                                    <span class="badge bg-warning text-dark">{{ __('Archived') }}</span>
                                @else
                                    <span class="badge bg-success text-white">{{ __('Active') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('sections.edit', $section->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-edit-alt m-0"></i></a>
                                    <form action="{{ route('sections.toggle-archive', $section->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if ($section->is_archived)
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Unarchive"><i class="bx bx-archive-out m-0"></i></button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive"><i class="bx bx-archive m-0"></i></button>
                                        @endif
                                    </form>
                                    <form action="{{ route('sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this section?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-trash-alt m-0"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">{{ __('No sections found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection