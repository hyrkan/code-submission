@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Quizzes') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">{{ __('All Quizzes') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Create Quiz') }}</li>
            </ol>
        </nav>
    </div>
</div>

<form method="POST" action="{{ route('quizzes.store') }}" id="quizForm">
    @csrf

    <!-- Quiz Details Card -->
    <div class="card border-top border-0 border-4 border-primary mb-4">
        <div class="card-body p-4">
            <div class="card-title d-flex align-items-center">
                <div><i class="bx bx-brain me-1 font-22 text-primary"></i></div>
                <h5 class="mb-0 text-primary">{{ __('Quiz Details') }}</h5>
            </div>
            <hr>
            <div class="row g-3">
                <!-- Quiz Name -->
                <div class="col-md-8">
                    <label for="name" class="form-label">{{ __('Quiz Name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Python Basics Challenge">
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Language -->
                <div class="col-md-4">
                    <label for="language" class="form-label">{{ __('Programming Language') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('language') is-invalid @enderror" id="language" name="language" required>
                        <option value="python" {{ old('language') == 'python' ? 'selected' : '' }}>Python</option>
                        <option value="java" {{ old('language') == 'java' ? 'selected' : '' }}>Java</option>
                        <option value="javascript" {{ old('language') == 'javascript' ? 'selected' : '' }}>JavaScript</option>
                        <option value="c" {{ old('language') == 'c' ? 'selected' : '' }}>C</option>
                        <option value="cpp" {{ old('language') == 'cpp' ? 'selected' : '' }}>C++</option>
                        <option value="php" {{ old('language') == 'php' ? 'selected' : '' }}>PHP</option>
                    </select>
                    @error('language')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Time Limit -->
                <div class="col-md-4">
                    <label for="time_limit" class="form-label">{{ __('Time Limit (minutes)') }}</label>
                    <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit') }}" min="1" placeholder="e.g. 60">
                    @error('time_limit')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label">{{ __('Description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Describe this quiz or coding challenge...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Year -->
                <div class="col-md-4">
                    <label for="year_id" class="form-label">{{ __('Year') }}</label>
                    <select class="form-select @error('year_id') is-invalid @enderror" id="year_id" name="year_id">
                        <option value="">{{ __('All Years') }}</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}" {{ old('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                    @error('year_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Section -->
                <div class="col-md-4">
                    <label for="section_id" class="form-label">{{ __('Section') }}</label>
                    <select class="form-select @error('section_id') is-invalid @enderror" id="section_id" name="section_id">
                        <option value="">{{ __('All Sections') }}</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" data-year-id="{{ $section->year_id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                    @error('section_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Scheduled At -->
                <div class="col-md-4">
                    <label for="scheduled_at" class="form-label">{{ __('Schedule') }}</label>
                    <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                    @error('scheduled_at')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <!-- Published -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">{{ __('Publish immediately') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Items Card -->
    <div class="card border-top border-0 border-4 border-success mb-4">
        <div class="card-body p-4">
            <div class="card-title d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div><i class="bx bx-code-block me-1 font-22 text-success"></i></div>
                    <h5 class="mb-0 text-success">{{ __('Coding Challenges') }}</h5>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                    <i class="bx bx-plus"></i> {{ __('Add Challenge') }}
                </button>
            </div>
            <hr>

            @error('items')
                <div class="alert alert-danger py-2">{{ $message }}</div>
            @enderror

            <div id="itemsContainer">
                <!-- Items will be added here dynamically -->
            </div>

            <div id="noItemsMsg" class="text-center py-4 text-muted">
                <i class="bx bx-code-block font-48 d-block mb-2"></i>
                <p class="mb-0">{{ __('No challenges added yet. Click "Add Challenge" to begin.') }}</p>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary px-5"><i class="bx bx-save"></i> {{ __('Create Quiz') }}</button>
        <a href="{{ route('quizzes.index') }}" class="btn btn-light px-5">{{ __('Cancel') }}</a>
    </div>
</form>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    var itemIndex = 0;

    function addItem() {
        var num = document.querySelectorAll('.item-card').length + 1;
        var idx = itemIndex;

        var html = ''
        + '<div class="card border mb-3 item-card" data-index="' + idx + '">'
        +   '<div class="card-body p-3">'
        +     '<div class="d-flex align-items-center justify-content-between mb-3">'
        +       '<h6 class="mb-0 text-primary"><i class="bx bx-code-block"></i> Challenge #<span class="item-number">' + num + '</span></h6>'
        +       '<button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Remove Challenge"><i class="bx bx-trash-alt"></i> Remove</button>'
        +     '</div>'
        +     '<div class="row g-3">'
        +       '<div class="col-md-6">'
        +         '<label class="form-label">Challenge Title <span class="text-danger">*</span></label>'
        +         '<input type="text" class="form-control" name="items[' + idx + '][title]" required placeholder="e.g. Reverse a String">'
        +       '</div>'
        +       '<div class="col-md-3">'
        +         '<label class="form-label">Difficulty <span class="text-danger">*</span></label>'
        +         '<select class="form-select" name="items[' + idx + '][difficulty]" required>'
        +           '<option value="easy">Easy</option>'
        +           '<option value="medium" selected>Medium</option>'
        +           '<option value="hard">Hard</option>'
        +         '</select>'
        +       '</div>'
        +       '<div class="col-md-3">'
        +         '<label class="form-label">Points <span class="text-danger">*</span></label>'
        +         '<input type="number" class="form-control" name="items[' + idx + '][points]" required min="0" value="10" placeholder="10">'
        +       '</div>'
        +       '<div class="col-12">'
        +         '<label class="form-label">Problem Description</label>'
        +         '<textarea class="form-control" name="items[' + idx + '][description]" rows="3" placeholder="Describe the coding challenge, requirements and constraints..."></textarea>'
        +       '</div>'
        +       '<div class="col-md-4">'
        +         '<label class="form-label">Sample Input</label>'
        +         '<textarea class="form-control font-monospace" name="items[' + idx + '][sample_input]" rows="2" placeholder="hello"></textarea>'
        +       '</div>'
        +       '<div class="col-md-4">'
        +         '<label class="form-label">Sample Output</label>'
        +         '<textarea class="form-control font-monospace" name="items[' + idx + '][sample_output]" rows="2" placeholder="olleh"></textarea>'
        +       '</div>'
        +       '<div class="col-md-4">'
        +         '<label class="form-label">Expected Output</label>'
        +         '<textarea class="form-control font-monospace" name="items[' + idx + '][expected_output]" rows="2" placeholder="olleh"></textarea>'
        +       '</div>'
        +       '<div class="col-12">'
        +         '<label class="form-label">Coding Standards & Guidelines</label>'
        +         '<textarea class="form-control" name="items[' + idx + '][coding_standards]" rows="3" placeholder="e.g. Use proper variable naming, include comments, follow PEP 8 style guide..."></textarea>'
        +       '</div>'
        +       '<div class="col-12">'
        +         '<label class="form-label">Grading Criteria</label>'
        +         '<textarea class="form-control" name="items[' + idx + '][grading_criteria]" rows="3" placeholder="e.g. Correctness (40%), Code style (20%), Efficiency (20%), Comments (20%)"></textarea>'
        +       '</div>'
        +     '</div>'
        +   '</div>'
        + '</div>';

        document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
        document.getElementById('noItemsMsg').style.display = 'none';
        itemIndex++;
    }

    // Filter sections by year (vanilla JS)
    var yearSelect = document.getElementById('year_id');
    var sectionSelect = document.getElementById('section_id');
    var allSectionOptions = [];
    for (var i = 0; i < sectionSelect.options.length; i++) {
        allSectionOptions.push({
            value: sectionSelect.options[i].value,
            text: sectionSelect.options[i].text,
            yearId: sectionSelect.options[i].getAttribute('data-year-id')
        });
    }

    yearSelect.addEventListener('change', function () {
        var selectedYearId = this.value;
        var currentVal = sectionSelect.value;
        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        for (var j = 0; j < allSectionOptions.length; j++) {
            var opt = allSectionOptions[j];
            if (!selectedYearId || opt.yearId === selectedYearId) {
                var newOpt = document.createElement('option');
                newOpt.value = opt.value;
                newOpt.text = opt.text;
                newOpt.setAttribute('data-year-id', opt.yearId);
                sectionSelect.appendChild(newOpt);
            }
        }
        sectionSelect.value = currentVal;
        if (sectionSelect.value !== currentVal) sectionSelect.value = '';
    });

    // Bind Add Challenge button
    document.getElementById('addItemBtn').addEventListener('click', function () {
        addItem();
    });

    // Auto-add first item on page load
    addItem();

    // Remove Item (delegated)
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-item')) {
            var card = e.target.closest('.item-card');
            card.parentNode.removeChild(card);
            // Re-number items
            var cards = document.querySelectorAll('.item-card');
            for (var i = 0; i < cards.length; i++) {
                cards[i].querySelector('.item-number').textContent = i + 1;
            }
            if (cards.length === 0) {
                document.getElementById('noItemsMsg').style.display = '';
            }
        }
    });
});
</script>
