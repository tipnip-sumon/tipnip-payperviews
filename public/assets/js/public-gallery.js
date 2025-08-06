// PayPerViews Public Gallery JavaScript
// This file handles all interactive functionality for the public gallery page

$(document).ready(function() {
    // Initialize page functionality
    initializeSearch();
    initializeFilters();
    initializeSmoothScrolling();
    initializeVideoActions();
    initializeNewsletterSubscription();
    
    // Newsletter subscription functionality
    function initializeNewsletterSubscription() {
        $('#newsletterForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const email = $('#subscriberEmail').val().trim();
            const submitBtn = form.find('.subscribe-btn');
            const btnText = submitBtn.find('.btn-text');
            const btnSpinner = submitBtn.find('.btn-spinner');
            const feedback = form.find('.subscription-feedback');
            
            // Validate email
            if (!isValidEmail(email)) {
                showSubscriptionFeedback(feedback, 'danger', 'Please enter a valid email address.');
                return;
            }
            
            // Show loading state
            submitBtn.prop('disabled', true);
            btnText.addClass('d-none');
            btnSpinner.removeClass('d-none');
            
            // Make AJAX request to Laravel backend
            $.ajax({
                url: '/newsletter/subscribe',
                method: 'POST',
                data: {
                    email: email,
                    _token: $('meta[name="csrf-token"]').attr('content') || 'no-token'
                },
                timeout: 10000, // 10 second timeout
                success: function(response) {
                    if (response.success) {
                        showSubscriptionFeedback(feedback, 'success', response.message);
                        form[0].reset();
                        
                        // Track subscription (analytics)
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'newsletter_subscribe', {
                                'event_category': 'engagement',
                                'event_label': 'footer_newsletter'
                            });
                        }
                    } else {
                        const feedbackType = response.type || 'danger';
                        showSubscriptionFeedback(feedback, feedbackType, response.message);
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Subscription failed. Please try again later.';
                    
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON?.errors;
                        if (errors && errors.email) {
                            errorMessage = errors.email[0];
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    } else if (xhr.status === 429) {
                        errorMessage = 'Too many requests. Please wait a moment and try again.';
                    } else if (status === 'timeout') {
                        errorMessage = 'Request timeout. Please check your connection and try again.';
                    }
                    
                    showSubscriptionFeedback(feedback, 'danger', errorMessage);
                    
                    // Log error for debugging
                    console.error('Newsletter subscription error:', {
                        status: xhr.status,
                        error: error,
                        response: xhr.responseJSON
                    });
                },
                complete: function() {
                    // Reset button state
                    submitBtn.prop('disabled', false);
                    btnText.removeClass('d-none');
                    btnSpinner.addClass('d-none');
                }
            });
        });
    }
    
    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Show subscription feedback
    function showSubscriptionFeedback(container, type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'danger' ? 'alert-danger' : 'alert-info';
        
        const icon = type === 'success' ? 'fas fa-check-circle' : 
                    type === 'danger' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle';
        
        container.html(`
            <div class="alert ${alertClass} d-flex align-items-center">
                <i class="${icon} me-2"></i>
                <span>${message}</span>
            </div>
        `);
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                container.find('.alert').fadeOut(() => {
                    container.empty();
                });
            }, 5000);
        }
    }
    
    // Search functionality
    function initializeSearch() {
        $('#searchInput').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.video-item').each(function() {
                const title = $(this).data('title');
                if (title.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }
    
    // Filter functionality
    function initializeFilters() {
        $('[data-filter]').click(function() {
            const filter = $(this).data('filter');
            
            // Update active button
            $('[data-filter]').removeClass('active');
            $(this).addClass('active');
            
            // Show/hide videos based on filter
            if (filter === 'all') {
                $('.video-item').show();
            } else if (filter === 'popular') {
                // Show videos with high view counts
                $('.video-item').hide();
                $('.video-item').slice(0, 6).show(); // Show first 6 as "popular"
            } else if (filter === 'recent') {
                // Show recent videos (reverse order)
                $('.video-item').hide();
                $('.video-item').slice(-6).show(); // Show last 6 as "recent"
            }
        });
    }
    
    // Smooth scrolling
    function initializeSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 500);
            }
        });
    }
    
    // Video actions
    function initializeVideoActions() {
        // Handle watch & earn buttons
        $(document).on('click', '.btn-watch-earn', function(e) {
            e.preventDefault();
            showLoginModal();
        });
        
        // Handle hero play button
        $(document).on('click', '.hero-play-btn', function(e) {
            e.preventDefault();
            scrollToVideos();
        });
        
        // Handle play overlay clicks
        $(document).on('click', '.play-overlay', function(e) {
            e.preventDefault();
            if ($(this).hasClass('hero-play-btn')) {
                scrollToVideos();
            } else {
                showLoginModal();
            }
        });
    }
});

// Global functions with enhanced error handling
function showLoginModal() {
    try {
        // Try Bootstrap 5 modal first
        if (typeof bootstrap !== 'undefined' && document.getElementById('loginModal')) {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }
        
        // Try jQuery modal fallback
        if (typeof $ !== 'undefined' && $('#loginModal').length) {
            $('#loginModal').modal('show');
            return;
        }
        
        // Final fallback: use custom modal or redirect
        if (typeof window.showLoginModalFallback === 'function') {
            window.showLoginModalFallback();
        } else {
            window.location.href = '/login';
        }
    } catch (error) {
        console.warn('Modal functionality not available, redirecting to login:', error);
        window.location.href = '/login';
    }
}

function scrollToVideos() {
    try {
        // Try modern smooth scroll first
        const videosSection = document.getElementById('videos');
        if (videosSection && 'scrollIntoView' in videosSection) {
            videosSection.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
            return;
        }
        
        // Try jQuery animate fallback
        if (typeof $ !== 'undefined' && $('#videos').length) {
            $('html, body').animate({
                scrollTop: $('#videos').offset().top - 70
            }, 500);
            return;
        }
        
        // Basic scroll fallback
        if (videosSection) {
            videosSection.scrollIntoView();
        }
    } catch (error) {
        console.warn('Smooth scroll not available, using fallback:', error);
        if (typeof window.scrollToVideosFallback === 'function') {
            window.scrollToVideosFallback();
        }
    }
}

// Prevent MetaMask and Web3 related errors
window.addEventListener('error', function(e) {
    // Suppress MetaMask related errors for non-crypto functionality
    if (e.error && e.error.message && 
        (e.error.message.includes('MetaMask') || 
         e.error.message.includes('ethereum') ||
         e.error.message.includes('web3'))) {
        e.preventDefault();
        console.log('MetaMask/Web3 functionality not required for this page');
        return false;
    }
});

// CSP Error handling
window.addEventListener('securitypolicyviolation', function(e) {
    console.warn('CSP Violation:', {
        blockedURI: e.blockedURI,
        violatedDirective: e.violatedDirective,
        originalPolicy: e.originalPolicy
    });
});
