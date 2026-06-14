@extends('main_layout.master')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ __('Settings') }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('AI Configuration') }}</li>
            </ol>
        </nav>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2">
        <div class="d-flex align-items-center">
            <div class="font-35 text-white"><i class="bx bxs-check-circle"></i></div>
            <div class="ms-3">
                <h6 class="mb-0 text-white">{{ __('Success') }}</h6>
                <div class="text-white">{{ session('success') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Active Provider Selection -->
<div class="card border-top border-0 border-4 border-primary mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <i class="bx bx-bot me-2 font-22 text-primary"></i>
            <h5 class="mb-0 text-primary">{{ __('AI Provider Configuration') }}</h5>
        </div>
        <hr>
        <p class="text-muted">Select the active AI provider and configure API credentials. API keys are stored securely in the database and can be changed at any time.</p>

        <form method="POST" action="{{ route('admin.settings.save-ai') }}" id="aiSettingsForm">
            @csrf

            <!-- Provider Selection -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="ai_provider" class="form-label fw-bold">{{ __('Active AI Provider') }} <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg @error('ai_provider') is-invalid @enderror" id="ai_provider" name="ai_provider" onchange="switchProvider(this.value)">
                        @foreach ($providers as $key => $provider)
                            <option value="{{ $key }}" {{ $currentProvider === $key ? 'selected' : '' }}>{{ $provider['name'] }}</option>
                        @endforeach
                    </select>
                    @error('ai_provider')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div id="providerStatus" class="d-flex align-items-center gap-2">
                        @if ($currentProvider)
                            <span class="badge bg-success text-white" id="statusBadge">
                                <i class="bx bx-check-circle"></i> {{ $providers[$currentProvider]['name'] ?? '' }} Active
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Provider Configuration Cards -->
            @foreach ($providers as $key => $provider)
                <div class="card border mb-3 provider-card" id="provider-{{ $key }}" style="{{ $currentProvider !== $key ? 'display: none;' : '' }}">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">
                                <i class="bx bx-chip text-primary"></i> {{ $provider['name'] }}
                            </h6>
                            @if ($currentProvider === $key)
                                <span class="badge bg-primary text-white">Active</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $provider['description'] }}</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="{{ $key }}_api_key" class="form-label">
                                    <i class="bx bx-key"></i> {{ __('API Key') }}
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error("{$key}_api_key") is-invalid @enderror"
                                           id="{{ $key }}_api_key"
                                           name="{{ $key }}_api_key"
                                           value="{{ $settings[$key]['api_key'] }}"
                                           placeholder="Enter your {{ $provider['name'] }} API key">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="toggleApiKeyVisibility('{{ $key }}_api_key')">
                                        <i class="bx bx-show" id="{{ $key }}_api_key_icon"></i>
                                    </button>
                                </div>
                                @if($key === 'mimo')
                                    <div class="mt-2" id="mimo-balance-container" style="display: none;">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-primary text-white" id="mimo-balance-badge">
                                                <i class="bx bx-wallet"></i> Remaining Credits: <span id="mimo-remaining-credits">Loading...</span>
                                            </span>
                                            <span class="badge bg-secondary text-white" id="mimo-usage-badge">
                                                Usage: <span id="mimo-usage-credits">-</span>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="refreshMimoBalance()">
                                                <i class="bx bx-refresh text-primary"></i> Refresh Balance
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                @error("{$key}_api_key")
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="{{ $key }}_base_url" class="form-label">
                                    <i class="bx bx-link"></i> {{ __('Base URL') }}
                                </label>
                                <input type="url"
                                       class="form-control"
                                       id="{{ $key }}_base_url"
                                       name="{{ $key }}_base_url"
                                       value="{{ $settings[$key]['base_url'] }}"
                                       placeholder="https://api.example.com/v1">
                            </div>
                            <div class="col-md-4">
                                <label for="{{ $key }}_model" class="form-label">
                                    <i class="bx bx-cube"></i> {{ __('Model') }}
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="{{ $key }}_model"
                                       name="{{ $key }}_model"
                                       value="{{ $settings[$key]['model'] }}"
                                       placeholder="model-name">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Actions -->
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bx bx-save"></i> {{ __('Save Settings') }}
                </button>
                <button type="button" class="btn btn-outline-success px-4" id="testBtn">
                    <i class="bx bx-link"></i> {{ __('Test Connection') }}
                </button>
            </div>
        </form>

        <!-- Test Result -->
        <div id="testResult" class="mt-3" style="display: none;">
            <div class="alert" id="testResultAlert" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i id="testResultIcon" class="bx font-20"></i>
                    <div>
                        <strong id="testResultTitle"></strong>
                        <p class="mb-0 small" id="testResultMessage"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Usage Instructions -->
<div class="card mb-4">
    <div class="card-body p-4">
        <h6 class="mb-3"><i class="bx bx-info-circle text-info"></i> {{ __('How to get API Keys') }}</h6>
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="card border h-100">
                    <div class="card-body p-3">
                        <h6 class="text-primary">Xiaomi MiMo Code</h6>
                        <small class="text-muted">Get your API key from the MiMo developer portal. Supports OpenAI-compatible API format.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card border h-100">
                    <div class="card-body p-3">
                        <h6 class="text-primary">Google Gemini Flash</h6>
                        <small class="text-muted">Get your API key from Google AI Studio (aistudio.google.com). Free tier available.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card border h-100">
                    <div class="card-body p-3">
                        <h6 class="text-primary">Anthropic Claude</h6>
                        <small class="text-muted">Get your API key from the Anthropic console (console.anthropic.com).</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card border h-100">
                    <div class="card-body p-3">
                        <h6 class="text-primary">OpenAI</h6>
                        <small class="text-muted">Get your API key from the OpenAI platform (platform.openai.com).</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchProvider(provider) {
        // Hide all provider cards
        document.querySelectorAll('.provider-card').forEach(function(card) {
            card.style.display = 'none';
        });

        // Show the selected provider card
        var selectedCard = document.getElementById('provider-' + provider);
        if (selectedCard) {
            selectedCard.style.display = '';
        }

        // Update status badge
        var badge = document.getElementById('statusBadge');
        if (badge) {
            var providerName = document.getElementById('ai_provider').options[document.getElementById('ai_provider').selectedIndex].text;
            badge.innerHTML = '<i class="bx bx-check-circle"></i> ' + providerName + ' Active';
        }
    }

    function toggleApiKeyVisibility(inputId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(inputId + '_icon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bx bx-hide';
        } else {
            input.type = 'password';
            icon.className = 'bx bx-show';
        }
    }

    document.getElementById('testBtn').addEventListener('click', function(e) {
        e.preventDefault();
        testConnection();
    });

    function testConnection() {
        var btn = document.getElementById('testBtn');
        var provider = document.getElementById('ai_provider').value;
        var resultDiv = document.getElementById('testResult');
        var resultAlert = document.getElementById('testResultAlert');
        var resultIcon = document.getElementById('testResultIcon');
        var resultTitle = document.getElementById('testResultTitle');
        var resultMessage = document.getElementById('testResultMessage');

        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Testing...';
        resultDiv.style.display = 'none';

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            resultDiv.style.display = '';
            resultAlert.className = 'alert alert-danger';
            resultIcon.className = 'bx bx-error font-20 text-danger';
            resultTitle.textContent = 'Error';
            resultMessage.textContent = 'CSRF token not found. Please refresh the page.';
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-link"></i> Test Connection';
            return;
        }

        var keyInput = document.getElementById(provider + '_api_key');
        var urlInput = document.getElementById(provider + '_base_url');
        var modelInput = document.getElementById(provider + '_model');

        var payload = {
            provider: provider,
            api_key: keyInput ? keyInput.value : '',
            base_url: urlInput ? urlInput.value : '',
            model: modelInput ? modelInput.value : ''
        };

        fetch('/settings/ai/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            resultDiv.style.display = '';

            if (data.success) {
                resultAlert.className = 'alert alert-success';
                resultIcon.className = 'bx bx-check-circle font-20 text-success';
                resultTitle.textContent = 'Connection Successful';
                resultMessage.textContent = data.message + (data.response ? ' Response: "' + data.response.substring(0, 100) + '"' : '');
            } else {
                resultAlert.className = 'alert alert-danger';
                resultIcon.className = 'bx bx-error font-20 text-danger';
                resultTitle.textContent = 'Connection Failed';
                resultMessage.textContent = data.message || 'Unknown error occurred.';
            }
        })
        .catch(function(error) {
            resultDiv.style.display = '';
            resultAlert.className = 'alert alert-danger';
            resultIcon.className = 'bx bx-error font-20 text-danger';
            resultTitle.textContent = 'Error';
            resultMessage.textContent = error.message;
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-link"></i> Test Connection';
        });
    }

    function refreshMimoBalance() {
        var keyInput = document.getElementById('mimo_api_key');
        if (!keyInput || !keyInput.value) {
            document.getElementById('mimo-balance-container').style.display = 'none';
            return;
        }

        document.getElementById('mimo-balance-container').style.display = 'block';
        document.getElementById('mimo-remaining-credits').textContent = 'Loading...';
        document.getElementById('mimo-usage-credits').textContent = '-';

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) return;

        fetch('/settings/ai/balance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                provider: 'mimo',
                api_key: keyInput.value
            }),
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(function(res) {
            if (res.success && res.data) {
                var data = res.data;
                var usage = data.usage !== null ? parseFloat(data.usage) : 0;
                var limit = data.limit !== null ? parseFloat(data.limit) : null;
                
                document.getElementById('mimo-usage-credits').textContent = '$' + usage.toFixed(4);
                
                if (limit === null || limit === 0) {
                    document.getElementById('mimo-remaining-credits').textContent = 'Unlimited / Free Tier';
                } else {
                    var remaining = limit - usage;
                    document.getElementById('mimo-remaining-credits').textContent = '$' + remaining.toFixed(4) + ' (Limit: $' + limit.toFixed(2) + ')';
                }
            } else {
                document.getElementById('mimo-remaining-credits').textContent = 'Error loading balance';
            }
        })
        .catch(function(err) {
            document.getElementById('mimo-remaining-credits').textContent = 'Unavailable';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var mimoKeyInput = document.getElementById('mimo_api_key');
        if (mimoKeyInput && mimoKeyInput.value) {
            refreshMimoBalance();
        }
        
        if (mimoKeyInput) {
            mimoKeyInput.addEventListener('blur', function() {
                refreshMimoBalance();
            });
        }
    });
</script>
@endpush