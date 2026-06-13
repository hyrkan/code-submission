<x-guest-layout>
    <div class="text-center mb-4">
        <h4>Student Portal Login</h4>
        <p class="text-muted">Please log in with your student credentials.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
            <div class="text-white">{{ session('status') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form class="row g-3" method="POST" action="{{ route('student.login') }}">
        @csrf

        <!-- Email Address -->
        <div class="col-12">
            <label for="inputEmailAddress" class="form-label">Email Address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="inputEmailAddress" name="email" value="{{ old('email') }}" required autofocus placeholder="student@example.com">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Password -->
        <div class="col-12">
            <label for="inputChoosePassword" class="form-label">Enter Password</label>
            <div class="input-group" id="show_hide_password">
                <input type="password" class="form-control border-end-0 @error('password') is-invalid @enderror" id="inputChoosePassword" name="password" required autocomplete="current-password" placeholder="Enter Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="col-md-6">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
            </div>
        </div>
        <div class="col-md-6 text-end">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot Password ?</a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary"><i class="bx bxs-lock-open"></i>Sign in as Student</button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        $(document).ready(function () {
            $("#show_hide_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>
