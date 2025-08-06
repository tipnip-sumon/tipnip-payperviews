<x-layout>
    @section('top_title', $pageTitle)
    @section('title',$pageTitle)

@section('content')
<!-- Settings Navigation -->
<x-admin.settings-navigation current="security" /> 

<div class="row mb-4 my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
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

                <form action="{{ route('admin.settings.security.update') }}" method="POST">
                    @csrf
                    
                    <!-- Authentication Security -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2">
                            <i class="fas fa-lock me-2"></i>Authentication Security
                        </h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_login_attempts" class="form-label">
                                <i class="fas fa-exclamation-triangle me-1"></i>Max Login Attempts
                            </label>
                            <input type="number" 
                                   class="form-control @error('max_login_attempts') is-invalid @enderror" 
                                   id="max_login_attempts" 
                                   name="max_login_attempts" 
                                   value="{{ old('max_login_attempts', $securitySettings['max_login_attempts'] ?? 5) }}" 
                                   min="1" max="10" required>
                            @error('max_login_attempts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Number of failed login attempts before account lockout</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lockout_duration" class="form-label">
                                <i class="fas fa-clock me-1"></i>Lockout Duration (minutes)
                            </label>
                            <input type="number" 
                                   class="form-control @error('lockout_duration') is-invalid @enderror" 
                                   id="lockout_duration" 
                                   name="lockout_duration" 
                                   value="{{ old('lockout_duration', $securitySettings['lockout_duration'] ?? 15) }}" 
                                   min="1" max="1440" required>
                            @error('lockout_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">How long to lock accounts after failed attempts</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="session_timeout" class="form-label">
                                <i class="fas fa-hourglass-half me-1"></i>Session Timeout (minutes)
                            </label>
                            <input type="number" 
                                   class="form-control @error('session_timeout') is-invalid @enderror" 
                                   id="session_timeout" 
                                   name="session_timeout" 
                                   value="{{ old('session_timeout', $securitySettings['session_timeout'] ?? 120) }}" 
                                   min="5" max="1440" required>
                            @error('session_timeout')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Auto-logout after inactivity</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="login_history_days" class="form-label">
                                <i class="fas fa-history me-1"></i>Login History Retention (days)
                            </label>
                            <input type="number" 
                                   class="form-control @error('login_history_days') is-invalid @enderror" 
                                   id="login_history_days" 
                                   name="login_history_days" 
                                   value="{{ old('login_history_days', $securitySettings['login_history_days'] ?? 30) }}" 
                                   min="1" max="365" required>
                            @error('login_history_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">How long to keep login history records</small>
                        </div>
                    </div>

                    <!-- Password Security -->
                    <div class="mb-4 mt-4">
                        <h5 class="text-primary border-bottom pb-2">
                            <i class="fas fa-key me-2"></i>Password Security
                        </h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password_min_length" class="form-label">
                                <i class="fas fa-ruler me-1"></i>Minimum Password Length
                            </label>
                            <input type="number" 
                                   class="form-control @error('password_min_length') is-invalid @enderror" 
                                   id="password_min_length" 
                                   name="password_min_length" 
                                   value="{{ old('password_min_length', $securitySettings['password_min_length'] ?? 8) }}" 
                                   min="6" max="50" required>
                            @error('password_min_length')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_expiry_days" class="form-label">
                                <i class="fas fa-calendar-times me-1"></i>Password Expiry (days)
                            </label>
                            <input type="number" 
                                   class="form-control @error('password_expiry_days') is-invalid @enderror" 
                                   id="password_expiry_days" 
                                   name="password_expiry_days" 
                                   value="{{ old('password_expiry_days', $securitySettings['password_expiry_days'] ?? 0) }}" 
                                   min="0" max="365" required>
                            @error('password_expiry_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">0 = passwords never expire</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="password_require_uppercase" 
                                       name="password_require_uppercase" value="1"
                                       {{ old('password_require_uppercase', $securitySettings['password_require_uppercase'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="password_require_uppercase">
                                    Require Uppercase Letters
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="password_require_lowercase" 
                                       name="password_require_lowercase" value="1"
                                       {{ old('password_require_lowercase', $securitySettings['password_require_lowercase'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="password_require_lowercase">
                                    Require Lowercase Letters
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="password_require_numbers" 
                                       name="password_require_numbers" value="1"
                                       {{ old('password_require_numbers', $securitySettings['password_require_numbers'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="password_require_numbers">
                                    Require Numbers
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="password_require_symbols" 
                                       name="password_require_symbols" value="1"
                                       {{ old('password_require_symbols', $securitySettings['password_require_symbols'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="password_require_symbols">
                                    Require Special Characters
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Access Control -->
                    <div class="mb-4 mt-4">
                        <h5 class="text-primary border-bottom pb-2">
                            <i class="fas fa-user-shield me-2"></i>Access Control
                        </h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="two_factor_enabled" 
                                       name="two_factor_enabled" value="1"
                                       {{ old('two_factor_enabled', $securitySettings['two_factor_enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="two_factor_enabled">
                                    Enable Two-Factor Authentication
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="prevent_concurrent_sessions" 
                                       name="prevent_concurrent_sessions" value="1"
                                       {{ old('prevent_concurrent_sessions', $securitySettings['prevent_concurrent_sessions'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="prevent_concurrent_sessions">
                                    Prevent Concurrent Sessions
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="auto_logout_inactive" 
                                       name="auto_logout_inactive" value="1"
                                       {{ old('auto_logout_inactive', $securitySettings['auto_logout_inactive'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_logout_inactive">
                                    Auto-logout Inactive Users
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ip_whitelist_enabled" 
                                       name="ip_whitelist_enabled" value="1"
                                       {{ old('ip_whitelist_enabled', $securitySettings['ip_whitelist_enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ip_whitelist_enabled">
                                    Enable IP Whitelist
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="ip_whitelist" class="form-label">
                                <i class="fas fa-list me-1"></i>IP Whitelist
                            </label>
                            <textarea class="form-control @error('ip_whitelist') is-invalid @enderror" 
                                      id="ip_whitelist" 
                                      name="ip_whitelist" 
                                      rows="4" 
                                      placeholder="Enter IP addresses, one per line">{{ old('ip_whitelist', implode("\n", $securitySettings['ip_whitelist'] ?? [])) }}</textarea>
                            @error('ip_whitelist')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">One IP address per line. Only these IPs will be allowed to access admin panel when enabled.</small>
                        </div>
                    </div>

                    <!-- Security Features -->
                    <div class="mb-4 mt-4">
                        <h5 class="text-primary border-bottom pb-2">
                            <i class="fas fa-cog me-2"></i>Security Features
                        </h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="force_https" 
                                       name="force_https" value="1"
                                       {{ old('force_https', $securitySettings['force_https'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="force_https">
                                    Force HTTPS
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="security_headers_enabled" 
                                       name="security_headers_enabled" value="1"
                                       {{ old('security_headers_enabled', $securitySettings['security_headers_enabled'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="security_headers_enabled">
                                    Enable Security Headers
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="failed_login_notifications" 
                                       name="failed_login_notifications" value="1"
                                       {{ old('failed_login_notifications', $securitySettings['failed_login_notifications'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="failed_login_notifications">
                                    Failed Login Notifications
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="audit_log_enabled" 
                                       name="audit_log_enabled" value="1"
                                       {{ old('audit_log_enabled', $securitySettings['audit_log_enabled'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="audit_log_enabled">
                                    Enable Audit Logging
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Security Settings
                        </button>
                        <a href="{{ route('admin.settings.general') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to General Settings
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Security Status Card -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-shield-check me-2"></i>Security Status
                </h6>
            </div>
            <div class="card-body">
                <div class="security-status">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Password Strength Requirements:</span>
                        <span class="badge bg-{{ ($securitySettings['password_require_uppercase'] || $securitySettings['password_require_lowercase'] || $securitySettings['password_require_numbers'] || $securitySettings['password_require_symbols']) ? 'success' : 'warning' }}">
                            {{ ($securitySettings['password_require_uppercase'] || $securitySettings['password_require_lowercase'] || $securitySettings['password_require_numbers'] || $securitySettings['password_require_symbols']) ? 'Enabled' : 'Basic' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Two-Factor Authentication:</span>
                        <span class="badge bg-{{ $securitySettings['two_factor_enabled'] ? 'success' : 'danger' }}">
                            {{ $securitySettings['two_factor_enabled'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>IP Whitelist:</span>
                        <span class="badge bg-{{ $securitySettings['ip_whitelist_enabled'] ? 'success' : 'secondary' }}">
                            {{ $securitySettings['ip_whitelist_enabled'] ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>HTTPS Enforcement:</span>
                        <span class="badge bg-{{ $securitySettings['force_https'] ? 'success' : 'warning' }}">
                            {{ $securitySettings['force_https'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Audit Logging:</span>
                        <span class="badge bg-{{ $securitySettings['audit_log_enabled'] ? 'success' : 'warning' }}">
                            {{ $securitySettings['audit_log_enabled'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Security Recommendations
                </h6>
            </div>
            <div class="card-body">
                <div class="security-recommendations">
                    @if(!$securitySettings['two_factor_enabled'])
                    <div class="alert alert-warning p-2 mb-2">
                        <small><i class="fas fa-shield-alt me-1"></i> Enable two-factor authentication for enhanced security</small>
                    </div>
                    @endif
                    
                    @if(!$securitySettings['force_https'])
                    <div class="alert alert-warning p-2 mb-2">
                        <small><i class="fas fa-lock me-1"></i> Enable HTTPS enforcement to encrypt data transmission</small>
                    </div>
                    @endif
                    
                    @if($securitySettings['password_min_length'] < 10)
                    <div class="alert alert-info p-2 mb-2">
                        <small><i class="fas fa-key me-1"></i> Consider increasing minimum password length to 10+ characters</small>
                    </div>
                    @endif
                    
                    @if($securitySettings['max_login_attempts'] > 5)
                    <div class="alert alert-info p-2 mb-2">
                        <small><i class="fas fa-exclamation-triangle me-1"></i> Consider reducing max login attempts to 5 or less</small>
                    </div>
                    @endif
                    
                    @if(!$securitySettings['audit_log_enabled'])
                    <div class="alert alert-warning p-2 mb-2">
                        <small><i class="fas fa-clipboard-list me-1"></i> Enable audit logging to track security events</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // IP Whitelist toggle
    const ipWhitelistToggle = document.getElementById('ip_whitelist_enabled');
    const ipWhitelistTextarea = document.getElementById('ip_whitelist');
    
    function toggleIpWhitelist() {
        if (ipWhitelistToggle.checked) {
            ipWhitelistTextarea.disabled = false;
            ipWhitelistTextarea.parentElement.style.opacity = '1';
        } else {
            ipWhitelistTextarea.disabled = true;
            ipWhitelistTextarea.parentElement.style.opacity = '0.6';
        }
    }
    
    ipWhitelistToggle.addEventListener('change', toggleIpWhitelist);
    toggleIpWhitelist(); // Initial state
    
    // Password requirements preview
    const passwordCheckboxes = document.querySelectorAll('[id^="password_require_"]');
    passwordCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updatePasswordPreview();
        });
    });
    
    function updatePasswordPreview() {
        // You can add password strength preview logic here
        console.log('Password requirements updated');
    }
});
</script>
@endsection
</x-layout>
