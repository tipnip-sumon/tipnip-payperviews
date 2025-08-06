@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary me-2"></i>
                Edit Popup
            </h1>
            <p class="text-muted mb-0">Update your popup settings and design</p>
        </div>
        <div>
            <a href="{{ route('admin.popups.show', $popup->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-1"></i>
                View Popup
            </a>
            <a href="{{ route('admin.popups.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Popups
            </a>
        </div>
    </div>

    <form action="{{ route('admin.popups.update', $popup->id) }}" method="POST" enctype="multipart/form-data" id="popupForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            Basic Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Popup Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $popup->title) }}" placeholder="Enter popup title" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Popup Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="announcement" {{ old('type', $popup->type) == 'announcement' ? 'selected' : '' }}>Announcement</option>
                                        <option value="promotion" {{ old('type', $popup->type) == 'promotion' ? 'selected' : '' }}>Promotion</option>
                                        <option value="warning" {{ old('type', $popup->type) == 'warning' ? 'selected' : '' }}>Warning</option>
                                        <option value="info" {{ old('type', $popup->type) == 'info' ? 'selected' : '' }}>Information</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="4" placeholder="Enter popup content">{{ old('content', $popup->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="display_type" class="form-label">Display Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('display_type') is-invalid @enderror" id="display_type" name="display_type" required>
                                        <option value="text" {{ old('display_type', $popup->display_type) == 'text' ? 'selected' : '' }}>Text Only</option>
                                        <option value="image" {{ old('display_type', $popup->display_type) == 'image' ? 'selected' : '' }}>Image Only</option>
                                        <option value="mixed" {{ old('display_type', $popup->display_type) == 'mixed' ? 'selected' : '' }}>Text + Image</option>
                                    </select>
                                    @error('display_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3" id="imageUpload">
                                    <label for="image" class="form-label">Popup Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Max size: 2MB. Supported formats: JPG, PNG, GIF</div>
                                    @if($popup->image)
                                        <div class="mt-2">
                                            <small class="text-success">Current image: {{ $popup->image }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button Configuration -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-mouse-pointer me-2"></i>
                            Button Configuration
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="button_text" class="form-label">Button Text <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                                           id="button_text" name="button_text" value="{{ old('button_text', $popup->button_text) }}" required>
                                    @error('button_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="button_url" class="form-label">Button URL (Optional)</label>
                                    <input type="url" class="form-control @error('button_url') is-invalid @enderror" 
                                           id="button_url" name="button_url" value="{{ old('button_url', $popup->button_url) }}" placeholder="https://example.com">
                                    @error('button_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-palette me-2"></i>
                            Appearance Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="button_color" class="form-label">Button Color</label>
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_color" name="button_color" value="{{ old('button_color', $popup->button_color) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="background_color" class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" 
                                           id="background_color" name="background_color" value="{{ old('background_color', $popup->background_color) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="text_color" class="form-label">Text Color</label>
                                    <input type="color" class="form-control form-control-color" 
                                           id="text_color" name="text_color" value="{{ old('text_color', $popup->text_color) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="overlay_color" class="form-label">Overlay Color</label>
                                    <input type="text" class="form-control" 
                                           id="overlay_color" name="overlay_color" value="{{ old('overlay_color', $popup->overlay_color) }}" 
                                           placeholder="rgba(0,0,0,0.5)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="size" class="form-label">Popup Size</label>
                                    <select class="form-control" id="size" name="size">
                                        <option value="small" {{ old('size', $popup->size) == 'small' ? 'selected' : '' }}>Small</option>
                                        <option value="medium" {{ old('size', $popup->size) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="large" {{ old('size', $popup->size) == 'large' ? 'selected' : '' }}>Large</option>
                                        <option value="fullscreen" {{ old('size', $popup->size) == 'fullscreen' ? 'selected' : '' }}>Fullscreen</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <select class="form-control" id="position" name="position">
                                        <option value="center" {{ old('position', $popup->position) == 'center' ? 'selected' : '' }}>Center</option>
                                        <option value="top" {{ old('position', $popup->position) == 'top' ? 'selected' : '' }}>Top</option>
                                        <option value="bottom" {{ old('position', $popup->position) == 'bottom' ? 'selected' : '' }}>Bottom</option>
                                        <option value="left" {{ old('position', $popup->position) == 'left' ? 'selected' : '' }}>Left</option>
                                        <option value="right" {{ old('position', $popup->position) == 'right' ? 'selected' : '' }}>Right</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="animation" class="form-label">Animation</label>
                                    <select class="form-control" id="animation" name="animation">
                                        <option value="fade" {{ old('animation', $popup->animation) == 'fade' ? 'selected' : '' }}>Fade</option>
                                        <option value="slide-up" {{ old('animation', $popup->animation) == 'slide-up' ? 'selected' : '' }}>Slide Up</option>
                                        <option value="slide-down" {{ old('animation', $popup->animation) == 'slide-down' ? 'selected' : '' }}>Slide Down</option>
                                        <option value="zoom" {{ old('animation', $popup->animation) == 'zoom' ? 'selected' : '' }}>Zoom</option>
                                        <option value="bounce" {{ old('animation', $popup->animation) == 'bounce' ? 'selected' : '' }}>Bounce</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="col-lg-4">
                <!-- Display Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog me-2"></i>
                            Display Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="delay" class="form-label">Delay (ms)</label>
                                    <input type="number" class="form-control" id="delay" name="delay" 
                                           value="{{ old('delay', $popup->delay) }}" min="0" step="100">
                                    <div class="form-text">Time before showing popup</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="auto_close" class="form-label">Auto Close (s)</label>
                                    <input type="number" class="form-control" id="auto_close" name="auto_close" 
                                           value="{{ old('auto_close', $popup->auto_close) }}" min="1" placeholder="Never">
                                    <div class="form-text">Auto close after seconds</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="frequency" class="form-label">Show Frequency</label>
                            <select class="form-control" id="frequency" name="frequency">
                                <option value="once" {{ old('frequency', $popup->frequency) == 'once' ? 'selected' : '' }}>Once per user</option>
                                <option value="daily" {{ old('frequency', $popup->frequency) == 'daily' ? 'selected' : '' }}>Once per day</option>
                                <option value="session" {{ old('frequency', $popup->frequency) == 'session' ? 'selected' : '' }}>Once per session</option>
                                <option value="always" {{ old('frequency', $popup->frequency) == 'always' ? 'selected' : '' }}>Every page load</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="priority" class="form-label">Priority (1-10)</label>
                            <input type="number" class="form-control" id="priority" name="priority" 
                                   value="{{ old('priority', $popup->priority) }}" min="1" max="10">
                            <div class="form-text">Higher number = higher priority</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="target_users" class="form-label">Target Users</label>
                            <select class="form-control" id="target_users" name="target_users[]" multiple>
                                @php
                                    $currentTargets = is_array($popup->target_users) ? $popup->target_users : json_decode($popup->target_users, true);
                                    $oldTargets = old('target_users', $currentTargets ?? ['all']);
                                @endphp
                                <option value="all" {{ in_array('all', $oldTargets) ? 'selected' : '' }}>All Users</option>
                                <option value="admin" {{ in_array('admin', $oldTargets) ? 'selected' : '' }}>Admins</option>
                                <option value="user" {{ in_array('user', $oldTargets) ? 'selected' : '' }}>Regular Users</option>
                                <option value="guest" {{ in_array('guest', $oldTargets) ? 'selected' : '' }}>Guests</option>
                            </select>
                            <div class="form-text">Hold Ctrl to select multiple</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="pages" class="form-label">Specific Pages (Optional)</label>
                            <input type="text" class="form-control" id="pages" name="pages" 
                                   value="{{ old('pages', $popup->pages) }}" placeholder="home,dashboard,profile">
                            <div class="form-text">Comma-separated page names</div>
                        </div>

                        <!-- Checkboxes -->
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="closable" name="closable" {{ old('closable', $popup->closable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="closable">Show close button</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="backdrop_close" name="backdrop_close" {{ old('backdrop_close', $popup->backdrop_close) ? 'checked' : '' }}>
                            <label class="form-check-label" for="backdrop_close">Close on backdrop click</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_on_mobile" name="show_on_mobile" {{ old('show_on_mobile', $popup->show_on_mobile) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_on_mobile">Show on mobile</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_on_desktop" name="show_on_desktop" {{ old('show_on_desktop', $popup->show_on_desktop) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_on_desktop">Show on desktop</label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $popup->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Activate immediately</label>
                        </div>
                    </div>
                </div>

                <!-- Schedule Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar me-2"></i>
                            Schedule Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="start_date" class="form-label">Start Date (Optional)</label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                   value="{{ old('start_date', $popup->start_date ? \Carbon\Carbon::parse($popup->start_date)->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="end_date" class="form-label">End Date (Optional)</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                   value="{{ old('end_date', $popup->end_date ? \Carbon\Carbon::parse($popup->end_date)->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>
                </div>

                <!-- Popup Statistics -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-bar me-2"></i>
                            Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="p-2 border rounded">
                                    <h5 class="text-primary mb-1">{{ $popup->views->count() }}</h5>
                                    <small class="text-muted">Total Views</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded">
                                    <h5 class="text-success mb-1">{{ $popup->views->where('clicked', true)->count() }}</h5>
                                    <small class="text-muted">Clicks</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                Created: {{ $popup->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Popup
                            </button>
                            <button type="button" id="previewBtn" class="btn btn-outline-info">
                                <i class="fas fa-eye me-1"></i>
                                Preview
                            </button>
                            <a href="{{ route('admin.popups.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('script')
<script>
$(document).ready(function() {
    // Toggle image upload based on display type
    $('#display_type').change(function() {
        const displayType = $(this).val();
        const imageUpload = $('#imageUpload');
        
        if (displayType === 'text') {
            imageUpload.hide();
        } else {
            imageUpload.show();
        }
    }).trigger('change');

    // Live preview functionality
    $('#previewBtn').click(function() {
        // Create preview popup with current form data
        const formData = new FormData($('#popupForm')[0]);
        
        // Create a simple preview modal
        showPreviewPopup({
            title: $('#title').val() || 'Popup Title',
            content: $('#content').val() || 'Popup content goes here...',
            button_text: $('#button_text').val() || 'Close',
            button_color: $('#button_color').val(),
            background_color: $('#background_color').val(),
            text_color: $('#text_color').val(),
            size: $('#size').val(),
            animation: $('#animation').val()
        });
    });

    function showPreviewPopup(data) {
        // Remove existing preview
        $('#previewPopup').remove();
        
        // Create preview popup
        const popup = $(`
            <div id="previewPopup" class="popup-overlay" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <div class="popup-content" style="
                    background: ${data.background_color};
                    color: ${data.text_color};
                    padding: 30px;
                    border-radius: 10px;
                    max-width: ${data.size === 'small' ? '400px' : data.size === 'large' ? '800px' : '600px'};
                    position: relative;
                    animation: ${data.animation === 'fade' ? 'fadeIn' : data.animation === 'zoom' ? 'zoomIn' : 'slideInUp'} 0.3s ease;
                ">
                    <button type="button" style="
                        position: absolute;
                        top: 15px;
                        right: 15px;
                        background: none;
                        border: none;
                        font-size: 20px;
                        cursor: pointer;
                        color: ${data.text_color};
                    " onclick="$('#previewPopup').remove()">×</button>
                    
                    <h4 style="margin-bottom: 15px; color: ${data.text_color};">${data.title}</h4>
                    <div style="margin-bottom: 20px; color: ${data.text_color};">${data.content}</div>
                    
                    <button type="button" style="
                        background: ${data.button_color};
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        cursor: pointer;
                    " onclick="$('#previewPopup').remove()">${data.button_text}</button>
                    
                    <div style="margin-top: 15px; font-size: 12px; color: #666;">
                        Preview Mode - This is how your popup will look
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(popup);
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes zoomIn {
        from { transform: scale(0.3); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    @keyframes slideInUp {
        from { transform: translateY(100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection
