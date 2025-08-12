/**
 * Post-logout session manager
 * Prevents accessing protected pages after logout by detecting logout state
 */

(function() {
    'use strict';
    
    // Check if we're on a protected page after logout
    function checkLogoutState() {
        // Check for logout indicators in localStorage
        const logoutTime = localStorage.getItem('logout_time');
        const currentTime = Date.now();
        
        // If user logged out recently (within last 30 minutes)
        if (logoutTime && (currentTime - parseInt(logoutTime)) < 1800000) {
            // Check if we're on a protected page
            const protectedPaths = ['/user/dashboard', '/user/', '/admin/'];
            const currentPath = window.location.pathname;
            
            const isProtectedPage = protectedPaths.some(path => 
                currentPath.startsWith(path)
            );
            
            if (isProtectedPage && !window.location.pathname.includes('/login')) {
                console.log('Redirecting from protected page after logout');
                window.location.href = '/login?from=post_logout&t=' + Date.now();
                return;
            }
        }
    }
    
    // Mark logout in localStorage when logout occurs
    function markLogout() {
        localStorage.setItem('logout_time', Date.now().toString());
        localStorage.setItem('logout_completed', 'true');
        
        // Clear any cached user data
        localStorage.removeItem('user_data');
        localStorage.removeItem('dashboard_cache');
        
        // Clear session storage as well
        sessionStorage.clear();
    }
    
    // Clear logout state when login is successful
    function clearLogoutState() {
        localStorage.removeItem('logout_time');
        localStorage.removeItem('logout_completed');
    }
    
    // Listen for logout events
    document.addEventListener('DOMContentLoaded', function() {
        // Check logout state on page load
        checkLogoutState();
        
        // Listen for logout button clicks
        const logoutLinks = document.querySelectorAll('a[href*="logout"], form[action*="logout"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', function() {
                markLogout();
            });
        });
        
        // Listen for successful login (clear logout state)
        if (window.location.pathname.includes('/user/dashboard') || 
            window.location.pathname.includes('/user/')) {
            clearLogoutState();
        }
        
        // Prevent back button navigation to protected pages after logout
        if (localStorage.getItem('logout_completed') === 'true') {
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page was loaded from cache (back button)
                    checkLogoutState();
                }
            });
        }
    });
    
    // Expose functions globally for debugging
    window.logoutManager = {
        checkState: checkLogoutState,
        markLogout: markLogout,
        clearState: clearLogoutState
    };
    
})();
