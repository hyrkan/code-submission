@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Users') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Employee List') }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('employees.create') }}" class="btn btn-primary radius-30 px-4"><i class="bx bx-plus"></i>{{ __('Add Employee') }}</a>
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
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Position') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                            <td>{{ $employee->user->email ?? __('N/A') }}</td>
                            <td>{{ $employee->department ?? __('N/A') }}</td>
                            <td>{{ $employee->position ?? __('N/A') }}</td>
                            <td>
                                @if ($employee->role === 'super admin')
                                    <span class="badge bg-danger text-white">{{ __('Super Admin') }}</span>
                                @else
                                    <span class="badge bg-success text-white">{{ __('Employee') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-edit-alt m-0"></i></a>
                                    
                                    <!-- Prevent self-deletion -->
                                    @if(auth()->id() !== $employee->user_id)
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this employee?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-trash-alt m-0"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('No employees found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
