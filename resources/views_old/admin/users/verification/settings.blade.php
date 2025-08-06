@extends('components.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cogs me-2"></i>
                        <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.users.verification.settings.update') }}">
                    @csrf
                    
                    <!-- Email Verification Settings -->
                    <div class="row g-4">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="fas fa-envelope me-2"></i>Email Verification Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Email Verification Required</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="email_verification_required" value="1" 
                                                   {{ $settings['email_verification_required'] ? 'checked' : '' }}
                                                   id="emailVerificationRequired">
                                            <label class="form-check-label" for="emailVerificationRequired">
                                                <span class="text-success">Required</span> / <span class="text-danger">Optional</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-1">
                                            Require users to verify their email address before account activation
                                        </small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Auto Verify Email</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="auto_verify_email" value="1" 
                                                   {{ $settings['auto_verify_email'] ? 'checked' : '' }}
                                                   id="autoVerifyEmail">
                                            <label class="form-check-label" for="autoVerifyEmail">
                                                <span class="text-success">Enabled</span> / <span class="text-danger">Disabled</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-1">
                                            Automatically verify email addresses upon registration
                                        </small>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">Email Template</label>
                                        <select name="verification_email_template" class="form-select">
                                            <option value="default" {{ $settings['verification_email_template'] == 'default' ? 'selected' : '' }}>Default Template</option>
                                            <option value="custom" {{ $settings['verification_email_template'] == 'custom' ? 'selected' : '' }}>Custom Template</option>
                                        </select>
                                        <small class="form-text text-muted mt-1">
                                            Choose the email template for verification emails
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SMS Verification Settings -->
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="fas fa-sms me-2"></i>SMS Verification Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">SMS Verification Required</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="sms_verification_required" value="1" 
                                                   {{ $settings['sms_verification_required'] ? 'checked' : '' }}
                                                   id="smsVerificationRequired">
                                            <label class="form-check-label" for="smsVerificationRequired">
                                                <span class="text-success">Required</span> / <span class="text-danger">Optional</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-1">
                                            Require users to verify their mobile number via SMS
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phone and Identity Verification Settings -->
                    <div class="row g-4">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="card border-secondary h-100">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="fas fa-phone me-2"></i>Phone Verification Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">Phone Verification Required</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="phone_verification_required" value="1" 
                                                   {{ $settings['phone_verification_required'] ? 'checked' : '' }}
                                                   id="phoneVerificationRequired">
                                            <label class="form-check-label" for="phoneVerificationRequired">
                                                <span class="text-success">Required</span> / <span class="text-danger">Optional</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-1">
                                            Require users to verify their phone number via voice call
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="card border-dark h-100">
                                <div class="card-header bg-dark text-white">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="fas fa-user-shield me-2"></i>Identity Verification Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">Identity Verification Required</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="identity_verification_required" value="1" 
                                                   {{ $settings['identity_verification_required'] ? 'checked' : '' }}
                                                   id="identityVerificationRequired">
                                            <label class="form-check-label" for="identityVerificationRequired">
                                                <span class="text-success">Required</span> / <span class="text-danger">Optional</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-1">
                                            Require users to submit identity documents for verification
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KYC and 2FA Settings -->
                    <div class="row g-4">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-id-card me-2"></i>KYC Verification Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">KYC Verification Required</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="kyc_verification_required" value="1" 
                                                   {{ $settings['kyc_verification_required'] ? 'checked' : '' }}
                                                   id="kycVerificationRequired">
                                            <label class="form-check-label" for="kycVerificationRequired">
                                                <span class="text-success">Required</span> / <span class="text-danger">Optional</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-1">
                                            Require users to complete full KYC verification process
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="card border-danger h-100">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="fas fa-lock me-2"></i>Two Factor Authentication Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">2FA Required</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="two_fa_required" value="1" 
                                                   {{ $settings['two_fa_required'] ? 'checked' : '' }}
                                                   id="twoFaRequired">
                                            <label class="form-check-label" for="twoFaRequired">
                                                <span class="text-success">Required</span> / <span class="text-danger">Optional</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-1">
                                            Require users to enable Two Factor Authentication
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon me-3">
                                        <i class="fas fa-info-circle fa-2x text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-2">
                                            <i class="fas fa-shield-alt me-2"></i>Security Notice
                                        </h6>
                                        <p class="mb-0 text-muted">
                                            Enabling verification requirements will enhance the security of your platform but may also affect user registration experience. 
                                            Consider your target audience and security requirements when configuring these settings.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Save Verification Settings
                                </button>
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
    'use strict';
    
    $(document).ready(function() {
        // Enhanced form switch styling and interactions
        $('.form-check-input').on('change', function() {
            const label = $(this).next('.form-check-label');
            const isChecked = $(this).is(':checked');
            
            // Update visual feedback
            if (isChecked) {
                $(this).closest('.card').addClass('border-success').removeClass('border-secondary');
                label.find('.text-success').addClass('fw-bold');
                label.find('.text-danger').removeClass('fw-bold');
            } else {
                $(this).closest('.card').removeClass('border-success').addClass('border-secondary');
                label.find('.text-danger').addClass('fw-bold');
                label.find('.text-success').removeClass('fw-bold');
            }
        });

        // Initialize current states
        $('.form-check-input').each(function() {
            $(this).trigger('change');
        });

        // Form submission with loading state
        $('form').on('submit', function(e) {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
            
            // Re-enable after 3 seconds in case of network issues
            setTimeout(function() {
                submitBtn.prop('disabled', false).html(originalText);
            }, 3000);
        });

        // Add confirmation for critical security settings
        $('#twoFaRequired, #kycVerificationRequired').on('change', function() {
            const settingName = $(this).closest('.card').find('.card-title').text().trim();
            
            if ($(this).is(':checked')) {
                if (!confirm(`Are you sure you want to make ${settingName} mandatory for all users? This will affect the user registration process.`)) {
                    $(this).prop('checked', false).trigger('change');
                }
            }
        });

        // Responsive card height equalization
        function equalizeCardHeights() {
            $('.row.g-4').each(function() {
                const cards = $(this).find('.card');
                let maxHeight = 0;
                
                // Reset heights
                cards.css('height', 'auto');
                
                // Find max height only on larger screens
                if ($(window).width() >= 768) {
                    cards.each(function() {
                        const height = $(this).outerHeight();
                        if (height > maxHeight) {
                            maxHeight = height;
                        }
                    });
                    
                    // Set all cards to max height
                    cards.css('height', maxHeight + 'px');
                }
            });
        }

        // Run on load and resize
        equalizeCardHeights();
        $(window).on('resize', equalizeCardHeights);
    });
</script>
@endpush
