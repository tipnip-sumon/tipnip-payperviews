<x-layout>
@section('title', $pageTitle ?? 'Theme Settings')

@section('content')
<!-- Settings Navigation -->
<x-admin.settings-navigation current="theme" />

<div class="row mb-4 my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-palette me-2"></i>
                    {{ $pageTitle }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.theme.update') }}" method="POST">
                    @csrf

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="themeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="colors-tab" data-bs-toggle="tab" 
                                    data-bs-target="#colors" type="button" role="tab">
                                <i class="fas fa-palette me-2"></i>Colors
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="custom-css-tab" data-bs-toggle="tab" 
                                    data-bs-target="#custom-css" type="button" role="tab">
                                <i class="fab fa-css3-alt me-2"></i>Custom CSS
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="custom-js-tab" data-bs-toggle="tab" 
                                    data-bs-target="#custom-js" type="button" role="tab">
                                <i class="fab fa-js-square me-2"></i>Custom JS
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="themeTabsContent">
                        <!-- Colors Tab -->
                        <div class="tab-pane fade show active" id="colors" role="tabpanel">
                            <div class="row">
                                <!-- Primary Colors -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-paint-brush me-2"></i>Primary Colors
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Base Color -->
                                            <div class="form-group mb-3">
                                                <label for="base_color" class="form-label">Primary Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color @error('base_color') is-invalid @enderror" 
                                                           name="base_color" id="base_color" 
                                                           value="{{ old('base_color', $settings->base_color ?? '#007bff') }}">
                                                    <input type="text" class="form-control" 
                                                           value="{{ old('base_color', $settings->base_color ?? '#007bff') }}" 
                                                           readonly>
                                                </div>
                                                @error('base_color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Secondary Color -->
                                            <div class="form-group mb-3">
                                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color @error('secondary_color') is-invalid @enderror" 
                                                           name="secondary_color" id="secondary_color" 
                                                           value="{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}">
                                                    <input type="text" class="form-control" 
                                                           value="{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}" 
                                                           readonly>
                                                </div>
                                                @error('secondary_color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Header Colors -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-window-maximize me-2"></i>Header Colors
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Header Background -->
                                            <div class="form-group mb-3">
                                                <label for="header_background_color" class="form-label">Header Background</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color @error('header_background_color') is-invalid @enderror" 
                                                           name="header_background_color" id="header_background_color" 
                                                           value="{{ old('header_background_color', $settings->header_background_color ?? '#ffffff') }}">
                                                    <input type="text" class="form-control" 
                                                           value="{{ old('header_background_color', $settings->header_background_color ?? '#ffffff') }}" 
                                                           readonly>
                                                </div>
                                                @error('header_background_color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Header Text -->
                                            <div class="form-group mb-3">
                                                <label for="header_text_color" class="form-label">Header Text Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color @error('header_text_color') is-invalid @enderror" 
                                                           name="header_text_color" id="header_text_color" 
                                                           value="{{ old('header_text_color', $settings->header_text_color ?? '#000000') }}">
                                                    <input type="text" class="form-control" 
                                                           value="{{ old('header_text_color', $settings->header_text_color ?? '#000000') }}" 
                                                           readonly>
                                                </div>
                                                @error('header_text_color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer Colors -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-window-minimize me-2"></i>Footer Colors
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Footer Background -->
                                            <div class="form-group mb-3">
                                                <label for="footer_background_color" class="form-label">Footer Background</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color @error('footer_background_color') is-invalid @enderror" 
                                                           name="footer_background_color" id="footer_background_color" 
                                                           value="{{ old('footer_background_color', $settings->footer_background_color ?? '#343a40') }}">
                                                    <input type="text" class="form-control" 
                                                           value="{{ old('footer_background_color', $settings->footer_background_color ?? '#343a40') }}" 
                                                           readonly>
                                                </div>
                                                @error('footer_background_color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Footer Text -->
                                            <div class="form-group mb-3">
                                                <label for="footer_text_color" class="form-label">Footer Text Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color @error('footer_text_color') is-invalid @enderror" 
                                                           name="footer_text_color" id="footer_text_color" 
                                                           value="{{ old('footer_text_color', $settings->footer_text_color ?? '#ffffff') }}">
                                                    <input type="text" class="form-control" 
                                                           value="{{ old('footer_text_color', $settings->footer_text_color ?? '#ffffff') }}" 
                                                           readonly>
                                                </div>
                                                @error('footer_text_color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Color Preview -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-eye me-2"></i>Color Preview
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="color-preview" class="border rounded p-3">
                                                <div class="preview-header p-2 mb-2 rounded">
                                                    <span class="preview-header-text">Header Text</span>
                                                </div>
                                                <div class="preview-content p-2 mb-2">
                                                    <button class="btn btn-sm me-2 preview-primary">Primary Button</button>
                                                    <button class="btn btn-sm preview-secondary">Secondary Button</button>
                                                </div>
                                                <div class="preview-footer p-2 rounded">
                                                    <span class="preview-footer-text">Footer Text</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom CSS Tab -->
                        <div class="tab-pane fade" id="custom-css" role="tabpanel">
                            <div class="form-group">
                                <label for="custom_css" class="form-label">
                                    <i class="fab fa-css3-alt me-2"></i>Custom CSS
                                </label>
                                <textarea class="form-control code-editor @error('custom_css') is-invalid @enderror" 
                                          name="custom_css" id="custom_css" rows="20" 
                                          placeholder="/* Enter your custom CSS here */
/* Example:
.custom-header {
    background: linear-gradient(45deg, #007bff, #6c757d);
}

.custom-button {
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
*/">{{ old('custom_css', $settings->custom_css) }}</textarea>
                                @error('custom_css')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Add your custom CSS here. This will be included in all pages of your website.
                                    <br><strong>Warning:</strong> Invalid CSS may break your website's layout.
                                </small>
                            </div>
                        </div>

                        <!-- Custom JS Tab -->
                        <div class="tab-pane fade" id="custom-js" role="tabpanel">
                            <div class="form-group">
                                <label for="custom_js" class="form-label">
                                    <i class="fab fa-js-square me-2"></i>Custom JavaScript
                                </label>
                                <textarea class="form-control code-editor @error('custom_js') is-invalid @enderror" 
                                          name="custom_js" id="custom_js" rows="20" 
                                          placeholder="// Enter your custom JavaScript here
// Example:
document.addEventListener('DOMContentLoaded', function() {
    // Your custom code here
    console.log('Custom JS loaded successfully');
    
    // Example: Add smooth scrolling
    document.querySelectorAll('a[href^=&quot;#&quot;]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});">{{ old('custom_js', $settings->custom_js) }}</textarea>
                                @error('custom_js')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Add your custom JavaScript here. This will be included in all pages of your website.
                                    <br><strong>Warning:</strong> Invalid JavaScript may break your website's functionality.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Theme Settings
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

<style>
.code-editor {
    font-family: 'Courier New', Courier, monospace;
    font-size: 14px;
    line-height: 1.5;
}

.preview-header {
    background-color: var(--header-bg, #ffffff);
    color: var(--header-text, #000000);
}

.preview-footer {
    background-color: var(--footer-bg, #343a40);
    color: var(--footer-text, #ffffff);
}

.preview-primary {
    background-color: var(--primary-color, #007bff);
    border-color: var(--primary-color, #007bff);
    color: white;
}

.preview-secondary {
    background-color: var(--secondary-color, #6c757d);
    border-color: var(--secondary-color, #6c757d);
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color picker sync
    function syncColorInputs() {
        const colorInputs = document.querySelectorAll('input[type="color"]');
        
        colorInputs.forEach(colorInput => {
            const textInput = colorInput.nextElementSibling;
            
            colorInput.addEventListener('input', function() {
                textInput.value = this.value;
                updatePreview();
            });
            
            textInput.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                    colorInput.value = this.value;
                    updatePreview();
                }
            });
        });
    }

    // Update color preview
    function updatePreview() {
        const preview = document.getElementById('color-preview');
        const primaryColor = document.getElementById('base_color').value;
        const secondaryColor = document.getElementById('secondary_color').value;
        const headerBg = document.getElementById('header_background_color').value;
        const headerText = document.getElementById('header_text_color').value;
        const footerBg = document.getElementById('footer_background_color').value;
        const footerText = document.getElementById('footer_text_color').value;

        preview.style.setProperty('--primary-color', primaryColor);
        preview.style.setProperty('--secondary-color', secondaryColor);
        preview.style.setProperty('--header-bg', headerBg);
        preview.style.setProperty('--header-text', headerText);
        preview.style.setProperty('--footer-bg', footerBg);
        preview.style.setProperty('--footer-text', footerText);
    }

    // Initialize
    syncColorInputs();
    updatePreview();

    // Code editor enhancements
    const codeEditors = document.querySelectorAll('.code-editor');
    codeEditors.forEach(editor => {
        // Add line numbers (basic implementation)
        editor.addEventListener('scroll', function() {
            // Sync line numbers if implemented
        });

        // Tab support
        editor.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 4;
            }
        });
    });
});
</script>
@endsection
</x-layout>
<!-- End of file -->
