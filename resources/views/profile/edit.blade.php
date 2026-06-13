@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('User Profile') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container">
    <div class="main-body">
        <div class="row">
            <!-- Left Profile Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center py-4">
                            <img src="{{ asset('assets/images/avatars/avatar-2.png') }}" alt="Profile Avatar" class="rounded-circle p-1 bg-primary" width="110">
                            <div class="mt-3">
                                <h4>{{ $user->name }}</h4>
                                <p class="text-secondary mb-1">{{ $user->employee?->position ?? __('Employee') }}</p>
                                <p class="text-muted font-size-sm mb-0">{{ $user->employee?->department ?? __('No Department') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Profile Forms -->
            <div class="col-lg-8">
                <!-- Update Profile Details -->
                <div class="card">
                    <div class="card-body p-4">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card mt-4">
                    <div class="card-body p-4">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="card mt-4 border-danger">
                    <div class="card-body p-4">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
