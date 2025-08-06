<x-layout>
    @section('top_title', 'Edit Video Link')
    
    @section('content')
        <div class="row mb-4 my-4">
            @section('title', 'Edit Video Link')
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Edit Video Link</h4>
                            <p class="text-muted mb-0">Update video link information and settings</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.video-links.show', $videoLink->id) }}" class="btn btn-info">
                                <i class="fe fe-eye"></i> View Details
                            </a>
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

                        <form method="POST" action="{{ route('admin.video-links.update', $videoLink->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
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
                                                           placeholder="Enter video title" value="{{ old('title', $videoLink->title) }}" required>
                                                    <div class="form-text">Enter a descriptive title for the video</div>
                                                </div>
                                                
                                                <div class="col-md-12 mb-3">
                                                    <label for="video_url" class="form-label">Video URL <span class="text-danger">*</span></label>
                                                    <input type="url" class="form-control" id="video_url" name="video_url" 
                                                           placeholder="https://example.com/video" value="{{ old('video_url', $videoLink->video_url) }}" required>
                                                    <div class="form-text">Enter the complete video URL (YouTube, Vimeo, etc.)</div>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="duration" class="form-label">Duration (seconds)</label>
                                                    <input type="number" class="form-control" id="duration" name="duration" 
                                                           placeholder="e.g., 120" value="{{ old('duration', $videoLink->duration) }}" min="1" max="7200">
                                                    <div class="form-text">Video duration in seconds (optional)</div>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="cost_per_click" class="form-label">Cost Per Click <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="cost_per_click" name="cost_per_click" 
                                                               placeholder="0.0100" value="{{ old('cost_per_click', $videoLink->cost_per_click) }}" 
                                                               step="0.0001" min="0" max="999.9999" required>
                                                    </div>
                                                    <div class="form-text">Amount users earn per view (in USD)</div>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="category" name="category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="entertainment" {{ old('category', $videoLink->category) == 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                                                        <option value="education" {{ old('category', $videoLink->category) == 'education' ? 'selected' : '' }}>Education</option>
                                                        <option value="music" {{ old('category', $videoLink->category) == 'music' ? 'selected' : '' }}>Music</option>
                                                        <option value="sports" {{ old('category', $videoLink->category) == 'sports' ? 'selected' : '' }}>Sports</option>
                                                        <option value="news" {{ old('category', $videoLink->category) == 'news' ? 'selected' : '' }}>News</option>
                                                        <option value="comedy" {{ old('category', $videoLink->category) == 'comedy' ? 'selected' : '' }}>Comedy</option>
                                                        <option value="technology" {{ old('category', $videoLink->category) == 'technology' ? 'selected' : '' }}>Technology</option>
                                                        <option value="lifestyle" {{ old('category', $videoLink->category) == 'lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                                                        <option value="gaming" {{ old('category', $videoLink->category) == 'gaming' ? 'selected' : '' }}>Gaming</option>
                                                        <option value="other" {{ old('category', $videoLink->category) == 'other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="source_platform" class="form-label">Source Platform</label>
                                                    <select class="form-select" id="source_platform" name="source_platform">
                                                        <option value="">Auto-detect from URL</option>
                                                        <option value="YouTube" {{ old('source_platform', $videoLink->source_platform) == 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                                        <option value="Vimeo" {{ old('source_platform', $videoLink->source_platform) == 'Vimeo' ? 'selected' : '' }}>Vimeo</option>
                                                        <option value="Dailymotion" {{ old('source_platform', $videoLink->source_platform) == 'Dailymotion' ? 'selected' : '' }}>Dailymotion</option>
                                                        <option value="Facebook" {{ old('source_platform', $videoLink->source_platform) == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                                        <option value="Instagram" {{ old('source_platform', $videoLink->source_platform) == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                                        <option value="TikTok" {{ old('source_platform', $videoLink->source_platform) == 'TikTok' ? 'selected' : '' }}>TikTok</option>
                                                        <option value="Other" {{ old('source_platform', $videoLink->source_platform) == 'Other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                    <div class="form-text">Leave blank to auto-detect from URL</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Video Settings & Stats -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Video Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active" {{ old('status', $videoLink->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status', $videoLink->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    <option value="paused" {{ old('status', $videoLink->status) == 'paused' ? 'selected' : '' }}>Paused</option>
                                                    <option value="completed" {{ old('status', $videoLink->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="ads_type" class="form-label">Ads Type</label>
                                                <select class="form-select" id="ads_type" name="ads_type">
                                                    <option value="">Select Ads Type</option>
                                                    <option value="pre_roll" {{ old('ads_type', $videoLink->ads_type) == 'pre_roll' ? 'selected' : '' }}>Pre-roll</option>
                                                    <option value="mid_roll" {{ old('ads_type', $videoLink->ads_type) == 'mid_roll' ? 'selected' : '' }}>Mid-roll</option>
                                                    <option value="post_roll" {{ old('ads_type', $videoLink->ads_type) == 'post_roll' ? 'selected' : '' }}>Post-roll</option>
                                                    <option value="overlay" {{ old('ads_type', $videoLink->ads_type) == 'overlay' ? 'selected' : '' }}>Overlay</option>
                                                    <option value="banner" {{ old('ads_type', $videoLink->ads_type) == 'banner' ? 'selected' : '' }}>Banner</option>
                                                    <option value="none" {{ old('ads_type', $videoLink->ads_type) == 'none' ? 'selected' : '' }}>No Ads</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="country" class="form-label">Target Country</label>
                                                <select class="form-select" id="country" name="country">
                                                    <option value="">All Countries</option>
                                                    <option value="US" {{ old('country', $videoLink->country) == 'US' ? 'selected' : '' }}>United States</option>
                                                    <option value="CA" {{ old('country', $videoLink->country) == 'CA' ? 'selected' : '' }}>Canada</option>
                                                    <option value="UK" {{ old('country', $videoLink->country) == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                                    <option value="AU" {{ old('country', $videoLink->country) == 'AU' ? 'selected' : '' }}>Australia</option>
                                                    <option value="DE" {{ old('country', $videoLink->country) == 'DE' ? 'selected' : '' }}>Germany</option>
                                                    <option value="FR" {{ old('country', $videoLink->country) == 'FR' ? 'selected' : '' }}>France</option>
                                                    <option value="JP" {{ old('country', $videoLink->country) == 'JP' ? 'selected' : '' }}>Japan</option>
                                                    <option value="IN" {{ old('country', $videoLink->country) == 'IN' ? 'selected' : '' }}>India</option>
                                                    <option value="BR" {{ old('country', $videoLink->country) == 'BR' ? 'selected' : '' }}>Brazil</option>
                                                    <option value="other" {{ old('country', $videoLink->country) == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Video Statistics -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Video Statistics</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="p-2 bg-primary-light rounded">
                                                        <h4 class="text-primary mb-0">{{ number_format($videoLink->views_count) }}</h4>
                                                        <small class="text-muted">Total Views</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 bg-success-light rounded">
                                                        <h4 class="text-success mb-0">{{ number_format($videoLink->clicks_count) }}</h4>
                                                        <small class="text-muted">Total Clicks</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Created:</span>
                                                    <span>{{ $videoLink->created_at->format('M d, Y') }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Last Updated:</span>
                                                    <span>{{ $videoLink->updated_at->format('M d, Y') }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Total Earnings:</span>
                                                    <span class="text-success fw-bold">
                                                        ${{ number_format($videoLink->views_count * $videoLink->cost_per_click, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Video Preview -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Video Preview</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="videoPreview" class="border rounded p-3 text-center">
                                                <!-- Preview will be loaded by JavaScript -->
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
                                            <i class="fe fe-save"></i> Update Video Link
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
    // Initialize video preview on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateVideoPreview();
    });

    // Video URL Preview
    document.getElementById('video_url').addEventListener('input', updateVideoPreview);

    function updateVideoPreview() {
        const url = document.getElementById('video_url').value;
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
            } else {
                previewDiv.innerHTML = `
                    <i class="fe fe-link display-4 text-muted"></i>
                    <p class="mb-0 small">Valid video URL detected</p>
                `;
            }
        } else {
            previewDiv.innerHTML = `
                <i class="fe fe-video display-4 text-muted"></i>
                <p class="mb-0 small">Enter a video URL to see preview</p>
            `;
        }
    }
    
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
