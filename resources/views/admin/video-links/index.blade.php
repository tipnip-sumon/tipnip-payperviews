<x-layout>
    @section('top_title', 'Video Links Management')
    
    @section('content')
        <div class="row mb-4 my-4">
            @section('title', 'Video Links Management')
            
            <!-- Statistics Cards -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Total Videos</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ $stats['total_videos'] }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-primary my-auto float-end">
                                    <i class="fe fe-video"></i> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Active Videos</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ $stats['active_videos'] }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-success my-auto float-end">
                                    <i class="fe fe-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Total Views</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ number_format($stats['total_views']) }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-warning my-auto float-end">
                                    <i class="fe fe-eye"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Earnings Paid</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">${{ number_format($stats['total_earnings_paid'], 2) }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-info my-auto float-end">
                                    <i class="fe fe-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Import Results -->
        @if(session('import_errors') || session('import_stats'))
            <div class="row">
                <div class="col-12">
                    @if(session('import_stats'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <h6><i class="fe fe-info"></i> Import Summary</h6>
                            <ul class="mb-0">
                                <li><strong>Imported:</strong> {{ session('import_stats')['imported'] }} video(s)</li>
                                @if(session('import_stats')['errors'] > 0)
                                    <li><strong>Errors:</strong> {{ session('import_stats')['errors'] }} row(s)</li>
                                @endif
                                @if(session('import_stats')['skipped'] > 0)
                                    <li><strong>Skipped:</strong> {{ session('import_stats')['skipped'] }} empty row(s)</li>
                                @endif
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('import_errors') && count(session('import_errors')) > 0)
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h6><i class="fe fe-alert-triangle"></i> Import Errors</h6>
                            <div class="max-height-300 overflow-auto">
                                <ul class="mb-0">
                                    @foreach(array_slice(session('import_errors'), 0, 10) as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    @if(count(session('import_errors')) > 10)
                                        <li><em>... and {{ count(session('import_errors')) - 10 }} more errors</em></li>
                                    @endif
                                </ul>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Video Links Management -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Video Links Management</h4>
                            <p class="text-muted mb-0">Manage video links and their settings</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.video-links.create') }}" class="btn btn-primary">
                                <i class="fe fe-plus"></i> Add New Video
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fe fe-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item export-link" href="{{ route('admin.video-links.export') }}?{{ http_build_query(request()->query()) }}" data-export-type="basic">
                                        <i class="fe fe-file-text"></i> Basic Export
                                    </a></li>
                                    <li><a class="dropdown-item export-link" href="{{ route('admin.video-links.export.advanced') }}?{{ http_build_query(request()->query()) }}" data-export-type="advanced">
                                        <i class="fe fe-bar-chart"></i> Advanced Export (with Analytics)
                                    </a></li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal" id="importButton" onclick="showImportModal()">
                                <i class="fe fe-upload"></i> Import
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form method="GET" action="{{ route('admin.video-links.index') }}" class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <select name="category" class="form-select">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                    {{ ucfirst($category) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search by title, URL, or platform..." 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fe fe-search"></i> Filter
                                            </button>
                                            <a href="{{ route('admin.video-links.index') }}" class="btn btn-secondary">
                                                <i class="fe fe-x"></i> Clear
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form id="bulkActionForm" method="POST" action="{{ route('admin.video-links.bulk-action') }}">
                                    @csrf
                                    <div class="d-flex gap-2 align-items-center">
                                        <select name="action" class="form-select" style="width: auto;">
                                            <option value="">Bulk Actions</option>
                                            <option value="activate">Activate</option>
                                            <option value="deactivate">Deactivate</option>
                                            <option value="pause">Pause</option>
                                            <option value="delete">Delete</option>
                                        </select>
                                        <button type="submit" class="btn btn-warning" onclick="return confirmBulkAction()">
                                            Apply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Video Links Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>
                                            <a href="{{ route('admin.video-links.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none">
                                                Title
                                                @if(request('sort') == 'title')
                                                    <i class="fe fe-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Platform</th>
                                        <th>Category</th>
                                        <th>
                                            <a href="{{ route('admin.video-links.index', array_merge(request()->query(), ['sort' => 'cost_per_click', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none">
                                                Cost/Click
                                                @if(request('sort') == 'cost_per_click')
                                                    <i class="fe fe-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ route('admin.video-links.index', array_merge(request()->query(), ['sort' => 'views_count', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none">
                                                Views
                                                @if(request('sort') == 'views_count')
                                                    <i class="fe fe-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Status</th>
                                        <th>
                                            <a href="{{ route('admin.video-links.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none">
                                                Created
                                                @if(request('sort') == 'created_at')
                                                    <i class="fe fe-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($videoLinks as $video)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="video_ids[]" value="{{ $video->id }}" 
                                                       class="form-check-input video-checkbox">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ Str::limit($video->title, 40) }}</h6>
                                                        <small class="text-muted">
                                                            @if($video->duration)
                                                                <i class="fe fe-clock"></i> {{ gmdate('i:s', $video->duration) }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $video->source_platform ?: 'Unknown' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($video->category) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">${{ number_format($video->cost_per_click, 4) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary">{{ number_format($video->views_count) }}</span>
                                                    <small class="text-muted">views</small>
                                                </div>
                                            </td>
                                            <td>
                                                @switch($video->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Active</span>
                                                        @break
                                                    @case('inactive')
                                                        <span class="badge bg-secondary">Inactive</span>
                                                        @break
                                                    @case('paused')
                                                        <span class="badge bg-warning">Paused</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-info">Completed</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">{{ ucfirst($video->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $video->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" 
                                                            id="dropdownMenuButton{{ $video->id }}" data-bs-toggle="dropdown" 
                                                            aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $video->id }}">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.video-links.show', $video->id) }}">
                                                                <i class="fe fe-eye"></i> View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.video-links.edit', $video->id) }}">
                                                                <i class="fe fe-edit"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ $video->video_url }}" target="_blank">
                                                                <i class="fe fe-external-link"></i> View Video
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.video-links.destroy', $video->id) }}" 
                                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this video link?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fe fe-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fe fe-video display-4 text-muted"></i>
                                                    <h5 class="mt-3">No Video Links Found</h5>
                                                    <p class="text-muted">Start by adding your first video link.</p>
                                                    <a href="{{ route('admin.video-links.create') }}" class="btn btn-primary">
                                                        <i class="fe fe-plus"></i> Add New Video
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($videoLinks->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $videoLinks->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Video Links</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.video-links.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Choose CSV File</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            <div class="form-text">Upload a CSV file with video links. Maximum file size: 2MB</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6>CSV Format:</h6>
                            <p class="mb-1">Your CSV file should have the following columns:</p>
                            <small>
                                <strong>Title, Video URL, Duration (seconds), Ads Type, Category, Country, Source Platform, Cost Per Click, Status</strong>
                            </small>
                            <p class="mt-2 mb-0">
                                <a href="{{ route('admin.video-links.sample-csv') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fe fe-download"></i> Download Sample CSV
                                </a>
                            </p>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <strong>Supported Video Platforms:</strong> YouTube, Vimeo, Dailymotion, Facebook, Instagram, TikTok, Twitch, and others.
                                    <br>
                                    <strong>URL Format:</strong> Must include http:// or https:// (e.g., https://www.youtube.com/watch?v=abc123)
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-upload"></i> Import Videos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @push('styles')
    <style>
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
        .max-height-300 {
            max-height: 300px;
        }
        .overflow-auto {
            overflow: auto;
        }
        .alert ul {
            padding-left: 1.5rem;
        }
        .alert ul li {
            margin-bottom: 0.25rem;
        }
    </style>
    @endpush
@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
    // Show import modal function
    function showImportModal() {
        const importModal = document.getElementById('importModal');
        if (importModal) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(importModal);
                modal.show();
            } else {
                // Fallback for older Bootstrap or missing Bootstrap
                importModal.style.display = 'block';
                importModal.classList.add('show');
                importModal.classList.add('d-block');
                document.body.classList.add('modal-open');
                
                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'modal-backdrop';
                document.body.appendChild(backdrop);
            }
        }
    }

    // Close modal function
    function closeImportModal() {
        const importModal = document.getElementById('importModal');
        const backdrop = document.getElementById('modal-backdrop');
        
        if (importModal) {
            importModal.style.display = 'none';
            importModal.classList.remove('show');
            importModal.classList.remove('d-block');
            document.body.classList.remove('modal-open');
            
            if (backdrop) {
                backdrop.remove();
            }
        }
    }

    // Select All Functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.video-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk Action Confirmation
    function confirmBulkAction() {
        const selectedBoxes = document.querySelectorAll('.video-checkbox:checked');
        const action = document.querySelector('select[name="action"]').value;
        
        if (selectedBoxes.length === 0) {
            alert('Please select at least one video link.');
            return false;
        }
        
        if (!action) {
            alert('Please select an action.');
            return false;
        }
        
        const actionText = action === 'delete' ? 'delete' : action;
        return confirm(`Are you sure you want to ${actionText} ${selectedBoxes.length} video link(s)?`);
    }

    // Add selected video IDs to bulk action form
    document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
        const selectedBoxes = document.querySelectorAll('.video-checkbox:checked');
        
        // Remove existing hidden inputs
        const existingInputs = this.querySelectorAll('input[name="video_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        // Add hidden inputs for selected videos
        selectedBoxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'video_ids[]';
            hiddenInput.value = checkbox.value;
            this.appendChild(hiddenInput);
        });
    });

    // Initialize modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to close buttons
        const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                closeImportModal();
            });
        });

        // Close modal when clicking outside
        document.getElementById('importModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });

        // Handle export clicks with loading states
        const exportLinks = document.querySelectorAll('.export-link');
        exportLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const exportType = this.dataset.exportType;
                const originalText = this.innerHTML;
                const exportUrl = this.href;
                
                // Show loading state
                this.innerHTML = '<i class="fe fe-loader fa-spin"></i> Exporting...';
                this.style.pointerEvents = 'none';
                
                // Show notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Exporting Data',
                        text: 'Please wait while we prepare your export file...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
                
                // Create hidden iframe for download
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = exportUrl;
                document.body.appendChild(iframe);
                
                // Reset button state after delay
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                    
                    // Remove iframe after download
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 5000);
                    
                    // Show success notification
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Export Complete!',
                            text: 'Your file should start downloading shortly.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                }, 3000);
            });
        });
    });
</script>
@endpush
</x-layout>
