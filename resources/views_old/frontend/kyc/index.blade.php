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
                        KYC Verification
                    </h5>
                </div>
                <div class="card-body">
                    @if($kycVerification && $kycVerification->status == 'pending')
                        <!-- KYC Pending -->
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>KYC Verification Pending</strong>
                            <p class="mb-0 mt-2">Your KYC verification is currently under review. We will notify you once it's processed.</p>
                            <small class="text-muted">Submitted on: {{ $kycVerification->submitted_at->format('F j, Y g:i A') }}</small>
                        </div>
                        
                        <!-- Show submitted information -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Personal Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td><strong>Full Name:</strong></td>
                                                <td>{{ $kycVerification->first_name }} {{ $kycVerification->last_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Date of Birth:</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($kycVerification->date_of_birth)->format('F j, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nationality:</strong></td>
                                                <td>{{ $kycVerification->nationality }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phone:</strong></td>
                                                <td>{{ $kycVerification->phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Document Type:</strong></td>
                                                <td>{{ ucwords(str_replace('_', ' ', $kycVerification->document_type)) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Document Number:</strong></td>
                                                <td>{{ $kycVerification->document_number }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Address Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td><strong>Address:</strong></td>
                                                <td>{{ $kycVerification->address }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>City:</strong></td>
                                                <td>{{ $kycVerification->city }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>State:</strong></td>
                                                <td>{{ $kycVerification->state }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Postal Code:</strong></td>
                                                <td>{{ $kycVerification->postal_code }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Country:</strong></td>
                                                <td>{{ $kycVerification->country }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @elseif($kycVerification && $kycVerification->status == 'approved')
                        <!-- KYC Approved -->
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>KYC Verification Approved</strong>
                            <p class="mb-0 mt-2">Your KYC verification has been approved successfully!</p>
                        </div>
                        
                    @elseif($kycVerification && $kycVerification->status == 'rejected')
                        <!-- KYC Rejected -->
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>KYC Verification Rejected</strong>
                            <p class="mb-0 mt-2">Your KYC verification has been rejected. Please review the notes below and resubmit.</p>
                            @if($kycVerification->admin_notes)
                                <div class="mt-2">
                                    <strong>Admin Notes:</strong>
                                    <p class="mb-0">{{ $kycVerification->admin_notes }}</p>
                                </div>
                            @endif
                            <small class="text-muted">Reviewed on: {{ $kycVerification->reviewed_at->format('F j, Y g:i A') }}</small>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('user.kyc.create') }}" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i>Resubmit KYC
                            </a>
                        </div>
                        
                    @else
                        <!-- No KYC Submitted -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>KYC Verification Required</strong>
                            <p class="mb-0 mt-2">Complete your KYC verification to unlock all features and increase your account limits.</p>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-8">
                                <h6>Benefits of KYC Verification:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Higher withdrawal limits</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Enhanced account security</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Access to premium features</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Priority customer support</li>
                                </ul>
                                
                                <a href="{{ route('user.kyc.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-id-card me-2"></i>Start KYC Verification
                                </a>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                        <h6>Secure & Encrypted</h6>
                                        <p class="small text-muted">Your documents are encrypted and stored securely</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-smart_layout>
