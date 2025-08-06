<x-layout>
    @section('top_title', $pageTitle)
    @section('title',$pageTitle)

@section('content')
<!-- Settings Navigation -->
<x-admin.settings-navigation current="sms-config" />

<div class="row mb-4 my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sms me-2"></i>
                    {{ $pageTitle }}
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-8">
                        <form action="{{ route('admin.settings.sms-config.update') }}" method="POST">
                            @csrf

                            <!-- SMS Configuration -->
                            <div class="mb-4">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-cogs me-2"></i>SMS Gateway Configuration
                                </h5>
                            </div>

                            <div class="row">
                                <!-- SMS Gateway -->
                                <div class="col-md-6 mb-3">
                                    <label for="gateway" class="form-label">
                                        <i class="fas fa-server me-1"></i>SMS Gateway
                                    </label>
                                    <select class="form-select @error('gateway') is-invalid @enderror" 
                                            id="gateway" name="gateway" required>
                                        <option value="twilio" {{ (old('gateway', $smsConfig['gateway'] ?? 'twilio') == 'twilio') ? 'selected' : '' }}>Twilio</option>
                                        <option value="nexmo" {{ (old('gateway', $smsConfig['gateway'] ?? 'twilio') == 'nexmo') ? 'selected' : '' }}>Nexmo (Vonage)</option>
                                        <option value="textlocal" {{ (old('gateway', $smsConfig['gateway'] ?? 'twilio') == 'textlocal') ? 'selected' : '' }}>TextLocal</option>
                                        <option value="msg91" {{ (old('gateway', $smsConfig['gateway'] ?? 'twilio') == 'msg91') ? 'selected' : '' }}>MSG91</option>
                                        <option value="clickatell" {{ (old('gateway', $smsConfig['gateway'] ?? 'twilio') == 'clickatell') ? 'selected' : '' }}>Clickatell</option>
                                        <option value="custom" {{ (old('gateway', $smsConfig['gateway'] ?? 'twilio') == 'custom') ? 'selected' : '' }}>Custom API</option>
                                    </select>
                                    @error('gateway')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Enable/Disable SMS -->
                                <div class="col-md-6 mb-3">
                                    <label for="enabled" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>SMS Enabled
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1" 
                                               {{ old('enabled', $smsConfig['enabled'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enabled">
                                            Enable SMS notifications
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- API Key -->
                                <div class="col-md-6 mb-3">
                                    <label for="api_key" class="form-label">
                                        <i class="fas fa-key me-1"></i>API Key / SID
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('api_key') is-invalid @enderror" 
                                           id="api_key" 
                                           name="api_key" 
                                           value="{{ old('api_key', $smsConfig['api_key'] ?? '') }}" 
                                           placeholder="Enter your API Key or Account SID">
                                    @error('api_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- API Secret -->
                                <div class="col-md-6 mb-3">
                                    <label for="api_secret" class="form-label">
                                        <i class="fas fa-lock me-1"></i>API Secret / Token
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('api_secret') is-invalid @enderror" 
                                               id="api_secret" 
                                               name="api_secret" 
                                               value="{{ old('api_secret', $smsConfig['api_secret'] ?? '') }}" 
                                               placeholder="Enter your API Secret or Auth Token">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('api_secret')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Sender ID -->
                                <div class="col-md-6 mb-3">
                                    <label for="sender_id" class="form-label">
                                        <i class="fas fa-id-card me-1"></i>Sender ID / Name
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('sender_id') is-invalid @enderror" 
                                           id="sender_id" 
                                           name="sender_id" 
                                           value="{{ old('sender_id', $smsConfig['sender_id'] ?? '') }}" 
                                           placeholder="ViewCash" 
                                           maxlength="11">
                                    @error('sender_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Sender ID (max 11 characters) or registered sender name
                                    </small>
                                </div>

                                <!-- From Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="from_number" class="form-label">
                                        <i class="fas fa-phone me-1"></i>From Number
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('from_number') is-invalid @enderror" 
                                           id="from_number" 
                                           name="from_number" 
                                           value="{{ old('from_number', $smsConfig['from_number'] ?? '') }}" 
                                           placeholder="+1234567890">
                                    @error('from_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Phone number in E.164 format (for services like Twilio)
                                    </small>
                                </div>
                            </div>

                            <!-- Test SMS Section -->
                            <div class="mb-4">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-vial me-2"></i>Test SMS Configuration
                                </h5>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="test_phone" class="form-label">
                                        <i class="fas fa-mobile-alt me-1"></i>Test Phone Number
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="test_phone" 
                                           name="test_phone" 
                                           placeholder="+1234567890">
                                    <small class="form-text text-muted">
                                        Enter a phone number to test SMS configuration
                                    </small>
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-info" id="testSmsBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Send Test SMS
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Update SMS Configuration
                                </button>
                                <a href="{{ route('admin.settings.general') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to General Settings
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- SMS Configuration Status -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>SMS Configuration Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Current Gateway:</strong>
                                    <span class="badge bg-primary">{{ ucfirst($smsConfig['gateway'] ?? 'Not Set') }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>SMS Status:</strong>
                                    @if($smsConfig['enabled'] ?? false)
                                        <span class="badge bg-success">Enabled</span>
                                    @else
                                        <span class="badge bg-warning">Disabled</span>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <strong>API Key:</strong>
                                    @if(!empty($smsConfig['api_key']))
                                        <span class="badge bg-success">Configured</span>
                                    @else
                                        <span class="badge bg-danger">Not Set</span>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <strong>API Secret:</strong>
                                    @if(!empty($smsConfig['api_secret']))
                                        <span class="badge bg-success">Configured</span>
                                    @else
                                        <span class="badge bg-danger">Not Set</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- SMS Gateway Documentation -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-book me-2"></i>Gateway Documentation
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="gatewayInfo">
                                    <div class="gateway-info" data-gateway="twilio">
                                        <h6>Twilio Setup:</h6>
                                        <ul class="small">
                                            <li>API Key: Account SID</li>
                                            <li>API Secret: Auth Token</li>
                                            <li>From Number: Your Twilio phone number</li>
                                        </ul>
                                        <a href="https://www.twilio.com/docs" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> Documentation
                                        </a>
                                    </div>
                                    <div class="gateway-info" data-gateway="nexmo" style="display: none;">
                                        <h6>Nexmo (Vonage) Setup:</h6>
                                        <ul class="small">
                                            <li>API Key: Your Nexmo API key</li>
                                            <li>API Secret: Your Nexmo API secret</li>
                                            <li>Sender ID: Your brand name (if approved)</li>
                                        </ul>
                                        <a href="https://developer.nexmo.com/" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> Documentation
                                        </a>
                                    </div>
                                    <div class="gateway-info" data-gateway="msg91" style="display: none;">
                                        <h6>MSG91 Setup:</h6>
                                        <ul class="small">
                                            <li>API Key: Your MSG91 auth key</li>
                                            <li>Sender ID: Your approved sender ID</li>
                                            <li>Route: Promotional/Transactional</li>
                                        </ul>
                                        <a href="https://docs.msg91.com/" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> Documentation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('api_secret');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Gateway selection change
    const gatewaySelect = document.getElementById('gateway');
    const gatewayInfos = document.querySelectorAll('.gateway-info');
    
    if (gatewaySelect) {
        gatewaySelect.addEventListener('change', function() {
            const selectedGateway = this.value;
            
            gatewayInfos.forEach(function(info) {
                if (info.getAttribute('data-gateway') === selectedGateway) {
                    info.style.display = 'block';
                } else {
                    info.style.display = 'none';
                }
            });
        });
        
        // Trigger change event on page load
        gatewaySelect.dispatchEvent(new Event('change'));
    }
    
    // Test SMS functionality
    const testSmsBtn = document.getElementById('testSmsBtn');
    if (testSmsBtn) {
        testSmsBtn.addEventListener('click', function() {
            const testPhone = document.getElementById('test_phone').value;
            
            if (!testPhone) {
                alert('Please enter a phone number to test.');
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            
            // Here you would make an AJAX call to test SMS
            fetch('{{ route("admin.settings.sms-config.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    test_sms: true,
                    test_phone: testPhone
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Test SMS sent successfully!');
                } else {
                    alert('Failed to send test SMS: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error sending test SMS: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Test SMS';
            });
        });
    }
});
</script>
@endsection
</x-layout>
