@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Settings') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Years') }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('years.create') }}" class="btn btn-primary radius-30 px-4"><i class="bx bx-plus"></i>{{ __('Add Year') }}</a>
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
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Sections') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($years as $year)
                        <tr>
                            <td><strong>{{ $year->name }}</strong></td>
                            <td><span class="badge bg-info text-white">{{ $year->sections_count }}</span></td>
                            <td>
                                @if ($year->is_archived)
                                    <span class="badge bg-warning text-dark">{{ __('Archived') }}</span>
                                @else
                                    <span class="badge bg-success text-white">{{ __('Active') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('years.edit', $year->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-edit-alt m-0"></i></a>
                                    <form action="{{ route('years.toggle-archive', $year->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if ($year->is_archived)
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Unarchive"><i class="bx bx-archive-out m-0"></i></button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive"><i class="bx bx-archive m-0"></i></button>
                                        @endif
                                    </form>
                                    <form action="{{ route('years.destroy', $year->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this year?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-trash-alt m-0"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">{{ __('No years found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection