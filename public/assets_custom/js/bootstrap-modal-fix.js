/**
 * Bootstrap Modal Fix for Mobile Layout
 * Fixes TypeError: Cannot read properties of null (reading 'style') errors
 */

(function() {
    'use strict';
    
    // Debug mode
    const DEBUG = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    // Bootstrap Modal Fix
    function fixBootstrapModals() {
        if (DEBUG) console.log('🔧 Initializing Bootstrap Modal fixes...');
        
        // Override Bootstrap Modal._showElement to handle null elements
        if (window.bootstrap && window.bootstrap.Modal) {
            const OriginalModal = window.bootstrap.Modal;
            const originalShow = OriginalModal.prototype.show;
            const originalHide = OriginalModal.prototype.hide;
            
            // Fix show method
            OriginalModal.prototype.show = function(relatedTarget) {
                try {
                    // Check if modal element exists and is valid
                    if (!this._element || !this._element.style) {
                        if (DEBUG) console.warn('Modal element is null or invalid:', this._element);
                        return;
                    }
                    
                    // Check if modal is already shown
                    if (this._element.classList.contains('show')) {
                        if (DEBUG) console.log('Modal is already shown, skipping');
                        return;
                    }
                    
                    return originalShow.call(this, relatedTarget);
                } catch (error) {
                    console.warn('Bootstrap Modal show error caught:', error.message);
                    // Try to handle gracefully
                    if (this._element && this._element.style) {
                        this._element.style.display = 'block';
                        this._element.classList.add('show');
                    }
                }
            };
            
            // Fix hide method
            OriginalModal.prototype.hide = function() {
                try {
                    // Check if modal element exists and is valid
                    if (!this._element || !this._element.style) {
                        if (DEBUG) console.warn('Modal element is null or invalid during hide:', this._element);
                        return;
                    }
                    
                    return originalHide.call(this);
                } catch (error) {
                    console.warn('Bootstrap Modal hide error caught:', error.message);
                    // Try to handle gracefully
                    if (this._element && this._element.style) {
                        this._element.style.display = 'none';
                        this._element.classList.remove('show');
                    }
                }
            };
            
            if (DEBUG) console.log('✅ Bootstrap Modal methods patched');
        }
    }
    
    // Safe Modal Helper Functions
    window.openMobileModal = function(modalName) {
        try {
            if (DEBUG) console.log('📱 Opening mobile modal:', modalName);
            
            const modalId = `mobile${modalName.charAt(0).toUpperCase() + modalName.slice(1)}Modal`;
            const modalElement = document.getElementById(modalId);
            
            if (!modalElement) {
                console.warn(`Modal element not found: ${modalId}`);
                return;
            }
            
            // Check if Bootstrap is loaded
            if (!window.bootstrap || !window.bootstrap.Modal) {
                console.warn('Bootstrap Modal not available');
                // Fallback: show modal with CSS
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                return;
            }
            
            // Close any existing modals first
            const existingModals = document.querySelectorAll('.modal.show');
            existingModals.forEach(modal => {
                try {
                    const bsModal = window.bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                } catch (e) {
                    // Silent fail
                }
            });
            
            // Small delay to ensure previous modal is closed
            setTimeout(() => {
                try {
                    const bsModal = new window.bootstrap.Modal(modalElement, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                    bsModal.show();
                    
                    if (DEBUG) console.log('✅ Modal opened successfully:', modalId);
                } catch (error) {
                    console.warn('Error opening modal:', error.message);
                    // Fallback
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                }
            }, 100);
            
        } catch (error) {
            console.warn('openMobileModal error:', error.message);
        }
    };
    
    // Safe logout function
    window.confirmLogout = function() {
        try {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirm Logout',
                    text: 'Are you sure you want to logout?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Logout',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performLogout();
                    }
                });
            } else {
                // Fallback without SweetAlert
                if (confirm('Are you sure you want to logout?')) {
                    performLogout();
                }
            }
        } catch (error) {
            console.warn('confirmLogout error:', error.message);
            // Direct logout as fallback
            performLogout();
        }
    };
    
    // Safe logout function
    function performLogout() {
        try {
            // Close any open modals first
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modal => {
                try {
                    const bsModal = window.bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    } else {
                        modal.style.display = 'none';
                        modal.classList.remove('show');
                    }
                } catch (e) {
                    // Silent fail
                }
            });
            
            // Show loading state
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
            
            // Try multiple logout methods
            const logoutMethods = [
                () => {
                    // Method 1: Simple logout (no CSRF, no middleware)
                    window.location.href = "/simple-logout";
                },
                () => {
                    // Method 2: Standard Laravel logout route
                    window.location.href = "/logout";
                },
                () => {
                    // Method 3: Emergency direct redirect to login
                    window.location.href = "/login?emergency_logout=1&t=" + Math.floor(Date.now() / 1000);
                }
            ];
            
            // Try first method
            logoutMethods[0]();
            
            // Fallback after 2 seconds if first method fails
            setTimeout(() => {
                try {
                    logoutMethods[1]();
                } catch (e) {
                    // Final fallback
                    setTimeout(() => logoutMethods[2](), 1000);
                }
            }, 2000);
            
        } catch (error) {
            console.warn('performLogout error:', error.message);
            // Emergency fallback
            window.location.href = '/login?emergency_logout=1&t=' + Math.floor(Date.now() / 1000);
        }
    }
    
    // DOM Safe Checker
    function isDOMElementSafe(element) {
        try {
            return element && 
                   element.nodeType === Node.ELEMENT_NODE && 
                   element.style !== undefined &&
                   element.style !== null;
        } catch (e) {
            return false;
        }
    }
    
    // Bootstrap Event Listener Fix
    function fixBootstrapEventListeners() {
        // Override Bootstrap's internal methods that cause null pointer issues
        const originalQuerySelector = document.querySelector;
        const originalQuerySelectorAll = document.querySelectorAll;
        
        document.querySelector = function(selector) {
            try {
                const result = originalQuerySelector.call(this, selector);
                return isDOMElementSafe(result) ? result : null;
            } catch (e) {
                if (DEBUG) console.warn('querySelector error:', e.message);
                return null;
            }
        };
        
        document.querySelectorAll = function(selector) {
            try {
                const results = originalQuerySelectorAll.call(this, selector);
                return Array.from(results).filter(isDOMElementSafe);
            } catch (e) {
                if (DEBUG) console.warn('querySelectorAll error:', e.message);
                return [];
            }
        };
    }
    
    // Initialize fixes when DOM is ready
    function initializeFixes() {
        if (DEBUG) console.log('🚀 Initializing Bootstrap Modal fixes...');
        
        try {
            fixBootstrapEventListeners();
            fixBootstrapModals();
            
            // Setup modal close event listeners with error handling
            document.addEventListener('click', function(e) {
                if (e.target && e.target.matches('[data-bs-dismiss="modal"]')) {
                    try {
                        const modal = e.target.closest('.modal');
                        if (modal && window.bootstrap && window.bootstrap.Modal) {
                            const bsModal = window.bootstrap.Modal.getInstance(modal);
                            if (bsModal) {
                                bsModal.hide();
                            }
                        }
                    } catch (error) {
                        if (DEBUG) console.warn('Modal close error:', error.message);
                        // Fallback close
                        const modal = e.target.closest('.modal');
                        if (modal) {
                            modal.style.display = 'none';
                            modal.classList.remove('show');
                        }
                    }
                }
            });
            
            if (DEBUG) console.log('✅ Bootstrap Modal fixes initialized successfully');
            
        } catch (error) {
            console.warn('Error initializing modal fixes:', error.message);
        }
    }
    
    // Wait for DOM and Bootstrap to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Small delay to ensure Bootstrap is loaded
            setTimeout(initializeFixes, 100);
        });
    } else {
        setTimeout(initializeFixes, 100);
    }
    
    // Additional safety: re-initialize if Bootstrap loads later
    if (!window.bootstrap) {
        const checkBootstrap = setInterval(() => {
            if (window.bootstrap && window.bootstrap.Modal) {
                clearInterval(checkBootstrap);
                setTimeout(initializeFixes, 100);
            }
        }, 500);
        
        // Stop checking after 10 seconds
        setTimeout(() => clearInterval(checkBootstrap), 10000);
    }
    
})();
