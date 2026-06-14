@extends('main_layout.master')

@push('styles')
<style>
    /* Override page wrapper to allow full-width editor */
    .page-content {
        padding-bottom: 0 !important;
    }

    .quiz-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 190px);
    }

    .challenge-nav {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .challenge-nav .btn {
        min-width: 42px;
    }

    .challenge-panel {
        flex: 1;
        display: flex;
        gap: 1rem;
        min-height: 0;
        margin-top: 1rem;
    }

    .problem-description {
        width: 40%;
        overflow-y: auto;
        background: #fff;
        border-radius: 0.5rem;
        padding: 1.25rem;
        border: 1px solid #dee2e6;
    }

    .editor-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .editor-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 1rem;
        background: #1e1e1e;
        border-radius: 0.5rem 0.5rem 0 0;
        border: 1px solid #333;
        border-bottom: none;
    }

    .editor-wrapper {
        flex: 1;
        min-height: 400px;
        border: 1px solid #333;
        border-radius: 0 0 0.5rem 0.5rem;
        overflow: hidden;
    }

    /* Active challenge styling */
    .challenge-content {
        display: none;
    }
    .challenge-content.active {
        display: block;
    }

    /* Language badge colors */
    .lang-python { color: #3776ab; }
    .lang-java { color: #f89820; }
    .lang-javascript { color: #f7df1e; }
    .lang-c { color: #555555; }
    .lang-cpp { color: #00599c; }

    /* Timer styling */
    .quiz-timer {
        font-family: 'Consolas', monospace;
        font-size: 1.25rem;
        font-weight: bold;
    }
    .quiz-timer.warning { color: #ffc107 !important; }
    .quiz-timer.danger {
        color: #dc3545 !important;
        animation: pulse 1s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Description styles */
    .problem-description h6 { color: #333; font-weight: 600; }
    .problem-description pre {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #e9ecef;
    }
    .problem-description code { font-size: 0.875rem; }
    .difficulty-easy   { color: #198754; font-weight: 600; }
    .difficulty-medium { color: #ffc107; font-weight: 600; }
    .difficulty-hard   { color: #dc3545; font-weight: 600; }

    /* Submit Quiz button pulse */
    #submitQuizBtn.ready {
        animation: btnPulse 2s ease-in-out infinite;
    }
    @keyframes btnPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(25,135,84,0.5); }
        50%       { box-shadow: 0 0 0 8px rgba(25,135,84,0); }
    }

    /* Responsive */
    @media (max-width: 991px) {
        .challenge-panel { flex-direction: column; }
        .problem-description { width: 100%; max-height: 300px; }
    }

    /* ── Quiz Completed Overlay ─────────────────────────────── */
    .quiz-completed-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(10, 15, 30, 0.88);
        backdrop-filter: blur(10px);
        align-items: center;
        justify-content: center;
    }
    .quiz-completed-overlay.show-overlay {
        display: flex;
        animation: fadeInOverlay 0.4s ease forwards;
    }
    @keyframes fadeInOverlay {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    .quiz-completed-card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 3rem 2.5rem;
        max-width: 540px;
        width: 90%;
        text-align: center;
        box-shadow: 0 30px 70px rgba(0,0,0,0.5);
        animation: slideUpCard 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards;
    }
    @keyframes slideUpCard {
        from { transform: translateY(40px) scale(0.95); opacity: 0; }
        to   { transform: translateY(0)    scale(1);    opacity: 1; }
    }
    .quiz-completed-icon {
        width: 90px; height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.5rem;
        animation: popIn 0.6s 0.2s cubic-bezier(0.34,1.56,0.64,1) both;
    }
    @keyframes popIn {
        from { transform: scale(0); }
        to   { transform: scale(1); }
    }
    .quiz-completed-icon i { font-size: 3rem; color: #fff; }
    .quiz-completed-card h3 { font-weight: 700; color: #1a1a2e; margin-bottom: 0.75rem; }
    .quiz-completed-card .subtitle {
        color: #6c757d; font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.6;
    }
    .quiz-completed-card .stats-row {
        display: flex; justify-content: center; gap: 1.5rem;
        margin-bottom: 2rem; padding: 1rem;
        background: #f8f9fa; border-radius: 0.75rem;
    }
    .quiz-completed-card .stat-item { text-align: center; }
    .quiz-completed-card .stat-value { font-size: 1.5rem; font-weight: 700; color: #28a745; }
    .quiz-completed-card .stat-label {
        font-size: 0.75rem; color: #6c757d;
        text-transform: uppercase; letter-spacing: 0.05em;
    }

    /* Submission progress inside modal */
    .submit-progress-item {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.5rem 0.75rem; border-radius: 0.5rem;
        background: #f8f9fa; margin-bottom: 0.4rem;
        font-size: 0.875rem;
    }
    .submit-progress-item .spi-icon { width: 22px; text-align: center; }
    .submit-progress-item.done   { background: #d1fae5; }
    .submit-progress-item.error  { background: #fee2e2; }
    .submit-progress-item.active { background: #dbeafe; }
</style>
@endpush

@section('content')
{{-- ── Quiz Header ──────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <div class="breadcrumb-title pe-3">
            <a href="{{ route('student.dashboard') }}" class="text-decoration-none text-muted">
                <i class="bx bx-arrow-back"></i> Back to Dashboard
            </a>
        </div>
        <h4 class="mb-0 mt-1">{{ $quiz->name }}</h4>
        @if ($quiz->description)
            <small class="text-muted">{{ $quiz->description }}</small>
        @endif
    </div>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        {{-- Timer --}}
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-time text-primary font-20"></i>
            @if ($quiz->time_limit)
                <span class="quiz-timer text-primary" id="quizTimer" data-minutes="{{ $quiz->time_limit }}">
                    {{ $quiz->time_limit }}:00
                </span>
            @else
                <span class="quiz-timer text-secondary">No Limit</span>
            @endif
        </div>
        {{-- Points --}}
        <span class="badge bg-info text-white fs-6">{{ $quiz->total_points }} pts</span>
        {{-- Progress --}}
        <span class="badge bg-secondary text-white" id="progressBadge">
            0/{{ $quiz->items->count() }} written
        </span>
        {{-- Single Submit Quiz button --}}
        <button class="btn btn-success" id="submitQuizBtn" title="Submit all challenges">
            <i class="bx bx-send"></i> Submit Quiz
        </button>
    </div>
</div>

{{-- ── Challenge Navigation Tabs ───────────────────────── --}}
<div class="challenge-nav mb-2" id="challengeNav">
    @foreach ($quiz->items as $item)
        <button class="btn btn-sm {{ $loop->first ? 'btn-primary' : 'btn-outline-secondary' }} challenge-tab"
                data-challenge="{{ $loop->index }}"
                data-item-id="{{ $item->id }}"
                title="{{ $item->title }}">
            <i class="bx bx-code-block"></i> #{{ $loop->iteration }}
            <span class="badge bg-{{ $item->difficulty === 'easy' ? 'success' : ($item->difficulty === 'medium' ? 'warning' : 'danger') }} text-white ms-1"
                  style="font-size: 0.65rem;">
                {{ $item->points }}pts
            </span>
        </button>
    @endforeach
</div>

{{-- ── Quiz Content Area ────────────────────────────────── --}}
<div class="quiz-container">
    @foreach ($quiz->items as $item)
        <div class="challenge-content {{ $loop->first ? 'active' : '' }}"
             id="challenge-{{ $loop->index }}"
             data-item-id="{{ $item->id }}">
            <div class="challenge-panel">

                {{-- Problem Description --}}
                <div class="problem-description">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">
                            <i class="bx bx-code-block text-primary"></i>
                            Challenge #{{ $loop->iteration }}
                        </h5>
                        <span class="difficulty-{{ $item->difficulty }}">{{ ucfirst($item->difficulty) }}</span>
                    </div>
                    <h6 class="mb-2">{{ $item->title }}</h6>

                    @if ($item->description)
                        <div class="mb-3">
                            <p class="mb-0">{!! nl2br(e($item->description)) !!}</p>
                        </div>
                    @endif

                    @if ($item->sample_input)
                        <div class="mb-2">
                            <small class="text-muted fw-bold d-block mb-1">
                                <i class="bx bx-right-arrow-circle"></i> Sample Input
                            </small>
                            <pre class="mb-0"><code>{{ $item->sample_input }}</code></pre>
                        </div>
                    @endif

                    @if ($item->sample_output)
                        <div class="mb-2">
                            <small class="text-muted fw-bold d-block mb-1">
                                <i class="bx bx-left-arrow-circle"></i> Sample Output
                            </small>
                            <pre class="mb-0"><code>{{ $item->sample_output }}</code></pre>
                        </div>
                    @endif

                    @if ($item->expected_output)
                        <div class="mb-2">
                            <small class="text-muted fw-bold d-block mb-1">
                                <i class="bx bx-check-circle"></i> Expected Output
                            </small>
                            <pre class="mb-0"><code>{{ $item->expected_output }}</code></pre>
                        </div>
                    @endif

                    @if ($item->coding_standards)
                        <div class="mb-2">
                            <small class="text-muted fw-bold d-block mb-1">
                                <i class="bx bx-check-shield"></i> Coding Standards &amp; Guidelines
                            </small>
                            <p class="mb-0 ps-2 small">{!! nl2br(e($item->coding_standards)) !!}</p>
                        </div>
                    @endif

                    @if ($item->grading_criteria)
                        <div class="mb-0">
                            <small class="text-muted fw-bold d-block mb-1">
                                <i class="bx bx-star"></i> Grading Criteria
                            </small>
                            <p class="mb-0 ps-2 small">{!! nl2br(e($item->grading_criteria)) !!}</p>
                        </div>
                    @endif

                    <div class="mt-3 pt-2 border-top">
                        <small class="text-muted">
                            <i class="bx bx-trophy"></i> Points: <strong>{{ $item->points }}</strong>
                        </small>
                    </div>
                </div>

                {{-- Editor Area --}}
                <div class="editor-area">
                    <div class="editor-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <label class="text-light small mb-0">Language:</label>
                            <span class="badge bg-dark text-white">
                                {{ $quiz->language === 'cpp' ? 'C++' : ucfirst($quiz->language) }}
                            </span>
                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.7rem;">
                                <i class="bx bx-lock"></i> Copy/Paste Disabled
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-light reset-btn"
                                    data-challenge="{{ $loop->index }}" title="Reset Code">
                                <i class="bx bx-reset"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="editor-wrapper" id="editor-{{ $loop->index }}"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ── Submit Quiz Modal ────────────────────────────────── --}}
<div class="modal fade" id="submitQuizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-send text-success"></i> Submit Quiz</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="modalCloseBtn"></button>
            </div>
            <div class="modal-body" id="submitModalBody">
                {{-- Confirmation view --}}
                <div id="submitConfirmView">
                    <p class="mb-2">You are about to submit <strong>all {{ $quiz->items->count() }} challenge(s)</strong> for <strong>{{ $quiz->name }}</strong>.</p>
                    <div class="alert alert-danger py-2 mb-0 d-flex align-items-center gap-2">
                        <i class="bx bx-error-circle fs-5"></i>
                        <span><strong>This cannot be undone.</strong> Retakes are not allowed once submitted.</span>
                    </div>
                </div>
                {{-- Progress view (shown while submitting) --}}
                <div id="submitProgressView" style="display:none;">
                    <p class="text-muted small mb-3">Submitting your answers and running AI analysis…</p>
                    <div id="submitProgressList"></div>
                </div>
            </div>
            <div class="modal-footer" id="submitModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmSubmitAllBtn">
                    <i class="bx bx-send"></i> Submit All
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- ── Quiz Completed Overlay ───────────────────────────── --}}
<div class="quiz-completed-overlay" id="quizCompletedOverlay">
    <div class="quiz-completed-card">
        <div class="quiz-completed-icon">
            <i class="bx bx-check-double"></i>
        </div>
        <h3>Quiz Submitted!</h3>
        <p class="subtitle">
            You have submitted all <strong>{{ $quiz->items->count() }}</strong> challenge(s).<br>
            Your code is being analyzed by AI. <strong>Retakes are not allowed.</strong><br>
            Please wait for your instructor to release the results.
        </p>
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-value">{{ $quiz->items->count() }}</div>
                <div class="stat-label">Challenges</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $quiz->total_points }}</div>
                <div class="stat-label">Total Points</div>
            </div>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-success btn-lg w-100">
            <i class="bx bx-home"></i> Back to Dashboard
        </a>
    </div>
</div>

@push('scripts')
@vite(['resources/js/monaco-editor.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const challengeTabs      = document.querySelectorAll('.challenge-tab');
    const challengeContents  = document.querySelectorAll('.challenge-content');
    const resetBtns          = document.querySelectorAll('.reset-btn');
    const totalChallenges    = {{ $quiz->items->count() }};
    const quizLanguage       = '{{ $quiz->language }}';
    const submitUrl          = '/student/quizzes/{{ $quiz->id }}/submit';
    const csrfToken          = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    // Map item index → quiz_item_id
    const itemIds = {
        @foreach ($quiz->items as $item)
        {{ $loop->index }}: '{{ $item->id }}',
        @endforeach
    };

    const answeredChallenges = new Set();
    let activeChallenge = 0;

    // Starter code templates
    const starterCode = {
        python:     `# Write your solution here\n\ndef main():\n    pass\n\nif __name__ == "__main__":\n    main()\n`,
        java:       `public class Solution {\n    public static void main(String[] args) {\n        // Write your solution here\n    }\n}\n`,
        javascript: `// Write your solution here\n\nfunction main() {\n    \n}\n\nmain();\n`,
        c:          `#include <stdio.h>\n\nint main() {\n    // Write your solution here\n    return 0;\n}\n`,
        cpp:        `#include <iostream>\nusing namespace std;\n\nint main() {\n    // Write your solution here\n    return 0;\n}\n`,
        php:        `${'<?' + 'php'}\n\n// Write your solution here\n\n`,
    };

    const monacoLanguageMap = {
        python: 'python', java: 'java', javascript: 'javascript',
        c: 'c', cpp: 'cpp', php: 'php',
    };

    // ── Initialize Monaco editors ───────────────────────────
    function initializeEditors() {
        if (typeof window.MonacoEditorManager === 'undefined') {
            setTimeout(initializeEditors, 100);
            return;
        }
        const monacoLang = monacoLanguageMap[quizLanguage] || 'python';

        for (let i = 0; i < totalChallenges; i++) {
            const editor = window.MonacoEditorManager.create(
                `editor-${i}`,
                monacoLang,
                starterCode[quizLanguage] || starterCode.python,
                { minimap: { enabled: true }, height: '100%' }
            );

            if (editor) {
                // ── Disable copy / paste / cut ─────────────
                editor.onKeyDown((e) => {
                    const ctrlOrMeta = e.ctrlKey || e.metaKey;
                    // Block Ctrl+C, Ctrl+V, Ctrl+X
                    if (ctrlOrMeta && (e.keyCode === 33 || e.keyCode === 52 || e.keyCode === 54)) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });

                // Also intercept clipboard events on the DOM node
                const domNode = editor.getDomNode();
                if (domNode) {
                    ['copy','cut','paste'].forEach(evt => {
                        domNode.addEventListener(evt, (e) => { e.preventDefault(); e.stopPropagation(); }, true);
                    });
                }

                // Track changes for progress badge
                editor.onDidChangeModelContent(() => {
                    const value       = editor.getValue().trim();
                    const defaultCode = (starterCode[quizLanguage] || starterCode.python).trim();
                    if (value && value !== defaultCode) {
                        answeredChallenges.add(i);
                    } else {
                        answeredChallenges.delete(i);
                    }
                    updateProgress();
                });
            }
        }
    }
    initializeEditors();

    // ── Tab switching ────────────────────────────────────────
    challengeTabs.forEach((tab) => {
        tab.addEventListener('click', function () {
            switchChallenge(parseInt(this.dataset.challenge));
        });
    });

    function switchChallenge(index) {
        activeChallenge = index;
        challengeTabs.forEach((tab, i) => {
            tab.classList.toggle('btn-primary', i === index);
            tab.classList.toggle('btn-outline-secondary', i !== index);
        });
        challengeContents.forEach((c, i) => c.classList.toggle('active', i === index));
        if (window.MonacoEditorManager?.editors[`editor-${index}`]) {
            setTimeout(() => window.MonacoEditorManager.editors[`editor-${index}`].layout(), 50);
        }
    }

    // ── Reset button ─────────────────────────────────────────
    resetBtns.forEach((btn) => {
        btn.addEventListener('click', function () {
            const index = parseInt(this.dataset.challenge);
            if (window.MonacoEditorManager) {
                window.MonacoEditorManager.setValue(`editor-${index}`, starterCode[quizLanguage] || starterCode.python);
            }
            answeredChallenges.delete(index);
            updateProgress();
        });
    });

    // ── Submit Quiz button → open modal ──────────────────────
    document.getElementById('submitQuizBtn').addEventListener('click', () => {
        // Reset modal to confirm view
        document.getElementById('submitConfirmView').style.display  = '';
        document.getElementById('submitProgressView').style.display = 'none';
        document.getElementById('submitProgressList').innerHTML      = '';
        document.getElementById('confirmSubmitAllBtn').style.display = '';
        document.getElementById('modalCloseBtn').disabled            = false;
        const cancelBtn = document.querySelector('#submitQuizModal .btn-secondary');
        if (cancelBtn) cancelBtn.style.display = '';

        new bootstrap.Modal(document.getElementById('submitQuizModal')).show();
    });

    // ── Confirm Submit All ────────────────────────────────────
    document.getElementById('confirmSubmitAllBtn').addEventListener('click', async function () {
        // Switch to progress view
        document.getElementById('submitConfirmView').style.display  = 'none';
        document.getElementById('submitProgressView').style.display = '';
        document.getElementById('confirmSubmitAllBtn').style.display = 'none';
        document.getElementById('modalCloseBtn').disabled            = true;
        const cancelBtn = document.querySelector('#submitQuizModal .btn-secondary');
        if (cancelBtn) cancelBtn.style.display = 'none';

        const progressList = document.getElementById('submitProgressList');
        let allOk = true;

        // Build progress rows
        for (let i = 0; i < totalChallenges; i++) {
            const row = document.createElement('div');
            row.className = 'submit-progress-item';
            row.id = `spi-${i}`;
            row.innerHTML = `
                <span class="spi-icon"><i class="bx bx-time-five text-muted"></i></span>
                <span>Challenge #${i + 1}</span>
                <span class="ms-auto text-muted small spi-status">Pending…</span>`;
            progressList.appendChild(row);
        }

        // Submit each item sequentially
        for (let i = 0; i < totalChallenges; i++) {
            const row       = document.getElementById(`spi-${i}`);
            const iconEl    = row.querySelector('.spi-icon');
            const statusEl  = row.querySelector('.spi-status');

            // Mark active
            row.classList.add('active');
            iconEl.innerHTML  = '<i class="bx bx-loader-alt bx-spin text-primary"></i>';
            statusEl.textContent = 'Submitting…';

            const code     = window.MonacoEditorManager ? window.MonacoEditorManager.getValue(`editor-${i}`) : '';
            const itemId   = itemIds[i];

            try {
                const response = await fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quiz_item_id: itemId, code: code, language: quizLanguage }),
                });
                const data = await response.json();

                row.classList.remove('active');
                if (data.success) {
                    row.classList.add('done');
                    iconEl.innerHTML   = '<i class="bx bx-check-circle text-success"></i>';
                    statusEl.textContent = 'Submitted ✓';
                    statusEl.className = 'ms-auto small text-success fw-semibold spi-status';
                    // Mark tab with checkmark
                    const tab = document.querySelector(`.challenge-tab[data-challenge="${i}"]`);
                    if (tab && !tab.querySelector('.submitted-check')) {
                        const chk = document.createElement('span');
                        chk.className = 'submitted-check ms-1';
                        chk.innerHTML = '<i class="bx bx-check text-success"></i>';
                        tab.appendChild(chk);
                    }
                } else {
                    row.classList.add('error');
                    iconEl.innerHTML   = '<i class="bx bx-x-circle text-danger"></i>';
                    statusEl.textContent = data.message || 'Failed';
                    statusEl.className = 'ms-auto small text-danger fw-semibold spi-status';
                    allOk = false;
                }
            } catch (err) {
                row.classList.remove('active');
                row.classList.add('error');
                iconEl.innerHTML   = '<i class="bx bx-x-circle text-danger"></i>';
                statusEl.textContent = 'Error: ' + err.message;
                statusEl.className = 'ms-auto small text-danger fw-semibold spi-status';
                allOk = false;
            }
        }

        // Close modal and show completed overlay
        setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('submitQuizModal'))?.hide();
            setTimeout(showQuizCompleted, 400);
        }, 800);
    });

    // ── Show completed overlay ───────────────────────────────
    function showQuizCompleted() {
        const overlay = document.getElementById('quizCompletedOverlay');
        if (overlay) overlay.classList.add('show-overlay');
    }

    // ── Progress badge ───────────────────────────────────────
    function updateProgress() {
        const badge = document.getElementById('progressBadge');
        const n = answeredChallenges.size;
        badge.textContent = `${n}/${totalChallenges} written`;

        badge.classList.remove('bg-secondary', 'bg-warning', 'bg-success');
        if (n === totalChallenges)     badge.classList.add('bg-success');
        else if (n > 0)                badge.classList.add('bg-warning');
        else                           badge.classList.add('bg-secondary');

        // Pulse the submit button when all items have code
        const submitBtn = document.getElementById('submitQuizBtn');
        submitBtn.classList.toggle('ready', n === totalChallenges);
    }

    // ── Timer ────────────────────────────────────────────────
    const timerEl = document.getElementById('quizTimer');
    if (timerEl && timerEl.dataset.minutes) {
        let totalSeconds = parseInt(timerEl.dataset.minutes) * 60;
        const timerInterval = setInterval(() => {
            totalSeconds--;
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                timerEl.textContent = '00:00';
                timerEl.classList.add('danger');
                // Auto-trigger submit
                document.getElementById('submitQuizBtn').click();
                return;
            }
            const m = Math.floor(totalSeconds / 60);
            const s = totalSeconds % 60;
            timerEl.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            if (totalSeconds <= 60) {
                timerEl.classList.add('danger');
                timerEl.classList.remove('warning');
            } else if (totalSeconds <= 300) {
                timerEl.classList.add('warning');
                timerEl.classList.remove('danger');
            }
        }, 1000);
    }
});
</script>
@endpush