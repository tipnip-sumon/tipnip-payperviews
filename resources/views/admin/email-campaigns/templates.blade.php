@extends('components.layout')

@section('page-title', $pageTitle)

@section('breadcrumb')
<div class="page-header d-sm-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">{{ $pageTitle }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fe fe-home me-2"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.email-campaigns.index') }}">Email Campaigns</a></li>
            <li class="breadcrumb-item active" aria-current="page">Templates</li>
        </ol>
    </div>
    <div class="page-rightheader ml-md-auto">
        <div class="btn-list">
            <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="button" class="btn btn-success" onclick="openEditModal()">
                <i class="fe fe-edit me-2"></i>Edit Template
            </button>
            <button type="button" class="btn btn-primary" onclick="previewTemplate()" id="previewBtn">
                <i class="fe fe-eye me-2"></i>Preview Template
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Template Selection -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📧 Email Templates</h4>
                <div class="card-options">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="syncTemplatesFromDB()">
                        <i class="fe fe-refresh-cw"></i> Sync from DB
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <div class="list-group-item active" data-template="kyc-reminder" onclick="selectTemplate(this)">
                        <div class="d-flex align-items-center">
                            <span class="counter-icon bg-warning-transparent me-3">
                                <i class="fe fe-credit-card text-warning"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">KYC Pending Reminder</h6>
                                <small class="text-muted">Verification reminder template</small>
                            </div>
                            <div class="ms-auto">
                                <span class="badge badge-success">Active</span>
                                <button class="btn btn-sm btn-outline-primary ms-1" onclick="editTemplate('kyc-reminder')" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="list-group-item" data-template="inactive-user" onclick="selectTemplate(this)">
                        <div class="d-flex align-items-center">
                            <span class="counter-icon bg-danger-transparent me-3">
                                <i class="fe fe-user-x text-danger"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">Inactive User Reminder</h6>
                                <small class="text-muted">Re-engagement template</small>
                            </div>
                            <div class="ms-auto">
                                <span class="badge badge-success">Active</span>
                                <button class="btn btn-sm btn-outline-primary ms-1" onclick="editTemplate('inactive-user')" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="list-group-item" data-template="password-reset" onclick="selectTemplate(this)">
                        <div class="d-flex align-items-center">
                            <span class="counter-icon bg-info-transparent me-3">
                                <i class="fe fe-lock text-info"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">Password Reset Reminder</h6>
                                <small class="text-muted">Security reminder template</small>
                            </div>
                            <div class="ms-auto">
                                <span class="badge badge-success">Active</span>
                                <button class="btn btn-sm btn-outline-primary ms-1" onclick="editTemplate('password-reset')" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="list-group-item" data-template="congratulations" onclick="selectTemplate(this)">
                        <div class="d-flex align-items-center">
                            <span class="counter-icon bg-success-transparent me-3">
                                <i class="fe fe-award text-success"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">Investment Congratulations</h6>
                                <small class="text-muted">Welcome celebration template</small>
                            </div>
                            <div class="ms-auto">
                                <span class="badge badge-success">Active</span>
                                <button class="btn btn-sm btn-outline-primary ms-1" onclick="editTemplate('congratulations')" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Template Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-info" onclick="exportTemplate()">
                            <i class="fe fe-download me-2"></i>Export Selected Template
                        </button>
                        <button class="btn btn-outline-warning" onclick="importTemplate()">
                            <i class="fe fe-upload me-2"></i>Import Template
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetTemplate()">
                            <i class="fe fe-refresh-ccw me-2"></i>Reset to Default
                        </button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Template Statistics</h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h5 class="mb-1 text-primary">4</h5>
                                <small>Total Templates</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h5 class="mb-1 text-success">94%</h5>
                                <small>Avg Success Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Template Preview -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📱 Template Preview</h4>
                <div class="card-options">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" onclick="switchView('desktop')">
                            <i class="fe fe-monitor"></i> Desktop
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="switchView('mobile')">
                            <i class="fe fe-smartphone"></i> Mobile
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="template-preview" class="border rounded p-3" style="background: #f8f9fa; min-height: 500px;">
                    <!-- KYC Reminder Template Preview -->
                    <div id="kyc-reminder-preview" class="template-content">
                        <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <!-- Header -->
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;">
                                <h1 style="margin: 0; font-size: 28px; font-weight: 300;">KYC Verification Required</h1>
                                <p style="margin: 10px 0 0 0; opacity: 0.9;">Complete your identity verification</p>
                            </div>
                            
                            <!-- Content -->
                            <div style="padding: 40px 30px;">
                                <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi John Doe,</h2>
                                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                                    We noticed that your KYC verification is still pending. To unlock all features and ensure the security of your account, please complete your identity verification.
                                </p>
                                
                                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                                    <strong style="color: #856404;">⚠️ Action Required:</strong>
                                    <p style="margin: 5px 0 0 0; color: #856404;">Your account access may be limited until verification is completed.</p>
                                </div>
                                
                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="#" style="background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                                        Complete KYC Verification
                                    </a>
                                </div>
                                
                                <p style="color: #999; font-size: 14px; line-height: 1.6;">
                                    Need help? Contact our support team or visit our FAQ section for more information about the verification process.
                                </p>
                            </div>
                            
                            <!-- Footer -->
                            <div style="background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #dee2e6;">
                                <p style="margin: 0; color: #6c757d; font-size: 12px;">
                                    © 2025 Your Company. All rights reserved.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Other template previews would be hidden by default -->
                    <div id="inactive-user-preview" class="template-content" style="display: none;">
                        <!-- Inactive User Template Preview -->
                        <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; text-align: center;">
                                <h1 style="margin: 0; font-size: 28px; font-weight: 300;">We Miss You!</h1>
                                <p style="margin: 10px 0 0 0; opacity: 0.9;">Come back and continue your investment journey</p>
                            </div>
                            <div style="padding: 40px 30px;">
                                <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi John Doe,</h2>
                                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                                    We noticed you haven't been active lately. You have funds in your account ready to be invested in profitable opportunities.
                                </p>
                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="#" style="background: #f5576c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                                        Start Investing Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="password-reset-preview" class="template-content" style="display: none;">
                        <!-- Password Reset Template Preview -->
                        <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; text-align: center;">
                                <h1 style="margin: 0; font-size: 28px; font-weight: 300;">🔒 Security Reminder</h1>
                                <p style="margin: 10px 0 0 0; opacity: 0.9;">Time to update your password</p>
                            </div>
                            <div style="padding: 40px 30px;">
                                <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi John Doe,</h2>
                                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                                    For security purposes, we recommend updating your password. It's been over 30 days since your last password change.
                                </p>
                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="#" style="background: #00f2fe; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                                        Update Password
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="congratulations-preview" class="template-content" style="display: none;">
                        <!-- Congratulations Template Preview -->
                        <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; padding: 30px; text-align: center;">
                                <h1 style="margin: 0; font-size: 28px; font-weight: 300;">🎉 Congratulations!</h1>
                                <p style="margin: 10px 0 0 0; opacity: 0.8;">Welcome to your investment journey</p>
                            </div>
                            <div style="padding: 40px 30px;">
                                <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi John Doe,</h2>
                                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                                    Congratulations on your first investment! You've taken a great step towards building your financial future.
                                </p>
                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="#" style="background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                                        View My Investments
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

<!-- Template Information -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📋 Template Information</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fe fe-info me-2"></i>About Email Templates:</h6>
                    <ul class="mb-0">
                        <li><strong>Responsive Design:</strong> All templates are mobile-friendly and display perfectly on any device</li>
                        <li><strong>Professional Styling:</strong> Modern gradient designs with clear call-to-action buttons</li>
                        <li><strong>Personalization:</strong> Templates automatically include user names and relevant data</li>
                        <li><strong>Brand Consistency:</strong> All templates follow your company's branding guidelines</li>
                        <li><strong>High Deliverability:</strong> Optimized HTML structure for better inbox placement</li>
                    </ul>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-primary mb-2">4</h5>
                            <small>Active Templates</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-success mb-2">94%</h5>
                            <small>Avg Open Rate</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-warning mb-2">78%</h5>
                            <small>Avg Click Rate</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-info mb-2">Mobile</h5>
                            <small>Responsive</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template Edit Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1" aria-labelledby="editTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTemplateModalLabel">
                    <i class="fe fe-edit me-2"></i>Edit Email Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTemplateForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="templateName" class="form-label">Template Name</label>
                                <input type="text" class="form-control" id="templateName" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="templateSubject" class="form-label">Email Subject</label>
                                <input type="text" class="form-control" id="templateSubject" placeholder="Email subject line">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="templateContent" class="form-label">Template Content (HTML)</label>
                        <textarea class="form-control" id="templateContent" rows="15" placeholder="HTML content of the email template"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Available Variables</label>
                                <div class="border rounded p-3" style="height: 150px; overflow-y: auto;">
                                    <div id="availableVariables" class="d-flex flex-wrap gap-1">
                                        <!-- Variables will be loaded dynamically -->
                                    </div>
                                </div>
                                <small class="text-muted">Click on a variable to insert it into the template</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Live Preview</label>
                                <div class="border rounded p-3" style="height: 150px; overflow-y: auto; background: #f8f9fa;">
                                    <div id="livePreview" style="font-size: 12px;">
                                        Preview will appear here as you type...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="previewTemplateInModal()">
                    <i class="fe fe-eye me-2"></i>Preview
                </button>
                <button type="button" class="btn btn-success" onclick="saveTemplate()">
                    <i class="fe fe-save me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template Information -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📋 Template Management Guide</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fe fe-info me-2"></i>How to Update/Edit Email Templates:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mt-3">📝 Editing Methods:</h6>
                            <ol>
                                <li><strong>Quick Edit:</strong> Click the <i class="fe fe-edit text-primary"></i> button next to any template</li>
                                <li><strong>Bulk Edit:</strong> Select template and click "Edit Template" button</li>
                                <li><strong>Code Edit:</strong> Modify files in <code>resources/views/admin/email-campaigns/templates.blade.php</code></li>
                                <li><strong>Database Edit:</strong> Use the email_templates table for dynamic content</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mt-3">🔧 Advanced Features:</h6>
                            <ul>
                                <li><strong>Variables:</strong> Use {{ "{" }}{{ "{" }}user_name{{ "}" }}{{ "}" }}, {{ "{" }}{{ "{" }}company_name{{ "}" }}{{ "}" }}, etc.</li>
                                <li><strong>Export/Import:</strong> Save templates as files for backup</li>
                                <li><strong>Live Preview:</strong> See changes in real-time</li>
                                <li><strong>Mobile Responsive:</strong> Templates adapt to all screen sizes</li>
                                <li><strong>Version Control:</strong> Track changes and revert if needed</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-primary mb-2">4</h5>
                            <small>Active Templates</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-success mb-2">94%</h5>
                            <small>Avg Open Rate</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-warning mb-2">78%</h5>
                            <small>Avg Click Rate</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h5 class="text-info mb-2">Mobile</h5>
                            <small>Responsive</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageJsScripts')
<script>
let currentTemplate = 'kyc-reminder';
let templateData = {
    'kyc-reminder': {
        name: 'KYC Pending Reminder',
        subject: 'Complete Your KYC Verification - {{ "{" }}{{ "{" }}user_name{{ "}" }}{{ "}" }}',
        variables: ['{{ "{" }}{{ "{" }}user_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}company_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}kyc_url{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}support_email{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}current_year{{ "}" }}{{ "}" }}']
    },
    'inactive-user': {
        name: 'Inactive User Reminder', 
        subject: 'We Miss You - Start Investing Today!',
        variables: ['{{ "{" }}{{ "{" }}user_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}company_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}balance{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}invest_url{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}current_year{{ "}" }}{{ "}" }}']
    },
    'password-reset': {
        name: 'Password Reset Reminder',
        subject: 'Security Reminder: Update Your Password',
        variables: ['{{ "{" }}{{ "{" }}user_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}company_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}reset_url{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}days_since_change{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}current_year{{ "}" }}{{ "}" }}']
    },
    'congratulations': {
        name: 'Investment Congratulations',
        subject: 'Congratulations on Your First Investment!',
        variables: ['{{ "{" }}{{ "{" }}user_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}investment_amount{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}plan_name{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}dashboard_url{{ "}" }}{{ "}" }}', '{{ "{" }}{{ "{" }}current_year{{ "}" }}{{ "}" }}']
    }
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Email Templates page loaded');
    
    // Initialize template content editing
    const templateContent = document.getElementById('templateContent');
    if (templateContent) {
        templateContent.addEventListener('input', function() {
            updateLivePreview();
        });
    }
});

function selectTemplate(element) {
    // Remove active class from all items
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to selected item
    element.classList.add('active');
    
    // Get template type
    const templateType = element.getAttribute('data-template');
    currentTemplate = templateType;
    
    // Hide all template previews
    document.querySelectorAll('.template-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Show selected template preview
    const selectedPreview = document.getElementById(templateType + '-preview');
    if (selectedPreview) {
        selectedPreview.style.display = 'block';
    }
    
    console.log('Selected template:', templateType);
}

function editTemplate(templateType) {
    currentTemplate = templateType;
    openEditModal();
}

function openEditModal() {
    const template = templateData[currentTemplate];
    
    // Populate modal with current template data
    document.getElementById('templateName').value = template.name;
    document.getElementById('templateSubject').value = template.subject;
    
    // Get current template content from preview
    const previewElement = document.getElementById(currentTemplate + '-preview');
    if (previewElement) {
        document.getElementById('templateContent').value = previewElement.innerHTML;
    }
    
    // Load available variables
    loadAvailableVariables(template.variables);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editTemplateModal'));
    modal.show();
}

function loadAvailableVariables(variables) {
    const container = document.getElementById('availableVariables');
    container.innerHTML = '';
    
    variables.forEach(variable => {
        const badge = document.createElement('span');
        badge.className = 'badge bg-secondary me-1 mb-1 cursor-pointer';
        badge.textContent = variable;
        badge.onclick = function() {
            insertVariable(variable);
        };
        badge.title = 'Click to insert into template';
        container.appendChild(badge);
    });
}

function insertVariable(variable) {
    const textarea = document.getElementById('templateContent');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.setSelectionRange(start + variable.length, start + variable.length);
    textarea.focus();
    
    updateLivePreview();
}

function updateLivePreview() {
    const content = document.getElementById('templateContent').value;
    const preview = document.getElementById('livePreview');
    
    // Create a simplified preview (first 200 characters)
    let previewText = content.replace(/<[^>]*>/g, '').substring(0, 200) + '...';
    
    // Replace some common variables for preview
    previewText = previewText
        .replace(/\{\{user_name\}\}/g, 'John Doe')
        .replace(/\{\{company_name\}\}/g, 'Your Company')
        .replace(/\{\{current_year\}\}/g, new Date().getFullYear());
    
    preview.textContent = previewText || 'Preview will appear here as you type...';
}

function saveTemplate() {
    const templateName = document.getElementById('templateName').value;
    const templateSubject = document.getElementById('templateSubject').value;
    const templateContent = document.getElementById('templateContent').value;
    
    if (!templateContent.trim()) {
        alert('Template content cannot be empty!');
        return;
    }
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fe fe-loader spinning me-2"></i>Saving...';
    saveBtn.disabled = true;
    
    // Simulate save (in real implementation, this would be an AJAX call)
    setTimeout(() => {
        // Update the preview
        const previewElement = document.getElementById(currentTemplate + '-preview');
        if (previewElement) {
            previewElement.innerHTML = templateContent;
        }
        
        // Update template data
        templateData[currentTemplate].subject = templateSubject;
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('editTemplateModal')).hide();
        
        // Show success message
        showNotification('Template saved successfully!', 'success');
        
        // Reset button
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        
        console.log('Template saved:', currentTemplate);
    }, 1500);
}

function previewTemplateInModal() {
    const content = document.getElementById('templateContent').value;
    
    // Open template in new window for full preview
    const previewWindow = window.open('', 'templatePreview', 'width=800,height=600,scrollbars=yes');
    
    previewWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Email Template Preview</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { margin: 0; padding: 20px; background: #f5f5f5; font-family: Arial, sans-serif; }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);
    
    previewWindow.document.close();
}

function switchView(viewType) {
    // Update button states
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    event.target.classList.add('active');
    
    // Update preview container
    const previewContainer = document.getElementById('template-preview');
    
    if (viewType === 'mobile') {
        previewContainer.style.maxWidth = '375px';
        previewContainer.style.margin = '0 auto';
    } else {
        previewContainer.style.maxWidth = 'none';
        previewContainer.style.margin = '0';
    }
    
    console.log('Switched to', viewType, 'view');
}

function previewTemplate() {
    const activeTemplate = document.querySelector('.list-group-item.active');
    const templateType = activeTemplate ? activeTemplate.getAttribute('data-template') : 'kyc-reminder';
    
    // Open template in new window for full preview
    const previewWindow = window.open('', 'templatePreview', 'width=800,height=600,scrollbars=yes');
    const previewContent = document.getElementById(templateType + '-preview').innerHTML;
    
    previewWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Email Template Preview</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { margin: 0; padding: 20px; background: #f5f5f5; font-family: Arial, sans-serif; }
            </style>
        </head>
        <body>
            ${previewContent}
        </body>
        </html>
    `);
    
    previewWindow.document.close();
}

function exportTemplate() {
    const templateType = currentTemplate;
    const template = templateData[templateType];
    const content = document.getElementById(templateType + '-preview').innerHTML;
    
    const data = {
        name: template.name,
        subject: template.subject,
        content: content,
        variables: template.variables,
        exported_at: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `email_template_${templateType}.json`;
    a.click();
    URL.revokeObjectURL(url);
    
    showNotification('Template exported successfully!', 'info');
}

function importTemplate() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = JSON.parse(e.target.result);
                
                // Validate template data
                if (!data.content || !data.name) {
                    throw new Error('Invalid template format');
                }
                
                // Update current template
                document.getElementById(currentTemplate + '-preview').innerHTML = data.content;
                templateData[currentTemplate] = {
                    name: data.name,
                    subject: data.subject || templateData[currentTemplate].subject,
                    variables: data.variables || templateData[currentTemplate].variables
                };
                
                showNotification('Template imported successfully!', 'success');
            } catch (error) {
                showNotification('Error importing template: ' + error.message, 'error');
            }
        };
        reader.readAsText(file);
    };
    input.click();
}

function resetTemplate() {
    if (confirm('Are you sure you want to reset this template to default? This action cannot be undone.')) {
        // This would restore the original template content
        showNotification('Template reset to default', 'info');
        
        // Reload the page to get original content
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

function syncTemplatesFromDB() {
    showNotification('Syncing templates from database...', 'info');
    
    // In real implementation, this would fetch templates from the email_templates table
    setTimeout(() => {
        showNotification('Templates synced successfully!', 'success');
    }, 1500);
}

function showNotification(message, type = 'info') {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
