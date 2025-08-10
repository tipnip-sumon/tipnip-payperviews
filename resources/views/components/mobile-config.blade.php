<script>
/**
 * Dynamic Configuration for PayPerViews Mobile
 * Server-side generated configuration based on environment
 */

// Update the static configuration with server values
if (window.PV_CONFIG) {
    window.PV_CONFIG.env = '{{ env("APP_ENV", "production") }}';
    window.PV_CONFIG.debug = {{ config('mobile.debug_console', false) ? 'true' : 'false' }};
    window.PV_CONFIG.version = '{{ config("app.version", "1.0.0") }}';
    
    window.PV_CONFIG.console.enabled = {{ config('mobile.js_config.console_enabled', false) ? 'true' : 'false' }};
    window.PV_CONFIG.console.level = '{{ config("mobile.js_config.error_reporting_level", "production") }}';
    
    window.PV_CONFIG.mobile.breakpoint = {{ config('mobile.mobile_breakpoint', 991) }};
    window.PV_CONFIG.mobile.forceDetection = {{ config('mobile.force_mobile_detection', false) ? 'true' : 'false' }};
    window.PV_CONFIG.mobile.cacheVersion = '{{ config("mobile.mobile_cache_version", config("app.version", "1.0.0")) }}';
    
    window.PV_CONFIG.errors.silent = {{ config('mobile.silent_errors', true) ? 'true' : 'false' }};
    window.PV_CONFIG.errors.boundaries = {{ config('mobile.js_config.error_boundaries', true) ? 'true' : 'false' }};
    
    window.PV_CONFIG.performance.enabled = {{ config('mobile.js_config.performance_monitoring', false) ? 'true' : 'false' }};
}
</script>
