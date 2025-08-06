@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye text-primary me-2"></i>
                View Popup Details
            </h1>
            <p class="text-muted mb-0">{{ $popup->title }}</p>
        </div>
        <div>
            <a href="{{ route('admin.popups.edit', $popup->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i>
                Edit Popup
            </a>
            <a href="{{ route('admin.popups.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Popups
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
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
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Title:</strong></div>
                        <div class="col-md-9">{{ $popup->title }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Type:</strong></div>
                        <div class="col-md-9">
                            <span class="badge bg-{{ $popup->type === 'warning' ? 'warning' : ($popup->type === 'promotion' ? 'success' : ($popup->type === 'info' ? 'info' : 'primary')) }}">
                                {{ ucfirst($popup->type) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Content:</strong></div>
                        <div class="col-md-9">{{ $popup->content ?: 'No content specified' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Display Type:</strong></div>
                        <div class="col-md-9">{{ ucfirst($popup->display_type) }}</div>
                    </div>
                    
                    @if($popup->image)
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Image:</strong></div>
                        <div class="col-md-9">{{ $popup->image }}</div>
                    </div>
                    @endif
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
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Button Text:</strong></div>
                        <div class="col-md-9">{{ $popup->button_text }}</div>
                    </div>
                    
                    @if($popup->button_url)
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Button URL:</strong></div>
                        <div class="col-md-9">
                            <a href="{{ $popup->button_url }}" target="_blank" class="text-decoration-none">
                                {{ $popup->button_url }} <i class="fas fa-external-link-alt fa-sm"></i>
                            </a>
                        </div>
                    </div>
                    @endif
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
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Colors:</strong></div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="color-preview me-2" style="width: 20px; height: 20px; background: {{ $popup->button_color }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                                    <small>Button</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="color-preview me-2" style="width: 20px; height: 20px; background: {{ $popup->background_color }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                                    <small>Background</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="color-preview me-2" style="width: 20px; height: 20px; background: {{ $popup->text_color }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                                    <small>Text</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Size:</strong></div>
                        <div class="col-md-9">{{ ucfirst($popup->size) }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Position:</strong></div>
                        <div class="col-md-9">{{ ucfirst($popup->position) }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Animation:</strong></div>
                        <div class="col-md-9">{{ ucfirst(str_replace('-', ' ', $popup->animation)) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Settings & Stats -->
        <div class="col-lg-4">
            <!-- Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-toggle-on me-2"></i>
                        Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="mb-2">
                            <span class="badge bg-{{ $popup->is_active ? 'success' : 'secondary' }} fs-6">
                                {{ $popup->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </h4>
                        <p class="text-muted mb-0">Current Status</p>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog me-2"></i>
                        Display Settings
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6"><strong>Priority:</strong></div>
                        <div class="col-6">{{ $popup->priority }}/10</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6"><strong>Frequency:</strong></div>
                        <div class="col-6">{{ ucfirst($popup->frequency) }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6"><strong>Delay:</strong></div>
                        <div class="col-6">{{ $popup->delay }}ms</div>
                    </div>
                    
                    @if($popup->auto_close)
                    <div class="row mb-2">
                        <div class="col-6"><strong>Auto Close:</strong></div>
                        <div class="col-6">{{ $popup->auto_close }}s</div>
                    </div>
                    @endif
                    
                    <div class="row mb-2">
                        <div class="col-6"><strong>Target Users:</strong></div>
                        <div class="col-6">
                            @php
                                $targets = is_array($popup->target_users) ? $popup->target_users : json_decode($popup->target_users, true);
                            @endphp
                            {{ implode(', ', $targets ?? ['all']) }}
                        </div>
                    </div>
                    
                    @if($popup->pages)
                    <div class="row mb-2">
                        <div class="col-6"><strong>Pages:</strong></div>
                        <div class="col-6">{{ $popup->pages }}</div>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <div class="small">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" disabled {{ $popup->closable ? 'checked' : '' }}>
                            <label class="form-check-label">Closable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" disabled {{ $popup->backdrop_close ? 'checked' : '' }}>
                            <label class="form-check-label">Backdrop Close</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" disabled {{ $popup->show_on_mobile ? 'checked' : '' }}>
                            <label class="form-check-label">Mobile</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" disabled {{ $popup->show_on_desktop ? 'checked' : '' }}>
                            <label class="form-check-label">Desktop</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <h4 class="text-primary mb-1">{{ $popup->views->count() }}</h4>
                                <small class="text-muted">Total Views</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <h4 class="text-success mb-1">{{ $popup->views->where('clicked', true)->count() }}</h4>
                                <small class="text-muted">Clicks</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($popup->views->count() > 0)
                    <div class="text-center">
                        <small class="text-muted">
                            Click Rate: {{ number_format(($popup->views->where('clicked', true)->count() / $popup->views->count()) * 100, 1) }}%
                        </small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Schedule -->
            @if($popup->start_date || $popup->end_date)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar me-2"></i>
                        Schedule
                    </h6>
                </div>
                <div class="card-body">
                    @if($popup->start_date)
                    <div class="row mb-2">
                        <div class="col-4"><strong>Start:</strong></div>
                        <div class="col-8">{{ \Carbon\Carbon::parse($popup->start_date)->format('M d, Y H:i') }}</div>
                    </div>
                    @endif
                    
                    @if($popup->end_date)
                    <div class="row mb-2">
                        <div class="col-4"><strong>End:</strong></div>
                        <div class="col-8">{{ \Carbon\Carbon::parse($popup->end_date)->format('M d, Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.popups.edit', $popup->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>
                            Edit Popup
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="previewPopup()">
                            <i class="fas fa-eye me-1"></i>
                            Preview
                        </button>
                        <a href="{{ route('admin.popups.analytics', $popup->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-chart-line me-1"></i>
                            Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
function previewPopup() {
    // Create preview popup with actual data
    showPreviewPopup({
        title: '{{ $popup->title }}',
        content: '{{ $popup->content }}',
        button_text: '{{ $popup->button_text }}',
        button_color: '{{ $popup->button_color }}',
        background_color: '{{ $popup->background_color }}',
        text_color: '{{ $popup->text_color }}',
        size: '{{ $popup->size }}',
        animation: '{{ $popup->animation }}'
    });
}

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
                    Preview Mode - This is how your popup looks
                </div>
            </div>
        </div>
    `);
    
    $('body').append(popup);
}

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
