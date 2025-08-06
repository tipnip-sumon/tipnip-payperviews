@props(['current' => ''])

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Settings Categories
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Primary Settings -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.general') }}" 
                           class="btn {{ $current === 'general' ? 'btn-primary' : 'btn-outline-primary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-cog fa-2x mb-2"></i>
                            <span>General</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.media') }}" 
                           class="btn {{ $current === 'media' ? 'btn-primary' : 'btn-outline-primary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-images fa-2x mb-2"></i>
                            <span>Media & Logos</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.seo') }}" 
                           class="btn {{ $current === 'seo' ? 'btn-primary' : 'btn-outline-primary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-search fa-2x mb-2"></i>
                            <span>SEO Settings</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.content') }}" 
                           class="btn {{ $current === 'content' ? 'btn-primary' : 'btn-outline-primary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-edit fa-2x mb-2"></i>
                            <span>Content</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.theme') }}" 
                           class="btn {{ $current === 'theme' ? 'btn-primary' : 'btn-outline-primary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-palette fa-2x mb-2"></i>
                            <span>Theme</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.social-media') }}" 
                           class="btn {{ $current === 'social-media' ? 'btn-primary' : 'btn-outline-primary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-share-alt fa-2x mb-2"></i>
                            <span>Social Media</span>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <!-- Secondary Settings -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.mail-config') }}" 
                           class="btn {{ $current === 'mail-config' ? 'btn-secondary' : 'btn-outline-secondary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-envelope fa-2x mb-2"></i>
                            <span>Mail Config</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.sms-config') }}" 
                           class="btn {{ $current === 'sms-config' ? 'btn-secondary' : 'btn-outline-secondary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-sms fa-2x mb-2"></i>
                            <span>SMS Config</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.settings.security') }}" 
                           class="btn {{ $current === 'security' ? 'btn-secondary' : 'btn-outline-secondary' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-shield-alt fa-2x mb-2"></i>
                            <span>Security</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="#" 
                           class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center" 
                           title="Coming Soon">
                            <i class="fas fa-bell fa-2x mb-2"></i>
                            <span>Notifications</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="#" 
                           class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center" 
                           title="Coming Soon">
                            <i class="fas fa-globe fa-2x mb-2"></i>
                            <span>Localization</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <a href="#" 
                           class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center" 
                           title="Coming Soon">
                            <i class="fas fa-database fa-2x mb-2"></i>
                            <span>Backup</span>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mt-3 pt-3 border-top">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Quick Actions</h6>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-refresh me-1"></i>
                                Clear Cache
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <form action="{{ route('admin.settings.toggle-maintenance') }}" method="POST" class="d-inline">
                            @csrf
                            @php
                                $maintenanceMode = \App\Models\GeneralSetting::getSetting('maintenance_mode', false);
                            @endphp
                            <button type="submit" class="btn {{ $maintenanceMode ? 'btn-success' : 'btn-danger' }} btn-sm w-100">
                                <i class="fas fa-tools me-1"></i>
                                {{ $maintenanceMode ? 'Disable' : 'Enable' }} Maintenance
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <a href="{{ route('admin.settings.export') }}" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-download me-1"></i>
                            Export Settings
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-upload me-1"></i>
                            Import Settings
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <button type="button" class="btn btn-secondary btn-sm w-100" onclick="showSystemInfo()">
                            <i class="fas fa-info-circle me-1"></i>
                            System Info
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                            <i class="fas fa-envelope me-1"></i>
                            Test Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Settings Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.settings.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="settings_file" class="form-label">Settings File (JSON)</label>
                        <input type="file" class="form-control" name="settings_file" id="settings_file" accept=".json" required>
                        <small class="form-text text-muted">Upload a JSON file exported from this system.</small>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Importing settings will overwrite current configuration. Make sure to export current settings first as a backup.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Import Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Email Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.settings.test-email') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="test_email" class="form-label">Test Email Address</label>
                        <input type="email" class="form-control" name="test_email" id="test_email" required>
                        <small class="form-text text-muted">Enter an email address to send a test email.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Test Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- System Info Modal -->
<div class="modal fade" id="systemInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">System Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="systemInfoContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showSystemInfo() {
    const modal = new bootstrap.Modal(document.getElementById('systemInfoModal'));
    modal.show();
    
    fetch('{{ route("admin.settings.system-info") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="table-responsive"><table class="table table-bordered">';
                for (const [key, value] of Object.entries(data.data)) {
                    html += `<tr><td><strong>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</strong></td><td>${value}</td></tr>`;
                }
                html += '</table></div>';
                document.getElementById('systemInfoContent').innerHTML = html;
            } else {
                document.getElementById('systemInfoContent').innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
            }
        })
        .catch(error => {
            document.getElementById('systemInfoContent').innerHTML = '<div class="alert alert-danger">Failed to load system information</div>';
        });
}
</script>
