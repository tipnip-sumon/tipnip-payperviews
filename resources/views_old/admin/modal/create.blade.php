@extends('components.layout')

@section('page-title', 'Create Modal')

@section('style')
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-section h6 {
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    .preview-card {
        border: 1px dashed #dee2e6;
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Create New Modal</h2>
                    <p class="text-muted">Create a new modal with custom settings</p>
                </div>
                <div>
                    <a href="{{ route('admin.modal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.modal.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="form-section">
                    <h6><i class="fas fa-info-circle"></i> Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_name" class="form-label">Modal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('modal_name') is-invalid @enderror" 
                                       id="modal_name" name="modal_name" value="{{ old('modal_name') }}" 
                                       placeholder="e.g., welcome_new_user">
                                <div class="form-text">Unique identifier for the modal (use lowercase, underscores)</div>
                                @error('modal_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Modal Title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtitle" class="form-label">Subtitle</label>
                                <input type="text" class="form-control @error('subtitle') is-invalid @enderror" 
                                       id="subtitle" name="subtitle" value="{{ old('subtitle') }}" 
                                       placeholder="Optional subtitle">
                                @error('subtitle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="heading" class="form-label">Heading</label>
                                <input type="text" class="form-control @error('heading') is-invalid @enderror" 
                                       id="heading" name="heading" value="{{ old('heading') }}" 
                                       placeholder="Optional heading">
                                @error('heading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Modal description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Targeting & Frequency -->
                <div class="form-section">
                    <h6><i class="fas fa-bullseye"></i> Targeting & Frequency</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="target_users" class="form-label">Target Users <span class="text-danger">*</span></label>
                                <select class="form-select @error('target_users') is-invalid @enderror" 
                                        id="target_users" name="target_users">
                                    <option value="all" {{ old('target_users') == 'all' ? 'selected' : '' }}>All Users</option>
                                    <option value="guests" {{ old('target_users') == 'guests' ? 'selected' : '' }}>Guests Only</option>
                                    <option value="new_users" {{ old('target_users') == 'new_users' ? 'selected' : '' }}>New Users</option>
                                    <option value="verified" {{ old('target_users') == 'verified' ? 'selected' : '' }}>Verified Users</option>
                                    <option value="unverified" {{ old('target_users') == 'unverified' ? 'selected' : '' }}>Unverified Users</option>
                                </select>
                                @error('target_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="show_frequency" class="form-label">Show Frequency <span class="text-danger">*</span></label>
                                <select class="form-select @error('show_frequency') is-invalid @enderror" 
                                        id="show_frequency" name="show_frequency">
                                    <option value="once" {{ old('show_frequency') == 'once' ? 'selected' : '' }}>Once Only</option>
                                    <option value="daily" {{ old('show_frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('show_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="session" {{ old('show_frequency') == 'session' ? 'selected' : '' }}>Per Session</option>
                                </select>
                                @error('show_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_shows" class="form-label">Maximum Shows <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_shows') is-invalid @enderror" 
                                       id="max_shows" name="max_shows" value="{{ old('max_shows', 7) }}" 
                                       min="1" max="100">
                                <div class="form-text">Maximum number of times to show this modal</div>
                                @error('max_shows')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delay_seconds" class="form-label">Delay (seconds) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('delay_seconds') is-invalid @enderror" 
                                       id="delay_seconds" name="delay_seconds" value="{{ old('delay_seconds', 3) }}" 
                                       min="0" max="60">
                                <div class="form-text">Delay before showing the modal</div>
                                @error('delay_seconds')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="form-section">
                    <h6><i class="fas fa-cogs"></i> Advanced Settings</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_on_mobile_only" 
                                           name="show_on_mobile_only" value="1" {{ old('show_on_mobile_only') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_on_mobile_only">
                                        Show on Mobile Only
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_on_desktop_only" 
                                           name="show_on_desktop_only" value="1" {{ old('show_on_desktop_only') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_on_desktop_only">
                                        Show on Desktop Only
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minimum_session_time" class="form-label">Minimum Session Time (seconds)</label>
                                <input type="number" class="form-control" id="minimum_session_time" 
                                       name="minimum_session_time" value="{{ old('minimum_session_time', 30) }}" min="0">
                                <div class="form-text">Minimum time user must be on site before showing modal</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exclude_routes" class="form-label">Exclude Routes</label>
                                <input type="text" class="form-control" id="exclude_routes" 
                                       name="exclude_routes" value="{{ old('exclude_routes') }}" 
                                       placeholder="login,register,admin">
                                <div class="form-text">Comma-separated routes where modal should NOT show</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="include_routes" class="form-label">Include Routes</label>
                                <input type="text" class="form-control" id="include_routes" 
                                       name="include_routes" value="{{ old('include_routes') }}" 
                                       placeholder="dashboard,home">
                                <div class="form-text">Comma-separated routes where modal SHOULD show (empty = all)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom CSS/JS -->
                <div class="form-section">
                    <h6><i class="fas fa-code"></i> Custom Styling</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_css" class="form-label">Custom CSS</label>
                                <textarea class="form-control" id="custom_css" name="custom_css" rows="4" 
                                          placeholder=".custom-modal { background: red; }">{{ old('custom_css') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_js" class="form-label">Custom JavaScript</label>
                                <textarea class="form-control" id="custom_js" name="custom_js" rows="4" 
                                          placeholder="console.log('Modal shown');">{{ old('custom_js') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status & Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Status & Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active</strong>
                                </label>
                                <div class="form-text">Enable this modal to be shown to users</div>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Create Modal
                        </button>
                    </div>
                </div>

                <!-- Preview -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="preview-card p-3">
                            <h6 id="preview-title">Modal Title</h6>
                            <p class="text-muted small mb-2" id="preview-subtitle">Subtitle</p>
                            <p class="small" id="preview-description">Description will appear here...</p>
                            <div class="d-flex gap-2">
                                <span class="badge bg-primary" id="preview-frequency">daily</span>
                                <span class="badge bg-secondary" id="preview-target">all</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
// Live preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fields = ['title', 'subtitle', 'description', 'show_frequency', 'target_users'];
    
    fields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('input', updatePreview);
        }
    });
    
    function updatePreview() {
        const title = document.getElementById('title').value || 'Modal Title';
        const subtitle = document.getElementById('subtitle').value || 'Subtitle';
        const description = document.getElementById('description').value || 'Description will appear here...';
        const frequency = document.getElementById('show_frequency').value || 'daily';
        const target = document.getElementById('target_users').value || 'all';
        
        document.getElementById('preview-title').textContent = title;
        document.getElementById('preview-subtitle').textContent = subtitle;
        document.getElementById('preview-description').textContent = description;
        document.getElementById('preview-frequency').textContent = frequency;
        document.getElementById('preview-target').textContent = target.replace('_', ' ');
    }
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush
