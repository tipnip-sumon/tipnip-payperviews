<x-smart_layout>

@section('title')
{{ __('Create Support Ticket') }}
@endsection

@section('content')
<div class="dashboard-content-inner">
    <div class="row">
        <div class="col-lg-12">
            <div class="dashboard-heading mb-4">
                <h4 class="title">{{ __('Create Support Ticket') }}</h4>
                <p class="subtitle">{{ __('Need help? Create a support ticket and our team will assist you.') }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>{{ __('New Support Ticket') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.support.store') }}" method="POST" enctype="multipart/form-data" id="ticketForm">
                        @csrf
                        
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
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                            <span class="badge bg-success">{{ __('Low') }}</span>
                                        </option>
                                        <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>
                                            <span class="badge bg-info">{{ __('Normal') }}</span>
                                        </option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                            <span class="badge bg-warning">{{ __('High') }}</span>
                                        </option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                            <span class="badge bg-danger">{{ __('Urgent') }}</span>
                                        </option>
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
                                   placeholder="{{ __('Brief description of your issue') }}" 
                                   maxlength="255" required>
                            @error('subject')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('Maximum 255 characters') }}</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="message" class="form-label">{{ __('Message') }} <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" class="form-control" rows="8" 
                                      placeholder="{{ __('Please describe your issue in detail...') }}" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('Please provide as much detail as possible to help us assist you better.') }}</div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="attachments" class="form-label">{{ __('Attachments') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="file" name="attachments[]" id="attachments" class="form-control" 
                                   multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip">
                            <div class="form-text">
                                {{ __('You can attach multiple files. Supported formats: JPG, PNG, GIF, PDF, DOC, DOCX, TXT, ZIP. Max size: 10MB per file.') }}
                            </div>
                            @error('attachments')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('attachments.*')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="email_notifications" id="email_notifications" class="form-check-input" value="1" 
                                       {{ old('email_notifications', 1) ? 'checked' : '' }}>
                                <label for="email_notifications" class="form-check-label">
                                    {{ __('Send me email notifications for responses to this ticket') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-actions d-flex justify-content-between">
                            <a href="{{ route('user.support.tickets') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Tickets') }}
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('Create Ticket') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Tips -->
            <div class="card custom-card">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>{{ __('Quick Tips') }}
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Be specific about your issue') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Include relevant screenshots') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Mention your browser/device info') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Include error messages if any') }}
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ __('Check our knowledge base first') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Response Time Info -->
            <div class="card custom-card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>{{ __('Response Times') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="fw-bold text-success">24 hrs</div>
                                <small class="text-muted">{{ __('Normal') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-danger">4 hrs</div>
                            <small class="text-muted">{{ __('Urgent') }}</small>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted">
                        {{ __('Response times are estimates and may vary during high volume periods.') }}
                    </small>
                </div>
            </div>

            <!-- FAQ Suggestion -->
            <div class="card custom-card mt-4">
                <div class="card-body text-center">
                    <i class="fas fa-question-circle fa-3x text-primary mb-3"></i>
                    <h6>{{ __('Need Quick Answers?') }}</h6>
                    <p class="text-muted mb-3">{{ __('Check our knowledge base for instant solutions to common questions.') }}</p>
                    <a href="{{ route('user.support.knowledge') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-book me-2"></i>{{ __('Browse FAQ') }}
                    </a>
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

.list-unstyled li {
    font-size: 0.9rem;
}

#attachments {
    padding: 0.5rem;
}

.form-text {
    font-size: 0.8rem;
    color: #6c757d;
}

.form-actions {
    border-top: 1px solid #e3e6f0;
    padding-top: 1.5rem;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('#ticketForm').on('submit', function(e) {
        let isValid = true;
        
        // Reset previous errors
        $('.is-invalid').removeClass('is-invalid');
        
        // Validate required fields
        if (!$('#category').val()) {
            $('#category').addClass('is-invalid');
            isValid = false;
        }
        
        if (!$('#priority').val()) {
            $('#priority').addClass('is-invalid');
            isValid = false;
        }
        
        if (!$('#subject').val().trim()) {
            $('#subject').addClass('is-invalid');
            isValid = false;
        }
        
        if (!$('#message').val().trim()) {
            $('#message').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            toastr.error('{{ __("Please fill in all required fields") }}');
            return false;
        }
        
        // Show loading state
        $('#submitBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Creating Ticket...") }}').prop('disabled', true);
    });
    
    // File upload validation
    $('#attachments').on('change', function() {
        const files = this.files;
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'];
        
        Array.from(files).forEach(file => {
            const extension = file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(extension)) {
                toastr.error(`{{ __("File type not allowed") }}: ${file.name}`);
                this.value = '';
                return;
            }
            
            if (file.size > maxSize) {
                toastr.error(`{{ __("File too large") }}: ${file.name}`);
                this.value = '';
                return;
            }
        });
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
});
</script>
@endpush
@endsection
</x-smart_layout>
