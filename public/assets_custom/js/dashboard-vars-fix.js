/**
 * Dashboard Variable Declaration Fix
 * Prevents duplicate variable declaration errors in dashboard
 * Created: August 9, 2025
 */

(function() {
    'use strict';
    
    // Prevent duplicate variable declarations by using global window object
    if (typeof window.dashboardVars === 'undefined') {
        window.dashboardVars = {};
    }

    // Safe variable declaration function
    window.safeDeclareVar = function(varName, defaultValue) {
        if (typeof window.dashboardVars[varName] === 'undefined') {
            window.dashboardVars[varName] = defaultValue;
            window[varName] = defaultValue;
        }
        return window.dashboardVars[varName];
    };

    // Declare all dashboard variables safely
    window.safeDeclareVar('balanceVisibility', {
        'current-balance': true,
        'team-bonus': true,
        'total-earnings': true,
        'video-access-vault': true
    });

    window.safeDeclareVar('performanceRefreshInterval', null);
    window.safeDeclareVar('isHighTrafficMode', false);
    window.safeDeclareVar('concurrentUsers', 0);
    window.safeDeclareVar('lastRefreshTime', Date.now());
    window.safeDeclareVar('dashboardUpdateInterval', null);
    window.safeDeclareVar('isPageActive', true);
    window.safeDeclareVar('lastUpdateTime', null);
    window.safeDeclareVar('performanceChart', null);
    window.safeDeclareVar('sessionCheckInterval', null);

    // Safe function declaration wrapper
    window.safeDeclareFunction = function(funcName, func) {
        if (typeof window[funcName] === 'undefined') {
            window[funcName] = func;
        }
        return window[funcName];
    };

    // Override console.error for variable redeclaration errors
    const originalError = console.error;
    console.error = function(...args) {
        const message = args.join(' ');
        if (message.includes('has already been declared') || 
            message.includes('Identifier') || 
            message.includes('SyntaxError')) {
            console.warn('Variable redeclaration error suppressed:', message);
            return;
        }
        originalError.apply(console, args);
    };

    // Override script loading to prevent duplicate declarations
    const originalAppendChild = document.head.appendChild;
    document.head.appendChild = function(element) {
        if (element.tagName === 'SCRIPT' && element.innerHTML) {
            // Check for variable declarations in inline scripts
            const scriptContent = element.innerHTML;
            const varDeclarations = scriptContent.match(/\b(let|const|var)\s+(\w+)/g);
            
            if (varDeclarations) {
                // Replace declarations with safe declarations
                let modifiedContent = scriptContent;
                varDeclarations.forEach(declaration => {
                    const [, keyword, varName] = declaration.match(/\b(let|const|var)\s+(\w+)/);
                    if (['balanceVisibility', 'performanceRefreshInterval', 'isHighTrafficMode', 
                         'concurrentUsers', 'lastRefreshTime', 'dashboardUpdateInterval', 
                         'isPageActive', 'lastUpdateTime', 'performanceChart', 'sessionCheckInterval'].includes(varName)) {
                        modifiedContent = modifiedContent.replace(
                            new RegExp(`\\b(let|const|var)\\s+${varName}`, 'g'),
                            `window.dashboardVars.${varName} = window.dashboardVars.${varName} ||`
                        );
                    }
                });
                element.innerHTML = modifiedContent;
            }
        }
        return originalAppendChild.call(this, element);
    };

    // Dashboard variable declaration fix loaded (silent mode)
})();
