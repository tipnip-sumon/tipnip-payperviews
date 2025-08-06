@extends('components.layout')

@section('page-title', 'Edit Modal')

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
@php
    $additionalSettings = json_decode($modalSetting->additional_settings ?? '{}', true);
@endphp

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Edit Modal: {{ $modalSetting->title }}</h2>
                    <p class="text-muted">Modify modal settings</p>
                </div>
                <div>
                    <a href="{{ route('admin.modal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('admin.modal.show', $modalSetting->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.modal.update', $modalSetting->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="form-section">
                    <h6><i class="fas fa-info-circle"></i> Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_name" class="form-label">Modal Name</label>
                                <input type="text" class="form-control" id="modal_name" 
                                       value="{{ $modalSetting->modal_name }}" readonly>
                                <div class="form-text">Modal name cannot be changed</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $modalSetting->title) }}">
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
                                       id="subtitle" name="subtitle" value="{{ old('subtitle', $modalSetting->subtitle) }}">
                                @error('subtitle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="heading" class="form-label">Heading</label>
                                <input type="text" class="form-control @error('heading') is-invalid @enderror" 
                                       id="heading" name="heading" value="{{ old('heading', $modalSetting->heading) }}">
                                @error('heading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $modalSetting->description) }}</textarea>
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
                                    <option value="all" {{ old('target_users', $modalSetting->target_users) == 'all' ? 'selected' : '' }}>All Users</option>
                                    <option value="guests" {{ old('target_users', $modalSetting->target_users) == 'guests' ? 'selected' : '' }}>Guests Only</option>
                                    <option value="new_users" {{ old('target_users', $modalSetting->target_users) == 'new_users' ? 'selected' : '' }}>New Users</option>
                                    <option value="verified" {{ old('target_users', $modalSetting->target_users) == 'verified' ? 'selected' : '' }}>Verified Users</option>
                                    <option value="unverified" {{ old('target_users', $modalSetting->target_users) == 'unverified' ? 'selected' : '' }}>Unverified Users</option>
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
                                    <option value="once" {{ old('show_frequency', $modalSetting->show_frequency) == 'once' ? 'selected' : '' }}>Once Only</option>
                                    <option value="daily" {{ old('show_frequency', $modalSetting->show_frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('show_frequency', $modalSetting->show_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="session" {{ old('show_frequency', $modalSetting->show_frequency) == 'session' ? 'selected' : '' }}>Per Session</option>
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
                                       id="max_shows" name="max_shows" value="{{ old('max_shows', $modalSetting->max_shows) }}" 
                                       min="1" max="100">
                                @error('max_shows')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delay_seconds" class="form-label">Delay (seconds) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('delay_seconds') is-invalid @enderror" 
                                       id="delay_seconds" name="delay_seconds" value="{{ old('delay_seconds', $modalSetting->delay_seconds) }}" 
                                       min="0" max="60">
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
                                           name="show_on_mobile_only" value="1" 
                                           {{ old('show_on_mobile_only', $additionalSettings['show_on_mobile_only'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_on_mobile_only">
                                        Show on Mobile Only
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_on_desktop_only" 
                                           name="show_on_desktop_only" value="1" 
                                           {{ old('show_on_desktop_only', $additionalSettings['show_on_desktop_only'] ?? false) ? 'checked' : '' }}>
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
                                       name="minimum_session_time" 
                                       value="{{ old('minimum_session_time', $additionalSettings['minimum_session_time'] ?? 30) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exclude_routes" class="form-label">Exclude Routes</label>
                                <input type="text" class="form-control" id="exclude_routes" 
                                       name="exclude_routes" 
                                       value="{{ old('exclude_routes', implode(',', $additionalSettings['exclude_routes'] ?? [])) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="include_routes" class="form-label">Include Routes</label>
                                <input type="text" class="form-control" id="include_routes" 
                                       name="include_routes" 
                                       value="{{ old('include_routes', implode(',', $additionalSettings['include_routes'] ?? [])) }}">
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
                                <textarea class="form-control" id="custom_css" name="custom_css" rows="4">{{ old('custom_css', $additionalSettings['custom_css'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_js" class="form-label">Custom JavaScript</label>
                                <textarea class="form-control" id="custom_js" name="custom_js" rows="4">{{ old('custom_js', $additionalSettings['custom_js'] ?? '') }}</textarea>
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
                                       name="is_active" value="1" 
                                       {{ old('is_active', $modalSetting->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active</strong>
                                </label>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save"></i> Update Modal
                        </button>
                        <button type="button" class="btn btn-danger w-100" onclick="deleteModal()">
                            <i class="fas fa-trash"></i> Delete Modal
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="small text-muted">
                            <div><strong>Created:</strong> {{ $modalSetting->created_at }}</div>
                            <div><strong>Updated:</strong> {{ $modalSetting->updated_at }}</div>
                            <div><strong>ID:</strong> {{ $modalSetting->id }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this modal? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.modal.destroy', $modalSetting->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
function deleteModal() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
