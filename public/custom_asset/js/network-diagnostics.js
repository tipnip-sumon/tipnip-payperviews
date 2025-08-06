/**
 * Network Diagnostics - Network connectivity and performance monitoring
 * Provides real-time network status and connection quality information
 */

(function() {
    'use strict';

    // Network diagnostics configuration
    const NETWORK_CONFIG = {
        testUrl: window.location.origin + '/ping',
        testInterval: 30000, // 30 seconds
        timeoutDuration: 5000, // 5 seconds
        retryAttempts: 3,
        slowConnectionThreshold: 2000, // 2 seconds
        enableLogging: true
    };

    // Network diagnostics class
    class NetworkDiagnostics {
        constructor() {
            this.isOnline = navigator.onLine;
            this.connectionQuality = 'unknown';
            this.lastLatency = null;
            this.testInProgress = false;
            this.retryCount = 0;
            this.listeners = new Set();
            
            this.init();
        }

        init() {
            this.setupEventListeners();
            this.initialNetworkTest();
            this.startPeriodicTesting();
        }

        setupEventListeners() {
            // Browser online/offline events
            window.addEventListener('online', () => {
                this.log('Browser detected online status');
                this.isOnline = true;
                this.onStatusChange('online');
                this.performNetworkTest();
            });

            window.addEventListener('offline', () => {
                this.log('Browser detected offline status');
                this.isOnline = false;
                this.connectionQuality = 'offline';
                this.onStatusChange('offline');
            });

            // Page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && this.isOnline) {
                    this.performNetworkTest();
                }
            });

            // Connection API support (experimental)
            if ('connection' in navigator) {
                navigator.connection.addEventListener('change', () => {
                    this.log('Connection change detected');
                    this.performNetworkTest();
                });
            }
        }

        initialNetworkTest() {
            if (this.isOnline) {
                this.performNetworkTest();
            }
        }

        startPeriodicTesting() {
            setInterval(() => {
                if (this.isOnline && !document.hidden) {
                    this.performNetworkTest();
                }
            }, NETWORK_CONFIG.testInterval);
        }

        async performNetworkTest() {
            if (this.testInProgress) {
                this.log('Network test already in progress, skipping');
                return;
            }

            this.testInProgress = true;
            const startTime = Date.now();

            try {
                // Create a unique URL to avoid caching
                const testUrl = NETWORK_CONFIG.testUrl + '?t=' + Date.now();
                
                const response = await Promise.race([
                    fetch(testUrl, {
                        method: 'HEAD',
                        cache: 'no-cache',
                        mode: 'cors'
                    }),
                    new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Timeout')), NETWORK_CONFIG.timeoutDuration)
                    )
                ]);

                const latency = Date.now() - startTime;
                this.lastLatency = latency;

                if (response.ok) {
                    this.onTestSuccess(latency);
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }

            } catch (error) {
                this.onTestFailure(error);
            } finally {
                this.testInProgress = false;
            }
        }

        onTestSuccess(latency) {
            this.retryCount = 0;
            this.isOnline = true;

            // Determine connection quality
            if (latency < 100) {
                this.connectionQuality = 'excellent';
            } else if (latency < 300) {
                this.connectionQuality = 'good';
            } else if (latency < 1000) {
                this.connectionQuality = 'fair';
            } else if (latency < NETWORK_CONFIG.slowConnectionThreshold) {
                this.connectionQuality = 'slow';
            } else {
                this.connectionQuality = 'very-slow';
            }

            this.log(`Network test successful - Latency: ${latency}ms, Quality: ${this.connectionQuality}`);
            this.onStatusChange('online', { latency, quality: this.connectionQuality });
        }

        onTestFailure(error) {
            this.log(`Network test failed: ${error.message}`);
            
            if (this.retryCount < NETWORK_CONFIG.retryAttempts) {
                this.retryCount++;
                this.log(`Retrying network test (${this.retryCount}/${NETWORK_CONFIG.retryAttempts})`);
                setTimeout(() => this.performNetworkTest(), 1000 * this.retryCount);
            } else {
                this.isOnline = false;
                this.connectionQuality = 'offline';
                this.onStatusChange('offline', { error: error.message });
                this.retryCount = 0;
            }
        }

        onStatusChange(status, details = {}) {
            const eventData = {
                online: this.isOnline,
                quality: this.connectionQuality,
                latency: this.lastLatency,
                timestamp: Date.now(),
                ...details
            };

            // Notify all listeners
            this.listeners.forEach(callback => {
                try {
                    callback(eventData);
                } catch (error) {
                    console.error('Network status listener error:', error);
                }
            });

            // Dispatch global event
            window.dispatchEvent(new CustomEvent('networkStatusChange', {
                detail: eventData
            }));

            // Update CSS classes for styling
            this.updateUI(eventData);
        }

        updateUI(data) {
            const classes = ['network-offline', 'network-online', 'network-slow', 'network-excellent', 'network-good', 'network-fair'];
            document.body.classList.remove(...classes);

            if (data.online) {
                document.body.classList.add('network-online', `network-${data.quality}`);
            } else {
                document.body.classList.add('network-offline');
            }

            // Update CSS custom properties
            document.documentElement.style.setProperty('--network-status', data.online ? 'online' : 'offline');
            document.documentElement.style.setProperty('--network-quality', data.quality);
            document.documentElement.style.setProperty('--network-latency', data.latency + 'ms');
        }

        // Public API methods
        addListener(callback) {
            this.listeners.add(callback);
            return () => this.listeners.delete(callback);
        }

        getStatus() {
            return {
                online: this.isOnline,
                quality: this.connectionQuality,
                latency: this.lastLatency,
                timestamp: Date.now()
            };
        }

        forceTest() {
            if (!this.testInProgress) {
                this.performNetworkTest();
            }
        }

        log(message) {
            if (NETWORK_CONFIG.enableLogging) {
                console.log(`[NetworkDiagnostics] ${message}`);
            }
        }
    }

    // Network speed test utility
    class NetworkSpeedTest {
        static async measureDownloadSpeed() {
            const testSize = 100 * 1024; // 100KB test
            const testUrl = '/assets/js/network-diagnostics.js?test=' + Date.now();
            
            const startTime = performance.now();
            
            try {
                const response = await fetch(testUrl, { cache: 'no-cache' });
                const data = await response.arrayBuffer();
                const endTime = performance.now();
                
                const duration = (endTime - startTime) / 1000; // seconds
                const speed = (data.byteLength * 8) / duration / 1000; // kbps
                
                return Math.round(speed);
            } catch (error) {
                console.error('Speed test failed:', error);
                return null;
            }
        }
    }

    // Initialize network diagnostics
    function initNetworkDiagnostics() {
        window.networkDiagnostics = new NetworkDiagnostics();
        window.NetworkSpeedTest = NetworkSpeedTest;
        
        // Global convenience methods
        window.getNetworkStatus = () => window.networkDiagnostics.getStatus();
        window.onNetworkChange = (callback) => window.networkDiagnostics.addListener(callback);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNetworkDiagnostics);
    } else {
        initNetworkDiagnostics();
    }

    // Export classes
    window.NetworkDiagnostics = NetworkDiagnostics;
    window.NetworkSpeedTest = NetworkSpeedTest;

})();
