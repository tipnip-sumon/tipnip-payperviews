<x-smart_layout>

@section('title', 'Security Settings')

@section('content')
<div class="content-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Security Settings</h1>
                        <p class="text-muted">Configure your account security preferences and session management</p>
                    </div>
                    <div>
                        <a href="{{ route('user.sessions.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Security Status Alert -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shield-alt me-2"></i>
                        <div>
                            <strong>Security Status: Active</strong>
                            <p class="mb-0">Your account security features are currently enabled and monitoring your sessions.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Session Notifications Settings -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-bell me-2"></i>Session Notifications
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="notificationSettingsForm">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                        <label class="form-check-label" for="emailNotifications">
                                            <strong>Email Notifications</strong>
                                            <small class="d-block text-muted">Receive email alerts for new logins</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="browserNotifications" checked>
                                        <label class="form-check-label" for="browserNotifications">
                                            <strong>Browser Notifications</strong>
                                            <small class="d-block text-muted">Show in-app notifications for suspicious activities</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="newDeviceAlerts" checked>
                                        <label class="form-check-label" for="newDeviceAlerts">
                                            <strong>New Device Alerts</strong>
                                            <small class="d-block text-muted">Alert when logging in from a new device</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="locationAlerts">
                                        <label class="form-check-label" for="locationAlerts">
                                            <strong>Location Change Alerts</strong>
                                            <small class="d-block text-muted">Alert when logging in from a different location</small>
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Notification Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Session Security Settings -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-lock me-2"></i>Session Security
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="securitySettingsForm">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="singleSessionMode">
                                        <label class="form-check-label" for="singleSessionMode">
                                            <strong>Single Session Mode</strong>
                                            <small class="d-block text-muted">Only allow one active session at a time</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="autoLogoutInactive" checked>
                                        <label class="form-check-label" for="autoLogoutInactive">
                                            <strong>Auto-logout Inactive Sessions</strong>
                                            <small class="d-block text-muted">Automatically logout sessions after specified time of inactivity</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="requireReauth">
                                        <label class="form-check-label" for="requireReauth">
                                            <strong>Require Re-authentication</strong>
                                            <small class="d-block text-muted">Require password for sensitive operations</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="sessionTimeout" class="form-label">
                                            <strong>Auto-Logout Timeout</strong>
                                        </label>
                                        <select class="form-select" id="sessionTimeout">
                                            <option value="0.25">15 minutes</option>
                                            <option value="0.5" selected>30 minutes (Recommended)</option>
                                            <option value="1">1 hour</option>
                                            <option value="2">2 hours</option>
                                            <option value="4">4 hours</option>
                                            <option value="8">8 hours</option>
                                            <option value="24">24 hours</option>
                                        </select>
                                        <small class="text-muted">How long you can remain inactive before automatic logout with complete session destruction</small>
                                        <div class="alert alert-info mt-2 small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <strong>Security Note:</strong> Shorter timeouts provide better security. You'll receive a warning 5 minutes before logout.
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Security Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trusted IPs Management -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-globe me-2"></i>Trusted IP Addresses
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    Add trusted IP addresses that won't trigger security alerts when logging in.
                                </p>
                                
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="newTrustedIP" placeholder="Enter IP address (e.g., 192.168.1.1)">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-success" onclick="addTrustedIP()">
                                            <i class="fas fa-plus me-2"></i>Add Trusted IP
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Current Trusted IPs -->
                                <div id="trustedIPsList">
                                    <h6 class="mb-3">Current Trusted IPs:</h6>
                                    <div class="list-group">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <code>{{ request()->ip() }}</code>
                                                <small class="text-muted ms-2">(Current IP - Auto-trusted)</small>
                                            </div>
                                            <span class="badge badge-success">Current</span>
                                        </div>
                                        <!-- Additional trusted IPs will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Quick Security Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <button class="btn btn-warning w-100" onclick="terminateAllOtherSessions()">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout All Other Sessions
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <button class="btn btn-danger w-100" onclick="clearAllNotifications()">
                                            <i class="fas fa-trash me-2"></i>Clear All Notifications
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <button class="btn btn-info w-100" onclick="resetSecuritySettings()">
                                            <i class="fas fa-undo me-2"></i>Reset to Defaults
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <strong>Warning:</strong> These actions will immediately affect your account security settings.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-1px);
}

.form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.list-group-item {
    border-left: 3px solid transparent;
}

.list-group-item:hover {
    border-left-color: #4e73df;
    background-color: #f8f9fc;
}

code {
    background-color: #f8f9fc;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.875rem;
}
</style>
@endpush

@push('script')
<script>
// Notification Settings Form
document.getElementById('notificationSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const settings = {
        email_notifications: document.getElementById('emailNotifications').checked,
        browser_notifications: document.getElementById('browserNotifications').checked,
        new_device_alerts: document.getElementById('newDeviceAlerts').checked,
        location_alerts: document.getElementById('locationAlerts').checked
    };
    
    saveSettings('notifications', settings);
});

// Security Settings Form
document.getElementById('securitySettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const settings = {
        single_session_mode: document.getElementById('singleSessionMode').checked,
        auto_logout_inactive: document.getElementById('autoLogoutInactive').checked,
        require_reauth: document.getElementById('requireReauth').checked,
        session_timeout: document.getElementById('sessionTimeout').value
    };
    
    saveSettings('security', settings);
});

function saveSettings(type, settings) {
    console.log('Sending request:', { type, settings });
    
    fetch(`{{ route('user.sessions.security.update') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            type: type,
            settings: settings
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response URL:', response.url);
        
        // Check if we were redirected (usually means authentication failed)
        if (response.url.includes('/login')) {
            throw new Error('Authentication required - please log in again');
        }
        
        return response.text().then(text => {
            console.log('Raw response:', text);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is HTML (usually means an error page or redirect)
            if (text.trim().startsWith('<')) {
                console.error('Received HTML instead of JSON:', text.substring(0, 200));
                throw new Error('Authentication required or server error - please refresh the page and try again');
            }
            
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                console.error('Response text was:', text);
                throw new Error('Server returned invalid JSON response. Check console for details.');
            }
        });
    })
    .then(data => {
        console.log('Parsed data:', data);
        if (data.success) {
            showAlert('success', `${type === 'notifications' ? 'Notification' : 'Security'} settings saved successfully`);
        } else {
            showAlert('error', data.message || 'Failed to save settings');
        }
    })
    .catch(error => {
        console.error('Full error:', error);
        showAlert('error', error.message || 'An error occurred while saving settings');
    });
}

function addTrustedIP() {
    const ipInput = document.getElementById('newTrustedIP');
    const ip = ipInput.value.trim();
    
    if (!ip) {
        showAlert('error', 'Please enter an IP address');
        return;
    }
    
    fetch(`{{ route('user.sessions.trusted-ip.add') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ ip_address: ip })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response URL:', response.url);
        
        // Check if we were redirected (usually means authentication failed)
        if (response.url.includes('/login')) {
            throw new Error('Authentication required - please log in again');
        }
        
        return response.text().then(text => {
            console.log('Raw response:', text);
            
            // Check if response is HTML (usually means an error page or redirect)
            if (text.trim().startsWith('<')) {
                console.error('Received HTML instead of JSON:', text.substring(0, 200));
                throw new Error('Authentication required or server error - please refresh the page and try again');
            }
            
            try {
                const data = JSON.parse(text);
                
                // For 400 errors, we still want to show the server's error message
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            } catch (e) {
                if (e.message.includes('HTTP error')) {
                    throw e; // Re-throw our custom error with server message
                }
                console.error('Invalid JSON response:', text);
                throw new Error('Server returned invalid JSON response: ' + text.substring(0, 200));
            }
        });
    })
    .then(data => {
        if (data.success) {
            showAlert('success', 'Trusted IP added successfully');
            ipInput.value = '';
            loadTrustedIPs();
        } else {
            showAlert('error', data.message || 'Failed to add trusted IP');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', error.message || 'An error occurred');
    });
}

function removeTrustedIP(index) {
    if (confirm('Are you sure you want to remove this trusted IP?')) {
        fetch(`{{ url('user/sessions/trusted-ip') }}/${index}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Trusted IP removed successfully');
                loadTrustedIPs();
            } else {
                showAlert('error', data.message || 'Failed to remove trusted IP');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred');
        });
    }
}

function terminateAllOtherSessions() {
    if (confirm('Are you sure you want to logout from all other devices? This will terminate all sessions except your current one.')) {
        fetch(`{{ route('user.sessions.terminate-others') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'All other sessions terminated successfully');
            } else {
                showAlert('error', data.message || 'Failed to terminate other sessions');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred');
        });
    }
}

function clearAllNotifications() {
    if (confirm('Are you sure you want to clear all session notifications? This action cannot be undone.')) {
        fetch(`{{ route('user.sessions.notifications.clear') }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'All notifications cleared successfully');
            } else {
                showAlert('error', data.message || 'Failed to clear notifications');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred');
        });
    }
}

function resetSecuritySettings() {
    if (confirm('Are you sure you want to reset all security settings to defaults?')) {
        // Reset form values to defaults
        document.getElementById('emailNotifications').checked = true;
        document.getElementById('browserNotifications').checked = true;
        document.getElementById('newDeviceAlerts').checked = true;
        document.getElementById('locationAlerts').checked = false;
        document.getElementById('singleSessionMode').checked = false;
        document.getElementById('autoLogoutInactive').checked = true;
        document.getElementById('requireReauth').checked = false;
        document.getElementById('sessionTimeout').value = '24';
        
        showAlert('success', 'Security settings reset to defaults. Click "Save" buttons to apply.');
    }
}

function loadTrustedIPs() {
    // Get trusted IPs passed from controller
    const trustedIPs = @json($settings['trusted_ips'] ?? []);
    const listContainer = document.getElementById('trustedIPsList').querySelector('.list-group');
    
    // Clear existing items except the current IP
    const currentIPItem = listContainer.querySelector('.list-group-item');
    listContainer.innerHTML = '';
    listContainer.appendChild(currentIPItem);
    
    // Add trusted IPs
    trustedIPs.forEach((ip, index) => {
        const item = document.createElement('div');
        item.className = 'list-group-item d-flex justify-content-between align-items-center';
        item.innerHTML = `
            <div>
                <code>${ip.ip}</code>
                <small class="text-muted ms-2">${ip.description} - Added: ${new Date(ip.added_at).toLocaleDateString()}</small>
            </div>
            <button class="btn btn-sm btn-outline-danger" onclick="removeTrustedIP(${index})" title="Remove this IP">
                <i class="fas fa-times"></i>
            </button>
        `;
        listContainer.appendChild(item);
    });
    
    if (trustedIPs.length === 0) {
        const noIPsItem = document.createElement('div');
        noIPsItem.className = 'list-group-item text-muted text-center';
        noIPsItem.innerHTML = '<small>No additional trusted IPs configured</small>';
        listContainer.appendChild(noIPsItem);
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.content-area .container-fluid').insertBefore(alertDiv, document.querySelector('.content-area .container-fluid').firstChild);
    
    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Load trusted IPs on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTrustedIPs();
});
</script>
@endpush

</x-smart_layout>
