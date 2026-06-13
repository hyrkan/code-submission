@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('My Profile') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
            </ol>
        </nav>
    </div>
</div>

@if(session('status') === 'profile-updated')
    <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2">
        <div class="d-flex align-items-center">
            <div class="font-35 text-white"><i class="bx bxs-check-circle"></i></div>
            <div class="ms-3">
                <h6 class="mb-0 text-white">{{ __('Saved!') }}</h6>
                <div class="text-white">{{ __('Your profile has been updated successfully.') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container">
    <div class="main-body">
        <div class="row">
            <!-- Left Profile Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center py-4">
                            @php
                                $nameParts = explode(' ', auth()->user()->name ?? 'Student');
                                $initials = isset($nameParts[1]) ? substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1) : substr($nameParts[0], 0, 1);
                            @endphp
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:110px;height:110px;font-size:2.5rem;">
                                {{ strtoupper($initials) }}
                            </div>
                            <div class="mt-3">
                                <h4>{{ $user->name }}</h4>
                                <p class="text-secondary mb-1">{{ $student->course ?? __('No Course Set') }}</p>
                                <p class="text-muted font-size-sm mb-0">{{ $student->student_number ? __('Student No: ') . $student->student_number : __('No Student Number') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Profile Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="mb-4">{{ __('Update Profile') }}</h5>
                        <form method="POST" action="{{ route('student.profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                                    @error('first_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                                    @error('last_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="course" class="form-label">{{ __('Course') }}</label>
                                    <input type="text" class="form-control @error('course') is-invalid @enderror" id="course" name="course" value="{{ old('course', $student->course) }}" placeholder="e.g. BS Computer Science">
                                    @error('course')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="student_number" class="form-label">{{ __('Student Number') }}</label>
                                    <input type="text" class="form-control @error('student_number') is-invalid @enderror" id="student_number" name="student_number" value="{{ old('student_number', $student->student_number) }}" placeholder="e.g. 2023-0001">
                                    @error('student_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-5">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
