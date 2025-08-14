<x-layout>

    <x-slot name="title">Export All Markdown Files</x-slot>

    <x-slot name="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.markdown.index') }}">Markdown Files</a></li>
        <li class="breadcrumb-item active">Export All</li>
    </x-slot>

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Export All Markdown Files</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="text-center mb-4">
                                <i class="ri-download-cloud-line text-primary" style="font-size: 4rem;"></i>
                                <h5 class="mt-3">Export Options</h5>
                                <p class="text-muted">Choose how you want to export your markdown files</p>
                            </div>

                            <div class="export-options">
                                <!-- Single File Export -->
                                <div class="card border mb-3">
                                    <div class="card-body text-center">
                                        <i class="ri-file-text-line text-success mb-2" style="font-size: 2rem;"></i>
                                        <h6>Export as Combined File</h6>
                                        <p class="text-muted small">Download all markdown files as a single combined markdown file</p>
                                        <button type="button" class="btn btn-success" onclick="exportAsCombined()">
                                            <i class="ri-download-line me-1"></i>Download Combined File
                                        </button>
                                    </div>
                                </div>

                                <!-- Individual Files Export -->
                                <div class="card border mb-3">
                                    <div class="card-body text-center">
                                        <i class="ri-file-text-line text-info mb-2" style="font-size: 2rem;"></i>
                                        <h6>Export Individual Files</h6>
                                        <p class="text-muted small">Download each file separately (opens multiple downloads)</p>
                                        <button type="button" class="btn btn-info" onclick="exportIndividual()">
                                            <i class="ri-download-line me-1"></i>Download All
                                        </button>
                                    </div>
                                </div>

                                <!-- Category-wise Export -->
                                <div class="card border mb-3">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <i class="ri-folder-line text-warning mb-2" style="font-size: 2rem;"></i>
                                            <h6>Export by Category</h6>
                                            <p class="text-muted small">Download files from specific categories as a combined markdown file</p>
                                        </div>
                                        
                                        <form id="categoryExportForm">
                                            <div class="mb-3">
                                                <label for="categorySelect" class="form-label">Select Categories</label>
                                                <select class="form-select" id="categorySelect" multiple>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category }}">{{ $category }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text">Hold Ctrl/Cmd to select multiple categories</div>
                                            </div>
                                            <div class="text-center">
                                                <button type="button" class="btn btn-warning" onclick="exportByCategory()">
                                                    <i class="ri-download-line me-1"></i>Download Selected
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="mt-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Export Statistics</h6>
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <i class="ri-file-text-line text-primary"></i>
                                                </div>
                                                <h5 class="mb-0">{{ $totalFiles }}</h5>
                                                <small class="text-muted">Total Files</small>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <i class="ri-folder-line text-success"></i>
                                                </div>
                                                <h5 class="mb-0">{{ $totalCategories }}</h5>
                                                <small class="text-muted">Categories</small>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <i class="ri-eye-line text-info"></i>
                                                </div>
                                                <h5 class="mb-0">{{ $activeFiles }}</h5>
                                                <small class="text-muted">Active Files</small>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-2">
                                                    <i class="ri-file-damage-line text-warning"></i>
                                                </div>
                                                <h5 class="mb-0">{{ $totalFiles - $activeFiles }}</h5>
                                                <small class="text-muted">Inactive Files</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="text-center mt-4">
                                <a href="{{ route('admin.markdown.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-1"></i>Back to Files
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportAsCombined() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Preparing...';
    btn.disabled = true;
    
    // Create a form to submit the export request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.markdown.export-zip") }}'; // We'll need to add this route
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button after a delay
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 3000);
}

function exportIndividual() {
    if (!confirm('This will start multiple downloads. Continue?')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Starting...';
    btn.disabled = true;
    
    // Get all file IDs and trigger individual downloads
    fetch('{{ route("admin.markdown.get-all-ids") }}') // We'll need to add this route
        .then(response => response.json())
        .then(data => {
            data.ids.forEach((id, index) => {
                setTimeout(() => {
                    window.open(`/admin/markdown/${id}/export`, '_blank');
                }, index * 500); // Stagger downloads by 500ms
            });
            
            // Reset button
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
        })
        .catch(error => {
            alert('Error fetching file list');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

function exportByCategory() {
    const select = document.getElementById('categorySelect');
    const selectedCategories = Array.from(select.selectedOptions).map(option => option.value);
    
    if (selectedCategories.length === 0) {
        alert('Please select at least one category');
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Preparing...';
    btn.disabled = true;
    
    // Create a form to submit the category export request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.markdown.export-categories") }}'; // We'll need to add this route
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add selected categories
    selectedCategories.forEach(category => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'categories[]';
        input.value = category;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button after a delay
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 3000);
}
</script>
</x-layout>