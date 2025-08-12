<!DOCTYPE html>
<html>
<head>
    <title>Logout Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Logout Behavior Test</h1>
    
    <div id="auth-status">
        <p>Loading auth status...</p>
    </div>
    
    <div>
        <button onclick="checkAuthStatus()">Check Auth Status</button>
        <button onclick="performLogout()">Test Logout (CSRF)</button>
        <button onclick="forceLogout()">Force Logout (No CSRF)</button>
        <button onclick="accessDashboard()">Try Dashboard Access</button>
        <button onclick="clearBrowserCache()">Clear Browser Cache</button>
    </div>
    
    <div id="results" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc;">
        <h3>Results:</h3>
        <div id="output"></div>
    </div>

    <script>
        const output = document.getElementById('output');
        const authStatus = document.getElementById('auth-status');

        function log(message) {
            const time = new Date().toLocaleTimeString();
            output.innerHTML += `<p><strong>[${time}]</strong> ${message}</p>`;
        }

        async function checkAuthStatus() {
            try {
                const response = await fetch('/auth-status');
                const data = await response.json();
                
                authStatus.innerHTML = `
                    <h3>Current Auth Status:</h3>
                    <p><strong>Authenticated:</strong> ${data.authenticated}</p>
                    <p><strong>User ID:</strong> ${data.user_id || 'None'}</p>
                    <p><strong>Session ID:</strong> ${data.session_id}</p>
                    <p><strong>Timestamp:</strong> ${data.timestamp}</p>
                `;
                
                log(`Auth Status: ${data.authenticated ? 'Logged In' : 'Logged Out'}`);
            } catch (error) {
                log(`Error checking auth status: ${error.message}`);
            }
        }

        async function performLogout() {
            try {
                log('Attempting logout...');
                
                // Get CSRF token from meta tag
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                log(`Using CSRF token: ${token.substring(0, 10)}...`);
                
                // Try logout with form data instead of JSON
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('_method', 'POST');
                
                const response = await fetch('/logout', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                log(`Logout response status: ${response.status}`);
                
                if (response.redirected) {
                    log(`Logout redirected to: ${response.url}`);
                } else if (response.ok) {
                    try {
                        const data = await response.json();
                        log(`Logout response: ${JSON.stringify(data)}`);
                    } catch (e) {
                        const text = await response.text();
                        log(`Logout response (text): ${text.substring(0, 200)}...`);
                    }
                } else {
                    const errorText = await response.text();
                    log(`Logout error (${response.status}): ${errorText.substring(0, 200)}...`);
                }
                
                // Check auth status after logout
                setTimeout(checkAuthStatus, 1000);
                
            } catch (error) {
                log(`Logout error: ${error.message}`);
                
                // Try alternative logout method (simple GET request)
                log('Trying alternative logout method...');
                try {
                    window.location.href = '/simple-logout';
                } catch (altError) {
                    log(`Alternative logout error: ${altError.message}`);
                }
            }
        }

        async function forceLogout() {
            try {
                log('Attempting force logout (no CSRF)...');
                
                const response = await fetch('/force-logout', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                log(`Force logout response: ${JSON.stringify(data)}`);
                
                if (data.success) {
                    log('Force logout successful!');
                    // Clear browser storage
                    clearBrowserCache();
                    // Check auth status
                    setTimeout(checkAuthStatus, 500);
                } else {
                    log(`Force logout failed: ${data.error}`);
                }
                
            } catch (error) {
                log(`Force logout error: ${error.message}`);
            }
        }

        async function accessDashboard() {
            try {
                log('Attempting to access dashboard...');
                
                const response = await fetch('/user/dashboard', {
                    method: 'GET',
                    credentials: 'same-origin',
                    redirect: 'manual'  // Don't follow redirects automatically
                });
                
                if (response.status === 0) {
                    log('Dashboard access was redirected (likely to login)');
                } else if (response.status === 302) {
                    const location = response.headers.get('Location');
                    log(`Dashboard access redirected to: ${location}`);
                } else if (response.status === 200) {
                    log('Dashboard access successful (user is logged in)');
                } else {
                    log(`Dashboard access returned status: ${response.status}`);
                }
                
            } catch (error) {
                log(`Dashboard access error: ${error.message}`);
            }
        }

        function clearBrowserCache() {
            // Mark logout in localStorage
            localStorage.setItem('logout_time', Date.now().toString());
            localStorage.setItem('logout_completed', 'true');
            
            // Clear any cached data
            localStorage.removeItem('user_data');
            localStorage.removeItem('dashboard_cache');
            sessionStorage.clear();
            
            log('Browser cache and storage cleared');
        }

        // Auto-check auth status on page load
        document.addEventListener('DOMContentLoaded', checkAuthStatus);
    </script>
</body>
</html>
