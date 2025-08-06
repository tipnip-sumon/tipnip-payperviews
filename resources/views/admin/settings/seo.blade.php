<x-layout>
@section('title', $pageTitle ?? 'SEO Settings')

@section('content')
<!-- Settings Navigation -->
<x-admin.settings-navigation current="seo" />

<div class="row mb-4 my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>
                    {{ $pageTitle }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.seo.update') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Meta Title -->
                        <div class="col-lg-12 mb-4">
                            <div class="form-group">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                       name="meta_title" id="meta_title" 
                                       value="{{ old('meta_title', $settings->meta_title ?? $settings->site_name) }}" 
                                       maxlength="60">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Recommended length: 50-60 characters. This appears in search engine results and browser tabs.
                                </small>
                                <div class="mt-1">
                                    <span id="title-counter" class="badge badge-info">0/60</span>
                                </div>
                            </div>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-lg-12 mb-4">
                            <div class="form-group">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                          name="meta_description" id="meta_description" rows="4" 
                                          maxlength="160">{{ old('meta_description', $settings->meta_description) }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Recommended length: 150-160 characters. This appears in search engine results below the title.
                                </small>
                                <div class="mt-1">
                                    <span id="description-counter" class="badge badge-info">0/160</span>
                                </div>
                            </div>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="col-lg-12 mb-4">
                            <div class="form-group">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                       name="meta_keywords" id="meta_keywords" 
                                       value="{{ old('meta_keywords', $settings->meta_keywords) }}" 
                                       placeholder="earn money, watch videos, online earning">
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Separate keywords with commas. Example: earn money, watch videos, online earning
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Preview -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-eye me-2"></i>
                                Google Search Preview
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="seo-preview">
                                <div class="seo-title" id="seo-preview-title">
                                    {{ $settings->meta_title ?? $settings->site_name }}
                                </div>
                                <div class="seo-url text-success">
                                    {{ url('/') }}
                                </div>
                                <div class="seo-description text-muted" id="seo-preview-description">
                                    {{ $settings->meta_description ?? 'ViewCash - Earn money by watching videos' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current SEO Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                SEO Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Meta Title: 
                                            @if($settings->meta_title)
                                                <span class="badge badge-success">Set</span>
                                            @else
                                                <span class="badge badge-warning">Using Site Name</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        @if($settings->meta_description)
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span>Meta Description: <span class="badge badge-success">Set</span></span>
                                        @else
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            <span>Meta Description: <span class="badge badge-warning">Not Set</span></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        @if($settings->meta_keywords)
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span>Meta Keywords: <span class="badge badge-success">Set</span></span>
                                        @else
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            <span>Meta Keywords: <span class="badge badge-warning">Not Set</span></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update SEO Settings
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
.seo-preview {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background-color: #f8f9fa;
}

.seo-title {
    color: #1a0dab;
    font-size: 18px;
    line-height: 1.3;
    margin-bottom: 4px;
    cursor: pointer;
}

.seo-title:hover {
    text-decoration: underline;
}

.seo-url {
    font-size: 14px;
    margin-bottom: 4px;
}

.seo-description {
    font-size: 13px;
    line-height: 1.4;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('meta_title');
    const descriptionInput = document.getElementById('meta_description');
    const titleCounter = document.getElementById('title-counter');
    const descriptionCounter = document.getElementById('description-counter');
    const previewTitle = document.getElementById('seo-preview-title');
    const previewDescription = document.getElementById('seo-preview-description');

    function updateCounter(input, counter, max) {
        const length = input.value.length;
        counter.textContent = `${length}/${max}`;
        
        if (length > max * 0.9) {
            counter.className = 'badge badge-warning';
        } else if (length > max) {
            counter.className = 'badge badge-danger';
        } else {
            counter.className = 'badge badge-info';
        }
    }

    function updatePreview() {
        previewTitle.textContent = titleInput.value || '{{ $settings->site_name }}';
        previewDescription.textContent = descriptionInput.value || 'ViewCash - Earn money by watching videos';
    }

    titleInput.addEventListener('input', function() {
        updateCounter(this, titleCounter, 60);
        updatePreview();
    });

    descriptionInput.addEventListener('input', function() {
        updateCounter(this, descriptionCounter, 160);
        updatePreview();
    });

    // Initialize counters
    updateCounter(titleInput, titleCounter, 60);
    updateCounter(descriptionInput, descriptionCounter, 160);
});
</script>
@endsection
</x-layout>
<!-- End of file -->
