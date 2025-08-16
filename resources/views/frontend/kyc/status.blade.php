<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    @section('content') 
        <div class="row mb-4 my-4">
            <div class="col-xl-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h4>KYC Verification Status</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($kycVerification)
                            <!-- KYC Status Overview -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="alert 
                                        @if($kycVerification->status == 'approved') alert-success
                                        @elseif($kycVerification->status == 'pending') alert-warning
                                        @elseif($kycVerification->status == 'rejected') alert-danger
                                        @else alert-info
                                        @endif
                                    ">
                                        <h5 class="alert-heading">
                                            <i class="fas 
                                                @if($kycVerification->status == 'approved') fa-check-circle
                                                @elseif($kycVerification->status == 'pending') fa-clock
                                                @elseif($kycVerification->status == 'rejected') fa-times-circle
                                                @else fa-info-circle
                                                @endif
                                            "></i>
                                            Status: {{ ucfirst($kycVerification->status) }}
                                        </h5>
                                        <p class="mb-0">
                                            @if($kycVerification->status == 'approved')
                                                Your KYC verification has been approved successfully.
                                            @elseif($kycVerification->status == 'pending')
                                                Your KYC verification is under review. We will notify you once it's processed.
                                            @elseif($kycVerification->status == 'rejected')
                                                Your KYC verification was rejected. Please check the remarks below and resubmit.
                                            @else
                                                Your KYC verification status is being processed.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fas fa-calendar"></i> Submission Date</h6>
                                            <p class="mb-2">{{ $kycVerification->submitted_at ? $kycVerification->submitted_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                            
                                            @if($kycVerification->processed_at)
                                                <h6><i class="fas fa-check"></i> Processed Date</h6>
                                                <p class="mb-0">{{ $kycVerification->processed_at->format('M d, Y h:i A') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KYC Details -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-user"></i> Personal Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td><strong>Document Type:</strong></td>
                                                    <td>{{ ucwords(str_replace('_', ' ', $kycVerification->document_type)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Nationality:</strong></td>
                                                    <td>{{ $kycVerification->nationality }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Phone Number:</strong></td>
                                                    <td>{{ $kycVerification->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Country:</strong></td>
                                                    <td>{{ $kycVerification->country }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>State:</strong></td>
                                                    <td>{{ $kycVerification->state }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>City:</strong></td>
                                                    <td>{{ $kycVerification->city }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Postal Code:</strong></td>
                                                    <td>{{ $kycVerification->postal_code }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Address Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Full Address:</strong></p>
                                            <address>
                                                {{ $kycVerification->address }}<br>
                                                {{ $kycVerification->city }}, {{ $kycVerification->state }}<br>
                                                {{ $kycVerification->postal_code }}<br>
                                                {{ $kycVerification->country }}
                                            </address>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Uploaded Documents -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-file-alt"></i> Uploaded Documents</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @if($kycVerification->document_front)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="text-center">
                                                            <h6>Document Front</h6>
                                                            <!-- Image Preview -->
                                                            <div class="mb-2">
                                                                <img src="{{ route('user.kyc.view', [$kycVerification->id, 'front']) }}" 
                                                                     alt="Document Front" 
                                                                     class="img-fluid border rounded" 
                                                                     style="max-height: 200px; object-fit: cover;"
                                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                <div style="display: none;" class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> Image preview unavailable
                                                                </div>
                                                            </div>
                                                            <div class="btn-group d-block">
                                                                <a href="{{ route('user.kyc.view', [$kycVerification->id, 'front']) }}" 
                                                                   class="btn btn-info btn-sm" target="_blank">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                                <a href="{{ route('user.kyc.download', [$kycVerification->id, 'front']) }}" 
                                                                   class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($kycVerification->document_back)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="text-center">
                                                            <h6>Document Back</h6>
                                                            <!-- Image Preview -->
                                                            <div class="mb-2">
                                                                <img src="{{ route('user.kyc.view', [$kycVerification->id, 'back']) }}" 
                                                                     alt="Document Back" 
                                                                     class="img-fluid border rounded" 
                                                                     style="max-height: 200px; object-fit: cover;"
                                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                <div style="display: none;" class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> Image preview unavailable
                                                                </div>
                                                            </div>
                                                            <div class="btn-group d-block">
                                                                <a href="{{ route('user.kyc.view', [$kycVerification->id, 'back']) }}" 
                                                                   class="btn btn-info btn-sm" target="_blank">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                                <a href="{{ route('user.kyc.download', [$kycVerification->id, 'back']) }}" 
                                                                   class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($kycVerification->selfie_image)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="text-center">
                                                            <h6>Selfie Image</h6>
                                                            <!-- Image Preview -->
                                                            <div class="mb-2">
                                                                <img src="{{ route('user.kyc.view', [$kycVerification->id, 'selfie']) }}" 
                                                                     alt="Selfie Image" 
                                                                     class="img-fluid border rounded" 
                                                                     style="max-height: 200px; object-fit: cover;"
                                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                <div style="display: none;" class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> Image preview unavailable
                                                                </div>
                                                            </div>
                                                            <div class="btn-group d-block">
                                                                <a href="{{ route('user.kyc.view', [$kycVerification->id, 'selfie']) }}" 
                                                                   class="btn btn-info btn-sm" target="_blank">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                                <a href="{{ route('user.kyc.download', [$kycVerification->id, 'selfie']) }}" 
                                                                   class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($kycVerification->admin_remarks)
                                <!-- Admin Remarks -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="fas fa-comment"></i> Admin Remarks</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="alert alert-info">
                                                    {{ $kycVerification->admin_remarks }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="text-center">
                                        @if($kycVerification->status == 'approved')
                                            <a href="{{ route('user.kyc.pdf', $kycVerification->id) }}" 
                                               class="btn btn-success">
                                                <i class="fas fa-file-pdf"></i> Download KYC Certificate
                                            </a>
                                        @elseif($kycVerification->status == 'rejected')
                                            <a href="{{ route('user.kyc.create') }}" 
                                               class="btn btn-primary">
                                                <i class="fas fa-redo"></i> Resubmit KYC
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('user.kyc.index') }}" 
                                           class="btn btn-secondary ms-2">
                                            <i class="fas fa-arrow-left"></i> Back to KYC
                                        </a>
                                    </div>
                                </div>
                            </div>

                        @else
                            <!-- No KYC Found -->
                            <div class="alert alert-info text-center">
                                <h5><i class="fas fa-info-circle"></i> No KYC Verification Found</h5>
                                <p>You haven't submitted any KYC verification yet.</p>
                                <a href="{{ route('user.kyc.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Submit KYC Verification
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-smart_layout>