<x-guest-layout>
    <div class="text-center mb-4">
        <h4>Student Portal Sign Up</h4>
        <p class="text-muted">Create your student account to get started.</p>
    </div>

    <form class="row g-3" method="POST" action="{{ route('student.register') }}">
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

        <!-- Student Number -->
        <div class="col-12">
            <label for="inputStudentNumber" class="form-label">Student Number</label>
            <input type="text" class="form-control @error('student_number') is-invalid @enderror" id="inputStudentNumber" name="student_number" value="{{ old('student_number') }}" required placeholder="2024-0001">
            @error('student_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Course -->
        <div class="col-sm-8">
            <label for="inputCourse" class="form-label">Course</label>
            <input type="text" class="form-control @error('course') is-invalid @enderror" id="inputCourse" name="course" value="{{ old('course') }}" required placeholder="BS Computer Science">
            @error('course')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Year -->
        <div class="col-sm-4">
            <label for="inputYear" class="form-label">Year</label>
            <select class="form-select @error('year_id') is-invalid @enderror" id="inputYear" name="year_id" required>
                <option value="">Select Year...</option>
                @foreach ($years as $year)
                    <option value="{{ $year->id }}" {{ old('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                @endforeach
            </select>
            @error('year_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Section (hidden by default, revealed when year is selected) -->
        <div class="col-12" id="sectionWrapper" style="display: none;">
            <label for="inputSection" class="form-label">Section</label>
            <select class="form-select @error('section_id') is-invalid @enderror" id="inputSection" name="section_id" required>
                <option value="">Select Section...</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" data-year-id="{{ $section->year_id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                @endforeach
            </select>
            @error('section_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="col-12">
            <label for="inputEmailAddress" class="form-label">Email Address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="inputEmailAddress" name="email" value="{{ old('email') }}" required placeholder="student@example.com">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Password -->
        <div class="col-12">
            <label for="inputChoosePassword" class="form-label">Password</label>
            <div class="input-group" id="show_hide_password">
                <input type="password" class="form-control border-end-0 @error('password') is-invalid @enderror" id="inputChoosePassword" name="password" required autocomplete="new-password" placeholder="Choose Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="col-12">
            <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
            <div class="input-group" id="show_hide_confirm_password">
                <input type="password" class="form-control border-end-0" id="inputConfirmPassword" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary"><i class="bx bx-user"></i>Sign Up as Student</button>
            </div>
        </div>

        <div class="col-12">
            <div class="text-center">
                <p class="mb-0">Already have an account? <a href="{{ route('student.login') }}">Log in here</a></p>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        $(document).ready(function () {
            // Store all section options for filtering
            var allSectionOptions = $('#inputSection option').clone();

            // Filter sections based on selected year and show/hide section field
            $('#inputYear').on('change', function () {
                var selectedYearId = $(this).val();
                var currentSectionVal = $('#inputSection').val();

                $('#inputSection').empty();
                $('#inputSection').append('<option value="">Select Section...</option>');

                if (selectedYearId) {
                    // Show section dropdown and filter options by selected year
                    $('#sectionWrapper').show();
                    allSectionOptions.each(function () {
                        var optionYearId = $(this).data('year-id');
                        if (optionYearId == selectedYearId) {
                            $('#inputSection').append($(this).clone());
                        }
                    });

                    // Restore previously selected value if still visible
                    if (currentSectionVal) {
                        $('#inputSection').val(currentSectionVal);
                        if ($('#inputSection').val() !== currentSectionVal) {
                            $('#inputSection').val('');
                        }
                    }
                } else {
                    // Hide section dropdown when no year is selected
                    $('#sectionWrapper').hide();
                    $('#inputSection').val('');
                }
            });

            // Trigger filter on page load if year is already selected
            if ($('#inputYear').val()) {
                $('#inputYear').trigger('change');
                @if(old('section_id'))
                    $('#inputSection').val('{{ old('section_id') }}');
                @endif
            }

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
            $("#show_hide_confirm_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_confirm_password input').attr("type") == "text") {
                    $('#show_hide_confirm_password input').attr('type', 'password');
                    $('#show_hide_confirm_password i').addClass("bx-hide");
                    $('#show_hide_confirm_password i').removeClass("bx-show");
                } else if ($('#show_hide_confirm_password input').attr("type") == "password") {
                    $('#show_hide_confirm_password input').attr('type', 'text');
                    $('#show_hide_confirm_password i').removeClass("bx-hide");
                    $('#show_hide_confirm_password i').addClass("bx-show");
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>