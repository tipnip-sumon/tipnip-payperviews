<x-layout>
    @section('top_title', $pageTitle)
    @section('title',$pageTitle)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center my-4">
        <div class="col-lg-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-envelope-open-text me-2"></i>
                            Mail Configuration
                        </h4>
                        <div>
                            <a href="{{ route('admin.settings.general') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Settings
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('admin.settings.mail-config.update') }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-cogs me-2"></i>SMTP Configuration
                                    </h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_driver" class="form-label">
                                                <i class="fas fa-server me-1"></i>Mail Driver
                                            </label>
                                            <select class="form-select @error('mail_driver') is-invalid @enderror" 
                                                    id="mail_driver" name="mail_driver" required>
                                                <option value="smtp" {{ (old('mail_driver', $mailConfig['driver'] ?? 'smtp') == 'smtp') ? 'selected' : '' }}>SMTP</option>
                                                <option value="mail" {{ (old('mail_driver', $mailConfig['driver'] ?? 'smtp') == 'mail') ? 'selected' : '' }}>Mail</option>
                                                <option value="sendmail" {{ (old('mail_driver', $mailConfig['driver'] ?? 'smtp') == 'sendmail') ? 'selected' : '' }}>Sendmail</option>
                                                <option value="mailgun" {{ (old('mail_driver', $mailConfig['driver'] ?? 'smtp') == 'mailgun') ? 'selected' : '' }}>Mailgun</option>
                                                <option value="ses" {{ (old('mail_driver', $mailConfig['driver'] ?? 'smtp') == 'ses') ? 'selected' : '' }}>Amazon SES</option>
                                            </select>
                                            @error('mail_driver')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_host" class="form-label">
                                                <i class="fas fa-globe me-1"></i>SMTP Host
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('mail_host') is-invalid @enderror" 
                                                   id="mail_host" 
                                                   name="mail_host" 
                                                   value="{{ old('mail_host', $mailConfig['host'] ?? '') }}" 
                                                   placeholder="smtp.gmail.com" 
                                                   required>
                                            @error('mail_host')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_port" class="form-label">
                                                <i class="fas fa-plug me-1"></i>SMTP Port
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('mail_port') is-invalid @enderror" 
                                                   id="mail_port" 
                                                   name="mail_port" 
                                                   value="{{ old('mail_port', $mailConfig['port'] ?? 587) }}" 
                                                   placeholder="587" 
                                                   required>
                                            @error('mail_port')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Common ports: 25, 465 (SSL), 587 (TLS)</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_encryption" class="form-label">
                                                <i class="fas fa-lock me-1"></i>Encryption
                                            </label>
                                            <select class="form-select @error('mail_encryption') is-invalid @enderror" 
                                                    id="mail_encryption" name="mail_encryption">
                                                <option value="">None</option>
                                                <option value="tls" {{ (old('mail_encryption', $mailConfig['encryption'] ?? 'tls') == 'tls') ? 'selected' : '' }}>TLS</option>
                                                <option value="ssl" {{ (old('mail_encryption', $mailConfig['encryption'] ?? 'tls') == 'ssl') ? 'selected' : '' }}>SSL</option>
                                            </select>
                                            @error('mail_encryption')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_username" class="form-label">
                                                <i class="fas fa-user me-1"></i>SMTP Username
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('mail_username') is-invalid @enderror" 
                                                   id="mail_username" 
                                                   name="mail_username" 
                                                   value="{{ old('mail_username', $mailConfig['username'] ?? '') }}" 
                                                   placeholder="your-email@gmail.com" 
                                                   required>
                                            @error('mail_username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_password" class="form-label">
                                                <i class="fas fa-key me-1"></i>SMTP Password
                                            </label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('mail_password') is-invalid @enderror" 
                                                       id="mail_password" 
                                                       name="mail_password" 
                                                       value="{{ old('mail_password', $mailConfig['password'] ?? '') }}" 
                                                       placeholder="Enter password" 
                                                       required>
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('mail_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-paper-plane me-2"></i>Email Settings
                                    </h5>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="from_address" class="form-label">
                                                <i class="fas fa-envelope me-1"></i>From Address
                                            </label>
                                            <input type="email" 
                                                   class="form-control @error('from_address') is-invalid @enderror" 
                                                   id="from_address" 
                                                   name="from_address" 
                                                   value="{{ old('from_address', $mailConfig['from_address'] ?? $settings->email_from) }}" 
                                                   placeholder="noreply@viewcash.com" 
                                                   required>
                                            @error('from_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="from_name" class="form-label">
                                                <i class="fas fa-signature me-1"></i>From Name
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('from_name') is-invalid @enderror" 
                                                   id="from_name" 
                                                   name="from_name" 
                                                   value="{{ old('from_name', $mailConfig['from_name'] ?? $settings->site_name) }}" 
                                                   placeholder="ViewCash" 
                                                   required>
                                            @error('from_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Configuration
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo me-2"></i>Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4">
                            <!-- Test Email Section --> 
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-paper-plane me-2"></i>Test Email
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.settings.test-email') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="test_email" class="form-label">Test Email Address</label>
                                            <input type="email" 
                                                   class="form-control @error('test_email') is-invalid @enderror" 
                                                   id="test_email" 
                                                   name="test_email" 
                                                   placeholder="test@example.com" 
                                                   required>
                                            @error('test_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-info btn-sm w-100">
                                            <i class="fas fa-paper-plane me-2"></i>Send Test Email
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Popular SMTP Providers -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Popular SMTP Providers
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <button class="btn btn-outline-primary btn-sm w-100" onclick="setGmailConfig()">
                                                <i class="fab fa-google me-1"></i>Gmail
                                            </button>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <button class="btn btn-outline-info btn-sm w-100" onclick="setOutlookConfig()">
                                                <i class="fab fa-microsoft me-1"></i>Outlook
                                            </button>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <button class="btn btn-outline-warning btn-sm w-100" onclick="setYahooConfig()">
                                                <i class="fab fa-yahoo me-1"></i>Yahoo
                                            </button>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <button class="btn btn-outline-success btn-sm w-100" onclick="setMailgunConfig()">
                                                Mailgun
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuration Status -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Configuration Status
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>SMTP Host:</span>
                                        <span class="badge {{ !empty($mailConfig['host']) ? 'bg-success' : 'bg-danger' }}">
                                            {{ !empty($mailConfig['host']) ? 'Configured' : 'Not Set' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Username:</span>
                                        <span class="badge {{ !empty($mailConfig['username']) ? 'bg-success' : 'bg-danger' }}">
                                            {{ !empty($mailConfig['username']) ? 'Configured' : 'Not Set' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Password:</span>
                                        <span class="badge {{ !empty($mailConfig['password']) ? 'bg-success' : 'bg-danger' }}">
                                            {{ !empty($mailConfig['password']) ? 'Configured' : 'Not Set' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Encryption:</span>
                                        <span class="badge {{ !empty($mailConfig['encryption']) ? 'bg-success' : 'bg-warning' }}">
                                            {{ !empty($mailConfig['encryption']) ? strtoupper($mailConfig['encryption']) : 'None' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('mail_password');
        const eyeIcon = this.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.querySelector('form').reset();
    }
}

function setGmailConfig() {
    document.getElementById('mail_driver').value = 'smtp';
    document.getElementById('mail_host').value = 'smtp.gmail.com';
    document.getElementById('mail_port').value = '587';
    document.getElementById('mail_encryption').value = 'tls';
}

function setOutlookConfig() {
    document.getElementById('mail_driver').value = 'smtp';
    document.getElementById('mail_host').value = 'smtp.live.com';
    document.getElementById('mail_port').value = '587';
    document.getElementById('mail_encryption').value = 'tls';
}

function setYahooConfig() {
    document.getElementById('mail_driver').value = 'smtp';
    document.getElementById('mail_host').value = 'smtp.mail.yahoo.com';
    document.getElementById('mail_port').value = '587';
    document.getElementById('mail_encryption').value = 'tls';
}

function setMailgunConfig() {
    document.getElementById('mail_driver').value = 'mailgun';
    document.getElementById('mail_host').value = 'smtp.mailgun.org';
    document.getElementById('mail_port').value = '587';
    document.getElementById('mail_encryption').value = 'tls';
}
</script>
@endsection
</x-layout>
