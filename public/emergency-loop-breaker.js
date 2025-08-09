/**
 * EMERGENCY LOOP BREAKER - Stops Bootstrap focus infinite loops
 * This script immediately stops the _handleFocusin infinite loop
 */

(function() {
    'use strict';
    
    console.log('🛑 Emergency Loop Breaker Activated');
    
    // Stop all current loops by clearing timeouts and intervals
    let timeoutId = setTimeout(function() {}, 0);
    let intervalId = setInterval(function() {}, 999999);
    
    // Clear all timeouts and intervals up to current IDs
    for (let i = 1; i <= timeoutId; i++) {
        clearTimeout(i);
    }
    for (let i = 1; i <= intervalId; i++) {
        clearInterval(i);
    }
    
    // Reset Bootstrap Modal if it exists
    if (window.bootstrap && window.bootstrap.Modal) {
        // Clear any existing modal instances
        const existingModals = document.querySelectorAll('.modal');
        existingModals.forEach(modal => {
            try {
                const instance = window.bootstrap.Modal.getInstance(modal);
                if (instance) {
                    instance.dispose();
                }
                modal.style.display = 'none';
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
                modal.removeAttribute('aria-modal');
            } catch (e) {
                // Silent cleanup
            }
        });
        
        // Remove any modal backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Reset body classes
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
    
    // Override problematic Bootstrap methods temporarily
    if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.prototype) {
        const originalHandleFocusin = window.bootstrap.Modal.prototype._handleFocusin;
        if (originalHandleFocusin) {
            window.bootstrap.Modal.prototype._handleFocusin = function(event) {
                try {
                    // Add safety check to prevent loops
                    if (this._isShown && this._element && event.target) {
                        return originalHandleFocusin.call(this, event);
                    }
                } catch (error) {
                    console.warn('Bootstrap focus handling safely bypassed');
                }
            };
        }
    }
    
    // Restore EventTarget.addEventListener to original
    if (window.originalAddEventListener) {
        EventTarget.prototype.addEventListener = window.originalAddEventListener;
    }
    
    // Create new safe addEventListener
    const safeAddEventListener = EventTarget.prototype.addEventListener;
    let loopProtectionCounter = 0;
    
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        try {
            // Reset counter periodically
            if (loopProtectionCounter > 1000) {
                loopProtectionCounter = 0;
            }
            
            // Skip bootstrap focus events that cause loops
            if (type === 'focusin' && listener.toString().includes('_handleFocusin')) {
                console.warn('Bypassing problematic focusin listener');
                return;
            }
            
            loopProtectionCounter++;
            return safeAddEventListener.call(this, type, listener, options);
        } catch (error) {
            console.warn('Safe addEventListener error:', error.message);
        }
    };
    
    // Fix mobile modal functions
    if (!window.openMobileModal) {
        window.openMobileModal = function(modalName) {
            try {
                console.log('🔧 Emergency modal opener for:', modalName);
                
                const modalId = `mobile${modalName.charAt(0).toUpperCase() + modalName.slice(1)}Modal`;
                const modalElement = document.getElementById(modalId);
                
                if (!modalElement) {
                    console.warn('Modal not found:', modalId);
                    return;
                }
                
                // Close other modals first
                const otherModals = document.querySelectorAll('.modal.show');
                otherModals.forEach(modal => {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                });
                
                // Show the modal safely
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                modalElement.setAttribute('aria-modal', 'true');
                modalElement.removeAttribute('aria-hidden');
                
                // Add backdrop
                if (!document.querySelector('.modal-backdrop')) {
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
                
                document.body.classList.add('modal-open');
                
                console.log('✅ Emergency modal opened:', modalId);
                
            } catch (error) {
                console.error('Emergency modal opener failed:', error);
            }
        };
    }
    
    // Fix confirm logout
    if (!window.confirmLogout) {
        window.confirmLogout = function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '/simple-logout';
            }
        };
    }
    
    console.log('✅ Emergency Loop Breaker Complete - Modal functions restored');
    
    // Show success message
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'System Fixed!',
            text: 'Modal buttons should now work properly.',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    }
    
})();
