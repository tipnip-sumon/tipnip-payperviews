/**
 * Post-Login URL Cleaner and Footer Button Fix
 * Fixes issues caused by cache busting parameters after login
 */

(function() {
    'use strict';
    
    console.log('🔧 Post-Login URL Cleaner & Footer Fix Activated');
    
    // Check if we're on a problematic URL with cache parameters
    function hasProblematicParameters() {
        const url = window.location.href;
        return url.includes('cache_bust=') || 
               url.includes('device_switch=') || 
               url.includes('?v=') || 
               url.includes('&v=');
    }
    
    // Clean the URL without reloading the page
    function cleanURL() {
        try {
            const url = new URL(window.location.href);
            const paramsToRemove = ['cache_bust', 'device_switch', 'v', 't', 'cache_version'];
            
            paramsToRemove.forEach(param => {
                url.searchParams.delete(param);
            });
            
            // Only update if parameters were actually removed
            const cleanUrl = url.toString();
            if (cleanUrl !== window.location.href) {
                console.log('🧹 Cleaning URL from:', window.location.href);
                console.log('🧹 Cleaning URL to:', cleanUrl);
                
                // Use replaceState to avoid page reload
                window.history.replaceState({}, document.title, cleanUrl);
                
                return true;
            }
        } catch (error) {
            console.warn('URL cleaning failed:', error);
        }
        return false;
    }
    
    // Force reload footer button functionality
    function ensureFooterButtonsWork() {
        console.log('🔧 Ensuring footer buttons work...');
        
        // Wait for page to be fully loaded
        if (document.readyState !== 'complete') {
            window.addEventListener('load', ensureFooterButtonsWork);
            return;
        }
        
        // Find all mobile nav links
        const footerButtons = document.querySelectorAll('.mobile-nav-link');
        console.log(`Found ${footerButtons.length} footer buttons`);
        
        footerButtons.forEach((button, index) => {
            const onclick = button.getAttribute('onclick');
            const text = button.querySelector('.nav-text')?.textContent || 'unknown';
            
            console.log(`Footer button ${index + 1}: ${text} - onclick: ${onclick || 'none'}`);
            
            // If button has onclick but it's not working, fix it
            if (onclick && onclick.includes('openMobileModal')) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    try {
                        console.log(`🖱️ Footer button clicked: ${text}`);
                        
                        // Extract modal name from onclick
                        const match = onclick.match(/openMobileModal\(['"]([^'"]+)['"]\)/);
                        if (match) {
                            const modalName = match[1];
                            console.log(`📱 Opening modal: ${modalName}`);
                            
                            // Ensure openMobileModal function exists
                            if (typeof window.openMobileModal === 'function') {
                                window.openMobileModal(modalName);
                            } else {
                                console.warn('openMobileModal function not found, creating fallback...');
                                createFallbackModalFunction();
                                window.openMobileModal(modalName);
                            }
                        }
                    } catch (error) {
                        console.error('Footer button click error:', error);
                    }
                });
            }
        });
    }
    
    // Create fallback modal function if original is missing
    function createFallbackModalFunction() {
        window.openMobileModal = function(modalName) {
            try {
                console.log('🚨 Using fallback modal opener for:', modalName);
                
                const modalId = `mobile${modalName.charAt(0).toUpperCase() + modalName.slice(1)}Modal`;
                const modalElement = document.getElementById(modalId);
                
                if (!modalElement) {
                    console.warn('Modal element not found:', modalId);
                    return;
                }
                
                // Close any existing modals
                const existingModals = document.querySelectorAll('.modal.show');
                existingModals.forEach(modal => {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    modal.setAttribute('aria-hidden', 'true');
                    modal.removeAttribute('aria-modal');
                });
                
                // Remove existing backdrops
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                
                // Show the new modal
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                modalElement.setAttribute('aria-modal', 'true');
                modalElement.removeAttribute('aria-hidden');
                
                // Create backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.addEventListener('click', function() {
                    // Close modal when backdrop is clicked
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    modalElement.setAttribute('aria-hidden', 'true');
                    modalElement.removeAttribute('aria-modal');
                    backdrop.remove();
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
                document.body.appendChild(backdrop);
                
                // Add modal-open class to body
                document.body.classList.add('modal-open');
                
                console.log('✅ Fallback modal opened:', modalId);
                
            } catch (error) {
                console.error('Fallback modal opener error:', error);
            }
        };
        
        console.log('✅ Fallback modal function created');
    }
    
    // Ensure logout function works
    function ensureLogoutWorks() {
        if (!window.confirmLogout) {
            window.confirmLogout = function() {
                try {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Confirm Logout',
                            text: 'Are you sure you want to logout?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Logout',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/simple-logout';
                            }
                        });
                    } else {
                        if (confirm('Are you sure you want to logout?')) {
                            window.location.href = '/simple-logout';
                        }
                    }
                } catch (error) {
                    console.error('Logout confirmation error:', error);
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = '/logout';
                    }
                }
            };
        }
    }
    
    // Main execution
    function initialize() {
        console.log('🚀 Initializing post-login fixes...');
        
        // Clean URL if needed
        if (hasProblematicParameters()) {
            const cleaned = cleanURL();
            if (cleaned) {
                console.log('✅ URL cleaned successfully');
            }
        }
        
        // Ensure functions work
        ensureLogoutWorks();
        
        // Fix footer buttons after a short delay
        setTimeout(() => {
            ensureFooterButtonsWork();
        }, 500);
        
        // Also fix them when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', ensureFooterButtonsWork);
        } else {
            ensureFooterButtonsWork();
        }
        
        console.log('✅ Post-login fixes initialized');
    }
    
    // Run immediately if page is already loaded, otherwise wait
    if (document.readyState === 'complete') {
        initialize();
    } else {
        window.addEventListener('load', initialize);
    }
    
    // Also run on URL changes (for SPA-like behavior)
    let lastUrl = window.location.href;
    setInterval(() => {
        if (window.location.href !== lastUrl) {
            lastUrl = window.location.href;
            console.log('🔄 URL changed, re-initializing fixes...');
            setTimeout(initialize, 100);
        }
    }, 1000);
    
    console.log('🔧 Post-Login URL Cleaner & Footer Fix Ready');
    
})();
