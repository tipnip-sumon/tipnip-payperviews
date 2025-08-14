<x-layout>
    <x-slot name="title">Edit Category</x-slot>

@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Edit Category</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Error Messages -->
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Edit Category: {{ ucfirst(str_replace('-', ' ', $categoryName)) }}</div>
                </div>
                <div class="card-body">
                    <!-- Warning Alert -->
                    @if($filesCount > 0)
                        <div class="alert alert-warning" role="alert">
                            <i class="ri-alert-line me-2"></i>
                            <strong>Warning:</strong> This category contains {{ $filesCount }} files. 
                            Renaming this category will update all associated files automatically.
                        </div>
                    @endif

                    <form action="{{ route('admin.categories.update', $categoryName) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Current Category Info -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Current Category Information</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Category Name:</strong> <code>{{ $categoryName }}</code>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Files Count:</strong> <span class="badge bg-primary">{{ $filesCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Category Name (Slug) -->
                        <div class="mb-3">
                            <label for="new_category_name" class="form-label">New Category Name (Slug) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('new_category_name') is-invalid @enderror" 
                                   id="new_category_name" name="new_category_name" 
                                   value="{{ old('new_category_name', $categoryName) }}" 
                                   placeholder="e.g., user-guides" pattern="[a-z0-9-]+" required>
                            <div class="form-text">
                                Use lowercase letters, numbers, and hyphens only. This will be used in URLs.
                            </div>
                            @error('new_category_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Display Name -->
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   id="display_name" name="display_name" 
                                   value="{{ old('display_name', ucfirst(str_replace('-', ' ', $categoryName))) }}" 
                                   placeholder="e.g., User Guides" required>
                            <div class="form-text">
                                This is how the category will be displayed in the admin interface.
                            </div>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Update Impact -->
                        @if($filesCount > 0)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Update Impact</h6>
                                <div class="alert alert-info mb-0">
                                    <ul class="mb-0">
                                        <li>{{ $filesCount }} files will be moved to the new category name</li>
                                        <li>All file URLs will be updated automatically</li>
                                        <li>File slugs and content will remain unchanged</li>
                                        <li>This operation cannot be undone</li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="ri-save-line me-1"></i>Update Category
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Back to Categories
                            </a>
                            <a href="{{ route('admin.markdown.index', ['category' => $categoryName]) }}" class="btn btn-outline-primary">
                                <i class="ri-eye-line me-1"></i>View Files ({{ $filesCount }})
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Auto-generate category name from display name
    $('#display_name').on('input', function() {
        let displayName = $(this).val();
        let categoryName = displayName.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single
            .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
        
        $('#new_category_name').val(categoryName);
    });

    // Validate category name format
    $('#new_category_name').on('input', function() {
        let value = $(this).val();
        let isValid = /^[a-z0-9-]*$/.test(value);
        
        if (value && !isValid) {
            $(this).addClass('is-invalid');
            $(this).siblings('.form-text').text('Only lowercase letters, numbers, and hyphens allowed.');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.form-text').text('Use lowercase letters, numbers, and hyphens only. This will be used in URLs.');
        }
    });

    // Confirm form submission for category rename
    $('form').on('submit', function(e) {
        const currentName = '{{ $categoryName }}';
        const newName = $('#new_category_name').val();
        const filesCount = {{ $filesCount }};
        
        if (currentName !== newName && filesCount > 0) {
            e.preventDefault();
            
            if (confirm(`Are you sure you want to rename "${currentName}" to "${newName}"? This will update ${filesCount} files and cannot be undone.`)) {
                this.submit();
            }
        }
    });
});
</script>
@endpush
</x-layout>
