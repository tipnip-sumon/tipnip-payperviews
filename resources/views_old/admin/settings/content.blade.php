<x-layout>
    @section('pageTitle', 'Content Management Settings')

@section('content')
<!-- Settings Navigation -->
<x-admin.settings-navigation current="content" />

<div class="row mb-4 my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    {{ $pageTitle }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.content.update') }}" method="POST">
                    @csrf

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="header-footer-tab" data-bs-toggle="tab" 
                                    data-bs-target="#header-footer" type="button" role="tab">
                                <i class="fas fa-window-maximize me-2"></i>Header & Footer
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pages-tab" data-bs-toggle="tab" 
                                    data-bs-target="#pages" type="button" role="tab">
                                <i class="fas fa-file-alt me-2"></i>Pages Content
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="maintenance-tab" data-bs-toggle="tab" 
                                    data-bs-target="#maintenance" type="button" role="tab">
                                <i class="fas fa-tools me-2"></i>Maintenance
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="contentTabsContent">
                        <!-- Header & Footer Tab -->
                        <div class="tab-pane fade show active" id="header-footer" role="tabpanel">
                            <div class="row">
                                <!-- Header Content -->
                                <div class="col-lg-6 mb-4">
                                    <div class="form-group">
                                        <label for="header_content" class="form-label">
                                            <i class="fas fa-heading me-2"></i>Header Content
                                        </label>
                                        <textarea class="form-control @error('header_content') is-invalid @enderror" 
                                                  name="header_content" id="header_content" rows="6" 
                                                  placeholder="Enter header content...">{{ old('header_content', $settings->header_content) }}</textarea>
                                        @error('header_content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            This content will appear in the header section of your website. HTML is allowed.
                                        </small>
                                    </div>
                                </div>

                                <!-- Footer Content -->
                                <div class="col-lg-6 mb-4">
                                    <div class="form-group">
                                        <label for="footer_content" class="form-label">
                                            <i class="fas fa-window-minimize me-2"></i>Footer Content
                                        </label>
                                        <textarea class="form-control @error('footer_content') is-invalid @enderror" 
                                                  name="footer_content" id="footer_content" rows="6" 
                                                  placeholder="Enter footer content...">{{ old('footer_content', $settings->footer_content) }}</textarea>
                                        @error('footer_content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            This content will appear in the footer section of your website. HTML is allowed.
                                        </small>
                                    </div>
                                </div>

                                <!-- Copyright Text -->
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label for="copyright_text" class="form-label">
                                            <i class="fas fa-copyright me-2"></i>Copyright Text
                                        </label>
                                        <input type="text" class="form-control @error('copyright_text') is-invalid @enderror" 
                                               name="copyright_text" id="copyright_text" 
                                               value="{{ old('copyright_text', $settings->copyright_text) }}" 
                                               placeholder="© {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.">
                                        @error('copyright_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            This text will appear in the footer. Use {year} for current year and {site_name} for site name.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pages Content Tab -->
                        <div class="tab-pane fade" id="pages" role="tabpanel">
                            <div class="row">
                                <!-- Home Page Content -->
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label for="home_page_content" class="form-label">
                                            <i class="fas fa-home me-2"></i>Home Page Content
                                        </label>
                                        <textarea class="form-control content-editor @error('home_page_content') is-invalid @enderror" 
                                                  name="home_page_content" id="home_page_content" rows="8" 
                                                  placeholder="Enter home page content...">{{ old('home_page_content', $settings->home_page_content) }}</textarea>
                                        @error('home_page_content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- About Us Content -->
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label for="about_us_content" class="form-label">
                                            <i class="fas fa-info-circle me-2"></i>About Us Content
                                        </label>
                                        <textarea class="form-control content-editor @error('about_us_content') is-invalid @enderror" 
                                                  name="about_us_content" id="about_us_content" rows="8" 
                                                  placeholder="Enter about us content...">{{ old('about_us_content', $settings->about_us_content) }}</textarea>
                                        @error('about_us_content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Terms & Conditions -->
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label for="terms_conditions" class="form-label">
                                            <i class="fas fa-file-contract me-2"></i>Terms & Conditions
                                        </label>
                                        <textarea class="form-control content-editor @error('terms_conditions') is-invalid @enderror" 
                                                  name="terms_conditions" id="terms_conditions" rows="8" 
                                                  placeholder="Enter terms and conditions...">{{ old('terms_conditions', $settings->terms_conditions) }}</textarea>
                                        @error('terms_conditions')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Privacy Policy -->
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label for="privacy_policy" class="form-label">
                                            <i class="fas fa-shield-alt me-2"></i>Privacy Policy
                                        </label>
                                        <textarea class="form-control content-editor @error('privacy_policy') is-invalid @enderror" 
                                                  name="privacy_policy" id="privacy_policy" rows="8" 
                                                  placeholder="Enter privacy policy...">{{ old('privacy_policy', $settings->privacy_policy) }}</textarea>
                                        @error('privacy_policy')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Tab -->
                        <div class="tab-pane fade" id="maintenance" role="tabpanel">
                            <div class="row">
                                <!-- Maintenance Message -->
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label for="maintenance_message" class="form-label">
                                            <i class="fas fa-tools me-2"></i>Maintenance Message
                                        </label>
                                        <textarea class="form-control @error('maintenance_message') is-invalid @enderror" 
                                                  name="maintenance_message" id="maintenance_message" rows="4" 
                                                  placeholder="We are currently performing maintenance. Please check back later.">{{ old('maintenance_message', $settings->maintenance_message) }}</textarea>
                                        @error('maintenance_message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            This message will be displayed when maintenance mode is enabled.
                                        </small>
                                    </div>
                                </div>

                                <!-- Maintenance Status -->
                                <div class="col-lg-12 mb-4">
                                    <div class="alert {{ $settings->maintenance_mode ? 'alert-warning' : 'alert-success' }}">
                                        <i class="fas fa-{{ $settings->maintenance_mode ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
                                        <strong>Maintenance Mode Status:</strong> 
                                        {{ $settings->maintenance_mode ? 'ENABLED' : 'DISABLED' }}
                                        @if($settings->maintenance_mode)
                                            <br><small>Your website is currently in maintenance mode. Visitors will see the maintenance message.</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Content Settings
                        </button>
                        <a href="{{ route('admin.settings.general') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to General Settings
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize content editors (you can integrate with a rich text editor like TinyMCE or CKEditor)
    const contentEditors = document.querySelectorAll('.content-editor');
    
    contentEditors.forEach(editor => {
        // Add auto-resize functionality
        editor.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Initial resize
        editor.style.height = 'auto';
        editor.style.height = (editor.scrollHeight) + 'px';
    });

    // Replace placeholders in copyright text
    const copyrightInput = document.getElementById('copyright_text');
    if (copyrightInput) {
        copyrightInput.addEventListener('blur', function() {
            let value = this.value;
            value = value.replace('{year}', new Date().getFullYear());
            value = value.replace('{site_name}', '{{ $settings->site_name }}');
            this.value = value;
        });
    }
});
</script>
@endsection
</x-layout>
<!-- End of file -->
