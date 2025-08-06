<x-layout>
@section('title', $pageTitle ?? 'Social Media Settings')

@section('content')
<!-- Settings Navigation -->
<x-admin.settings-navigation current="social-media" />

<div class="row mb-4 my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-share-alt me-2"></i>
                    {{ $pageTitle }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.social-media.update') }}" method="POST">
                    @csrf

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="socialTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="social-links-tab" data-bs-toggle="tab" 
                                    data-bs-target="#social-links" type="button" role="tab">
                                <i class="fas fa-link me-2"></i>Social Links
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-info-tab" data-bs-toggle="tab" 
                                    data-bs-target="#contact-info" type="button" role="tab">
                                <i class="fas fa-address-card me-2"></i>Contact Information
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="socialTabsContent">
                        <!-- Social Links Tab -->
                        <div class="tab-pane fade show active" id="social-links" role="tabpanel">
                            @php
                                $socialLinks = $settings->social_media_links ?? [];
                                $platforms = [
                                    'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'color' => '#1877f2'],
                                    'twitter' => ['name' => 'Twitter/X', 'icon' => 'fab fa-twitter', 'color' => '#1da1f2'],
                                    'instagram' => ['name' => 'Instagram', 'icon' => 'fab fa-instagram', 'color' => '#e4405f'],
                                    'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'color' => '#0077b5'],
                                    'youtube' => ['name' => 'YouTube', 'icon' => 'fab fa-youtube', 'color' => '#ff0000'],
                                    'tiktok' => ['name' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => '#000000'],
                                    'discord' => ['name' => 'Discord', 'icon' => 'fab fa-discord', 'color' => '#5865f2'],
                                    'telegram' => ['name' => 'Telegram', 'icon' => 'fab fa-telegram-plane', 'color' => '#0088cc'],
                                    'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#25d366'],
                                    'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest-p', 'color' => '#bd081c'],
                                    'snapchat' => ['name' => 'Snapchat', 'icon' => 'fab fa-snapchat-ghost', 'color' => '#fffc00'],
                                    'reddit' => ['name' => 'Reddit', 'icon' => 'fab fa-reddit-alien', 'color' => '#ff4500'],
                                ];
                            @endphp

                            <div class="row">
                                @foreach($platforms as $key => $platform)
                                    <div class="col-lg-6 mb-4">
                                        <div class="form-group">
                                            <label for="social_{{ $key }}" class="form-label">
                                                <i class="{{ $platform['icon'] }} me-2" style="color: {{ $platform['color'] }}"></i>
                                                {{ $platform['name'] }}
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="{{ $platform['icon'] }}" style="color: {{ $platform['color'] }}"></i>
                                                </span>
                                                <input type="url" 
                                                       class="form-control @error('social_media_links.'.$key) is-invalid @enderror" 
                                                       name="social_media_links[{{ $key }}]" 
                                                       id="social_{{ $key }}" 
                                                       value="{{ old('social_media_links.'.$key, $socialLinks[$key] ?? '') }}" 
                                                       placeholder="https://{{ $key }}.com/yourusername">
                                                @error('social_media_links.'.$key)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Custom Social Platform -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-plus me-2"></i>
                                        Custom Social Platform
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="custom_platform_name" class="form-label">Platform Name</label>
                                                <input type="text" class="form-control" 
                                                       name="social_media_links[custom_name]" 
                                                       id="custom_platform_name" 
                                                       value="{{ old('social_media_links.custom_name', $socialLinks['custom_name'] ?? '') }}" 
                                                       placeholder="e.g., Custom Platform">
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="custom_platform_url" class="form-label">Platform URL</label>
                                                <input type="url" class="form-control" 
                                                       name="social_media_links[custom_url]" 
                                                       id="custom_platform_url" 
                                                       value="{{ old('social_media_links.custom_url', $socialLinks['custom_url'] ?? '') }}" 
                                                       placeholder="https://example.com/profile">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Links Preview -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-eye me-2"></i>
                                        Social Links Preview
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="social-preview" class="d-flex flex-wrap gap-2">
                                        <!-- Preview will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Tab -->
                        <div class="tab-pane fade" id="contact-info" role="tabpanel">
                            <div class="row">
                                <!-- Contact Email -->
                                <div class="col-lg-6 mb-4">
                                    <div class="form-group">
                                        <label for="contact_email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Contact Email
                                        </label>
                                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                               name="contact_email" id="contact_email" 
                                               value="{{ old('contact_email', $settings->contact_email) }}" 
                                               placeholder="contact@yoursite.com">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            This email will be displayed publicly for customer inquiries.
                                        </small>
                                    </div>
                                </div>

                                <!-- Contact Phone -->
                                <div class="col-lg-6 mb-4">
                                    <div class="form-group">
                                        <label for="contact_phone" class="form-label">
                                            <i class="fas fa-phone me-2"></i>Contact Phone
                                        </label>
                                        <input type="tel" class="form-control @error('contact_phone') is-invalid @enderror" 
                                               name="contact_phone" id="contact_phone" 
                                               value="{{ old('contact_phone', $settings->contact_phone) }}" 
                                               placeholder="+1 (555) 123-4567">
                                        @error('contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Include country code for international numbers.
                                        </small>
                                    </div>
                                </div>

                                <!-- Contact Address -->
                                <div class="col-lg-12 mb-4">
                                    <div class="form-group">
                                        <label for="contact_address" class="form-label">
                                            <i class="fas fa-map-marker-alt me-2"></i>Contact Address
                                        </label>
                                        <textarea class="form-control @error('contact_address') is-invalid @enderror" 
                                                  name="contact_address" id="contact_address" rows="4" 
                                                  placeholder="123 Business Street, Suite 100, City, State 12345, Country">{{ old('contact_address', $settings->contact_address) }}</textarea>
                                        @error('contact_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            This address will be displayed publicly and used for business correspondence.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Preview -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-eye me-2"></i>
                                        Contact Information Preview
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="contact-preview">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="contact-item mb-3">
                                                    <i class="fas fa-envelope text-primary me-2"></i>
                                                    <span id="email-preview">{{ $settings->contact_email ?: 'Not set' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="contact-item mb-3">
                                                    <i class="fas fa-phone text-primary me-2"></i>
                                                    <span id="phone-preview">{{ $settings->contact_phone ?: 'Not set' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="contact-item">
                                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                    <span id="address-preview">{{ $settings->contact_address ?: 'Not set' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Social Media Settings
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
    const platforms = @json($platforms);
    
    // Update social preview
    function updateSocialPreview() {
        const preview = document.getElementById('social-preview');
        preview.innerHTML = '';
        
        Object.keys(platforms).forEach(key => {
            const input = document.getElementById(`social_${key}`);
            const url = input.value.trim();
            
            if (url) {
                const platform = platforms[key];
                const link = document.createElement('a');
                link.href = url;
                link.target = '_blank';
                link.className = 'btn btn-outline-primary btn-sm';
                link.innerHTML = `<i class="${platform.icon} me-1"></i>${platform.name}`;
                link.style.borderColor = platform.color;
                link.style.color = platform.color;
                
                link.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = platform.color;
                    this.style.color = 'white';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent';
                    this.style.color = platform.color;
                });
                
                preview.appendChild(link);
            }
        });
        
        // Add custom platform if provided
        const customName = document.getElementById('custom_platform_name').value.trim();
        const customUrl = document.getElementById('custom_platform_url').value.trim();
        
        if (customName && customUrl) {
            const link = document.createElement('a');
            link.href = customUrl;
            link.target = '_blank';
            link.className = 'btn btn-outline-secondary btn-sm';
            link.innerHTML = `<i class="fas fa-link me-1"></i>${customName}`;
            preview.appendChild(link);
        }
        
        if (preview.children.length === 0) {
            preview.innerHTML = '<span class="text-muted">No social links configured</span>';
        }
    }
    
    // Update contact preview
    function updateContactPreview() {
        const emailInput = document.getElementById('contact_email');
        const phoneInput = document.getElementById('contact_phone');
        const addressInput = document.getElementById('contact_address');
        
        document.getElementById('email-preview').textContent = emailInput.value.trim() || 'Not set';
        document.getElementById('phone-preview').textContent = phoneInput.value.trim() || 'Not set';
        document.getElementById('address-preview').textContent = addressInput.value.trim() || 'Not set';
    }
    
    // Add event listeners
    Object.keys(platforms).forEach(key => {
        const input = document.getElementById(`social_${key}`);
        input.addEventListener('input', updateSocialPreview);
    });
    
    document.getElementById('custom_platform_name').addEventListener('input', updateSocialPreview);
    document.getElementById('custom_platform_url').addEventListener('input', updateSocialPreview);
    
    document.getElementById('contact_email').addEventListener('input', updateContactPreview);
    document.getElementById('contact_phone').addEventListener('input', updateContactPreview);
    document.getElementById('contact_address').addEventListener('input', updateContactPreview);
    
    // Initial preview update
    updateSocialPreview();
    updateContactPreview();
});
</script>
@endsection
</x-layout>
