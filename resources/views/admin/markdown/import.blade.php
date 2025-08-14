<x-layout>
    <x-slot name="title">Import Markdown Files</x-slot>

@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Import Markdown Files</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.markdown.index') }}">Markdown Files</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Import</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Import Markdown Files from Directory</div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <i class="ri-information-line me-2"></i>
                        <strong>Import Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Place your <code>.md</code> files in the <code>storage/app/markdown-import/</code> directory</li>
                            <li>Files will be imported with their filename as the title</li>
                            <li>File metadata will be extracted from frontmatter if present</li>
                            <li>Files will be automatically categorized based on subdirectory names</li>
                            <li>Existing files with the same slug will be skipped</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.markdown.import') }}" method="POST">
                        @csrf
                        
                        <!-- Import Directory -->
                        <div class="mb-3">
                            <label for="import_directory" class="form-label">Import Directory</label>
                            <input type="text" class="form-control @error('import_directory') is-invalid @enderror" 
                                   id="import_directory" name="import_directory" 
                                   value="{{ old('import_directory', 'storage/app/markdown-import') }}" 
                                   placeholder="storage/app/markdown-import">
                            <div class="form-text">
                                Path to the directory containing markdown files to import.
                            </div>
                            @error('import_directory')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Default Category -->
                        <div class="mb-3">
                            <label for="default_category" class="form-label">Default Category</label>
                            <input type="text" class="form-control @error('default_category') is-invalid @enderror" 
                                   id="default_category" name="default_category" 
                                   value="{{ old('default_category', 'imported') }}" 
                                   placeholder="imported">
                            <div class="form-text">
                                Category to assign to files that don't have a category specified.
                            </div>
                            @error('default_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Default Status -->
                        <div class="mb-3">
                            <label for="default_status" class="form-label">Default Status</label>
                            <select class="form-select @error('default_status') is-invalid @enderror" 
                                    id="default_status" name="default_status">
                                <option value="draft" {{ old('default_status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('default_status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('default_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="form-text">
                                Status to assign to imported files.
                            </div>
                            @error('default_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Import Options -->
                        <div class="mb-4">
                            <h6 class="mb-3">Import Options</h6>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="overwrite_existing" 
                                       name="overwrite_existing" value="1" {{ old('overwrite_existing') ? 'checked' : '' }}>
                                <label class="form-check-label" for="overwrite_existing">
                                    Overwrite existing files with same slug
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="parse_frontmatter" 
                                       name="parse_frontmatter" value="1" {{ old('parse_frontmatter', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="parse_frontmatter">
                                    Parse YAML frontmatter for metadata
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="auto_publish" 
                                       name="auto_publish" value="1" {{ old('auto_publish') ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_publish">
                                    Auto-publish imported files
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="create_categories" 
                                       name="create_categories" value="1" {{ old('create_categories', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_categories">
                                    Auto-create categories from subdirectories
                                </label>
                            </div>
                        </div>

                        <!-- Directory Preview -->
                        <div class="mb-4">
                            <h6 class="mb-3">Directory Preview</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-2"><strong>Expected Directory Structure:</strong></p>
                                <pre class="mb-0"><code>storage/app/markdown-import/
├── general/
│   ├── welcome.md
│   └── getting-started.md
├── tutorials/
│   ├── basic-tutorial.md
│   └── advanced-guide.md
└── standalone-file.md</code></pre>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-upload-line me-1"></i>Start Import
                            </button>
                            <a href="{{ route('admin.markdown.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Back to Files
                            </a>
                            <button type="button" class="btn btn-outline-info" id="checkDirectoryBtn">
                                <i class="ri-folder-line me-1"></i>Check Directory
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Directory Check Modal -->
    <div class="modal fade" id="directoryCheckModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Directory Contents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="directoryContents">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Check directory contents
    $('#checkDirectoryBtn').on('click', function() {
        const directory = $('#import_directory').val();
        const modal = new bootstrap.Modal(document.getElementById('directoryCheckModal'));
        
        modal.show();
        
        // Simulate directory check (you would implement an actual endpoint)
        setTimeout(function() {
            $('#directoryContents').html(`
                <div class="alert alert-info">
                    <strong>Directory:</strong> ${directory}
                </div>
                <p>This feature would show:</p>
                <ul>
                    <li>List of .md files found</li>
                    <li>Detected categories</li>
                    <li>File count per category</li>
                    <li>Any potential conflicts</li>
                </ul>
                <p class="text-muted"><em>Directory scanning endpoint needs to be implemented.</em></p>
            `);
        }, 1000);
    });

    // Form validation
    $('form').on('submit', function(e) {
        const directory = $('#import_directory').val().trim();
        
        if (!directory) {
            e.preventDefault();
            alert('Please specify an import directory.');
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ri-loader-2-line me-1 spinner-border spinner-border-sm"></i>Importing...');
    });
});
</script>
@endpush
</x-layout>
