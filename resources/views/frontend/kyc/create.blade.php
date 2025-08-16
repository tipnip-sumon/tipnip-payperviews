<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    @section('content')
    <div class="row mb-4 my-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        KYC Verification Form
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Progress Steps -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="progress-steps">
                                <div class="step active" data-step="1">
                                    <div class="step-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="step-title">Personal Info</div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="step-title">Address</div>
                                </div>
                                <div class="step" data-step="3">
                                    <div class="step-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="step-title">Documents</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important:</strong> Please ensure all information is accurate and documents are clear and readable.
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Form Validation Errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form id="kycForm" action="{{ route('user.kyc.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        
                        <!-- Step 1: Personal Information -->
                        <div class="form-step active" id="step1">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                                       id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                                @error('date_of_birth')
                                                    <div class="invalid-feedback msg">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                                       id="nationality" name="nationality" value="{{ old('nationality') }}" required>
                                                @error('nationality')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                               id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Address Information -->
                        <div class="form-step" id="step2">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label for="address" class="form-label">Street Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                       id="city" name="city" value="{{ old('city') }}" required>
                                                @error('city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="state" class="form-label">State/Province <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                                       id="state" name="state" value="{{ old('state') }}" required>
                                                @error('state')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                                       id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                                @error('postal_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        @php  
                                        $c = json_decode(file_get_contents(resource_path('views/country/country.json')));
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                                <select class="form-control @error('country') is-invalid @enderror" id="country" name="country" required>
                                                    <option value="">Select Country</option>
                                                    @foreach ($c as $k => $country)
                                                        <option value="{{ $country->country }}" {{ old('country') == $country->country ? 'selected' : '' }}>
                                                            {{ $country->country }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('country')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Documents -->
                        <div class="form-step" id="step3">
                            <!-- Document Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Document Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                                                <select class="form-control @error('document_type') is-invalid @enderror" 
                                                        id="document_type" name="document_type" required>
                                                    <option value="">Select Document Type</option>
                                                    <option value="passport" {{ old('document_type') == 'passport' ? 'selected' : '' }}>Passport</option>
                                                    <option value="driving_license" {{ old('document_type') == 'driving_license' ? 'selected' : '' }}>Driving License</option>
                                                    <option value="national_id" {{ old('document_type') == 'national_id' ? 'selected' : '' }}>National ID Card</option>
                                                </select>
                                                @error('document_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="document_number" class="form-label">Document Number <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('document_number') is-invalid @enderror" 
                                                       id="document_number" name="document_number" value="{{ old('document_number') }}" required>
                                                <div class="document-check-status mt-2"></div>
                                                @error('document_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Upload -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Document Upload</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="document_front" class="form-label">Document Front Side <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('document_front') is-invalid @enderror" 
                                                       id="document_front" name="document_front" accept="image/*,.pdf" required>
                                                <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, PDF (Max size: 5MB)</small>
                                                @error('document_front')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="document_back" class="form-label">Document Back Side (if applicable)</label>
                                                <input type="file" class="form-control @error('document_back') is-invalid @enderror" 
                                                       id="document_back" name="document_back" accept="image/*,.pdf">
                                                <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, PDF (Max size: 5MB)</small>
                                                @error('document_back')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="selfie_image" class="form-label">Selfie with Document <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('selfie_image') is-invalid @enderror" 
                                               id="selfie_image" name="selfie_image" accept="image/*" required>
                                        <small class="form-text text-muted">Take a clear selfie holding your document next to your face</small>
                                        @error('selfie_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" 
                                               id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I certify that all information provided is accurate and complete. I understand that providing false information may result in account suspension.
                                        </label>
                                        @error('terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                                <i class="fas fa-arrow-left me-2"></i>Previous
                            </button>
                            <a href="{{ route('user.kyc.index') }}" class="btn btn-outline-secondary" id="cancelBtn">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                                Next<i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                <i class="fas fa-paper-plane me-2"></i>Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-bottom: 30px;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            background: white;
            padding: 0 20px;
            cursor: pointer;
        }

        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .step.active .step-icon {
            background: #007bff;
            color: white;
        }

        .step.completed .step-icon {
            background: #28a745;
            color: white;
        }

        .step-title {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .step.active .step-title {
            color: #007bff;
            font-weight: 600;
        }

        .step.completed .step-title {
            color: #28a745;
            font-weight: 600;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .document-check-status {
            font-size: 12px;
            font-weight: 500;
        }

        .document-check-status.available {
            color: #28a745;
        }

        .document-check-status.unavailable {
            color: #dc3545;
        }

        .document-check-status.checking {
            color: #ffc107;
        }

        @media (max-width: 768px) {
            .progress-steps {
                flex-direction: column;
                gap: 20px;
            }
            
            .progress-steps::before {
                display: none;
            }
            
            .step {
                flex-direction: row;
                gap: 15px;
            }
            
            .step-icon {
                margin-bottom: 0;
            }
        }
    </style>
    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 3;

        $(document).ready(function() {
            // Initialize form
            showStep(currentStep);
            
            // Document number validation
            $('#document_number').on('input', function() {
                const documentNumber = $(this).val().trim();
                if (documentNumber.length > 3) {
                    checkDocumentNumber(documentNumber);
                }
            });
            
            // Form submission
            $('#kycForm').on('submit', function(e) {
                if (!validateStep(currentStep)) {
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');
                
                return true;
            });            // Document type change handler
            $('#document_type').on('change', function() {
                const docType = $(this).val();
                const backLabel = $('#document_back').siblings('label');
                const backHelp = $('#document_back').siblings('small');
                
                if (docType === 'passport') {
                    backLabel.html('Document Back Side');
                    backHelp.text('Optional for passport');
                    $('#document_back').removeAttr('required');
                } else {
                    backLabel.html('Document Back Side <span class="text-danger">*</span>');
                    backHelp.text('Required for ID cards and licenses');
                    $('#document_back').attr('required', true);
                }
            });
            
            // File upload validation
            $('input[type="file"]').on('change', function() {
                const file = this.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file && file.size > maxSize) {
                    alert('File size must be less than 5MB');
                    $(this).val('');
                    return false;
                }
            });
        });

        function showStep(step) {
            // Hide all steps
            $('.form-step').removeClass('active');
            $('.step').removeClass('active completed');
            
            // Show current step
            $('#step' + step).addClass('active');
            $('.step[data-step="' + step + '"]').addClass('active');
            
            // Mark completed steps
            for (let i = 1; i < step; i++) {
                $('.step[data-step="' + i + '"]').addClass('completed');
            }
            
            // Update buttons
            $('#prevBtn').toggle(step > 1);
            $('#nextBtn').toggle(step < totalSteps);
            $('#submitBtn').toggle(step === totalSteps);
            $('#cancelBtn').toggle(step < totalSteps);
        }

        function changeStep(direction) {
            if (direction === 1 && currentStep < totalSteps) {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                }
            } else if (direction === -1 && currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        function validateStep(step) {
            let isValid = true;
            const currentStepEl = $('#step' + step);
            
            // Check required fields in current step
            currentStepEl.find('input[required], select[required], textarea[required]').each(function() {
                if (!$(this).val() || ($(this).attr('type') === 'checkbox' && !$(this).is(':checked'))) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields.');
            }
            
            return isValid;
        }

        function checkDocumentNumber(documentNumber) {
            const statusDiv = $('.document-check-status');
            statusDiv.removeClass('available unavailable').addClass('checking');
            statusDiv.html('<i class="fas fa-spinner fa-spin"></i> Checking availability...');
            
            $.ajax({
                url: '{{ route("user.kyc.check-document") }}',
                method: 'POST',
                data: {
                    document_number: documentNumber,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.available) {
                        statusDiv.removeClass('checking unavailable').addClass('available');
                        statusDiv.html('<i class="fas fa-check-circle"></i> Document number is available');
                    } else {
                        statusDiv.removeClass('checking available').addClass('unavailable');
                        statusDiv.html('<i class="fas fa-times-circle"></i> Document number already exists');
                    }
                },
                error: function() {
                    statusDiv.removeClass('checking available unavailable');
                    statusDiv.html('');
                }
            });
        }

        // Allow clicking on steps to navigate (only to completed steps)
        $('.step').on('click', function() {
            const stepNumber = parseInt($(this).data('step'));
            if (stepNumber < currentStep || $(this).hasClass('completed')) {
                currentStep = stepNumber;
                showStep(currentStep);
            }
        });
    </script>
    @endpush
</x-smart_layout>
