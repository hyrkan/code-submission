<x-guest-layout>
    <form class="row g-3" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div class="col-sm-6">
            <label for="inputFirstName" class="form-label">First Name</label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="inputFirstName" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="John">
            @error('first_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="col-sm-6">
            <label for="inputLastName" class="form-label">Last Name</label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="inputLastName" name="last_name" value="{{ old('last_name') }}" required placeholder="Doe">
            @error('last_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Department -->
        <div class="col-12">
            <label for="inputDepartment" class="form-label">Department (Optional)</label>
            <input type="text" class="form-control @error('department') is-invalid @enderror" id="inputDepartment" name="department" value="{{ old('department') }}" placeholder="Engineering">
            @error('department')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Position -->
        <div class="col-12">
            <label for="inputPosition" class="form-label">Position (Optional)</label>
            <input type="text" class="form-control @error('position') is-invalid @enderror" id="inputPosition" name="position" value="{{ old('position') }}" placeholder="Software Engineer">
            @error('position')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="col-12">
            <label for="inputEmailAddress" class="form-label">Email Address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="inputEmailAddress" name="email" value="{{ old('email') }}" required placeholder="email@example.com">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Password -->
        <div class="col-12">
            <label for="inputChoosePassword" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="inputChoosePassword" name="password" required autocomplete="new-password" placeholder="Choose Password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="col-12">
            <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="inputConfirmPassword" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary"><i class="bx bx-user"></i>Sign Up</button>
            </div>
        </div>

        <div class="col-12">
            <div class="text-center">
                <p class="mb-0">Already registered? <a href="{{ route('login') }}">Log in here</a></p>
            </div>
        </div>
    </form>
</x-guest-layout>
