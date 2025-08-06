// PayPerViews Error Handler
// Handles various browser compatibility and security issues

class PayPerViewsErrorHandler {
    constructor() {
        this.init();
    }

    init() {
        this.setupCSPErrorHandling();
        this.setupMetaMaskErrorSuppression();
        this.setupTrustedScriptHandling();
        this.setupFallbacks();
    }

    // Handle Content Security Policy violations
    setupCSPErrorHandling() {
        document.addEventListener('securitypolicyviolation', (e) => {
            console.group('🛡️ CSP Violation Detected');
            console.warn('Blocked URI:', e.blockedURI);
            console.warn('Violated Directive:', e.violatedDirective);
            console.warn('Source File:', e.sourceFile);
            console.warn('Line Number:', e.lineNumber);
            console.groupEnd();

            // Handle specific CSP violations
            this.handleCSPViolation(e);
        });
    }

    // Suppress MetaMask and Web3 related errors for non-crypto pages
    setupMetaMaskErrorSuppression() {
        window.addEventListener('error', (e) => {
            const errorMessage = e.error?.message || e.message || '';
            
            // List of Web3/MetaMask related errors to suppress
            const web3Errors = [
                'MetaMask',
                'ethereum',
                'web3',
                'chrome-extension',
                'extension context invalidated',
                'Extension context invalidated',
                'Cannot access contents of url',
                'Script error'
            ];

            // Check if error is Web3/MetaMask related
            if (web3Errors.some(keyword => errorMessage.includes(keyword))) {
                console.log('🔇 Suppressed Web3/MetaMask error (not needed for this page):', errorMessage);
                e.preventDefault();
                return false;
            }
        });

        // Also handle unhandled promise rejections
        window.addEventListener('unhandledrejection', (e) => {
            const reason = e.reason?.message || e.reason || '';
            
            if (typeof reason === 'string' && 
                (reason.includes('MetaMask') || reason.includes('ethereum') || reason.includes('web3'))) {
                console.log('🔇 Suppressed Web3/MetaMask promise rejection:', reason);
                e.preventDefault();
                return false;
            }
        });
    }

    // Handle TrustedScript issues
    setupTrustedScriptHandling() {
        // Polyfill for browsers that don't support Trusted Types
        if (!window.trustedTypes) {
            window.trustedTypes = {
                createPolicy: () => ({
                    createScript: (script) => script,
                    createScriptURL: (url) => url,
                    createHTML: (html) => html
                })
            };
        }

        // Create a trusted types policy for our scripts
        if (window.trustedTypes && window.trustedTypes.createPolicy) {
            try {
                this.policy = window.trustedTypes.createPolicy('payperviews-policy', {
                    createScript: (script) => {
                        // Validate and sanitize scripts if needed
                        return script;
                    },
                    createScriptURL: (url) => {
                        // Validate script URLs
                        const allowedDomains = [
                            'cdn.jsdelivr.net',
                            'code.jquery.com',
                            'cdnjs.cloudflare.com',
                            window.location.origin
                        ];

                        const urlObj = new URL(url, window.location.origin);
                        if (allowedDomains.some(domain => urlObj.hostname.includes(domain))) {
                            return url;
                        }
                        
                        throw new Error(`Script URL not allowed: ${url}`);
                    },
                    createHTML: (html) => {
                        // Basic HTML sanitization could go here
                        return html;
                    }
                });
            } catch (error) {
                console.warn('Could not create Trusted Types policy:', error);
            }
        }
    }

    // Setup fallback mechanisms
    setupFallbacks() {
        // Fallback for modal functionality
        window.showLoginModalFallback = () => {
            const loginUrl = '/login';
            const registerUrl = '/register';
            
            if (confirm('Please log in to watch videos and earn money.\n\nClick OK to go to login page, Cancel to go to registration.')) {
                window.location.href = loginUrl;
            } else {
                window.location.href = registerUrl;
            }
        };

        // Fallback for smooth scrolling
        window.scrollToVideosFallback = () => {
            const videosSection = document.getElementById('videos');
            if (videosSection) {
                videosSection.scrollIntoView();
            }
        };
    }

    // Handle specific CSP violations
    handleCSPViolation(violation) {
        const directive = violation.violatedDirective;
        const blockedURI = violation.blockedURI;

        // Handle script-src violations
        if (directive.includes('script-src')) {
            console.warn('🚫 Script blocked by CSP. Consider adding to allowlist:', blockedURI);
        }

        // Handle style-src violations
        if (directive.includes('style-src')) {
            console.warn('🎨 Style blocked by CSP. Consider adding to allowlist:', blockedURI);
        }

        // Handle frame-src violations (for video embeds)
        if (directive.includes('frame-src')) {
            console.warn('🖼️ Frame blocked by CSP. Video embed may not work:', blockedURI);
        }
    }

    // Safe script execution with Trusted Types
    executeScript(scriptContent) {
        try {
            if (this.policy && this.policy.createScript) {
                const trustedScript = this.policy.createScript(scriptContent);
                return trustedScript;
            }
            return scriptContent;
        } catch (error) {
            console.error('Script execution blocked by Trusted Types:', error);
            return null;
        }
    }

    // Safe HTML insertion with Trusted Types
    insertHTML(element, htmlContent) {
        try {
            if (this.policy && this.policy.createHTML) {
                const trustedHTML = this.policy.createHTML(htmlContent);
                element.innerHTML = trustedHTML;
            } else {
                element.innerHTML = htmlContent;
            }
        } catch (error) {
            console.error('HTML insertion blocked by Trusted Types:', error);
        }
    }
}

// Initialize error handler when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.payPerViewsErrorHandler = new PayPerViewsErrorHandler();
    console.log('✅ PayPerViews Error Handler initialized');
});

// Also initialize immediately in case DOMContentLoaded already fired
if (document.readyState === 'loading') {
    // Document still loading, wait for DOMContentLoaded
} else {
    // Document already loaded
    window.payPerViewsErrorHandler = new PayPerViewsErrorHandler();
    console.log('✅ PayPerViews Error Handler initialized (immediate)');
}
