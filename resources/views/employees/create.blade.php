@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Users') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">{{ __('Employee List') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Add Employee') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-9 mx-auto">
        <div class="card border-top border-0 border-4 border-primary">
            <div class="card-body p-5">
                <div class="card-title d-flex align-items-center">
                    <div><i class="bx bxs-user-plus me-1 font-22 text-primary"></i></div>
                    <h5 class="mb-0 text-primary">{{ __('Add New Employee') }}</h5>
                </div>
                <hr>
                <form class="row g-3" method="POST" action="{{ route('employees.store') }}">
                    @csrf

                    <!-- First Name -->
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="John">
                        @error('first_name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required placeholder="Doe">
                        @error('last_name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="col-12">
                        <label for="email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="email@example.com">
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div class="col-md-6">
                        <label for="department" class="form-label">{{ __('Department') }}</label>
                        <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department') }}" placeholder="Engineering">
                        @error('department')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div class="col-md-6">
                        <label for="position" class="form-label">{{ __('Position') }}</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}" placeholder="Software Developer">
                        @error('position')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="col-12">
                        <label for="role" class="form-label">{{ __('Role') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>{{ __('Select Role') }}</option>
                            <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>{{ __('Employee') }}</option>
                            <option value="super admin" {{ old('role') === 'super admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
                        </select>
                        @error('role')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">{{ __('Password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Min 8 characters">
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Repeat password">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-5">{{ __('Save Employee') }}</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-light px-5 ms-2">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
