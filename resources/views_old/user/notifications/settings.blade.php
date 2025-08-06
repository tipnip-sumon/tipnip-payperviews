<x-smart_layout>

@section('title', 'Notification Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Notification Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.notifications.index') }}">Notifications</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Notifications
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Notification Preferences -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>Notification Preferences
                    </h5>
                </div>
                <form id="notification-settings-form" method="POST" action="{{ route('user.notifications.settings.update') }}">
                    @csrf
                    <div class="card-body">
                        @php
                            $settings = $user->getNotificationSettings();
                        @endphp

                        <!-- Email Notifications -->
                        <div class="notification-section border-bottom pb-4 mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-envelope me-2 text-primary"></i>Email Notifications
                                @if(!$globalSettings['email_enabled'])
                                    <span class="badge bg-warning ms-2">Globally Disabled</span>
                                @endif
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email-notifications" 
                                               name="email_notifications" value="1" 
                                               {{ $settings['email_notifications'] && $globalSettings['email_enabled'] ? 'checked' : '' }}
                                               {{ !$globalSettings['email_enabled'] ? 'disabled' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="email-notifications">
                                            <strong>Enable Email Notifications</strong>
                                            <small class="text-muted">
                                                @if($globalSettings['email_enabled'])
                                                    Receive notifications via email
                                                @else
                                                    Email notifications are disabled by administrator
                                                @endif
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SMS Notifications -->
                        <div class="notification-section border-bottom pb-4 mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-sms me-2 text-success"></i>SMS Notifications
                                @if(!$globalSettings['sms_enabled'])
                                    <span class="badge bg-warning ms-2">Globally Disabled</span>
                                @endif
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms-notifications" 
                                               name="sms_notifications" value="1"
                                               {{ $settings['sms_notifications'] && $globalSettings['sms_enabled'] ? 'checked' : '' }}
                                               {{ !$globalSettings['sms_enabled'] ? 'disabled' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="sms-notifications">
                                            <strong>Enable SMS Notifications</strong>
                                            <small class="text-muted">
                                                @if($globalSettings['sms_enabled'])
                                                    Receive notifications via text message
                                                @else
                                                    SMS notifications are disabled by administrator
                                                @endif
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Browser Notifications -->
                        <div class="notification-section border-bottom pb-4 mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-desktop me-2 text-info"></i>Browser Notifications
                                @if(!$globalSettings['browser_enabled'])
                                    <span class="badge bg-warning ms-2">Globally Disabled</span>
                                @endif
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="browser-notifications" 
                                               name="browser_notifications" value="1"
                                               {{ $settings['browser_notifications'] && $globalSettings['browser_enabled'] ? 'checked' : '' }}
                                               {{ !$globalSettings['browser_enabled'] ? 'disabled' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="browser-notifications">
                                            <strong>Enable Browser Notifications</strong>
                                            <small class="text-muted">
                                                @if($globalSettings['browser_enabled'])
                                                    Receive push notifications in your browser
                                                @else
                                                    Browser notifications are disabled by administrator
                                                @endif
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Categories -->
                        <div class="notification-section border-bottom pb-4 mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-list me-2 text-warning"></i>Notification Categories
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="transaction-notifications" 
                                               name="transaction_notifications" value="1"
                                               {{ $settings['transaction_notifications'] ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="transaction-notifications">
                                            <strong>Transaction Notifications</strong>
                                            <small class="text-muted">Deposits, withdrawals, and transfers</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="security-notifications" 
                                               name="security_notifications" value="1"
                                               {{ $settings['security_notifications'] ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="security-notifications">
                                            <strong>Security Notifications</strong>
                                            <small class="text-muted">Login alerts and security updates</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="lottery-notifications" 
                                               name="lottery_notifications" value="1"
                                               {{ $settings['lottery_notifications'] ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="lottery-notifications">
                                            <strong>Lottery Notifications</strong>
                                            <small class="text-muted">Draw results and ticket updates</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="referral-notifications" 
                                               name="referral_notifications" value="1"
                                               {{ $settings['referral_notifications'] ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="referral-notifications">
                                            <strong>Referral Notifications</strong>
                                            <small class="text-muted">Referral earnings and updates</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="system-notifications" 
                                               name="system_notifications" value="1"
                                               {{ $settings['system_notifications'] ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="system-notifications">
                                            <strong>System Notifications</strong>
                                            <small class="text-muted">Platform updates and announcements</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="maintenance-notifications" 
                                               name="maintenance_notifications" value="1"
                                               {{ $settings['maintenance_notifications'] ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="maintenance-notifications">
                                            <strong>Maintenance Notifications</strong>
                                            <small class="text-muted">Scheduled maintenance alerts</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing Notifications -->
                        <div class="notification-section">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-bullhorn me-2 text-purple"></i>Marketing & Promotional
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="marketing-notifications" 
                                               name="marketing_notifications" value="1"
                                               {{ $settings['marketing_notifications'] ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex flex-column" for="marketing-notifications">
                                            <strong>Marketing Notifications</strong>
                                            <small class="text-muted">Promotional offers, newsletters, and marketing content</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Changes will be applied immediately
                            </small>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Quick Stats & Info -->
        <div class="col-lg-4">
            <!-- Current Status -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Current Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="notification-status">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span>Email Notifications</span>
                            @if(!$globalSettings['email_enabled'])
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Globally Disabled
                                </span>
                            @else
                                <span class="badge {{ $settings['email_notifications'] ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas {{ $settings['email_notifications'] ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $settings['email_notifications'] ? 'Active' : 'Disabled' }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span>SMS Notifications</span>
                            @if(!$globalSettings['sms_enabled'])
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Globally Disabled
                                </span>
                            @else
                                <span class="badge {{ $settings['sms_notifications'] ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas {{ $settings['sms_notifications'] ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $settings['sms_notifications'] ? 'Active' : 'Disabled' }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span>Browser Notifications</span>
                            @if(!$globalSettings['browser_enabled'])
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Globally Disabled
                                </span>
                            @else
                                <span class="badge {{ $settings['browser_notifications'] ? 'bg-success' : 'bg-secondary' }}" id="browser-status">
                                    <i class="fas {{ $settings['browser_notifications'] ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $settings['browser_notifications'] ? 'Active' : 'Disabled' }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mb-0">
                            <span>Marketing</span>
                            <span class="badge {{ $settings['marketing_notifications'] ? 'bg-success' : 'bg-secondary' }}">
                                <i class="fas {{ $settings['marketing_notifications'] ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $settings['marketing_notifications'] ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="account-info">
                        <div class="mb-3">
                            <label class="fw-semibold text-muted small">Email Address</label>
                            <div class="d-flex align-items-center mt-1">
                                <span class="text-primary me-2">{{ $user->email }}</span>
                                @if($user->email_verified_at)
                                    <i class="fas fa-check-circle text-success" title="Verified"></i>
                                @else
                                    <i class="fas fa-exclamation-circle text-warning" title="Not Verified"></i>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="fw-semibold text-muted small">Phone Number</label>
                            <div class="mt-1">
                                @if($user->mobile)
                                    <span class="text-primary">{{ $user->mobile }}</span>
                                @else
                                    <span class="text-muted">Not provided</span>
                                    <a href="{{ route('user.profile') }}" class="btn btn-sm btn-outline-primary ms-2">
                                        Add Phone
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="fw-semibold text-muted small">Last Login</label>
                            <div class="mt-1">
                                <small class="text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-2"></i>View All Notifications
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="testNotifications()">
                            <i class="fas fa-paper-plane me-2"></i>Send Test Notification
                        </button>
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        checkBrowserNotificationPermission();
        initializeFormHandlers();
    });

    function checkBrowserNotificationPermission() {
        if ('Notification' in window) {
            const permission = Notification.permission;
            const browserStatus = document.getElementById('browser-status');
            const browserCheckbox = document.getElementById('browser-notifications');
            
            if (permission === 'granted') {
                browserStatus.innerHTML = '<i class="fas fa-check-circle me-1"></i>Active';
                browserStatus.className = 'badge bg-success';
            } else if (permission === 'denied') {
                browserStatus.innerHTML = '<i class="fas fa-times-circle me-1"></i>Blocked';
                browserStatus.className = 'badge bg-danger';
            } else {
                browserStatus.innerHTML = '<i class="fas fa-question-circle me-1"></i>Permission Needed';
                browserStatus.className = 'badge bg-warning';
            }
        }
    }

    function requestNotificationPermission() {
        if ('Notification' in window) {
            Notification.requestPermission().then(function(permission) {
                checkBrowserNotificationPermission();
                if (permission === 'granted') {
                    showNotification('Browser notifications enabled successfully!', 'success');
                    new Notification('Test Notification', {
                        body: 'You will now receive browser notifications!',
                        icon: '/favicon.ico'
                    });
                }
            });
        }
    }

    function initializeFormHandlers() {
        const form = document.getElementById('notification-settings-form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
                
                // Reset button after 5 seconds in case of issues
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            });
        }

        // Add change handlers for visual feedback
        const switches = document.querySelectorAll('.form-check-input');
        switches.forEach(function(switchEl) {
            switchEl.addEventListener('change', function() {
                updateVisualFeedback();
            });
        });
    }

    function updateVisualFeedback() {
        // Update status indicators based on current form values
        const emailSwitch = document.getElementById('email-notifications');
        const smsSwitch = document.getElementById('sms-notifications');
        const browserSwitch = document.getElementById('browser-notifications');
        const marketingSwitch = document.getElementById('marketing-notifications');

        // Update status badges in sidebar
        updateStatusBadge('email', emailSwitch.checked);
        updateStatusBadge('sms', smsSwitch.checked);
        updateStatusBadge('browser', browserSwitch.checked);
        updateStatusBadge('marketing', marketingSwitch.checked);
    }

    function updateStatusBadge(type, isEnabled) {
        // This would update the status indicators in real-time
        // Implementation depends on your specific UI structure
    }

    function resetForm() {
        const form = document.getElementById('notification-settings-form');
        if (confirm('Are you sure you want to reset all notification settings to default values?')) {
            // Reset to default values
            const switches = form.querySelectorAll('.form-check-input');
            switches.forEach(function(switchEl) {
                // Set defaults - you may want to customize these
                if (switchEl.name === 'marketing_notifications') {
                    switchEl.checked = false;
                } else {
                    switchEl.checked = true;
                }
            });
            updateVisualFeedback();
        }
    }

    function testNotifications() {
        // Send a test notification
        fetch('{{ route("user.notifications.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                type: 'test'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Test notification sent successfully!', 'success');
                
                // Also show browser notification if enabled
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('Test Notification', {
                        body: 'This is a test notification from your account settings.',
                        icon: '/favicon.ico'
                    });
                }
            } else {
                showNotification('Failed to send test notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while sending test notification', 'error');
        });
    }

    function showNotification(message, type = 'info') {
        // Simple notification system - you can replace with your preferred notification library
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            const alert = document.querySelector('.alert.position-fixed');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Enable browser notifications on checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.id === 'browser-notifications' && e.target.checked) {
            if ('Notification' in window && Notification.permission === 'default') {
                requestNotificationPermission();
            }
        }
    });
</script>
@endpush
                            </div>
                        </div>

                        <!-- Browser Notifications -->
                        <div class="notification-section mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="fe fe-monitor me-2 text-info"></i>Browser Notifications
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="browser-notifications" 
                                               name="browser_notifications">
                                        <label class="form-check-label" for="browser-notifications">
                                            <strong>Enable Browser Notifications</strong>
                                            <br><small class="text-muted">Show desktop notifications when browsing</small>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="alert alert-info" id="browser-permission-alert" style="display: none;">
                                        <i class="fe fe-info me-2"></i>
                                        Please allow browser notifications to receive real-time updates.
                                        <button class="btn btn-sm btn-info ms-2" onclick="requestNotificationPermission()">
                                            Enable
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SMS Notifications -->
                        <div class="notification-section mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="fe fe-smartphone me-2 text-success"></i>SMS Notifications
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms-notifications" 
                                               name="sms_notifications">
                                        <label class="form-check-label" for="sms-notifications">
                                            <strong>Enable SMS Notifications</strong>
                                            <br><small class="text-muted">Receive important alerts via SMS</small>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <i class="fe fe-alert-triangle me-2"></i>
                                        SMS notifications are only available for critical security alerts and urgent account updates.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing Communications -->
                        <div class="notification-section mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="fe fe-megaphone me-2 text-warning"></i>Marketing Communications
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="marketing-notifications" 
                                               name="marketing_notifications">
                                        <label class="form-check-label" for="marketing-notifications">
                                            <strong>Promotional Notifications</strong>
                                            <br><small class="text-muted">Receive updates about new features, promotions, and special offers</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Frequency -->
                        <div class="notification-section mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="fe fe-clock me-2 text-purple"></i>Notification Frequency
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email Digest Frequency</label>
                                    <select class="form-select" name="email_digest_frequency">
                                        <option value="immediate">Immediate</option>
                                        <option value="daily" selected>Daily Digest</option>
                                        <option value="weekly">Weekly Digest</option>
                                        <option value="never">Never</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Preferred Time</label>
                                    <select class="form-select" name="preferred_time">
                                        <option value="09:00">9:00 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="18:00" selected>6:00 PM</option>
                                        <option value="21:00">9:00 PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetToDefaults()">
                                <i class="fe fe-refresh-cw me-2"></i>Reset to Defaults
                            </button>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="testNotifications()">
                                    <i class="fe fe-send me-2"></i>Send Test
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Stats & Info -->
        <div class="col-xl-4">
            <!-- Current Status -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Current Status</div>
                </div>
                <div class="card-body">
                    <div class="notification-status">
                        <div class="status-item mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span>Email Notifications</span>
                                <span class="badge bg-success-transparent text-success">
                                    <i class="fe fe-check-circle me-1"></i>Active
                                </span>
                            </div>
                        </div>
                        
                        <div class="status-item mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span>Browser Notifications</span>
                                <span class="badge bg-secondary-transparent text-secondary" id="browser-status">
                                    <i class="fe fe-x-circle me-1"></i>Disabled
                                </span>
                            </div>
                        </div>
                        
                        <div class="status-item mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span>SMS Notifications</span>
                                <span class="badge bg-secondary-transparent text-secondary">
                                    <i class="fe fe-x-circle me-1"></i>Disabled
                                </span>
                            </div>
                        </div>
                        
                        <div class="status-item mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span>Marketing</span>
                                <span class="badge bg-secondary-transparent text-secondary">
                                    <i class="fe fe-x-circle me-1"></i>Disabled
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Account Information</div>
                </div>
                <div class="card-body">
                    <div class="account-info">
                        <div class="info-item mb-3">
                            <label class="fw-semibold text-muted">Email Address</label>
                            <div class="mt-1">
                                <span class="text-primary">{{ $user->email }}</span>
                                @if($user->email_verified_at)
                                    <i class="fe fe-check-circle text-success ms-1" title="Verified"></i>
                                @else
                                    <i class="fe fe-alert-circle text-warning ms-1" title="Not Verified"></i>
                                @endif
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="fw-semibold text-muted">Phone Number</label>
                            <div class="mt-1">
                                @if($user->phone)
                                    <span class="text-primary">{{ $user->phone }}</span>
                                @else
                                    <span class="text-muted">Not provided</span>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary ms-2">
                                        Add Phone
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="fw-semibold text-muted">Last Login</label>
                            <div class="mt-1">
                                <small class="text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Quick Actions</div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fe fe-list me-2"></i>View All Notifications
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="testNotifications()">
                            <i class="fe fe-send me-2"></i>Send Test Notification
                        </button>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fe fe-user me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        checkBrowserNotificationPermission();
        initializeFormHandlers();
    });

    function checkBrowserNotificationPermission() {
        if ('Notification' in window) {
            const permission = Notification.permission;
            const browserStatus = document.getElementById('browser-status');
            const browserAlert = document.getElementById('browser-permission-alert');
            const browserCheckbox = document.getElementById('browser-notifications');
            
            if (permission === 'granted') {
                browserStatus.innerHTML = '<i class="fe fe-check-circle me-1"></i>Active';
                browserStatus.className = 'badge bg-success-transparent text-success';
                browserCheckbox.checked = true;
            } else if (permission === 'denied') {
                browserStatus.innerHTML = '<i class="fe fe-x-circle me-1"></i>Blocked';
                browserStatus.className = 'badge bg-danger-transparent text-danger';
                browserAlert.style.display = 'block';
                browserAlert.innerHTML = '<i class="fe fe-alert-triangle me-2"></i>Browser notifications are blocked. Please enable them in your browser settings.';
                browserAlert.className = 'alert alert-danger';
            } else {
                browserAlert.style.display = 'block';
            }
        }
    }

    function requestNotificationPermission() {
        if ('Notification' in window) {
            Notification.requestPermission().then(function(permission) {
                checkBrowserNotificationPermission();
                if (permission === 'granted') {
                    showToast('Browser notifications enabled successfully!', 'success');
                    new Notification('Test Notification', {
                        body: 'You will now receive browser notifications!',
                        icon: '/favicon.ico'
                    });
                }
            });
        }
    }

    function initializeFormHandlers() {
        const form = document.getElementById('notification-settings-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings();
        });

        // Handle switch changes
        const switches = form.querySelectorAll('input[type="checkbox"]');
        switches.forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                if (this.id === 'browser-notifications' && this.checked) {
                    requestNotificationPermission();
                }
            });
        });
    }

    function saveSettings() {
        const form = document.getElementById('notification-settings-form');
        const formData = new FormData(form);
        
        // Add unchecked checkboxes as false
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                formData.append(checkbox.name, '0');
            }
        });

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Notification settings saved successfully!', 'success');
                updateStatusIndicators();
            } else {
                showToast('Failed to save settings. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while saving settings.', 'error');
        });
    }

    function resetToDefaults() {
        if (confirm('Are you sure you want to reset all notification settings to defaults?')) {
            // Reset form to defaults
            document.getElementById('email-notifications').checked = true;
            document.getElementById('email-investment').checked = true;
            document.getElementById('email-withdrawal').checked = true;
            document.getElementById('email-referral').checked = true;
            document.getElementById('email-security').checked = true;
            document.getElementById('browser-notifications').checked = false;
            document.getElementById('sms-notifications').checked = false;
            document.getElementById('marketing-notifications').checked = false;
            
            document.querySelector('select[name="email_digest_frequency"]').value = 'daily';
            document.querySelector('select[name="preferred_time"]').value = '18:00';
            
            showToast('Settings reset to defaults. Click Save to apply changes.', 'info');
        }
    }

    function testNotifications() {
        fetch('{{ route("user.notifications.test") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Test notification sent successfully!', 'success');
                
                // Also show browser notification if enabled
                if (Notification.permission === 'granted') {
                    new Notification('Test Notification', {
                        body: 'This is a test notification from PayperViews',
                        icon: '/favicon.ico'
                    });
                }
            } else {
                showToast('Failed to send test notification.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while sending test notification.', 'error');
        });
    }

    function updateStatusIndicators() {
        // Update status indicators based on form values
        const emailEnabled = document.getElementById('email-notifications').checked;
        const browserEnabled = document.getElementById('browser-notifications').checked;
        const smsEnabled = document.getElementById('sms-notifications').checked;
        const marketingEnabled = document.getElementById('marketing-notifications').checked;
        
        // Update visual indicators
        updateStatusBadge('email', emailEnabled);
        updateStatusBadge('browser', browserEnabled && Notification.permission === 'granted');
        updateStatusBadge('sms', smsEnabled);
        updateStatusBadge('marketing', marketingEnabled);
    }

    function updateStatusBadge(type, enabled) {
        const statusItems = document.querySelectorAll('.status-item');
        statusItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(type)) {
                const badge = item.querySelector('.badge');
                if (enabled) {
                    badge.innerHTML = '<i class="fe fe-check-circle me-1"></i>Active';
                    badge.className = 'badge bg-success-transparent text-success';
                } else {
                    badge.innerHTML = '<i class="fe fe-x-circle me-1"></i>Disabled';
                    badge.className = 'badge bg-secondary-transparent text-secondary';
                }
            }
        });
    }

    function showToast(message, type = 'info') {
        // Create toast notification
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fe fe-${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
</script>
@endpush

@endsection
</x-smart_layout>
