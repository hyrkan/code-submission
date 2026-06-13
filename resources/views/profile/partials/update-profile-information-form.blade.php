<section>
    <header class="mb-4">
        <h5 class="mb-1 text-dark">{{ __('Profile Information') }}</h5>
        <p class="mb-0 text-muted small">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="row g-3">
        @csrf
        @method('patch')

        <!-- First Name -->
        <div class="col-md-6">
            <label for="first_name" class="form-label">{{ __('First Name') }}</label>
            <input id="first_name" name="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->employee?->first_name) }}" required autofocus autocomplete="given-name">
            @error('first_name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="col-md-6">
            <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
            <input id="last_name" name="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->employee?->last_name) }}" required autocomplete="family-name">
            @error('last_name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Department -->
        <div class="col-md-6">
            <label for="department" class="form-label">{{ __('Department') }}</label>
            <input id="department" name="department" type="text" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $user->employee?->department) }}">
            @error('department')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Position -->
        <div class="col-md-6">
            <label for="position" class="form-label">{{ __('Position') }}</label>
            <input id="position" name="position" type="text" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $user->employee?->position) }}">
            @error('position')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="col-12">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-sm text-dark">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline text-sm">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2" role="alert">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Save Button -->
        <div class="col-12 d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small"><i class="bx bx-check-circle"></i> {{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
