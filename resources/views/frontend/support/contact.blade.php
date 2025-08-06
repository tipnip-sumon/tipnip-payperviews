<x-smart_layout>

@section('title')
{{ __('Contact Support') }}
@endsection

@section('content')
<div class="dashboard-content-inner">
    <div class="row">
        <div class="col-lg-12">
            <div class="dashboard-heading mb-4">
                <h4 class="title">{{ __('Contact Support') }}</h4>
                <p class="subtitle">{{ __('Get in touch with our support team for quick assistance.') }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-envelope me-2"></i>{{ __('Send us a Message') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.support.contact.send') }}" method="POST" id="contactForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" 
                                           value="{{ old('name', auth()->user()->firstname . ' ' . auth()->user()->lastname) }}" 
                                           required readonly>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" 
                                           value="{{ old('email', auth()->user()->email) }}" 
                                           required readonly>
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category" class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-select" required>
                                        <option value="">{{ __('Select Category') }}</option>
                                        <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>{{ __('General Inquiry') }}</option>
                                        <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>{{ __('Technical Support') }}</option>
                                        <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>{{ __('Billing & Payments') }}</option>
                                        <option value="account" {{ old('category') == 'account' ? 'selected' : '' }}>{{ __('Account Issues') }}</option>
                                        <option value="feature" {{ old('category') == 'feature' ? 'selected' : '' }}>{{ __('Feature Request') }}</option>
                                        <option value="bug" {{ old('category') == 'bug' ? 'selected' : '' }}>{{ __('Bug Report') }}</option>
                                        <option value="feedback" {{ old('category') == 'feedback' ? 'selected' : '' }}>{{ __('Feedback') }}</option>
                                    </select>
                                    @error('category')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="priority" class="form-label">{{ __('Priority') }} <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="">{{ __('Select Priority') }}</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                        <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                                    </select>
                                    @error('priority')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="subject" class="form-label">{{ __('Subject') }} <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control" 
                                   value="{{ old('subject') }}" 
                                   placeholder="{{ __('Brief description of your inquiry') }}" 
                                   maxlength="255" required>
                            @error('subject')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="message" class="form-label">{{ __('Message') }} <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" class="form-control" rows="6" 
                                      placeholder="{{ __('Please describe your inquiry in detail...') }}" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('Please provide as much detail as possible to help us assist you better.') }}</div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="create_ticket" id="create_ticket" class="form-check-input" value="1">
                                <label for="create_ticket" class="form-check-label">
                                    {{ __('Create a support ticket for tracking (recommended for technical issues)') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-actions d-flex justify-content-between">
                            <a href="{{ route('user.support.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Support') }}
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('Send Message') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Contact Information -->
            <div class="card custom-card">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-address-book me-2"></i>{{ __('Contact Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="contact-info">
                        <div class="contact-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-envelope fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ __('Email Support') }}</h6>
                                    <p class="text-muted mb-0">support@{{ request()->getHost() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contact-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-clock fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ __('Business Hours') }}</h6>
                                    <p class="text-muted mb-0">
                                        {{ __('Monday - Friday') }}<br>
                                        {{ __('9:00 AM - 6:00 PM (UTC)') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contact-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-reply fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ __('Response Time') }}</h6>
                                    <p class="text-muted mb-0">{{ __('Within 24 hours') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alternative Support Options -->
            <div class="card custom-card mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-life-ring me-2"></i>{{ __('Other Support Options') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="support-options">
                        <a href="{{ route('user.support.tickets') }}" class="support-option d-block mb-3 text-decoration-none">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <i class="fas fa-ticket-alt fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1">{{ __('Support Tickets') }}</h6>
                                    <small class="text-muted">{{ __('Track your requests') }}</small>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('user.support.knowledge') }}" class="support-option d-block mb-3 text-decoration-none">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <i class="fas fa-book fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1">{{ __('Knowledge Base') }}</h6>
                                    <small class="text-muted">{{ __('Find instant answers') }}</small>
                                </div>
                            </div>
                        </a>
                        
                        <div class="support-option d-block mb-0">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <i class="fas fa-phone fa-2x text-muted me-3"></i>
                                <div>
                                    <h6 class="mb-1 text-muted">{{ __('Phone Support') }}</h6>
                                    <small class="text-muted">{{ __('Coming soon') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card custom-card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>{{ __('Quick Tips') }}
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Check our FAQ first') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Be specific about your issue') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Include screenshots if helpful') }}
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Mention your browser/device') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.custom-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.75rem;
    overflow: hidden;
}

.custom-card .card-header {
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.custom-card .card-body {
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #e3e6f0;
    padding: 0.75rem;
    transition: all 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-control[readonly] {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.btn {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.15s ease-in-out;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dashboard-heading {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.contact-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 50%;
}

.support-option {
    transition: all 0.3s ease;
}

.support-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.support-option .border {
    border-color: #e3e6f0 !important;
}

.support-option:hover .border {
    border-color: #667eea !important;
}

.form-actions {
    border-top: 1px solid #e3e6f0;
    padding-top: 1.5rem;
    margin-top: 1rem;
}

.list-unstyled li {
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
    }
    
    .contact-item .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .contact-icon {
        align-self: center;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('#contactForm').on('submit', function(e) {
        let isValid = true;
        
        // Reset previous errors
        $('.is-invalid').removeClass('is-invalid');
        
        // Validate required fields
        const requiredFields = ['category', 'priority', 'subject', 'message'];
        requiredFields.forEach(field => {
            if (!$(`#${field}`).val().trim()) {
                $(`#${field}`).addClass('is-invalid');
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            toastr.error('{{ __("Please fill in all required fields") }}');
            return false;
        }
        
        // Show loading state
        $('#submitBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Sending...") }}').prop('disabled', true);
    });
    
    // Character counter for subject
    $('#subject').on('input', function() {
        const maxLength = 255;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        
        let counterText = `${currentLength}/${maxLength}`;
        if (remaining < 20) {
            counterText = `<span class="text-warning">${counterText}</span>`;
        }
        if (remaining < 0) {
            counterText = `<span class="text-danger">${counterText}</span>`;
        }
        
        $(this).next('.form-text').html(counterText);
    });
    
    // Auto-resize textarea
    $('#message').on('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
    
    // Category change suggestions
    $('#category').on('change', function() {
        const category = $(this).val();
        let suggestion = '';
        
        switch(category) {
            case 'technical':
                suggestion = '{{ __("Please include your browser version, operating system, and any error messages you encountered.") }}';
                break;
            case 'billing':
                suggestion = '{{ __("Please include your transaction ID or invoice number if applicable.") }}';
                break;
            case 'account':
                suggestion = '{{ __("Please describe the account issue you are experiencing in detail.") }}';
                break;
            case 'bug':
                suggestion = '{{ __("Please provide steps to reproduce the bug and any error messages.") }}';
                break;
            case 'feature':
                suggestion = '{{ __("Please describe the feature you would like to see and how it would help you.") }}';
                break;
        }
        
        if (suggestion) {
            $('#message').attr('placeholder', suggestion);
        } else {
            $('#message').attr('placeholder', '{{ __("Please describe your inquiry in detail...") }}');
        }
    });
    
    // Priority change helper
    $('#priority').on('change', function() {
        const priority = $(this).val();
        const $createTicket = $('#create_ticket');
        
        if (priority === 'high' || priority === 'urgent') {
            if (!$createTicket.is(':checked')) {
                $createTicket.prop('checked', true);
                toastr.info('{{ __("We recommend creating a support ticket for high priority issues.") }}');
            }
        }
    });
});
</script>
@endpush
@endsection
</x-smart_layout>
