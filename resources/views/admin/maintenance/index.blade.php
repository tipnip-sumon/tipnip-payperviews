<x-layout>
    @section('top_title', 'Maintenance Mode Manager')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Maintenance Mode Manager')
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-tools me-2"></i>
                            Maintenance Mode Manager
                        </h4>
                        <p class="card-subtitle mb-0 mt-1 opacity-75">
                            Customize and control your site's maintenance mode
                        </p>
                    </div>
                    <div class="card-body">
                        <!-- Current Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Current Status</h6>
                                    <div id="maintenanceStatus">
                                        @if($isInMaintenance)
                                            <span class="badge bg-danger">🔴 MAINTENANCE MODE ACTIVE</span>
                                            <p class="mb-0 mt-2">Site is currently in maintenance mode</p>
                                        @else
                                            <span class="badge bg-success">🟢 SITE ONLINE</span>
                                            <p class="mb-0 mt-2">Site is accessible to all users</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-secondary">
                                    <h6><i class="fas fa-clock me-2"></i>Quick Actions</h6>
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="enableMaintenance('quick_fix')">
                                            🔴 Quick Fix (10min)
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" onclick="disableMaintenance()">
                                            🟢 Bring Online
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Maintenance Form -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Custom Maintenance Mode</h5>
                            </div>
                            <div class="card-body">
                                <form id="customMaintenanceForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="maintenanceMessage" class="form-label">Custom Message</label>
                                                <textarea class="form-control" id="maintenanceMessage" rows="3" 
                                                    placeholder="Enter a custom message for users...">We're performing important updates to enhance your experience. We'll be back shortly!</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="retryAfter" class="form-label">Estimated Duration (minutes)</label>
                                                <select class="form-select" id="retryAfter">
                                                    <option value="600">10 minutes</option>
                                                    <option value="1800">30 minutes</option>
                                                    <option value="3600" selected>1 hour</option>
                                                    <option value="7200">2 hours</option>
                                                    <option value="14400">4 hours</option>
                                                    <option value="28800">8 hours</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="refreshInterval" class="form-label">Auto-refresh Interval (seconds)</label>
                                                <select class="form-select" id="refreshInterval">
                                                    <option value="60">1 minute</option>
                                                    <option value="180">3 minutes</option>
                                                    <option value="300" selected>5 minutes</option>
                                                    <option value="600">10 minutes</option>
                                                    <option value="0">No auto-refresh</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="maintenanceTemplate" class="form-label">Template Style</label>
                                                <select class="form-select" id="maintenanceTemplate">
                                                    <option value="default">🎨 Default (Full Featured)</option>
                                                    <option value="minimal">📱 Minimal (Simple)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bypassSecret" class="form-label">Bypass Secret (Optional)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="bypassSecret" 
                                                        placeholder="Generate or enter secret...">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="generateSecret()">
                                                        🎲 Generate
                                                    </button>
                                                </div>
                                                <small class="form-text text-muted">Allows admin access during maintenance</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-danger" onclick="enableCustomMaintenance()">
                                            🔴 Enable Maintenance Mode
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="previewMaintenance()">
                                            👁️ Preview Page
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Predefined Scenarios -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Predefined Scenarios</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 scenario-card" data-scenario="quick_fix">
                                            <div class="card-body text-center">
                                                <div class="scenario-icon">⚡</div>
                                                <h6>Quick Fix</h6>
                                                <p class="small text-muted">10 minutes - Minor bug fixes</p>
                                                <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('quick_fix')">
                                                    Enable
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 scenario-card" data-scenario="scheduled_maintenance">
                                            <div class="card-body text-center">
                                                <div class="scenario-icon">🔧</div>
                                                <h6>Scheduled Maintenance</h6>
                                                <p class="small text-muted">2 hours - Regular updates</p>
                                                <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('scheduled_maintenance')">
                                                    Enable
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 scenario-card" data-scenario="security_patch">
                                            <div class="card-body text-center">
                                                <div class="scenario-icon">🛡️</div>
                                                <h6>Security Updates</h6>
                                                <p class="small text-muted">1 hour - Security patches</p>
                                                <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('security_patch')">
                                                    Enable
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 scenario-card" data-scenario="major_update">
                                            <div class="card-body text-center">
                                                <div class="scenario-icon">🚀</div>
                                                <h6>Major Updates</h6>
                                                <p class="small text-muted">3 hours - New features</p>
                                                <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('major_update')">
                                                    Enable
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 scenario-card" data-scenario="emergency_fix">
                                            <div class="card-body text-center">
                                                <div class="scenario-icon">🚨</div>
                                                <h6>Emergency Fix</h6>
                                                <p class="small text-muted">30 minutes - Critical issues</p>
                                                <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('emergency_fix')">
                                                    Enable
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 scenario-card bg-success text-white">
                                            <div class="card-body text-center">
                                                <div class="scenario-icon">🟢</div>
                                                <h6>Disable Maintenance</h6>
                                                <p class="small">Bring site back online</p>
                                                <button class="btn btn-light btn-sm w-100" onclick="disableMaintenance()">
                                                    🟢 Enable Site
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Settings (if in maintenance) -->
                        @if($isInMaintenance)
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h5 class="card-title mb-0 text-dark">Current Maintenance Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div id="currentSettings">
                                        @if($maintenanceData)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Status:</strong> {{ $maintenanceData['status'] ?? '503' }}</p>
                                                    <p><strong>Retry After:</strong> {{ $maintenanceData['retry'] ?? 'Not set' }} seconds</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Refresh Interval:</strong> {{ $maintenanceData['refresh'] ?? 'Not set' }} seconds</p>
                                                    <p><strong>Secret Set:</strong> {{ isset($maintenanceData['secret']) ? 'Yes' : 'No' }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('script')
        <script>
        // Maintenance mode management functions
        function enableMaintenance(scenario) {
            Swal.fire({
                title: 'Enable Maintenance Mode?',
                text: `This will put your site offline using the ${scenario} scenario.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '🔴 Yes, Enable Maintenance',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeMaintenanceScenario(scenario);
                }
            });
        }

        function disableMaintenance() {
            Swal.fire({
                title: 'Disable Maintenance Mode?',
                text: 'This will bring your site back online.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '🟢 Yes, Bring Online',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeMaintenanceDisable();
                }
            });
        }

        function enableCustomMaintenance() {
            const message = document.getElementById('maintenanceMessage').value;
            const retry = document.getElementById('retryAfter').value;
            const refresh = document.getElementById('refreshInterval').value;
            const template = document.getElementById('maintenanceTemplate').value;
            const secret = document.getElementById('bypassSecret').value;

            Swal.fire({
                title: 'Enable Custom Maintenance Mode?',
                html: `
                    <div class="text-start">
                        <p><strong>Message:</strong> ${message || 'Default message'}</p>
                        <p><strong>Duration:</strong> ${retry / 60} minutes</p>
                        <p><strong>Auto-refresh:</strong> ${refresh === '0' ? 'Disabled' : refresh + ' seconds'}</p>
                        <p><strong>Template:</strong> ${template}</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '🔴 Enable Maintenance',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeCustomMaintenance({
                        message: message,
                        retry: retry,
                        refresh: refresh,
                        template: template,
                        secret: secret
                    });
                }
            });
        }

        function executeMaintenanceScenario(scenario) {
            fetch('{{ route("admin.maintenance.enable-scenario") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    scenario: scenario
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to enable maintenance mode',
                    icon: 'error'
                });
            });
        }

        function executeMaintenanceDisable() {
            fetch('{{ route("admin.maintenance.disable") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to disable maintenance mode',
                    icon: 'error'
                });
            });
        }

        function executeCustomMaintenance(options) {
            fetch('{{ route("admin.maintenance.enable") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(options)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to enable maintenance mode',
                    icon: 'error'
                });
            });
        }

        function generateSecret() {
            fetch('{{ route("admin.maintenance.generate-secret") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('bypassSecret').value = data.secret;
            })
            .catch(error => {
                console.error('Failed to generate secret:', error);
            });
        }

        function previewMaintenance() {
            const template = document.getElementById('maintenanceTemplate').value;
            const message = document.getElementById('maintenanceMessage').value;
            
            const url = `{{ route("admin.maintenance.preview") }}?template=${template}&message=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        }
        </script>

        <style>
        .scenario-card {
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
        }

        .scenario-card:hover {
            transform: translateY(-2px);
        }

        .scenario-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        </style>
    @endpush
</x-layout>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Maintenance Mode Manager
                    </h4>
                    <p class="card-subtitle mb-0 mt-1 opacity-75">
                        Customize and control your site's maintenance mode
                    </p>
                </div>
                <div class="card-body">
                    <!-- Current Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Current Status</h6>
                                <div id="maintenanceStatus">
                                    @if(app()->isDownForMaintenance())
                                        <span class="badge bg-danger">🔴 MAINTENANCE MODE ACTIVE</span>
                                        <p class="mb-0 mt-2">Site is currently in maintenance mode</p>
                                    @else
                                        <span class="badge bg-success">🟢 SITE ONLINE</span>
                                        <p class="mb-0 mt-2">Site is accessible to all users</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-secondary">
                                <h6><i class="fas fa-clock me-2"></i>Quick Actions</h6>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="enableMaintenance('quick_fix')">
                                        🔴 Quick Fix (10min)
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" onclick="disableMaintenance()">
                                        🟢 Bring Online
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Maintenance Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Custom Maintenance Mode</h5>
                        </div>
                        <div class="card-body">
                            <form id="customMaintenanceForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="maintenanceMessage" class="form-label">Custom Message</label>
                                            <textarea class="form-control" id="maintenanceMessage" rows="3" 
                                                placeholder="Enter a custom message for users...">We're performing important updates to enhance your experience. We'll be back shortly!</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="retryAfter" class="form-label">Estimated Duration (minutes)</label>
                                            <select class="form-select" id="retryAfter">
                                                <option value="600">10 minutes</option>
                                                <option value="1800">30 minutes</option>
                                                <option value="3600" selected>1 hour</option>
                                                <option value="7200">2 hours</option>
                                                <option value="14400">4 hours</option>
                                                <option value="28800">8 hours</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="refreshInterval" class="form-label">Auto-refresh Interval (seconds)</label>
                                            <select class="form-select" id="refreshInterval">
                                                <option value="60">1 minute</option>
                                                <option value="180">3 minutes</option>
                                                <option value="300" selected>5 minutes</option>
                                                <option value="600">10 minutes</option>
                                                <option value="0">No auto-refresh</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="maintenanceTemplate" class="form-label">Template Style</label>
                                            <select class="form-select" id="maintenanceTemplate">
                                                <option value="default">🎨 Default (Full Featured)</option>
                                                <option value="minimal">📱 Minimal (Simple)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="bypassSecret" class="form-label">Bypass Secret (Optional)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="bypassSecret" 
                                                    placeholder="Generate or enter secret...">
                                                <button type="button" class="btn btn-outline-secondary" onclick="generateSecret()">
                                                    🎲 Generate
                                                </button>
                                            </div>
                                            <small class="form-text text-muted">Allows admin access during maintenance</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-danger" onclick="enableCustomMaintenance()">
                                        🔴 Enable Maintenance Mode
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="previewMaintenance()">
                                        👁️ Preview Page
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Predefined Scenarios -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Predefined Scenarios</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 scenario-card" data-scenario="quick_fix">
                                        <div class="card-body text-center">
                                            <div class="scenario-icon">⚡</div>
                                            <h6>Quick Fix</h6>
                                            <p class="small text-muted">10 minutes - Minor bug fixes</p>
                                            <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('quick_fix')">
                                                Enable
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 scenario-card" data-scenario="scheduled_maintenance">
                                        <div class="card-body text-center">
                                            <div class="scenario-icon">🔧</div>
                                            <h6>Scheduled Maintenance</h6>
                                            <p class="small text-muted">2 hours - Regular updates</p>
                                            <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('scheduled_maintenance')">
                                                Enable
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 scenario-card" data-scenario="security_patch">
                                        <div class="card-body text-center">
                                            <div class="scenario-icon">🛡️</div>
                                            <h6>Security Updates</h6>
                                            <p class="small text-muted">1 hour - Security patches</p>
                                            <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('security_patch')">
                                                Enable
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 scenario-card" data-scenario="major_update">
                                        <div class="card-body text-center">
                                            <div class="scenario-icon">🚀</div>
                                            <h6>Major Updates</h6>
                                            <p class="small text-muted">3 hours - New features</p>
                                            <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('major_update')">
                                                Enable
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 scenario-card" data-scenario="emergency_fix">
                                        <div class="card-body text-center">
                                            <div class="scenario-icon">🚨</div>
                                            <h6>Emergency Fix</h6>
                                            <p class="small text-muted">30 minutes - Critical issues</p>
                                            <button class="btn btn-outline-danger btn-sm w-100" onclick="enableMaintenance('emergency_fix')">
                                                Enable
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 scenario-card bg-success text-white">
                                        <div class="card-body text-center">
                                            <div class="scenario-icon">🟢</div>
                                            <h6>Disable Maintenance</h6>
                                            <p class="small">Bring site back online</p>
                                            <button class="btn btn-light btn-sm w-100" onclick="disableMaintenance()">
                                                🟢 Enable Site
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Settings (if in maintenance) -->
                    @if(app()->isDownForMaintenance())
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h5 class="card-title mb-0 text-dark">Current Maintenance Settings</h5>
                            </div>
                            <div class="card-body">
                                <div id="currentSettings">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Maintenance mode management functions
function enableMaintenance(scenario) {
    Swal.fire({
        title: 'Enable Maintenance Mode?',
        text: `This will put your site offline using the ${scenario} scenario.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🔴 Yes, Enable Maintenance',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Execute the maintenance command
            executeMaintenanceCommand(`maintenance:enable --scenario=${scenario}`);
        }
    });
}

function disableMaintenance() {
    Swal.fire({
        title: 'Disable Maintenance Mode?',
        text: 'This will bring your site back online.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🟢 Yes, Bring Online',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            executeMaintenanceCommand('up');
        }
    });
}

function enableCustomMaintenance() {
    const message = document.getElementById('maintenanceMessage').value;
    const retry = document.getElementById('retryAfter').value;
    const refresh = document.getElementById('refreshInterval').value;
    const template = document.getElementById('maintenanceTemplate').value;
    const secret = document.getElementById('bypassSecret').value;

    let command = 'down';
    if (message) command += ` --message="${message}"`;
    if (retry) command += ` --retry=${retry}`;
    if (refresh && refresh !== '0') command += ` --refresh=${refresh}`;
    if (template !== 'default') command += ` --render="errors::503-${template}"`;
    if (secret) command += ` --secret="${secret}"`;

    Swal.fire({
        title: 'Enable Custom Maintenance Mode?',
        html: `
            <div class="text-start">
                <p><strong>Message:</strong> ${message || 'Default message'}</p>
                <p><strong>Duration:</strong> ${retry / 60} minutes</p>
                <p><strong>Auto-refresh:</strong> ${refresh === '0' ? 'Disabled' : refresh + ' seconds'}</p>
                <p><strong>Template:</strong> ${template}</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🔴 Enable Maintenance',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            executeMaintenanceCommand(command);
        }
    });
}

function executeMaintenanceCommand(command) {
    // This would call your existing system command execution
    fetch('{{ route("admin.system-commands.execute") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            command: command,
            confirm: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'Failed to execute command',
            icon: 'error'
        });
    });
}

function generateSecret() {
    const secret = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    document.getElementById('bypassSecret').value = secret;
}

function previewMaintenance() {
    window.open('/maintenance-preview', '_blank');
}

// Load current maintenance settings if in maintenance mode
@if(app()->isDownForMaintenance())
    // Load current settings here
@endif
</script>

<style>
.scenario-card {
    transition: transform 0.2s ease-in-out;
    cursor: pointer;
}

.scenario-card:hover {
    transform: translateY(-2px);
}

.scenario-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
