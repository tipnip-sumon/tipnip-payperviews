<x-layout>
    @section('title', $pageTitle)
    @section('content')
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt me-2"></i>
                        Transfer & Withdrawal Conditions
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetToDefaults()">
                            <i class="fas fa-undo"></i> Reset to Defaults
                        </button>
                    </div>
                </div>
                
                <form action="{{ route('admin.transfer-withdraw-conditions.update') }}" method="POST" id="conditionsForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Transfer Conditions -->
                            <div class="col-lg-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-exchange-alt me-2"></i>
                                            Transfer Conditions
                                        </h5>
                                        <small>Requirements for money transfers between users</small>
                                    </div>
                                    <div class="card-body">
                                        <!-- KYC Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="transfer_kyc_required" 
                                                   name="transfer_kyc_required" 
                                                   {{ $transferConditions['kyc_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="transfer_kyc_required">
                                                <strong>KYC Verification Required</strong>
                                                <br><small class="text-muted">Users must complete identity verification</small>
                                            </label>
                                        </div>

                                        <!-- Email Verification Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="transfer_email_verification_required" 
                                                   name="transfer_email_verification_required" 
                                                   {{ $transferConditions['email_verification_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="transfer_email_verification_required">
                                                <strong>Email Verification Required</strong>
                                                <br><small class="text-muted">Users must verify their email address</small>
                                            </label>
                                        </div>

                                        <!-- Profile Completion Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="transfer_profile_complete_required" 
                                                   name="transfer_profile_complete_required" 
                                                   {{ $transferConditions['profile_complete_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="transfer_profile_complete_required">
                                                <strong>Profile Completion Required</strong>
                                                <br><small class="text-muted">Users must complete their profile information</small>
                                            </label>
                                        </div>

                                        <!-- Referral Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="transfer_referral_required" 
                                                   name="transfer_referral_required" 
                                                   {{ $transferConditions['referral_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="transfer_referral_required">
                                                <strong>Referral Requirement</strong>
                                                <br><small class="text-muted">Require users to have referred users with investments</small>
                                            </label>
                                        </div>

                                        <!-- Advanced Referral Configuration -->
                                        <div id="transfer_referral_config" class="border rounded p-3 mb-3" style="{{ $transferConditions['referral_required'] ? '' : 'display: none;' }}">
                                            <h6 class="text-primary">
                                                <i class="fas fa-cog me-1"></i>
                                                Referral Configuration
                                            </h6>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="transfer_minimum_referrals" class="form-label">Minimum Referrals</label>
                                                        <input type="number" class="form-control" id="transfer_minimum_referrals" 
                                                               name="transfer_minimum_referrals" min="1" max="10"
                                                               value="{{ $transferConditions['referral_conditions']['minimum_referrals'] ?? 1 }}">
                                                        <small class="text-muted">Number of referrals required</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="transfer_minimum_investment_amount" class="form-label">Minimum Investment ($)</label>
                                                        <input type="number" class="form-control" id="transfer_minimum_investment_amount" 
                                                               name="transfer_minimum_investment_amount" min="1" step="0.01"
                                                               value="{{ $transferConditions['referral_conditions']['minimum_investment_amount'] ?? 50 }}">
                                                        <small class="text-muted">Minimum investment amount per referral</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="transfer_require_active_investment" 
                                                               name="transfer_require_active_investment" 
                                                               {{ ($transferConditions['referral_conditions']['require_active_investment'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="transfer_require_active_investment">
                                                            Require Active Investment
                                                            <br><small class="text-muted">Referrals must have active investments</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="transfer_allow_multiple_small_investments" 
                                                               name="transfer_allow_multiple_small_investments" 
                                                               {{ ($transferConditions['referral_conditions']['allow_multiple_small_investments'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="transfer_allow_multiple_small_investments">
                                                            Allow Multiple Small Investments
                                                            <br><small class="text-muted">Sum of smaller investments can meet minimum</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="transfer_minimum_investment_duration_days" class="form-label">Minimum Investment Duration (Days)</label>
                                                <input type="number" class="form-control" id="transfer_minimum_investment_duration_days" 
                                                       name="transfer_minimum_investment_duration_days" min="0" max="365"
                                                       value="{{ $transferConditions['referral_conditions']['minimum_investment_duration_days'] ?? 0 }}">
                                                <small class="text-muted">How long investment must exist (0 = no minimum)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Withdrawal Conditions -->
                            <div class="col-lg-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-money-bill-wave me-2"></i>
                                            Withdrawal Conditions
                                        </h5>
                                        <small>Requirements for withdrawing funds</small>
                                    </div>
                                    <div class="card-body">
                                        <!-- KYC Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="withdrawal_kyc_required" 
                                                   name="withdrawal_kyc_required" 
                                                   {{ $withdrawalConditions['kyc_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="withdrawal_kyc_required">
                                                <strong>KYC Verification Required</strong>
                                                <br><small class="text-muted">Users must complete identity verification</small>
                                            </label>
                                        </div>

                                        <!-- Email Verification Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="withdrawal_email_verification_required" 
                                                   name="withdrawal_email_verification_required" 
                                                   {{ $withdrawalConditions['email_verification_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="withdrawal_email_verification_required">
                                                <strong>Email Verification Required</strong>
                                                <br><small class="text-muted">Users must verify their email address</small>
                                            </label>
                                        </div>

                                        <!-- Profile Completion Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="withdrawal_profile_complete_required" 
                                                   name="withdrawal_profile_complete_required" 
                                                   {{ $withdrawalConditions['profile_complete_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="withdrawal_profile_complete_required">
                                                <strong>Profile Completion Required</strong>
                                                <br><small class="text-muted">Users must complete their profile information</small>
                                            </label>
                                        </div>

                                        <!-- Referral Requirement -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="withdrawal_referral_required" 
                                                   name="withdrawal_referral_required" 
                                                   {{ $withdrawalConditions['referral_required'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="withdrawal_referral_required">
                                                <strong>Referral Requirement</strong>
                                                <br><small class="text-muted">Require users to have referred users with investments</small>
                                            </label>
                                        </div>

                                        <!-- Advanced Referral Configuration -->
                                        <div id="withdrawal_referral_config" class="border rounded p-3 mb-3" style="{{ $withdrawalConditions['referral_required'] ? '' : 'display: none;' }}">
                                            <h6 class="text-primary">
                                                <i class="fas fa-cog me-1"></i>
                                                Referral Configuration
                                            </h6>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="withdrawal_minimum_referrals" class="form-label">Minimum Referrals</label>
                                                        <input type="number" class="form-control" id="withdrawal_minimum_referrals" 
                                                               name="withdrawal_minimum_referrals" min="1" max="10"
                                                               value="{{ $withdrawalConditions['referral_conditions']['minimum_referrals'] ?? 1 }}">
                                                        <small class="text-muted">Number of referrals required</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="withdrawal_minimum_investment_amount" class="form-label">Minimum Investment ($)</label>
                                                        <input type="number" class="form-control" id="withdrawal_minimum_investment_amount" 
                                                               name="withdrawal_minimum_investment_amount" min="1" step="0.01"
                                                               value="{{ $withdrawalConditions['referral_conditions']['minimum_investment_amount'] ?? 50 }}">
                                                        <small class="text-muted">Minimum investment amount per referral</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="withdrawal_require_active_investment" 
                                                               name="withdrawal_require_active_investment" 
                                                               {{ ($withdrawalConditions['referral_conditions']['require_active_investment'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="withdrawal_require_active_investment">
                                                            Require Active Investment
                                                            <br><small class="text-muted">Referrals must have active investments</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="withdrawal_allow_multiple_small_investments" 
                                                               name="withdrawal_allow_multiple_small_investments" 
                                                               {{ ($withdrawalConditions['referral_conditions']['allow_multiple_small_investments'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="withdrawal_allow_multiple_small_investments">
                                                            Allow Multiple Small Investments
                                                            <br><small class="text-muted">Sum of smaller investments can meet minimum</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="withdrawal_minimum_investment_duration_days" class="form-label">Minimum Investment Duration (Days)</label>
                                                <input type="number" class="form-control" id="withdrawal_minimum_investment_duration_days" 
                                                       name="withdrawal_minimum_investment_duration_days" min="0" max="365"
                                                       value="{{ $withdrawalConditions['referral_conditions']['minimum_investment_duration_days'] ?? 0 }}">
                                                <small class="text-muted">How long investment must exist (0 = no minimum)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Information Panel -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Important Information</h6>
                                    <ul class="mb-0">
                                        <li><strong>KYC Verification:</strong> Users must submit and get approved identity documents</li>
                                        <li><strong>Email Verification:</strong> Users must click the verification link sent to their email</li>
                                        <li><strong>Profile Completion:</strong> Users must fill all required profile fields (name, mobile, country, address)</li>
                                        <li><strong>Referral Requirement:</strong> Users must have at least one referred user with an active deposit plan</li>
                                        <li><strong>Impact:</strong> These conditions will be checked before allowing transfer or withdrawal operations</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Quick Actions</h6>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="enableAllTransfer()">
                                                Enable All Transfer
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="disableAllTransfer()">
                                                Disable All Transfer
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="enableAllWithdrawal()">
                                                Enable All Withdrawal
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="disableAllWithdrawal()">
                                                Disable All Withdrawal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Last updated: {{ $settings->updated_at ? $settings->updated_at->format('M d, Y g:i A') : 'Never' }}
                                </small>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>
                                    Save Conditions
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
    <script>
        // Quick action functions
        function enableAllTransfer() {
            $('#transfer_kyc_required, #transfer_email_verification_required, #transfer_profile_complete_required, #transfer_referral_required').prop('checked', true);
            toggleReferralConfig();
        }

        function disableAllTransfer() {
            $('#transfer_kyc_required, #transfer_email_verification_required, #transfer_profile_complete_required, #transfer_referral_required').prop('checked', false);
            toggleReferralConfig();
        }

        function enableAllWithdrawal() {
            $('#withdrawal_kyc_required, #withdrawal_email_verification_required, #withdrawal_profile_complete_required, #withdrawal_referral_required').prop('checked', true);
            toggleReferralConfig();
        }

        function disableAllWithdrawal() {
            $('#withdrawal_kyc_required, #withdrawal_email_verification_required, #withdrawal_profile_complete_required, #withdrawal_referral_required').prop('checked', false);
            toggleReferralConfig();
        }

        function toggleReferralConfig() {
            // Show/hide transfer referral config
            if ($('#transfer_referral_required').is(':checked')) {
                $('#transfer_referral_config').slideDown();
            } else {
                $('#transfer_referral_config').slideUp();
            }

            // Show/hide withdrawal referral config
            if ($('#withdrawal_referral_required').is(':checked')) {
                $('#withdrawal_referral_config').slideDown();
            } else {
                $('#withdrawal_referral_config').slideUp();
            }
        }

        // Document ready
        $(document).ready(function() {
            // Toggle referral config sections when checkbox changes
            $('#transfer_referral_required, #withdrawal_referral_required').change(function() {
                toggleReferralConfig();
            });

            // Initialize referral config visibility
            toggleReferralConfig();
        });

        function resetToDefaults() {
            if (confirm('Are you sure you want to reset all conditions to default values? This will enable all conditions for both transfer and withdrawal.')) {
                fetch('{{ route("admin.transfer-withdraw-conditions.reset") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Enable all checkboxes
                        enableAllTransfer();
                        enableAllWithdrawal();
                        
                        // Show success message
                        showToast('success', data.message);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Failed to reset conditions');
                });
            }
        }

        function showToast(type, message) {
            // Create toast element
            const toast = $(`
                <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `);
            
            // Add to container
            if (!$('#toast-container').length) {
                $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            }
            
            $('#toast-container').append(toast);
            
            // Initialize and show toast
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
            
            // Remove after hiding
            toast.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }

        // Form submission with loading state
        $('#conditionsForm').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
            
            // Re-enable button after 3 seconds as fallback
            setTimeout(() => {
                submitBtn.prop('disabled', false).html(originalText);
            }, 3000);
        });
    </script>
    @endpush

    @endsection
</x-layout>
