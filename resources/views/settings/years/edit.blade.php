@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Settings') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('years.index') }}">{{ __('Years') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Year') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mx-auto">
        <div class="card border-top border-0 border-4 border-primary">
            <div class="card-body p-5">
                <div class="card-title d-flex align-items-center">
                    <div><i class="bx bx-calendar me-1 font-22 text-primary"></i></div>
                    <h5 class="mb-0 text-primary">{{ __('Edit Year') }}</h5>
                </div>
                <hr>
                <form class="row g-3" method="POST" action="{{ route('years.update', $year->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Year Name -->
                    <div class="col-12">
                        <label for="name" class="form-label">{{ __('Year Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $year->name) }}" required placeholder="e.g. 1st Year, 2nd Year">
                        @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-5">{{ __('Update Year') }}</button>
                        <a href="{{ route('years.index') }}" class="btn btn-light px-5 ms-2">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection