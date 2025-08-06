@extends('components.layout')

@section('page-title', $pageTitle)

@section('breadcrumb')
<div class="page-header d-sm-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">{{ $pageTitle }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fe fe-home me-2"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.email-campaigns.index') }}">Email Campaigns</a></li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
        </ol>
    </div>
    <div class="page-rightheader ml-md-auto">
        <div class="btn-list">
            <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="button" class="btn btn-success" onclick="saveSettings()">
                <i class="fe fe-save me-2"></i>Save Settings
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Email Campaign Configuration -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">⚙️ Email Campaign Configuration</h4>
            </div>
            <div class="card-body">
                <form id="campaignSettingsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">KYC Reminder Frequency</label>
                                <select class="form-select" name="kyc_frequency">
                                    <option value="daily">Daily</option>
                                    <option value="weekly" selected>Weekly</option>
                                    <option value="bi-weekly">Bi-weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                <small class="text-muted">How often to send KYC reminders to pending users</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max KYC Reminders</label>
                                <input type="number" class="form-control" name="kyc_max_reminders" value="3" min="1" max="10">
                                <small class="text-muted">Maximum number of reminders to send per user</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Inactive User Threshold (Days)</label>
                                <input type="number" class="form-control" name="inactive_threshold" value="15" min="1" max="365">
                                <small class="text-muted">Consider user inactive after this many days</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password Reset Interval (Days)</label>
                                <input type="number" class="form-control" name="password_interval" value="30" min="7" max="365">
                                <small class="text-muted">Remind users to change password after this period</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Batch Size</label>
                                <input type="number" class="form-control" name="batch_size" value="50" min="1" max="500">
                                <small class="text-muted">Number of emails to send per batch</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Queue Retry Attempts</label>
                                <input type="number" class="form-control" name="retry_attempts" value="3" min="1" max="10">
                                <small class="text-muted">Number of retry attempts for failed jobs</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>🕒 Automated Scheduling</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_kyc" name="auto_kyc" checked>
                                    <label class="form-check-label" for="auto_kyc">
                                        Auto KYC Reminders
                                    </label>
                                </div>
                                <small class="text-muted">Automatically send KYC reminders based on schedule</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_inactive" name="auto_inactive" checked>
                                    <label class="form-check-label" for="auto_inactive">
                                        Auto Inactive User Reminders
                                    </label>
                                </div>
                                <small class="text-muted">Automatically send inactive user reminders</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_password" name="auto_password" checked>
                                    <label class="form-check-label" for="auto_password">
                                        Auto Password Reset Reminders
                                    </label>
                                </div>
                                <small class="text-muted">Automatically remind users to change passwords</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_congratulations" name="auto_congratulations" checked>
                                    <label class="form-check-label" for="auto_congratulations">
                                        Auto Investment Congratulations
                                    </label>
                                </div>
                                <small class="text-muted">Automatically send congratulations on first investment</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>⏰ Schedule Times</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">KYC Reminder Time</label>
                                <input type="time" class="form-control" name="kyc_time" value="09:00">
                                <small class="text-muted">Time to send KYC reminders (server time)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Inactive User Reminder Time</label>
                                <input type="time" class="form-control" name="inactive_time" value="10:00">
                                <small class="text-muted">Time to send inactive user reminders</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password Reminder Time</label>
                                <input type="time" class="form-control" name="password_time" value="11:00">
                                <small class="text-muted">Time to send password reset reminders</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Server Timezone</label>
                                <select class="form-select" name="timezone">
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">Eastern Time</option>
                                    <option value="America/Chicago">Central Time</option>
                                    <option value="America/Denver">Mountain Time</option>
                                    <option value="America/Los_Angeles">Pacific Time</option>
                                    <option value="Europe/London">London</option>
                                    <option value="Asia/Dhaka" selected>Dhaka</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- System Status & Information -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📊 System Status</h4>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fe fe-mail text-primary me-2"></i>
                            <strong>SMTP Status</strong>
                        </div>
                        <span class="badge badge-success">Active</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fe fe-server text-info me-2"></i>
                            <strong>Queue Worker</strong>
                        </div>
                        <span class="badge badge-success">Running</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fe fe-database text-warning me-2"></i>
                            <strong>Database</strong>
                        </div>
                        <span class="badge badge-success">Connected</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fe fe-clock text-secondary me-2"></i>
                            <strong>Cron Jobs</strong>
                        </div>
                        <span class="badge badge-success">Active</span>
                    </div>
                </div>
                
                <hr>
                
                <h6>📈 Today's Statistics</h6>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-2">
                            <h5 class="mb-1 text-success">127</h5>
                            <small>Emails Sent</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-2">
                            <h5 class="mb-1 text-primary">98.4%</h5>
                            <small>Success Rate</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h5 class="mb-1 text-warning">23</h5>
                            <small>Pending</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h5 class="mb-1 text-danger">2</h5>
                            <small>Failed</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h6>🔧 Quick Actions</h6>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="testConnection()">
                        <i class="fe fe-wifi me-2"></i>Test SMTP Connection
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="clearCache()">
                        <i class="fe fe-refresh-cw me-2"></i>Clear Email Cache
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="viewLogs()">
                        <i class="fe fe-file-text me-2"></i>View Email Logs
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="exportSettings()">
                        <i class="fe fe-download me-2"></i>Export Settings
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Cron Job Instructions -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">⏰ Cron Job Setup</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fe fe-info me-2"></i>Add these to your server's crontab:</h6>
                    <hr>
                    <code style="font-size: 11px;">
                        # Email Campaigns (Every day at 9 AM)<br>
                        0 9 * * * php /path/to/artisan email:kyc-pending-reminders<br><br>
                        
                        # Inactive Users (Every Monday at 10 AM)<br>
                        0 10 * * 1 php /path/to/artisan email:inactive-user-reminders<br><br>
                        
                        # Password Resets (1st of every month at 11 AM)<br>
                        0 11 1 * * php /path/to/artisan email:monthly-password-resets<br><br>
                        
                        # Queue Worker (Keep running)<br>
                        * * * * * php /path/to/artisan queue:work --timeout=300 --sleep=3 --tries=3
                    </code>
                </div>
                
                <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="copyCronJobs()">
                    <i class="fe fe-copy me-2"></i>Copy Cron Commands
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Email Templates Configuration -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📧 Email Template Configuration</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th>Status</th>
                                <th>Subject Line</th>
                                <th>From Name</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="counter-icon bg-warning-transparent me-2">
                                            <i class="fe fe-credit-card text-warning"></i>
                                        </span>
                                        <strong>KYC Pending Reminder</strong>
                                    </div>
                                </td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>Complete Your KYC Verification</td>
                                <td>{{ config('app.name') }} Security Team</td>
                                <td>2 days ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editTemplate('kyc')">
                                        <i class="fe fe-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="previewTemplate('kyc')">
                                        <i class="fe fe-eye"></i> Preview
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="counter-icon bg-danger-transparent me-2">
                                            <i class="fe fe-user-x text-danger"></i>
                                        </span>
                                        <strong>Inactive User Reminder</strong>
                                    </div>
                                </td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>We Miss You! Come Back and Invest</td>
                                <td>{{ config('app.name') }} Team</td>
                                <td>1 week ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editTemplate('inactive')">
                                        <i class="fe fe-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="previewTemplate('inactive')">
                                        <i class="fe fe-eye"></i> Preview
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="counter-icon bg-info-transparent me-2">
                                            <i class="fe fe-lock text-info"></i>
                                        </span>
                                        <strong>Password Reset Reminder</strong>
                                    </div>
                                </td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>Security Alert: Update Your Password</td>
                                <td>{{ config('app.name') }} Security</td>
                                <td>3 days ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editTemplate('password')">
                                        <i class="fe fe-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="previewTemplate('password')">
                                        <i class="fe fe-eye"></i> Preview
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="counter-icon bg-success-transparent me-2">
                                            <i class="fe fe-award text-success"></i>
                                        </span>
                                        <strong>Investment Congratulations</strong>
                                    </div>
                                </td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>🎉 Congratulations on Your First Investment!</td>
                                <td>{{ config('app.name') }} Team</td>
                                <td>1 day ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editTemplate('congratulations')">
                                        <i class="fe fe-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="previewTemplate('congratulations')">
                                        <i class="fe fe-eye"></i> Preview
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageJsScripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Email Campaign Settings page loaded');
});

function saveSettings() {
    const formData = new FormData(document.getElementById('campaignSettingsForm'));
    
    showNotification('Saving email campaign settings...', 'info');
    
    // Simulate saving
    setTimeout(() => {
        showNotification('Email campaign settings saved successfully!', 'success');
    }, 1500);
}

function testConnection() {
    showNotification('Testing SMTP connection...', 'info');
    
    // Simulate connection test
    setTimeout(() => {
        showNotification('SMTP connection test successful!', 'success');
    }, 2000);
}

function clearCache() {
    showNotification('Clearing email cache...', 'info');
    
    setTimeout(() => {
        showNotification('Email cache cleared successfully!', 'success');
    }, 1000);
}

function viewLogs() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Email Logs',
            html: `
                <div style="text-align: left; font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto;">
                    [2025-08-02 09:15:23] INFO: KYC reminder sent to user #123<br>
                    [2025-08-02 09:15:24] INFO: KYC reminder sent to user #124<br>
                    [2025-08-02 09:15:25] ERROR: Failed to send KYC reminder to user #125 - Invalid email<br>
                    [2025-08-02 10:30:15] INFO: Password reset reminder sent to user #200<br>
                    [2025-08-02 11:45:32] INFO: Investment congratulations sent to user #156<br>
                    [2025-08-02 14:20:18] INFO: Inactive user reminder sent to user #89<br>
                </div>
            `,
            width: '600px',
            confirmButtonText: 'Close'
        });
    } else {
        alert('Email logs would be displayed here');
    }
}

function exportSettings() {
    const settings = {
        kyc_frequency: 'weekly',
        kyc_max_reminders: 3,
        inactive_threshold: 15,
        password_interval: 30,
        batch_size: 50,
        retry_attempts: 3,
        auto_kyc: true,
        auto_inactive: true,
        auto_password: true,
        auto_congratulations: true,
        kyc_time: '09:00',
        inactive_time: '10:00',
        password_time: '11:00',
        timezone: 'Asia/Dhaka'
    };
    
    const settingsJson = JSON.stringify(settings, null, 2);
    const blob = new Blob([settingsJson], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement('a');
    link.href = url;
    link.download = 'email_campaign_settings_' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    showNotification('Settings exported successfully!', 'success');
}

function copyCronJobs() {
    const cronCommands = `# Email Campaigns (Every day at 9 AM)
0 9 * * * php /path/to/artisan email:kyc-pending-reminders

# Inactive Users (Every Monday at 10 AM)
0 10 * * 1 php /path/to/artisan email:inactive-user-reminders

# Password Resets (1st of every month at 11 AM)
0 11 1 * * php /path/to/artisan email:monthly-password-resets

# Queue Worker (Keep running)
* * * * * php /path/to/artisan queue:work --timeout=300 --sleep=3 --tries=3`;

    navigator.clipboard.writeText(cronCommands).then(() => {
        showNotification('Cron commands copied to clipboard!', 'success');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = cronCommands;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Cron commands copied to clipboard!', 'success');
    });
}

function editTemplate(templateType) {
    showNotification('Opening template editor for ' + templateType + '...', 'info');
    // This would typically redirect to a template editor
}

function previewTemplate(templateType) {
    // This would open the template preview from the templates page
    window.open('{{ route("admin.email-campaigns.templates") }}', '_blank');
}

function showNotification(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Notice',
            text: message,
            icon: type,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}
</script>
@endsection
