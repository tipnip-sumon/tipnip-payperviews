<x-layout>
    @section('top_title','Admin Transfer Funds')
    @section('title','Transfer Funds to Users')
    
    @push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush

    @section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h5 class="card-title text-white mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Admin Fund Transfer
                </h5>
            </div>
            <div class="card-body">
                <!-- Admin Balance Display -->
                <div class="row mb-4">
                    <div class="col-md-6 mx-auto">
                        <div class="alert alert-info text-center">
                            <h4 class="mb-2">
                                <i class="fas fa-wallet me-2"></i>
                                Available Balance
                            </h4>
                            <h3 class="text-primary mb-0">
                                ${{ number_format(auth()->guard('admin')->user()->balance ?? 0, 2) }}
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Transfer Form -->
                <form action="{{ route('admin.transfer_member.store') }}" method="POST" id="transferForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <!-- User Selection -->
                            <div class="form-group mb-3">
                                <label for="user_receive" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    Select User to Transfer
                                </label>
                                <div class="position-relative">
                                    <input type="text" 
                                           name="user_receive" 
                                           id="user_receive" 
                                           class="form-control @error('user_receive') is-invalid @enderror" 
                                           placeholder="Search by username, email, or name..." 
                                           value="{{ old('user_receive') }}"
                                           autocomplete="off"
                                           required>
                                    <div class="user-suggestions position-absolute w-100 bg-white border rounded-bottom shadow-lg" 
                                         id="userSuggestions" 
                                         style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto; border-top: none !important;">
                                    </div>
                                </div>
                                @error('user_receive')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount Input -->
                            <div class="form-group mb-3">
                                <label for="amount" class="form-label">
                                    <i class="fas fa-dollar-sign me-2"></i>
                                    Transfer Amount
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           name="amount" 
                                           id="amount" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           placeholder="0.00" 
                                           step="0.01"
                                           min="1"
                                           value="{{ old('amount') }}"
                                           required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <!-- Quick Amount Buttons -->
                                <div class="mt-2">
                                    <small class="text-muted">Quick amounts:</small>
                                    <div class="btn-group-sm mt-1" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1 amount-btn" data-amount="10">$10</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1 amount-btn" data-amount="25">$25</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1 amount-btn" data-amount="50">$50</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1 amount-btn" data-amount="100">$100</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1 amount-btn" data-amount="500">$500</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm amount-btn" data-amount="1000">$1000</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Note -->
                            <div class="form-group mb-3">
                                <label for="note" class="form-label">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Transfer Note (Optional)
                                </label>
                                <textarea name="note" 
                                          id="note" 
                                          class="form-control @error('note') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Add a note or reason for this transfer...">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Transaction Password -->
                            <div class="form-group mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    Transaction Password
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Enter your admin password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="passwordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="loadingSpinner"></span>
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Transfer Funds
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Transfer History Link -->
                <div class="text-center mt-4">
                    <a href="{{ route('admin.transfer_history') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-history me-2"></i>
                        View Transfer History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3">
                    <i class="fas fa-check-circle fa-4x"></i>
                </div>
                <h4 class="text-success mb-3">Transfer Successful!</h4>
                <p class="mb-4" id="successMessage">Your transfer has been processed successfully.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                        <i class="fas fa-redo me-2"></i>New Transfer
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Wait for document ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Transfer page loaded successfully');
    
    // Check if jQuery is loaded
    if (typeof $ !== 'undefined') {
        console.log('jQuery is loaded');
        initializeTransferForm();
    } else {
        console.error('jQuery not loaded');
        // Fallback to vanilla JavaScript
        initializeTransferFormVanilla();
    }
});

function initializeTransferForm() {
    // Get admin balance for validation
    const adminBalance = {{ auth()->guard('admin')->user()->balance ?? 0 }};
    console.log('Admin balance:', adminBalance);
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // User search functionality
    let searchTimeout;
    $('#user_receive').on('input', function() {
        const query = $(this).val().trim();
        const suggestions = $('#userSuggestions');
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            suggestions.hide();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            // Show loading
            suggestions.html('<div class="p-3 text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>').show();
            
            // AJAX search
            $.ajax({
                url: '/admin/users/search',
                method: 'GET',
                data: { 
                    query: query,
                    limit: 8 
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Search response:', response);
                    if (response.success && response.users.length > 0) {
                        let html = '';
                        response.users.forEach(user => {
                            const fullName = user.firstname && user.lastname 
                                ? `${user.firstname} ${user.lastname}`
                                : user.username;
                            
                            const balance = user.balance ? parseFloat(user.balance).toFixed(2) : '0.00';
                            const status = user.status == 1 ? 'Active' : 'Inactive';
                            const statusClass = user.status == 1 ? 'text-success' : 'text-warning';
                            
                            html += `
                                <div class="user-suggestion p-3 border-bottom cursor-pointer hover-bg-light" 
                                     data-username="${user.username}" 
                                     data-name="${fullName}"
                                     data-balance="${balance}"
                                     style="cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px; font-weight: bold;">
                                                ${user.username[0].toUpperCase()}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">${fullName}</div>
                                            <small class="text-muted">@${user.username} • ${user.email}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">$${balance}</div>
                                            <small class="${statusClass}">${status}</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        suggestions.html(html).show();
                    } else {
                        suggestions.html('<div class="p-3 text-center text-muted"><i class="fas fa-search"></i> No users found</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', xhr, status, error);
                    suggestions.html('<div class="p-3 text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Search failed</div>').show();
                }
            });
        }, 300);
    });

    // User selection
    $(document).on('click', '.user-suggestion', function() {
        const username = $(this).data('username');
        const name = $(this).data('name');
        const balance = $(this).data('balance');
        
        if (username) {
            $('#user_receive').val(username);
            $('#userSuggestions').hide();
            $('#amount').focus();
            
            // Show success notification
            showNotification(`Selected user: ${name} (Balance: $${balance})`, 'success');
        }
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#user_receive, #userSuggestions').length) {
            $('#userSuggestions').hide();
        }
    });

    // Amount suggestions
    $('.amount-btn').on('click', function() {
        const amount = $(this).data('amount');
        $('#amount').val(amount);
        validateAmount(amount);
        showNotification(`Amount set to $${amount}`, 'success');
    });

    // Amount validation
    $('#amount').on('input', function() {
        let value = $(this).val();
        const amount = parseFloat(value);
        
        if (value && !isNaN(amount)) {
            validateAmount(amount);
        }
    });

    // Form submission
    $('#transferForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        if (!validateForm()) {
            return false;
        }
        
        showLoading();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Transfer response:', response);
                hideLoading();
                
                if (response.success) {
                    $('#successMessage').text(response.message);
                    if (typeof bootstrap !== 'undefined') {
                        $('#successModal').modal('show');
                    } else {
                        alert(response.message);
                    }
                    $('#transferForm')[0].reset();
                } else {
                    showNotification(response.message || 'Transfer failed. Please try again.', 'error');
                }
            },
            error: function(xhr) {
                console.error('Transfer error:', xhr);
                hideLoading();
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(field) {
                        showNotification(errors[field][0], 'error');
                    });
                } else {
                    console.error('Transfer error - Response:', xhr);
                    let errorMessage = 'An error occurred. Please try again.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.debug && xhr.responseJSON.debug.error) {
                            console.error('Debug info:', xhr.responseJSON.debug);
                            errorMessage += ` (${xhr.responseJSON.debug.error})`;
                        }
                    } else if (xhr.statusText) {
                        errorMessage = `Server Error: ${xhr.statusText}`;
                    }
                    
                    showNotification(errorMessage, 'error');
                }
            }
        });
    });

    // Helper functions
    function validateAmount(amount) {
        const amountInput = $('#amount');
        amountInput.removeClass('is-invalid is-valid');
        
        if (isNaN(amount) || amount <= 0) {
            amountInput.addClass('is-invalid');
            return false;
        }
        
        if (amount < 1) {
            amountInput.addClass('is-invalid');
            showNotification('Minimum transfer amount is $1.00', 'error');
            return false;
        }
        
        if (amount > adminBalance) {
            amountInput.addClass('is-invalid');
            showNotification(`Amount exceeds available balance ($${adminBalance.toFixed(2)})`, 'error');
            return false;
        }
        
        amountInput.addClass('is-valid');
        return true;
    }

    function validateForm() {
        let isValid = true;
        
        // Validate user
        const userInput = $('#user_receive');
        if (!userInput.val().trim()) {
            userInput.addClass('is-invalid');
            showNotification('Please select a user', 'error');
            isValid = false;
        }
        
        // Validate amount
        const amount = parseFloat($('#amount').val());
        if (!validateAmount(amount)) {
            isValid = false;
        }
        
        // Validate password
        const passwordInput = $('#password');
        if (!passwordInput.val().trim()) {
            passwordInput.addClass('is-invalid');
            showNotification('Please enter your transaction password', 'error');
            isValid = false;
        }
        
        return isValid;
    }

    function showLoading() {
        $('#submitBtn').prop('disabled', true);
        $('#loadingSpinner').removeClass('d-none');
    }

    function hideLoading() {
        $('#submitBtn').prop('disabled', false);
        $('#loadingSpinner').addClass('d-none');
    }
    
    function showNotification(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        const icon = type === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, 4000);
    }
}

function initializeTransferFormVanilla() {
    console.log('Initializing with vanilla JavaScript');
    // Basic vanilla JavaScript fallback
    const form = document.getElementById('transferForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Basic form submission without AJAX
            this.submit();
        });
    }
}

// Password toggle function
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');
    
    if (input.type === "password") {
        input.type = "text";
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = "password";
        icon.className = 'fas fa-eye';
    }
}
</script>

<style>
.hover-bg-light:hover {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 50%, #fff3e0 100%) !important;
    color: #1976d2 !important;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.2);
    transform: translateY(-1px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.cursor-pointer {
    cursor: pointer;
}

.user-suggestions {
    max-height: 300px;
    overflow-y: auto;
    background: #ffffff !important;
    border: 2px solid #dee2e6 !important;
    border-radius: 0 0 0.5rem 0.5rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.2) !important;
    backdrop-filter: none !important;
    z-index: 1050 !important;
}

.user-suggestion {
    background: #ffffff;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.user-suggestion:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f1f3f4 100%) !important;
    border-left: 4px solid #007bff;
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
    border-radius: 0 8px 8px 0;
}

.user-suggestion:last-child {
    border-bottom: none;
    border-radius: 0 0 0.5rem 0.5rem;
}

.user-suggestion .bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
}

.user-suggestion .fw-bold {
    color: #212529 !important;
    font-weight: 600 !important;
}

.user-suggestion .text-muted {
    color: #6c757d !important;
    font-size: 0.875rem;
}

.user-suggestion .text-success {
    color: #198754 !important;
    font-weight: 500;
}

.user-suggestion .text-warning {
    color: #fd7e14 !important;
    font-weight: 500;
}

.amount-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    color: white !important;
    border-color: #007bff !important;
}

.card {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #007bff 100%);
    transform: translateY(-1px);
}

.alert {
    border-radius: 0.5rem;
    border: none;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card-header {
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

/* Enhanced input styling */
#user_receive {
    border: 2px solid #dee2e6;
    border-radius: 0.5rem 0.5rem 0 0;
    transition: all 0.3s ease;
}

#user_receive:focus {
    border-color: #007bff !important;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

#user_receive:focus + .user-suggestions {
    border-top: none !important;
    border-color: #007bff !important;
}

/* Loading state for suggestions */
.user-suggestions .fa-spinner {
    color: #007bff;
    font-size: 1.2rem;
}

/* No results state */
.user-suggestions .fa-search {
    color: #6c757d;
    font-size: 1.2rem;
}

/* Error state */
.user-suggestions .fa-exclamation-triangle {
    color: #dc3545;
    font-size: 1.2rem;
}

/* Scrollbar styling for user suggestions */
.user-suggestions::-webkit-scrollbar {
    width: 6px;
}

.user-suggestions::-webkit-scrollbar-track {
    background: #f8f9fa;
}

.user-suggestions::-webkit-scrollbar-thumb {
    background: #dee2e6;
    border-radius: 3px;
}

.user-suggestions::-webkit-scrollbar-thumb:hover {
    background: #adb5bd;
}
</style>
@endpush
</x-layout>
