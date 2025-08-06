<x-layout>
    @section('top_title', $pageTitle)
    @section('title',$pageTitle)

@section('content')
<div class="container-fluid"> 
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-0">General Settings</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">General Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="ri-refresh-line"></i> Clear Cache
                </button>
            </form>
            <form action="{{ route('admin.settings.toggle-maintenance') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn {{ ($settings->maintenance_mode ?? false) ? 'btn-success' : 'btn-danger' }}">
                    <i class="ri-tools-line"></i> 
                    {{ ($settings->maintenance_mode ?? false) ? 'Disable' : 'Enable' }} Maintenance
                </button>
            </form>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($settings->maintenance_mode ?? false)
        <div class="alert alert-warning" role="alert">
            <i class="ri-tools-line me-2"></i>
            <strong>Maintenance Mode is Active!</strong> Your site is currently in maintenance mode.
        </div>
    @endif

    <!-- Settings Navigation -->
    <div class="row mb-4 my-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Settings Categories
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.general') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                                <i class="fas fa-cog fa-2x mb-2"></i>
                                <span>General</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.media') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-images fa-2x mb-2"></i>
                                <span>Media & Logos</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.seo') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <span>SEO Settings</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.content') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-edit fa-2x mb-2"></i>
                                <span>Content</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.theme') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-palette fa-2x mb-2"></i>
                                <span>Theme</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.social-media') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-share-alt fa-2x mb-2"></i>
                                <span>Social Media</span>
                            </a>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.mail-config') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-envelope fa-2x mb-2"></i>
                                <span>Mail Config</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.sms-config') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-sms fa-2x mb-2"></i>
                                <span>SMS Config</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.settings.security') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                <span>Security</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="#" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-bell fa-2x mb-2"></i>
                                <span>Notifications</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="#" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-globe fa-2x mb-2"></i>
                                <span>Localization</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="#" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-database fa-2x mb-2"></i>
                                <span>Backup</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#general-tab" role="tab">
                                <i class="ri-settings-line me-2"></i>General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#appearance-tab" role="tab">
                                <i class="ri-palette-line me-2"></i>Appearance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#email-tab" role="tab">
                                <i class="ri-mail-line me-2"></i>Email
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#sms-tab" role="tab">
                                <i class="ri-message-line me-2"></i>SMS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#finance-tab" role="tab">
                                <i class="ri-money-dollar-circle-line me-2"></i>Finance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#system-tab" role="tab">
                                <i class="ri-computer-line me-2"></i>System
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#backup-tab" role="tab">
                                <i class="ri-download-cloud-line me-2"></i>Backup
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="tab-content">
                            <!-- General Settings Tab -->
                            <div class="tab-pane fade show active" id="general-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                                    value="{{ old('site_name', $settings->site_name ?? 'ViewCash') }}" required>
                                            @error('site_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email_from" class="form-label">Email From <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email_from" name="email_from" 
                                                    value="{{ old('email_from', $settings->email_from ?? 'admin@viewcash.com') }}" required>
                                            @error('email_from')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cur_text" class="form-label">Currency Text <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="cur_text" name="cur_text" 
                                                    value="{{ old('cur_text', $settings->cur_text ?? 'USD') }}" required>
                                            @error('cur_text')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cur_sym" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="cur_sym" name="cur_sym" 
                                                    value="{{ old('cur_sym', $settings->cur_sym ?? '$') }}" required>
                                            @error('cur_sym')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="active_template" class="form-label">Active Template</label>
                                            <select class="form-select" id="active_template" name="active_template">
                                                <option value="default" {{ ($settings->active_template ?? 'default') == 'default' ? 'selected' : '' }}>Default</option>
                                                <option value="modern" {{ ($settings->active_template ?? 'default') == 'modern' ? 'selected' : '' }}>Modern</option>
                                                <option value="classic" {{ ($settings->active_template ?? 'default') == 'classic' ? 'selected' : '' }}>Classic</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- System Controls -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">System Controls</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="registration" name="registration" 
                                                    {{ ($settings->registration ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="registration">
                                                Allow Registration
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="force_ssl" name="force_ssl" 
                                                    {{ $settings->force_ssl ? 'checked' : '' }}>
                                            <label class="form-check-label" for="force_ssl">
                                                Force SSL
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="secure_password" name="secure_password" 
                                                    {{ $settings->secure_password ? 'checked' : '' }}>
                                            <label class="form-check-label" for="secure_password">
                                                Secure Password Required
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="agree" name="agree" 
                                                    {{ $settings->agree ? 'checked' : '' }}>
                                            <label class="form-check-label" for="agree">
                                                Terms & Conditions Agreement Required
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Verification Settings -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">Verification Settings</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="ev" name="ev" 
                                                    {{ $settings->ev ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ev">
                                                Email Verification Required
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="sv" name="sv" 
                                                    {{ $settings->sv ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sv">
                                                SMS Verification Required
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="kv" name="kv" 
                                                    {{ $settings->kv ? 'checked' : '' }}>
                                            <label class="form-check-label" for="kv">
                                                KYC Verification Required
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Appearance Settings Tab -->
                            <div class="tab-pane fade" id="appearance-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="base_color" class="form-label">Primary Color</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="color" class="form-control form-control-color" id="base_color" 
                                                        name="base_color" value="{{ old('base_color', $settings->base_color ?? '#007bff') }}">
                                                <input type="text" class="form-control" value="{{ old('base_color', $settings->base_color ?? '#007bff') }}" 
                                                        readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="secondary_color" class="form-label">Secondary Color</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="color" class="form-control form-control-color" id="secondary_color" 
                                                        name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}">
                                                <input type="text" class="form-control" value="{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}" 
                                                        readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="language_switch" name="language_switch" 
                                                    {{ $settings->language_switch ? 'checked' : '' }}>
                                            <label class="form-check-label" for="language_switch">
                                                Enable Language Switcher
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Color Preview</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex gap-3">
                                                    <div class="preview-box p-3 rounded text-white" 
                                                            style="background-color: {{ $settings->base_color ?? '#007bff' }}">
                                                        Primary Color
                                                    </div>
                                                    <div class="preview-box p-3 rounded text-white" 
                                                            style="background-color: {{ $settings->secondary_color ?? '#6c757d' }}">
                                                        Secondary Color
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Settings Tab -->
                            <div class="tab-pane fade" id="email-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="en" name="en" 
                                                    {{ $settings->en ? 'checked' : '' }}>
                                            <label class="form-check-label" for="en">
                                                Enable Email Notifications
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="email_template" class="form-label">Email Template</label>
                                            <textarea class="form-control" id="email_template" name="email_template" rows="10">{{ old('email_template', $settings->email_template) }}</textarea>
                                            <div class="form-text">Available placeholders: </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Test Email Configuration</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <input type="email" class="form-control" id="test_email_input" 
                                                        placeholder="Enter email address to test">
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-info" onclick="showTestEmailModal()">
                                                    <i class="ri-send-plane-line"></i> Test Email Configuration
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SMS Settings Tab -->
                            <div class="tab-pane fade" id="sms-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="sn" name="sn" 
                                                    {{ $settings->sn ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sn">
                                                Enable SMS Notifications
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sms_from" class="form-label">SMS From</label>
                                            <input type="text" class="form-control" id="sms_from" name="sms_from" 
                                                    value="{{ old('sms_from', $settings->sms_from) }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="sms_body" class="form-label">SMS Template</label>
                                            <textarea class="form-control" id="sms_body" name="sms_body" rows="5">{{ old('sms_body', $settings->sms_body) }}</textarea>
                                            <div class="form-text">Available placeholders: </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Finance Settings Tab -->
                            <div class="tab-pane fade" id="finance-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="signup_bonus_amount" class="form-label">Signup Bonus Amount</label>
                                            <input type="number" class="form-control" id="signup_bonus_amount" 
                                                    name="signup_bonus_amount" step="0.01" min="0"
                                                    value="{{ old('signup_bonus_amount', $settings->signup_bonus_amount) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3 mt-4">
                                            <input class="form-check-input" type="checkbox" id="signup_bonus_control" 
                                                    name="signup_bonus_control" {{ $settings->signup_bonus_control ? 'checked' : '' }}>
                                            <label class="form-check-label" for="signup_bonus_control">
                                                Enable Signup Bonus
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="f_charge" class="form-label">Fixed Charge</label>
                                            <input type="number" class="form-control" id="f_charge" name="f_charge" 
                                                    step="0.00000001" min="0"
                                                    value="{{ old('f_charge', $settings->f_charge) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="p_charge" class="form-label">Percentage Charge (%)</label>
                                            <input type="number" class="form-control" id="p_charge" name="p_charge" 
                                                    step="0.01" min="0" max="100"
                                                    value="{{ old('p_charge', $settings->p_charge) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">Commission Settings</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="deposit_commission" 
                                                    name="deposit_commission" {{ $settings->deposit_commission ? 'checked' : '' }}>
                                            <label class="form-check-label" for="deposit_commission">
                                                Enable Deposit Commission
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="invest_commission" 
                                                    name="invest_commission" {{ $settings->invest_commission ? 'checked' : '' }}>
                                            <label class="form-check-label" for="invest_commission">
                                                Enable Investment Commission
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="invest_return_commission" 
                                                    name="invest_return_commission" {{ $settings->invest_return_commission ? 'checked' : '' }}>
                                            <label class="form-check-label" for="invest_return_commission">
                                                Enable Investment Return Commission
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="b_transfer" 
                                                    name="b_transfer" {{ $settings->b_transfer ? 'checked' : '' }}>
                                            <label class="form-check-label" for="b_transfer">
                                                Enable Balance Transfer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Settings Tab -->
                            <div class="tab-pane fade" id="system-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="holiday_withdraw" 
                                                    name="holiday_withdraw" {{ $settings->holiday_withdraw ? 'checked' : '' }}>
                                            <label class="form-check-label" for="holiday_withdraw">
                                                Allow Holiday Withdrawals
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="promotional_tool" 
                                                    name="promotional_tool" {{ $settings->promotional_tool ? 'checked' : '' }}>
                                            <label class="form-check-label" for="promotional_tool">
                                                Enable Promotional Tools
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="push_notify" 
                                                    name="push_notify" {{ $settings->push_notify ? 'checked' : '' }}>
                                            <label class="form-check-label" for="push_notify">
                                                Enable Push Notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">System Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row" id="system-info">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Backup Settings Tab -->
                            <div class="tab-pane fade" id="backup-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Export Settings</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted mb-3">Export all general settings as JSON file for backup purposes.</p>
                                                <a href="{{ route('admin.settings.export') }}" class="btn btn-primary">
                                                    <i class="ri-download-line"></i> Export Settings
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Import Settings</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted mb-3">Import settings from a previously exported JSON file.</p>
                                                <form action="{{ route('admin.settings.import') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <input type="file" class="form-control" name="settings_file" accept=".json" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="ri-upload-line"></i> Import Settings
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="ri-refresh-line"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load system information
    loadSystemInfo();
    
    // Color picker sync
    document.querySelectorAll('input[type="color"]').forEach(colorInput => {
        colorInput.addEventListener('input', function() {
            const textInput = this.parentElement.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = this.value;
            }
            
            // Update preview
            if (this.id === 'base_color') {
                document.querySelector('.preview-box:first-child').style.backgroundColor = this.value;
            } else if (this.id === 'secondary_color') {
                document.querySelector('.preview-box:last-child').style.backgroundColor = this.value;
            }
        });
    });
});

function loadSystemInfo() {
    fetch('{{ route("admin.settings.system-info") }}')
        .then(response => response.json())
        .then(data => {
            const systemInfoDiv = document.getElementById('system-info');
            let html = '';
            
            Object.entries(data).forEach(([key, value]) => {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">${label}:</span>
                            <span class="fw-medium">${value}</span>
                        </div>
                    </div>
                `;
            });
            
            systemInfoDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading system info:', error);
            document.getElementById('system-info').innerHTML = '<div class="col-12 text-center text-danger">Failed to load system information</div>';
        });
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.querySelector('form').reset();
    }
}
</script>
@endsection
</x-layout>
