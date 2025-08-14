<x-layout>
    <x-slot name="title">Create New Category</x-slot>

@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Create New Category</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
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
                    <div class="card-title">Create New Category</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        
                        <!-- Category Name (Slug) -->
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name (Slug) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('category_name') is-invalid @enderror" 
                                   id="category_name" name="category_name" value="{{ old('category_name') }}" 
                                   placeholder="e.g., user-guides" pattern="[a-z0-9-]+" required>
                            <div class="form-text">
                                Use lowercase letters, numbers, and hyphens only. This will be used in URLs.
                            </div>
                            @error('category_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Display Name -->
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   id="display_name" name="display_name" value="{{ old('display_name') }}" 
                                   placeholder="e.g., User Guides" required>
                            <div class="form-text">
                                This is how the category will be displayed in the admin interface.
                            </div>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Brief description of what this category contains...">{{ old('description') }}</textarea>
                            <div class="form-text">
                                Optional description that will be included in the welcome file.
                            </div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-line me-1"></i>Create Category
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Back to Categories
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
        
        $('#category_name').val(categoryName);
    });

    // Validate category name format
    $('#category_name').on('input', function() {
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
});
</script>
@endpush
</x-layout>
