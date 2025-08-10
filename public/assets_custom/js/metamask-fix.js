/**
 * MetaMask Error Handler
 * Handles MetaMask connection errors gracefully
 * Created: August 9, 2025
 */

(function() {
    'use strict';

    // MetaMask error handler
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('MetaMask')) {
            console.warn('MetaMask error handled:', e.message);
            return true; // Prevent error from bubbling up
        }
    });

    // Handle promise rejections for MetaMask
    window.addEventListener('unhandledrejection', function(e) {
        if (e.reason && e.reason.message && e.reason.message.includes('MetaMask')) {
            console.warn('MetaMask promise rejection handled:', e.reason.message);
            e.preventDefault(); // Prevent error from bubbling up
        }
    });

    // Safe MetaMask detection
    window.safeDetectMetaMask = function() {
        try {
            if (typeof window.ethereum !== 'undefined') {
                return window.ethereum.isMetaMask;
            }
            return false;
        } catch (error) {
            console.warn('MetaMask detection error:', error);
            return false;
        }
    };

    // Safe MetaMask connection
    window.safeConnectMetaMask = async function() {
        try {
            if (!window.safeDetectMetaMask()) {
                throw new Error('MetaMask not detected');
            }
            
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });
            
            return accounts;
        } catch (error) {
            console.warn('MetaMask connection error handled:', error.message);
            
            // Show user-friendly message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'MetaMask Not Available',
                    text: 'MetaMask extension is not installed or not available. Please install MetaMask to use this feature.',
                    confirmButtonText: 'OK',
                    toast: true,
                    position: 'top-end',
                    timer: 5000,
                    timerProgressBar: true
                });
            }
            
            throw error;
        }
    };

    // Override any existing MetaMask connect functions
    if (typeof window.ethereum !== 'undefined') {
        const originalConnect = window.ethereum.request;
        window.ethereum.request = function(args) {
            try {
                return originalConnect.call(this, args);
            } catch (error) {
                console.warn('MetaMask request error handled:', error);
                return Promise.reject(error);
            }
        };
    }

    // MetaMask error handler loaded (silent mode)
})();
