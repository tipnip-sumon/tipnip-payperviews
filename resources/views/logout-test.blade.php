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
        <button onclick="performLogout()">Test Logout</button>
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
                
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (response.redirected) {
                    log(`Logout redirected to: ${response.url}`);
                } else {
                    const data = await response.json();
                    log(`Logout response: ${JSON.stringify(data)}`);
                }
                
                // Check auth status after logout
                setTimeout(checkAuthStatus, 1000);
                
            } catch (error) {
                log(`Logout error: ${error.message}`);
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
