/**
 * Network Diagnostics for Font Loading Issues
 * Helps identify and report network problems affecting Google Fonts
 */

class NetworkDiagnostics {
    constructor() {
        this.testResults = {};
        this.isRunning = false;
        this.startTime = Date.now();
    }

    async runDiagnostics() {
        if (this.isRunning) return this.testResults;
        
        this.isRunning = true;
        console.log('🔍 Running network diagnostics...');
        
        try {
            const results = await Promise.allSettled([
                this.testConnectivity(),
                this.testDNSResolution(),
                this.testGoogleFontsAPI(),
                this.testFontFileAccess(),
                this.testCacheStatus(),
                this.measureLatency()
            ]);
            
            this.testResults = {
                connectivity: results[0].value || results[0].reason,
                dns: results[1].value || results[1].reason,
                googleFontsAPI: results[2].value || results[2].reason,
                fontFiles: results[3].value || results[3].reason,
                cache: results[4].value || results[4].reason,
                latency: results[5].value || results[5].reason,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                onLine: navigator.onLine,
                connection: this.getConnectionInfo()
            };
            
            this.logResults();
            return this.testResults;
            
        } catch (error) {
            console.error('Diagnostics failed:', error);
            return { error: error.message };
        } finally {
            this.isRunning = false;
        }
    }

    async testConnectivity() {
        try {
            const response = await fetch('https://fonts.googleapis.com/', {
                method: 'HEAD',
                cache: 'no-cache',
                signal: AbortSignal.timeout(5000)
            });
            
            return {
                status: 'success',
                statusCode: response.status,
                headers: Object.fromEntries(response.headers.entries())
            };
        } catch (error) {
            return {
                status: 'failed',
                error: error.message,
                type: error.name
            };
        }
    }

    async testDNSResolution() {
        const hosts = [
            'fonts.googleapis.com',
            'fonts.gstatic.com'
        ];
        
        const results = {};
        
        for (const host of hosts) {
            try {
                const start = performance.now();
                const response = await fetch(`https://${host}/`, {
                    method: 'HEAD',
                    cache: 'no-cache',
                    signal: AbortSignal.timeout(3000)
                });
                const end = performance.now();
                
                results[host] = {
                    status: 'resolved',
                    responseTime: Math.round(end - start),
                    statusCode: response.status
                };
            } catch (error) {
                results[host] = {
                    status: 'failed',
                    error: error.message
                };
            }
        }
        
        return results;
    }

    async testGoogleFontsAPI() {
        try {
            const fontUrl = 'https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap';
            const start = performance.now();
            
            const response = await fetch(fontUrl, {
                cache: 'no-cache',
                signal: AbortSignal.timeout(5000)
            });
            
            const end = performance.now();
            const cssText = await response.text();
            
            return {
                status: 'success',
                responseTime: Math.round(end - start),
                contentLength: cssText.length,
                hasWoff2: cssText.includes('woff2'),
                statusCode: response.status
            };
        } catch (error) {
            return {
                status: 'failed',
                error: error.message,
                type: error.name
            };
        }
    }

    async testFontFileAccess() {
        try {
            // Test a specific Inter font file
            const fontUrl = 'https://fonts.gstatic.com/s/inter/v19/UcC73FwrK3iLTeHuS_nVMrMxCp50SjIa1ZL7.woff2';
            const start = performance.now();
            
            const response = await fetch(fontUrl, {
                method: 'HEAD',
                cache: 'no-cache',
                signal: AbortSignal.timeout(5000)
            });
            
            const end = performance.now();
            
            return {
                status: 'accessible',
                responseTime: Math.round(end - start),
                contentType: response.headers.get('content-type'),
                contentLength: response.headers.get('content-length'),
                statusCode: response.status
            };
        } catch (error) {
            return {
                status: 'failed',
                error: error.message,
                type: error.name
            };
        }
    }

    async testCacheStatus() {
        if (!('caches' in window)) {
            return { status: 'unsupported' };
        }
        
        try {
            const cacheNames = await caches.keys();
            const fontCaches = cacheNames.filter(name => 
                name.includes('font') || name.includes('google')
            );
            
            const cacheContents = {};
            for (const cacheName of fontCaches) {
                const cache = await caches.open(cacheName);
                const requests = await cache.keys();
                cacheContents[cacheName] = requests.map(req => req.url);
            }
            
            return {
                status: 'available',
                cacheNames: fontCaches,
                contents: cacheContents,
                totalCaches: cacheNames.length
            };
        } catch (error) {
            return {
                status: 'error',
                error: error.message
            };
        }
    }

    async measureLatency() {
        const measurements = [];
        const testUrl = 'https://fonts.googleapis.com/';
        
        for (let i = 0; i < 3; i++) {
            try {
                const start = performance.now();
                await fetch(testUrl, {
                    method: 'HEAD',
                    cache: 'no-cache',
                    signal: AbortSignal.timeout(5000)
                });
                const end = performance.now();
                measurements.push(Math.round(end - start));
            } catch (error) {
                measurements.push(-1); // Failed measurement
            }
        }
        
        const validMeasurements = measurements.filter(m => m > 0);
        
        if (validMeasurements.length === 0) {
            return {
                status: 'failed',
                measurements: measurements
            };
        }
        
        return {
            status: 'success',
            measurements: measurements,
            average: Math.round(validMeasurements.reduce((a, b) => a + b) / validMeasurements.length),
            min: Math.min(...validMeasurements),
            max: Math.max(...validMeasurements)
        };
    }

    getConnectionInfo() {
        if ('connection' in navigator) {
            const conn = navigator.connection;
            return {
                effectiveType: conn.effectiveType,
                downlink: conn.downlink,
                rtt: conn.rtt,
                saveData: conn.saveData
            };
        }
        return { status: 'unsupported' };
    }

    logResults() {
        console.group('🔍 Network Diagnostics Results');
        
        Object.entries(this.testResults).forEach(([key, value]) => {
            if (typeof value === 'object' && value.status) {
                const emoji = value.status === 'success' || value.status === 'accessible' || value.status === 'resolved' ? '✅' : '❌';
                console.log(`${emoji} ${key}:`, value);
            } else {
                console.log(`ℹ️ ${key}:`, value);
            }
        });
        
        console.groupEnd();
        
        // Show summary
        this.showDiagnosticsSummary();
    }

    showDiagnosticsSummary() {
        const issues = [];
        
        if (this.testResults.connectivity?.status === 'failed') {
            issues.push('Google Fonts API unreachable');
        }
        
        if (this.testResults.fontFiles?.status === 'failed') {
            issues.push('Font files cannot be downloaded');
        }
        
        if (this.testResults.latency?.average > 5000) {
            issues.push('High network latency detected');
        }
        
        if (!navigator.onLine) {
            issues.push('Device appears to be offline');
        }
        
        if (issues.length > 0) {
            console.warn('⚠️ Network issues detected:', issues);
            this.suggestSolutions(issues);
        } else {
            console.log('✅ Network connectivity appears normal');
        }
    }

    suggestSolutions(issues) {
        const solutions = {
            'Google Fonts API unreachable': 'Check firewall settings or try a VPN',
            'Font files cannot be downloaded': 'Clear browser cache or try incognito mode',
            'High network latency detected': 'Consider using local font files',
            'Device appears to be offline': 'Check internet connection'
        };
        
        console.group('💡 Suggested Solutions');
        issues.forEach(issue => {
            if (solutions[issue]) {
                console.log(`• ${issue}: ${solutions[issue]}`);
            }
        });
        console.groupEnd();
    }

    // Export results for support
    exportResults() {
        return {
            ...this.testResults,
            exportedAt: new Date().toISOString(),
            version: '1.0.0'
        };
    }
}

// Global diagnostics runner
window.runNetworkDiagnostics = async () => {
    const diagnostics = new NetworkDiagnostics();
    return await diagnostics.runDiagnostics();
};

// Auto-run diagnostics on font loading failure
document.addEventListener('fontfailed', async () => {
    console.log('🔍 Font loading failed - running network diagnostics...');
    await window.runNetworkDiagnostics();
});

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NetworkDiagnostics;
}
