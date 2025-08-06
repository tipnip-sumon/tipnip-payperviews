<x-layout>
    @section('top_title', 'Add New Video Link')
    
    @section('content')
        <div class="row mb-4 my-4">
            @section('title', 'Add New Video Link') 
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Add New Video Link</h4>
                            <p class="text-muted mb-0">Create a new video link for users to watch and earn</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.video-links.index') }}" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.video-links.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Video Information -->
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Video Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="title" class="form-label">Video Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="title" name="title" 
                                                           placeholder="Enter video title" value="{{ old('title') }}" required>
                                                    <div class="form-text">Enter a descriptive title for the video</div>
                                                </div>
                                                
                                                <div class="col-md-12 mb-3">
                                                    <label for="video_url" class="form-label">Video URL <span class="text-danger">*</span></label>
                                                    <input type="url" class="form-control" id="video_url" name="video_url" 
                                                           placeholder="https://example.com/video" value="{{ old('video_url') }}" required>
                                                    <div class="form-text">Enter the complete video URL (YouTube, Vimeo, etc.)</div>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="duration" class="form-label">Duration (seconds)</label>
                                                    <input type="number" class="form-control" id="duration" name="duration" 
                                                           placeholder="e.g., 120" value="{{ old('duration') }}" min="1" max="7200">
                                                    <div class="form-text">Video duration in seconds (optional)</div>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="cost_per_click" class="form-label">Cost Per Click <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="cost_per_click" name="cost_per_click" 
                                                               placeholder="0.0100" value="{{ old('cost_per_click', '0.01') }}" 
                                                               step="0.0001" min="0" max="999.9999" required>
                                                    </div>
                                                    <div class="form-text">Amount users earn per view (in USD)</div>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="category" name="category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="entertainment" {{ old('category') == 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                                                        <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>Education</option>
                                                        <option value="music" {{ old('category') == 'music' ? 'selected' : '' }}>Music</option>
                                                        <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>Sports</option>
                                                        <option value="news" {{ old('category') == 'news' ? 'selected' : '' }}>News</option>
                                                        <option value="comedy" {{ old('category') == 'comedy' ? 'selected' : '' }}>Comedy</option>
                                                        <option value="technology" {{ old('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                                                        <option value="lifestyle" {{ old('category') == 'lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                                                        <option value="gaming" {{ old('category') == 'gaming' ? 'selected' : '' }}>Gaming</option>
                                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="source_platform" class="form-label">Source Platform</label>
                                                    <select class="form-select" id="source_platform" name="source_platform">
                                                        <option value="">Auto-detect from URL</option>
                                                        <option value="YouTube" {{ old('source_platform') == 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                                        <option value="Vimeo" {{ old('source_platform') == 'Vimeo' ? 'selected' : '' }}>Vimeo</option>
                                                        <option value="Dailymotion" {{ old('source_platform') == 'Dailymotion' ? 'selected' : '' }}>Dailymotion</option>
                                                        <option value="Facebook" {{ old('source_platform') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                                        <option value="Instagram" {{ old('source_platform') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                                        <option value="TikTok" {{ old('source_platform') == 'TikTok' ? 'selected' : '' }}>TikTok</option>
                                                        <option value="Other" {{ old('source_platform') == 'Other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                    <div class="form-text">Leave blank to auto-detect from URL</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Video Settings -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Video Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    <option value="paused" {{ old('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="ads_type" class="form-label">Ads Type</label>
                                                <select class="form-select" id="ads_type" name="ads_type">
                                                    <option value="">Select Ads Type</option>
                                                    <option value="pre_roll" {{ old('ads_type') == 'pre_roll' ? 'selected' : '' }}>Pre-roll</option>
                                                    <option value="mid_roll" {{ old('ads_type') == 'mid_roll' ? 'selected' : '' }}>Mid-roll</option>
                                                    <option value="post_roll" {{ old('ads_type') == 'post_roll' ? 'selected' : '' }}>Post-roll</option>
                                                    <option value="overlay" {{ old('ads_type') == 'overlay' ? 'selected' : '' }}>Overlay</option>
                                                    <option value="banner" {{ old('ads_type') == 'banner' ? 'selected' : '' }}>Banner</option>
                                                    <option value="none" {{ old('ads_type') == 'none' ? 'selected' : '' }}>No Ads</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="country" class="form-label">Target Country</label>
                                                <select class="form-select" id="country" name="country">
                                                    <option value="">All Countries</option>
                                                    <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                                                    <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                                    <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                                    <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
                                                    <option value="DE" {{ old('country') == 'DE' ? 'selected' : '' }}>Germany</option>
                                                    <option value="FR" {{ old('country') == 'FR' ? 'selected' : '' }}>France</option>
                                                    <option value="JP" {{ old('country') == 'JP' ? 'selected' : '' }}>Japan</option>
                                                    <option value="IN" {{ old('country') == 'IN' ? 'selected' : '' }}>India</option>
                                                    <option value="BR" {{ old('country') == 'BR' ? 'selected' : '' }}>Brazil</option>
                                                    <option value="other" {{ old('country') == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Video Preview -->
                                            <div class="mb-3">
                                                <label class="form-label">Video Preview</label>
                                                <div id="videoPreview" class="border rounded p-3 text-center text-muted">
                                                    <i class="fe fe-video display-4"></i>
                                                    <p class="mb-0 small">Enter a video URL to see preview</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.video-links.index') }}" class="btn btn-secondary">
                                            <i class="fe fe-x"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe fe-save"></i> Save Video Link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-layout>

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
    // Video URL Preview
    document.getElementById('video_url').addEventListener('input', function() {
        const url = this.value;
        const previewDiv = document.getElementById('videoPreview');
        
        if (url) {
            // Detect platform and show appropriate preview
            let embedUrl = '';
            let platform = '';
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                platform = 'YouTube';
                let videoId = '';
                if (url.includes('youtu.be/')) {
                    videoId = url.split('youtu.be/')[1].split('?')[0];
                } else if (url.includes('youtube.com/watch?v=')) {
                    videoId = url.split('v=')[1].split('&')[0];
                }
                if (videoId) {
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                }
            } else if (url.includes('vimeo.com')) {
                platform = 'Vimeo';
                const videoId = url.split('vimeo.com/')[1].split('?')[0];
                if (videoId) {
                    embedUrl = `https://player.vimeo.com/video/${videoId}`;
                }
            }
            
            if (embedUrl) {
                previewDiv.innerHTML = `
                    <div class="ratio ratio-16x9">
                        <iframe src="${embedUrl}" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <small class="text-muted mt-2 d-block">Platform: ${platform}</small>
                `;
                
                // Auto-set platform if not selected
                const platformSelect = document.getElementById('source_platform');
                if (!platformSelect.value) {
                    platformSelect.value = platform;
                }
            } else {
                previewDiv.innerHTML = `
                    <i class="fe fe-link display-4"></i>
                    <p class="mb-0 small">Valid video URL detected</p>
                `;
            }
        } else {
            previewDiv.innerHTML = `
                <i class="fe fe-video display-4"></i>
                <p class="mb-0 small">Enter a video URL to see preview</p>
            `;
        }
    });
    
    // Auto-fill title from URL if possible
    document.getElementById('video_url').addEventListener('blur', function() {
        const titleInput = document.getElementById('title');
        if (!titleInput.value && this.value) {
            // Try to extract title from URL
            const url = this.value;
            if (url.includes('youtube.com') && url.includes('v=')) {
                // For YouTube, we could potentially fetch the title via API
                // For now, just suggest the user to add a title
                titleInput.placeholder = 'Add a descriptive title for this video';
            }
        }
    });
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['title', 'video_url', 'category', 'cost_per_click', 'status'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
</script>
@endpush
