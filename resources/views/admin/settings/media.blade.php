<x-layout>

@section('content')
<!-- Settings Navigation -->
<x-admin.settings-navigation current="media" />

<div class="row mb-4 my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-images me-2"></i>
                    {{ $pageTitle }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.media.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Site Logo -->
                        <div class="col-lg-6 mb-4">
                            <div class="form-group">
                                <label for="logo" class="form-label">Site Logo</label>
                                <div class="mb-3">
                                    @if($settings->logo)
                                        <img src="{{ siteLogo() }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                        <p class="text-muted small mt-2">Current Logo</p>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No logo uploaded yet
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                       name="logo" id="logo" accept="image/*">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Recommended: 200x50px, Max size: 2MB, Formats: JPG, PNG, GIF
                                </small>
                            </div> 
                        </div>

                        <!-- Admin Logo -->
                        <div class="col-lg-6 mb-4">
                            <div class="form-group">
                                <label for="admin_logo" class="form-label">Admin Panel Logo</label>
                                <div class="mb-3">
                                    @if($settings->admin_logo)
                                        <img src="{{ adminLogo() }}" alt="Current Admin Logo" class="img-thumbnail" style="max-height: 100px;">
                                        <p class="text-muted small mt-2">Current Admin Logo</p>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No admin logo uploaded (using site logo)
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('admin_logo') is-invalid @enderror" 
                                       name="admin_logo" id="admin_logo" accept="image/*">
                                @error('admin_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Recommended: 200x50px, Max size: 2MB, Formats: JPG, PNG, GIF
                                </small>
                            </div>
                        </div>

                        <!-- Favicon -->
                        <div class="col-lg-6 mb-4">
                            <div class="form-group">
                                <label for="favicon" class="form-label">Favicon</label>
                                <div class="mb-3">
                                    @if($settings->favicon)
                                        <img src="{{ siteFavicon() }}" alt="Current Favicon" class="img-thumbnail" style="max-height: 50px;">
                                        <p class="text-muted small mt-2">Current Favicon</p>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No favicon uploaded yet
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror" 
                                       name="favicon" id="favicon" accept="image/*,.ico">
                                @error('favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Recommended: 32x32px or 16x16px, Max size: 1MB, Formats: ICO, PNG, JPG
                                </small>
                            </div>
                        </div>

                        <!-- Meta Image -->
                        <div class="col-lg-6 mb-4">
                            <div class="form-group">
                                <label for="meta_image" class="form-label">Social Media Image (Open Graph)</label>
                                <div class="mb-3">
                                    @if($settings->meta_image)
                                        <img src="{{ getSetting('meta_image') ? asset('storage/images/meta/' . getSetting('meta_image')) : '' }}" 
                                             alt="Current Meta Image" class="img-thumbnail" style="max-height: 100px;">
                                        <p class="text-muted small mt-2">Current Meta Image</p>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No meta image uploaded (using site logo)
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('meta_image') is-invalid @enderror" 
                                       name="meta_image" id="meta_image" accept="image/*">
                                @error('meta_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Recommended: 1200x630px, Max size: 2MB, Formats: JPG, PNG, GIF
                                </small>
                            </div>
                        </div>

                        <!-- Maintenance Image -->
                        <div class="col-lg-6 mb-4">
                            <div class="form-group">
                                <label for="maintenance_image" class="form-label">Maintenance Page Image</label>
                                <div class="mb-3">
                                    @if($settings->maintenance_image)
                                        <img src="{{ asset('storage/images/maintenance/' . $settings->maintenance_image) }}" 
                                             alt="Current Maintenance Image" class="img-thumbnail" style="max-height: 100px;">
                                        <p class="text-muted small mt-2">Current Maintenance Image</p>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No maintenance image uploaded
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('maintenance_image') is-invalid @enderror" 
                                       name="maintenance_image" id="maintenance_image" accept="image/*">
                                @error('maintenance_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Recommended: 600x400px, Max size: 2MB, Formats: JPG, PNG, GIF
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Media Settings
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
    // Preview image before upload
    function setupImagePreview(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = input.parentElement.querySelector('.img-thumbnail');
                        if (preview) {
                            preview.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    setupImagePreview('logo');
    setupImagePreview('admin_logo');
    setupImagePreview('favicon');
    setupImagePreview('meta_image');
    setupImagePreview('maintenance_image');
});
</script>
@endsection
</x-layout>
