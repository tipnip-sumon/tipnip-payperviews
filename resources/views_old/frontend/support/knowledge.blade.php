<x-smart_layout>

@section('title')
{{ __('Knowledge Base') }}
@endsection

@section('content')
<div class="dashboard-content-inner">
    <div class="row">
        <div class="col-lg-12">
            <div class="dashboard-heading mb-4">
                <h4 class="title">{{ __('Knowledge Base') }}</h4>
                <p class="subtitle">{{ __('Find answers to frequently asked questions and helpful guides.') }}</p>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="search-box position-relative">
                        <input type="text" id="knowledgeSearch" class="form-control form-control-lg" 
                               placeholder="{{ __('Search for answers...') }}">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="category-filter text-center">
                <button class="btn btn-outline-primary category-btn active" data-category="all">
                    {{ __('All Categories') }}
                </button>
                <button class="btn btn-outline-primary category-btn" data-category="account">
                    <i class="fas fa-user me-2"></i>{{ __('Account') }}
                </button>
                <button class="btn btn-outline-primary category-btn" data-category="payments">
                    <i class="fas fa-credit-card me-2"></i>{{ __('Payments') }}
                </button>
                <button class="btn btn-outline-primary category-btn" data-category="security">
                    <i class="fas fa-shield-alt me-2"></i>{{ __('Security') }}
                </button>
                <button class="btn btn-outline-primary category-btn" data-category="technical">
                    <i class="fas fa-cog me-2"></i>{{ __('Technical') }}
                </button>
                <button class="btn btn-outline-primary category-btn" data-category="general">
                    <i class="fas fa-question-circle me-2"></i>{{ __('General') }}
                </button>
            </div>
        </div>
    </div>

    <!-- FAQ Items -->
    <div class="row">
        <div class="col-lg-12">
            <div class="faq-container">
                <!-- Account Category -->
                <div class="faq-item" data-category="account">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    {{ __('How do I update my profile information?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq1" class="collapse">
                            <div class="card-body">
                                <p>{{ __('To update your profile information:') }}</p>
                                <ol>
                                    <li>{{ __('Navigate to your Dashboard') }}</li>
                                    <li>{{ __('Click on "Profile Settings" in the menu') }}</li>
                                    <li>{{ __('Update the fields you want to change') }}</li>
                                    <li>{{ __('Click "Save Changes" to confirm') }}</li>
                                </ol>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ __('Some changes may require email verification.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="account">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="fas fa-lock me-2 text-primary"></i>
                                    {{ __('How do I change my password?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq2" class="collapse">
                            <div class="card-body">
                                <p>{{ __('To change your password:') }}</p>
                                <ol>
                                    <li>{{ __('Go to Profile Settings') }}</li>
                                    <li>{{ __('Click on "Change Password"') }}</li>
                                    <li>{{ __('Enter your current password') }}</li>
                                    <li>{{ __('Enter your new password twice') }}</li>
                                    <li>{{ __('Click "Update Password"') }}</li>
                                </ol>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('Use a strong password with at least 8 characters, including letters, numbers, and symbols.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Category -->
                <div class="faq-item" data-category="payments">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="fas fa-credit-card me-2 text-primary"></i>
                                    {{ __('What payment methods do you accept?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq3" class="collapse">
                            <div class="card-body">
                                <p>{{ __('We accept the following payment methods:') }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul>
                                            <li><i class="fab fa-cc-visa me-2"></i>{{ __('Visa') }}</li>
                                            <li><i class="fab fa-cc-mastercard me-2"></i>{{ __('Mastercard') }}</li>
                                            <li><i class="fab fa-cc-amex me-2"></i>{{ __('American Express') }}</li>
                                            <li><i class="fab fa-paypal me-2"></i>{{ __('PayPal') }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul>
                                            <li><i class="fab fa-bitcoin me-2"></i>{{ __('Bitcoin') }}</li>
                                            <li><i class="fas fa-university me-2"></i>{{ __('Bank Transfer') }}</li>
                                            <li><i class="fas fa-mobile-alt me-2"></i>{{ __('Mobile Payments') }}</li>
                                            <li><i class="fas fa-wallet me-2"></i>{{ __('Digital Wallets') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="payments">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    {{ __('How long do deposits take to process?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq4" class="collapse">
                            <div class="card-body">
                                <p>{{ __('Processing times vary by payment method:') }}</p>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Payment Method') }}</th>
                                                <th>{{ __('Processing Time') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ __('Credit/Debit Card') }}</td>
                                                <td><span class="badge bg-success">{{ __('Instant') }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('PayPal') }}</td>
                                                <td><span class="badge bg-success">{{ __('Instant') }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Cryptocurrency') }}</td>
                                                <td><span class="badge bg-warning">{{ __('15-30 minutes') }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Bank Transfer') }}</td>
                                                <td><span class="badge bg-info">{{ __('1-3 business days') }}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Category -->
                <div class="faq-item" data-category="security">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    <i class="fas fa-shield-alt me-2 text-primary"></i>
                                    {{ __('How do you protect my data?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq5" class="collapse">
                            <div class="card-body">
                                <p>{{ __('We use industry-standard security measures:') }}</p>
                                <ul>
                                    <li><strong>{{ __('SSL Encryption:') }}</strong> {{ __('All data is encrypted in transit') }}</li>
                                    <li><strong>{{ __('Database Encryption:') }}</strong> {{ __('Sensitive data is encrypted at rest') }}</li>
                                    <li><strong>{{ __('Two-Factor Authentication:') }}</strong> {{ __('Available for all accounts') }}</li>
                                    <li><strong>{{ __('Regular Audits:') }}</strong> {{ __('Security assessments and penetration testing') }}</li>
                                    <li><strong>{{ __('Compliance:') }}</strong> {{ __('GDPR and industry compliance standards') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="security">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    <i class="fas fa-mobile-alt me-2 text-primary"></i>
                                    {{ __('How do I enable two-factor authentication?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq6" class="collapse">
                            <div class="card-body">
                                <p>{{ __('To enable 2FA:') }}</p>
                                <ol>
                                    <li>{{ __('Download an authenticator app (Google Authenticator, Authy, etc.)') }}</li>
                                    <li>{{ __('Go to Security Settings in your profile') }}</li>
                                    <li>{{ __('Click "Enable Two-Factor Authentication"') }}</li>
                                    <li>{{ __('Scan the QR code with your authenticator app') }}</li>
                                    <li>{{ __('Enter the verification code from your app') }}</li>
                                    <li>{{ __('Save your backup codes in a secure location') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Category -->
                <div class="faq-item" data-category="technical">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                    <i class="fas fa-browser me-2 text-primary"></i>
                                    {{ __('Which browsers are supported?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq7" class="collapse">
                            <div class="card-body">
                                <p>{{ __('We support the following browsers:') }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('Recommended:') }}</h6>
                                        <ul>
                                            <li><i class="fab fa-chrome me-2"></i>{{ __('Chrome 90+') }}</li>
                                            <li><i class="fab fa-firefox me-2"></i>{{ __('Firefox 88+') }}</li>
                                            <li><i class="fab fa-safari me-2"></i>{{ __('Safari 14+') }}</li>
                                            <li><i class="fab fa-edge me-2"></i>{{ __('Edge 90+') }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('Mobile:') }}</h6>
                                        <ul>
                                            <li><i class="fab fa-android me-2"></i>{{ __('Chrome Mobile') }}</li>
                                            <li><i class="fab fa-apple me-2"></i>{{ __('Safari Mobile') }}</li>
                                            <li><i class="fab fa-firefox me-2"></i>{{ __('Firefox Mobile') }}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ __('Please ensure JavaScript is enabled for the best experience.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- General Category -->
                <div class="faq-item" data-category="general">
                    <div class="card custom-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-decoration-none collapsed w-100 text-start" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                                    <i class="fas fa-headset me-2 text-primary"></i>
                                    {{ __('How can I contact support?') }}
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </button>
                            </h6>
                        </div>
                        <div id="faq8" class="collapse">
                            <div class="card-body">
                                <p>{{ __('You can reach our support team through:') }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="support-method mb-3">
                                            <h6><i class="fas fa-ticket-alt me-2 text-primary"></i>{{ __('Support Tickets') }}</h6>
                                            <p class="text-muted">{{ __('Best for detailed issues') }}</p>
                                            <a href="{{ route('user.support.create') }}" class="btn btn-sm btn-outline-primary">{{ __('Create Ticket') }}</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="support-method mb-3">
                                            <h6><i class="fas fa-envelope me-2 text-primary"></i>{{ __('Contact Form') }}</h6>
                                            <p class="text-muted">{{ __('Quick questions') }}</p>
                                            <a href="{{ route('user.support.contact') }}" class="btn btn-sm btn-outline-primary">{{ __('Send Message') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Results -->
            <div id="noResults" class="text-center py-5" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>{{ __('No results found') }}</h5>
                <p class="text-muted">{{ __('Try adjusting your search terms or browse different categories.') }}</p>
                <a href="{{ route('user.support.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('Create Support Ticket') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Still Need Help -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="card custom-card text-center">
                <div class="card-body">
                    <h5>{{ __('Still need help?') }}</h5>
                    <p class="text-muted mb-4">{{ __("Couldn't find what you're looking for? Our support team is here to help.") }}</p>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('user.support.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-ticket-alt me-2"></i>{{ __('Create Support Ticket') }}
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('user.support.contact') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-envelope me-2"></i>{{ __('Contact Us') }}
                            </a>
                        </div>
                    </div>
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
    transition: all 0.3s ease;
}

.custom-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.search-box {
    position: relative;
}

.search-box .form-control {
    padding-left: 3rem;
    border-radius: 2rem;
    border: 2px solid #e3e6f0;
    font-size: 1.1rem;
}

.search-box .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 1.2rem;
}

.category-filter .category-btn {
    margin: 0.25rem;
    border-radius: 2rem;
    padding: 0.5rem 1.5rem;
    transition: all 0.3s ease;
}

.category-filter .category-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
}

.category-filter .category-btn:not(.active):hover {
    background-color: #f8f9fa;
    border-color: #667eea;
}

.faq-item .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.faq-item .btn-link {
    color: #495057;
    font-weight: 600;
    text-decoration: none !important;
}

.faq-item .btn-link:hover {
    color: #667eea;
}

.faq-item .btn-link .fas {
    transition: transform 0.3s ease;
}

.faq-item .btn-link[aria-expanded="true"] .fas.fa-chevron-down {
    transform: rotate(180deg);
}

.support-method {
    padding: 1rem;
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    text-align: center;
}

.dashboard-heading {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

@media (max-width: 768px) {
    .category-filter {
        text-align: left !important;
    }
    
    .category-filter .category-btn {
        display: block;
        width: 100%;
        margin: 0.25rem 0;
    }
    
    .search-box .form-control {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Search functionality
    $('#knowledgeSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        searchFAQ(searchTerm);
    });
    
    // Category filtering
    $('.category-btn').on('click', function() {
        const category = $(this).data('category');
        
        // Update active button
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        
        // Filter items
        filterByCategory(category);
    });
    
    function searchFAQ(searchTerm) {
        let visibleCount = 0;
        
        $('.faq-item').each(function() {
            const text = $(this).text().toLowerCase();
            if (searchTerm === '' || text.includes(searchTerm)) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            $('#noResults').show();
        } else {
            $('#noResults').hide();
        }
    }
    
    function filterByCategory(category) {
        let visibleCount = 0;
        
        $('.faq-item').each(function() {
            const itemCategory = $(this).data('category');
            if (category === 'all' || itemCategory === category) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        
        // Clear search when filtering by category
        $('#knowledgeSearch').val('');
        
        // Show/hide no results message
        if (visibleCount === 0) {
            $('#noResults').show();
        } else {
            $('#noResults').hide();
        }
    }
    
    // Smooth scrolling for internal links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // Track FAQ interactions (for analytics)
    $('.collapse').on('show.bs.collapse', function() {
        const question = $(this).prev().find('button').text().trim();
        console.log('FAQ Opened:', question);
        // You can add analytics tracking here
    });
});
</script>
@endpush
@endsection
</x-smart_layout>
