/**
 * Mobile Layout JavaScript Functions
 * Well-organized and optimized for mobile interface
 * Author: PayPerViews Mobile Team
 * Version: 2.0
 */

// =================================================================
// UTILITY FUNCTIONS
// =================================================================

/**
 * Safe DOM query selector with error handling
 */
function safeQuerySelector(selector) {
    try {
        return document.querySelector(selector);
    } catch (error) {
        console.warn('Query selector failed:', selector, error);
        return null;
    }
}

/**
 * Safe DOM query selector all with error handling
 */
function safeQuerySelectorAll(selector) {
    try {
        return document.querySelectorAll(selector);
    } catch (error) {
        console.warn('Query selector all failed:', selector, error);
        return [];
    }
}

/**
 * Check if Bootstrap is available
 */
function isBootstrapAvailable() {
    return typeof bootstrap !== 'undefined' && bootstrap.Modal;
}

/**
 * Clean up modal backdrops and body classes
 */
function cleanupModalBackdrops() {
    // Remove all modal backdrops
    const backdrops = safeQuerySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => {
        if (backdrop.parentNode) {
            backdrop.parentNode.removeChild(backdrop);
        }
    });
    
    // Clean up body classes and styles
    if (document.body) {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
}

// =================================================================
// THEME FUNCTIONS
// =================================================================

/**
 * Toggle between light and dark theme
 */
window.toggleMobileTheme = function() {
    try {
        const html = document.documentElement;
        const themeIcon = safeQuerySelector('.theme-icon');
        
        if (!html) {
            console.error('Document element not found');
            return;
        }
        
        const currentTheme = html.getAttribute('data-theme-mode');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        // Update theme attribute
        html.setAttribute('data-theme-mode', newTheme);
        
        // Update theme icon
        if (themeIcon) {
            themeIcon.className = newTheme === 'dark' 
                ? 'bx bx-sun theme-icon' 
                : 'bx bx-moon theme-icon';
        }
        
        // Save preference to localStorage
        try {
            localStorage.setItem('theme-mode', newTheme);
        } catch (storageError) {
            console.warn('Could not save theme preference:', storageError);
        }
        
        // Add haptic feedback if available
        if ('vibrate' in navigator) {
            navigator.vibrate(50);
        }
        
        console.log('Theme switched to:', newTheme);
        
    } catch (error) {
        console.error('Error in toggleMobileTheme:', error);
    }
};

/**
 * Initialize theme on page load
 */
function initializeTheme() {
    try {
        const savedTheme = localStorage.getItem('theme-mode') || 'light';
        const html = document.documentElement;
        const themeIcon = safeQuerySelector('.theme-icon');
        
        if (html) {
            html.setAttribute('data-theme-mode', savedTheme);
        }
        
        if (themeIcon) {
            themeIcon.className = savedTheme === 'dark' 
                ? 'bx bx-sun theme-icon' 
                : 'bx bx-moon theme-icon';
        }
    } catch (error) {
        console.warn('Error initializing theme:', error);
    }
}

// =================================================================
// MODAL FUNCTIONS
// =================================================================

/**
 * Open mobile modal with improved toggle behavior
 */
window.openMobileModal = function(type) {
    try {
        if (!type) {
            console.error('Modal type is required');
            return;
        }
        
        const modalId = `mobile${type.charAt(0).toUpperCase() + type.slice(1)}Modal`;
        const modalElement = safeQuerySelector(`#${modalId}`);
        
        if (!modalElement) {
            console.warn('Modal not found:', modalId);
            return;
        }
        
        // Check if Bootstrap is available
        if (!isBootstrapAvailable()) {
            console.error('Bootstrap Modal not available');
            showModalFallback(modalElement);
            return;
        }
        
        // Clean up and check current state
        const currentModalInstance = bootstrap.Modal.getInstance(modalElement);
        const isCurrentlyShown = modalElement.classList.contains('show') || 
                                modalElement.getAttribute('aria-modal') === 'true' ||
                                modalElement.style.display === 'block';
        
        console.log(`Modal ${modalId} - Currently shown: ${isCurrentlyShown}, Instance exists: ${!!currentModalInstance}`);
        
        // If modal is open, close it (toggle behavior)
        if (isCurrentlyShown || currentModalInstance) {
            console.log(`Closing modal ${modalId}`);
            closeModal(modalElement, currentModalInstance);
            return;
        }
        
        // Close all other modals first
        closeAllModals();
        
        // Clean up any leftover backdrops and open the modal
        setTimeout(() => {
            cleanupModalBackdrops();
            openModal(modalElement, type);
        }, 150); // Increased timeout for better cleanup
        
    } catch (error) {
        console.error('Error in openMobileModal:', error);
    }
};

/**
 * Close a specific modal with thorough cleanup
 */
function closeModal(modalElement, modalInstance) {
    try {
        console.log('Closing modal:', modalElement?.id || 'unknown');
        
        // First, hide using Bootstrap instance if it exists
        if (modalInstance) {
            modalInstance.hide();
            // Dispose the instance to clean up event listeners
            setTimeout(() => {
                modalInstance.dispose();
            }, 300);
        }
        
        // Always perform manual cleanup regardless
        if (modalElement) {
            // Remove all modal-related classes and attributes
            modalElement.classList.remove('show');
            modalElement.classList.remove('fade');
            modalElement.style.display = 'none';
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');
            
            // Reset any inline styles that might interfere
            modalElement.style.paddingRight = '';
        }
        
        // Remove body classes that modal adds
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Clean up backdrops with a longer delay
        setTimeout(() => {
            cleanupModalBackdrops();
        }, 200);
        
        console.log('Modal closed and cleaned up');
        
    } catch (error) {
        console.error('Error closing modal:', error);
        // Force cleanup even if there's an error
        setTimeout(() => {
            cleanupModalBackdrops();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 100);
    }
}

/**
 * Close all open modals
 */
function closeAllModals() {
    try {
        const allModals = safeQuerySelectorAll('.modal');
        allModals.forEach(modal => {
            const instance = isBootstrapAvailable() ? bootstrap.Modal.getInstance(modal) : null;
            if (instance) {
                try {
                    instance.hide();
                } catch (error) {
                    console.warn('Error hiding modal instance:', error);
                }
            }
            
            // Manual cleanup
            if (modal.classList) {
                modal.classList.remove('show');
            }
            if (modal.style) {
                modal.style.display = 'none';
            }
        });
    } catch (error) {
        console.error('Error closing all modals:', error);
    }
}

/**
 * Open a modal with Bootstrap - Enhanced with proper cleanup
 */
function openModal(modalElement, type) {
    try {
        console.log('Opening modal:', modalElement.id, 'Type:', type);
        
        // Ensure modal is completely reset first
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.removeAttribute('aria-modal');
        modalElement.removeAttribute('role');
        
        // Destroy existing instance completely
        const existingInstance = bootstrap.Modal.getInstance(modalElement);
        if (existingInstance) {
            console.log('Disposing existing modal instance');
            existingInstance.dispose();
        }
        
        // Wait a moment before creating new instance
        setTimeout(() => {
            try {
                // Create completely fresh modal instance
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                
                // Add event listener for when modal is fully shown
                modalElement.addEventListener('shown.bs.modal', function onShown() {
                    console.log('Modal fully opened:', type);
                    modalElement.removeEventListener('shown.bs.modal', onShown);
                });
                
                // Add event listener for when modal is fully hidden
                modalElement.addEventListener('hidden.bs.modal', function onHidden() {
                    console.log('Modal fully closed:', type);
                    // Perform additional cleanup
                    setTimeout(() => {
                        cleanupModalBackdrops();
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }, 100);
                    modalElement.removeEventListener('hidden.bs.modal', onHidden);
                });
                
                // Show the modal
                modal.show();
                
                // Special handling for wallet modal
                if (type === 'wallet' && typeof updateWalletBalance === 'function') {
                    setTimeout(() => {
                        updateWalletBalance();
                    }, 300);
                }
                
                // Add haptic feedback if available
                if ('vibrate' in navigator) {
                    navigator.vibrate(15);
                }
                
                console.log('Modal opening initiated:', type);
                
            } catch (innerError) {
                console.error('Error creating new modal instance:', innerError);
                showModalFallback(modalElement);
            }
        }, 50); // Small delay to ensure cleanup is complete
        
    } catch (error) {
        console.error('Error in openModal:', error);
        showModalFallback(modalElement);
    }
}

/**
 * Fallback modal display (without Bootstrap)
 */
function showModalFallback(modalElement) {
    try {
        if (modalElement) {
            modalElement.classList.add('show');
            modalElement.style.display = 'block';
            modalElement.setAttribute('aria-modal', 'true');
            modalElement.removeAttribute('aria-hidden');
            
            // Add backdrop manually
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.onclick = () => {
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                backdrop.remove();
            };
            document.body.appendChild(backdrop);
        }
    } catch (error) {
        console.error('Error in modal fallback:', error);
    }
}

// =================================================================
// NOTIFICATION FUNCTIONS
// =================================================================

/**
 * Mark all notifications as read
 */
window.markAllNotificationsRead = function() {
    try {
        console.log('Marking all notifications as read...');
        
        // Update UI immediately
        const notificationDots = safeQuerySelectorAll('.notification-dot');
        notificationDots.forEach(dot => {
            if (dot.style) {
                dot.style.display = 'none';
            }
        });
        
        // Add haptic feedback
        if ('vibrate' in navigator) {
            navigator.vibrate([100, 50, 100]);
        }
        
        // Close notifications modal after delay
        setTimeout(() => {
            const modal = safeQuerySelector('#mobileNotificationsModal');
            if (modal && isBootstrapAvailable()) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        }, 1000);
        
    } catch (error) {
        console.error('Error in markAllNotificationsRead:', error);
    }
};

/**
 * Clear all notifications
 */
window.clearAllNotifications = function() {
    try {
        if (confirm('Are you sure you want to delete all notifications? This action cannot be undone.')) {
            console.log('Clearing all notifications...');
            
            // Update UI immediately
            const notificationDots = safeQuerySelectorAll('.notification-dot');
            notificationDots.forEach(dot => {
                if (dot.style) {
                    dot.style.display = 'none';
                }
            });
            
            // Update preview list
            const previewList = safeQuerySelector('.notification-preview-list');
            if (previewList) {
                previewList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bx bx-bell-off display-6 text-muted mb-2"></i>
                        <p class="text-muted mb-0">All notifications cleared</p>
                        <small class="text-muted">You're all caught up!</small>
                    </div>
                `;
            }
            
            // Add haptic feedback
            if ('vibrate' in navigator) {
                navigator.vibrate([100, 50, 100]);
            }
            
            // Close modal after delay
            setTimeout(() => {
                const modal = safeQuerySelector('#mobileNotificationsModal');
                if (modal && isBootstrapAvailable()) {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            }, 1500);
        }
    } catch (error) {
        console.error('Error in clearAllNotifications:', error);
    }
};

// =================================================================
// AUTH FUNCTIONS
// =================================================================

/**
 * Confirm logout with user
 */
window.confirmLogout = function() {
    try {
        // Check if SweetAlert is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Confirm Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel',
                backdrop: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    performLogout();
                }
            });
        } else {
            // Fallback to native confirm
            if (confirm('Are you sure you want to logout?')) {
                performLogout();
            }
        }
    } catch (error) {
        console.error('Error in confirmLogout:', error);
        // Final fallback
        if (confirm('Are you sure you want to logout?')) {
            performLogout();
        }
    }
};

/**
 * Perform logout action
 */
function performLogout() {
    try {
        // Show loading if SweetAlert is available
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
        
        // Add small delay for better UX
        setTimeout(() => {
            // Try different logout methods
            if (window.logoutForm) {
                window.logoutForm.submit();
            } else if (document.getElementById('logout-form')) {
                document.getElementById('logout-form').submit();
            } else {
                // Fallback: redirect to logout URL
                window.location.href = '/logout';
            }
        }, 500);
        
    } catch (error) {
        console.error('Error in performLogout:', error);
        // Ultimate fallback
        window.location.href = '/logout';
    }
}

// =================================================================
// WALLET FUNCTIONS
// =================================================================

/**
 * Update wallet balance display
 */
window.updateWalletBalance = function() {
    try {
        console.log('Updating wallet balance...');
        
        // Find balance indicators
        const balanceIndicators = safeQuerySelectorAll('.balance-indicator');
        balanceIndicators.forEach(indicator => {
            // Add loading state
            if (indicator.textContent) {
                indicator.style.opacity = '0.6';
            }
        });
        
        // Simulate balance refresh (replace with actual API call)
        setTimeout(() => {
            balanceIndicators.forEach(indicator => {
                indicator.style.opacity = '1';
            });
        }, 1000);
        
    } catch (error) {
        console.error('Error updating wallet balance:', error);
    }
};

// =================================================================
// INITIALIZATION
// =================================================================

/**
 * Initialize mobile functions when DOM is ready
 */
function initializeMobileFunctions() {
    try {
        // Only log in development
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log('Initializing mobile functions...');
        }
        
        // Initialize theme
        initializeTheme();
        
        // Set current year in footer
        const yearElement = safeQuerySelector('#year-mobile');
        if (yearElement) {
            yearElement.textContent = new Date().getFullYear();
        }
        
        // Add keyboard navigation support
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeAllModals();
            }
        });
        
        // Log successful initialization (dev only)
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log('Mobile functions initialized successfully:', {
                openMobileModal: typeof window.openMobileModal,
                toggleMobileTheme: typeof window.toggleMobileTheme,
                confirmLogout: typeof window.confirmLogout,
                markAllNotificationsRead: typeof window.markAllNotificationsRead,
                clearAllNotifications: typeof window.clearAllNotifications,
                updateWalletBalance: typeof window.updateWalletBalance
            });
        }
        
    } catch (error) {
        console.error('Error initializing mobile functions:', error);
    }
}

// =================================================================
// AUTO-INITIALIZATION
// =================================================================

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMobileFunctions);
} else {
    initializeMobileFunctions();
}

// Also initialize on window load as fallback
window.addEventListener('load', () => {
    // Double-check that functions are available
    if (typeof window.openMobileModal !== 'function') {
        console.warn('Reinitializing mobile functions...');
        initializeMobileFunctions();
    }
});

// Export functions for manual access if needed
window.MobileFunctions = {
    openMobileModal: window.openMobileModal,
    toggleMobileTheme: window.toggleMobileTheme,
    confirmLogout: window.confirmLogout,
    markAllNotificationsRead: window.markAllNotificationsRead,
    clearAllNotifications: window.clearAllNotifications,
    updateWalletBalance: window.updateWalletBalance,
    reinitialize: initializeMobileFunctions
};

console.log('Mobile Functions v2.0 loaded successfully');
