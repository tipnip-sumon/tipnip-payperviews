<x-layout>

@section('title', 'Notification Settings')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Notification Settings</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Notifications</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <button class="btn btn-success btn-wave" onclick="saveSettings()">
                <i class="fe fe-save me-2"></i>Save Settings
            </button>
            <button class="btn btn-outline-secondary btn-wave ms-2" onclick="resetToDefaults()">
                <i class="fe fe-refresh-cw me-2"></i>Reset to Defaults
            </button>
        </div>
    </div>

    <div class="row">
        <!-- General Settings -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-settings me-2"></i>General Settings
                    </div>
                </div>
                <div class="card-body">
                    <form id="generalSettingsForm">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable-notifications" 
                                           name="enable_notifications" checked>
                                    <label class="form-check-label" for="enable-notifications">
                                        Enable Admin Notifications
                                    </label>
                                    <div class="form-text">Turn on/off all admin notifications system-wide</div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable-popup-notifications" 
                                           name="enable_popup_notifications" checked>
                                    <label class="form-check-label" for="enable-popup-notifications">
                                        Enable Pop-up Notifications
                                    </label>
                                    <div class="form-text">Show real-time pop-up notifications for urgent alerts</div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable-sound" 
                                           name="enable_sound" checked>
                                    <label class="form-check-label" for="enable-sound">
                                        Enable Sound Alerts
                                    </label>
                                    <div class="form-text">Play sound for important notifications</div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable-browser-notifications" 
                                           name="enable_browser_notifications">
                                    <label class="form-check-label" for="enable-browser-notifications">
                                        Enable Browser Notifications
                                    </label>
                                    <div class="form-text">Send browser notifications even when tab is not active</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="notification-refresh-interval" class="form-label">Refresh Interval (seconds)</label>
                                <select class="form-select" id="notification-refresh-interval" name="refresh_interval">
                                    <option value="10">10 seconds</option>
                                    <option value="30" selected>30 seconds</option>
                                    <option value="60">1 minute</option>
                                    <option value="300">5 minutes</option>
                                </select>
                                <div class="form-text">How often to check for new notifications</div>
                            </div>

                            <div class="col-md-6">
                                <label for="max-notifications" class="form-label">Max Notifications to Display</label>
                                <input type="number" class="form-control" id="max-notifications" 
                                       name="max_notifications" value="50" min="10" max="200" step="10">
                                <div class="form-text">Maximum number of notifications in dropdown</div>
                            </div>

                            <div class="col-md-6">
                                <label for="auto-cleanup-days" class="form-label">Auto Cleanup (days)</label>
                                <input type="number" class="form-control" id="auto-cleanup-days" 
                                       name="auto_cleanup_days" value="30" min="7" max="365">
                                <div class="form-text">Automatically delete read notifications after this many days</div>
                            </div>

                            <div class="col-md-6">
                                <label for="popup-duration" class="form-label">Pop-up Duration (seconds)</label>
                                <input type="number" class="form-control" id="popup-duration" 
                                       name="popup_duration" value="5" min="3" max="30">
                                <div class="form-text">How long pop-up notifications stay visible</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification Types -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-bell me-2"></i>Notification Types
                    </div>
                </div>
                <div class="card-body">
                    <form id="notificationTypesForm">
                        <div class="notification-type-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fe fe-user-plus text-success me-2"></i>User Registration
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify-user-registration" 
                                           name="notify_user_registration" checked>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" name="user_registration_priority">
                                        <option value="low">Low Priority</option>
                                        <option value="normal" selected>Normal Priority</option>
                                        <option value="high">High Priority</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="popup-user-registration" 
                                               name="popup_user_registration">
                                        <label class="form-check-label" for="popup-user-registration">Pop-up</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="notification-type-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fe fe-dollar-sign text-primary me-2"></i>New Deposits
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify-deposits" 
                                           name="notify_deposits" checked>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" name="deposits_priority">
                                        <option value="low">Low Priority</option>
                                        <option value="normal">Normal Priority</option>
                                        <option value="high" selected>High Priority</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="popup-deposits" 
                                               name="popup_deposits" checked>
                                        <label class="form-check-label" for="popup-deposits">Pop-up</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="form-label form-label-sm">Minimum amount for notification:</label>
                                <input type="number" class="form-control form-control-sm" 
                                       name="deposits_min_amount" value="10" step="0.01">
                            </div>
                        </div>

                        <div class="notification-type-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fe fe-arrow-up text-warning me-2"></i>Withdrawal Requests
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify-withdrawals" 
                                           name="notify_withdrawals" checked>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" name="withdrawals_priority">
                                        <option value="low">Low Priority</option>
                                        <option value="normal">Normal Priority</option>
                                        <option value="high">High Priority</option>
                                        <option value="urgent" selected>Urgent</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="popup-withdrawals" 
                                               name="popup_withdrawals" checked>
                                        <label class="form-check-label" for="popup-withdrawals">Pop-up</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="notification-type-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fe fe-message-circle text-info me-2"></i>Support Tickets
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify-support-tickets" 
                                           name="notify_support_tickets" checked>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" name="support_tickets_priority">
                                        <option value="low">Low Priority</option>
                                        <option value="normal" selected>Normal Priority</option>
                                        <option value="high">High Priority</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="popup-support-tickets" 
                                               name="popup_support_tickets">
                                        <label class="form-check-label" for="popup-support-tickets">Pop-up</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="notification-type-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fe fe-alert-triangle text-danger me-2"></i>System Errors
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify-system-errors" 
                                           name="notify_system_errors" checked>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" name="system_errors_priority">
                                        <option value="high">High Priority</option>
                                        <option value="urgent" selected>Urgent</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="popup-system-errors" 
                                               name="popup_system_errors" checked>
                                        <label class="form-check-label" for="popup-system-errors">Pop-up</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="notification-type-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fe fe-shield text-warning me-2"></i>Security Alerts
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notify-security-alerts" 
                                           name="notify_security_alerts" checked>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" name="security_alerts_priority">
                                        <option value="high">High Priority</option>
                                        <option value="urgent" selected>Urgent</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="popup-security-alerts" 
                                               name="popup_security_alerts" checked>
                                        <label class="form-check-label" for="popup-security-alerts">Pop-up</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Settings -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-sliders me-2"></i>Advanced Settings
                    </div>
                </div>
                <div class="card-body">
                    <form id="advancedSettingsForm">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0">Email Notifications</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable-email-notifications" 
                                                   name="enable_email_notifications">
                                            <label class="form-check-label" for="enable-email-notifications">
                                                Send Email Notifications
                                            </label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Recipients</label>
                                            <textarea class="form-control" name="email_recipients" rows="3" 
                                                      placeholder="admin@example.com, admin2@example.com"></textarea>
                                            <div class="form-text">Comma-separated email addresses</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Template</label>
                                            <select class="form-select" name="email_template">
                                                <option value="default">Default Template</option>
                                                <option value="minimal">Minimal Template</option>
                                                <option value="detailed">Detailed Template</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0">SMS Notifications</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable-sms-notifications" 
                                                   name="enable_sms_notifications">
                                            <label class="form-check-label" for="enable-sms-notifications">
                                                Send SMS Notifications
                                            </label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone Numbers</label>
                                            <textarea class="form-control" name="sms_recipients" rows="3" 
                                                      placeholder="+1234567890, +0987654321"></textarea>
                                            <div class="form-text">Comma-separated phone numbers</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">SMS Priority Filter</label>
                                            <select class="form-select" name="sms_priority_filter">
                                                <option value="high">High Priority Only</option>
                                                <option value="urgent" selected>Urgent Only</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0">Slack Integration</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable-slack-notifications" 
                                                   name="enable_slack_notifications">
                                            <label class="form-check-label" for="enable-slack-notifications">
                                                Send to Slack
                                            </label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Webhook URL</label>
                                            <input type="url" class="form-control" name="slack_webhook_url" 
                                                   placeholder="https://hooks.slack.com/...">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Channel</label>
                                            <input type="text" class="form-control" name="slack_channel" 
                                                   placeholder="#admin-alerts" value="#admin-alerts">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Sound Settings -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0">Sound Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Default Sound</label>
                                            <select class="form-select" name="default_sound">
                                                <option value="bell">Bell</option>
                                                <option value="chime" selected>Chime</option>
                                                <option value="ding">Ding</option>
                                                <option value="notification">Notification</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Urgent Sound</label>
                                            <select class="form-select" name="urgent_sound">
                                                <option value="alert">Alert</option>
                                                <option value="siren" selected>Siren</option>
                                                <option value="alarm">Alarm</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Volume</label>
                                            <input type="range" class="form-range" name="sound_volume" 
                                                   min="0" max="100" value="70">
                                            <div class="d-flex justify-content-between">
                                                <small>0%</small>
                                                <small>100%</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="testSound()">
                                            <i class="fe fe-volume-2 me-1"></i>Test Sound
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0">Maintenance & Cleanup</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable-auto-cleanup" 
                                                   name="enable_auto_cleanup" checked>
                                            <label class="form-check-label" for="enable-auto-cleanup">
                                                Enable Auto Cleanup
                                            </label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Cleanup Schedule</label>
                                            <select class="form-select" name="cleanup_schedule">
                                                <option value="daily" selected>Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Database Optimization</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable-db-optimization" 
                                                       name="enable_db_optimization" checked>
                                                <label class="form-check-label" for="enable-db-optimization">
                                                    Optimize notification tables weekly
                                                </label>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="runCleanupNow()">
                                            <i class="fe fe-trash-2 me-1"></i>Run Cleanup Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function saveSettings() {
        const generalSettings = new FormData(document.getElementById('generalSettingsForm'));
        const notificationTypes = new FormData(document.getElementById('notificationTypesForm'));
        const advancedSettings = new FormData(document.getElementById('advancedSettingsForm'));
        
        const allSettings = {};
        
        // Combine all form data
        for (let [key, value] of generalSettings.entries()) {
            allSettings[key] = value;
        }
        for (let [key, value] of notificationTypes.entries()) {
            allSettings[key] = value;
        }
        for (let [key, value] of advancedSettings.entries()) {
            allSettings[key] = value;
        }
        
        // Convert checkboxes to boolean
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            allSettings[checkbox.name] = checkbox.checked;
        });
        
        fetch('{{ route("admin.notifications.settings.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(allSettings)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Settings saved successfully!', 'success');
            } else {
                showToast('Failed to save settings', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while saving settings', 'error');
        });
    }
    
    function resetToDefaults() {
        if (!confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
            return;
        }
        
        fetch('{{ route("admin.notifications.settings.reset") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Settings reset to defaults', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast('Failed to reset settings', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
    
    function testSound() {
        const soundType = document.querySelector('[name="default_sound"]').value;
        const volume = document.querySelector('[name="sound_volume"]').value / 100;
        
        // Create audio element and play test sound
        const audio = new Audio(`/sounds/notification-${soundType}.mp3`);
        audio.volume = volume;
        audio.play().catch(error => {
            console.error('Could not play sound:', error);
            showToast('Could not play test sound. Please check your browser settings.', 'warning');
        });
    }
    
    function runCleanupNow() {
        if (!confirm('Run notification cleanup now? This will remove old read notifications.')) {
            return;
        }
        
        fetch('{{ route("admin.notifications.cleanup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(`Cleanup completed! Removed ${data.cleaned_count || 0} old notifications.`, 'success');
            } else {
                showToast('Cleanup failed', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred during cleanup', 'error');
        });
    }
    
    // Request browser notification permission if enabled
    document.getElementById('enable-browser-notifications').addEventListener('change', function() {
        if (this.checked) {
            if ('Notification' in window) {
                if (Notification.permission === 'default') {
                    Notification.requestPermission().then(permission => {
                        if (permission !== 'granted') {
                            this.checked = false;
                            showToast('Browser notification permission denied', 'warning');
                        } else {
                            showToast('Browser notifications enabled!', 'success');
                        }
                    });
                } else if (Notification.permission === 'denied') {
                    this.checked = false;
                    showToast('Browser notifications are blocked. Please enable them in your browser settings.', 'warning');
                }
            } else {
                this.checked = false;
                showToast('Browser notifications are not supported', 'warning');
            }
        }
    });
    
    // Load saved settings on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("admin.notifications.settings.get") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.settings) {
                    const settings = data.settings;
                    
                    // Apply settings to form elements
                    Object.keys(settings).forEach(key => {
                        const element = document.querySelector(`[name="${key}"]`);
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = settings[key];
                            } else {
                                element.value = settings[key];
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading settings:', error);
            });
    });
    
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fe fe-${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'} me-2"></i>
                ${message}
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fe fe-x"></i>
            </button>
        `;
        
        if (!document.getElementById('toast-styles')) {
            const toastStyles = document.createElement('style');
            toastStyles.id = 'toast-styles';
            toastStyles.textContent = `
                .toast-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    border-left: 4px solid #007bff;
                    min-width: 300px;
                    animation: slideInRight 0.3s ease-out;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 12px 16px;
                    margin-bottom: 10px;
                }
                .toast-success { border-left-color: #28a745; }
                .toast-error { border-left-color: #dc3545; }
                .toast-warning { border-left-color: #ffc107; }
                .toast-content { flex: 1; display: flex; align-items: center; }
                .toast-close { 
                    background: none; 
                    border: none; 
                    cursor: pointer; 
                    padding: 4px;
                    margin-left: 8px;
                    border-radius: 4px;
                    transition: background-color 0.2s;
                }
                .toast-close:hover { background-color: rgba(0,0,0,0.1); }
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(toastStyles);
        }
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    }
</script>
@endpush
</x-layout>
