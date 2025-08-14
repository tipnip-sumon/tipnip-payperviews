<x-layout>
    <x-slot name="title">Markdown Categories Management</x-slot>
    
@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Markdown Categories</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.markdown.index') }}">Markdown Files</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Categories Overview</div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="ri-add-line me-1"></i>Add New Category
                        </button>
                        <a href="{{ route('admin.markdown.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i>Back to Files
                        </a>
                        <a href="{{ route('admin.markdown.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-1"></i>Create New File
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($categories->isNotEmpty())
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-1">
                                                    <i class="fe fe-folder text-primary me-2"></i>
                                                    {{ ucfirst(str_replace('-', ' ', $category->category)) }}
                                                </h5>
                                                <span class="badge bg-primary">{{ $category->count }} files</span>
                                            </div>
                                            <p class="text-muted mb-3">
                                                Category: <code>{{ $category->category }}</code>
                                            </p>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.markdown.index', ['category' => $category->category]) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fe fe-eye me-1"></i>View Files
                                                </a>
                                                <a href="{{ route('admin.markdown.create', ['category' => $category->category]) }}" 
                                                   class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-plus me-1"></i>Add File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fe fe-folder-plus fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Categories Found</h4>
                            <p class="text-muted mb-4">Create your first markdown file to establish categories.</p>
                            <a href="{{ route('admin.markdown.create') }}" class="btn btn-primary">
                                <i class="fe fe-plus me-2"></i>Create First File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Statistics -->
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Category Statistics</div>
                </div>
                <div class="card-body">
                    @if($categories->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Display Name</th>
                                        <th>Total Files</th>
                                        <th>Published Files</th>
                                        <th>Draft Files</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        @php
                                            $publishedCount = \App\Models\MarkdownFile::where('category', $category->category)->where('is_published', true)->count();
                                            $draftCount = \App\Models\MarkdownFile::where('category', $category->category)->where('is_published', false)->count();
                                        @endphp
                                        <tr>
                                            <td><code>{{ $category->category }}</code></td>
                                            <td>{{ ucfirst(str_replace('-', ' ', $category->category)) }}</td>
                                            <td><span class="badge bg-primary">{{ $category->count }}</span></td>
                                            <td><span class="badge bg-success">{{ $publishedCount }}</span></td>
                                            <td><span class="badge bg-warning">{{ $draftCount }}</span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.markdown.index', ['category' => $category->category]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fe fe-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('admin.markdown.create', ['category' => $category->category]) }}" 
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="fe fe-plus"></i> Add
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fe fe-info-circle me-2"></i>
                            No categories available. Categories are automatically created when you create markdown files.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="fe fe-folder-plus me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categorySlug" class="form-label">Category Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categorySlug" name="category" required 
                               placeholder="e.g., user-guides" pattern="[a-z0-9-]+" 
                               title="Only lowercase letters, numbers, and hyphens allowed">
                        <div class="form-text">
                            Use lowercase letters, numbers, and hyphens only. This will be used in URLs.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="categoryTitle" class="form-label">Display Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryTitle" name="title" required 
                               placeholder="e.g., User Guides">
                        <div class="form-text">
                            This is how the category will be displayed in the admin interface.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3" 
                                  placeholder="Brief description of what this category contains..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fe fe-plus me-1"></i>Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from title
document.getElementById('categoryTitle').addEventListener('input', function(e) {
    const title = e.target.value;
    const slug = title
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple hyphens with single
        .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
    
    document.getElementById('categorySlug').value = slug;
});

// Handle form submission
document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const categorySlug = formData.get('category');
    const categoryTitle = formData.get('title');
    
    // Validate slug format
    const slugPattern = /^[a-z0-9-]+$/;
    if (!slugPattern.test(categorySlug)) {
        alert('Category slug must contain only lowercase letters, numbers, and hyphens.');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fe fe-loader me-1"></i>Creating...';
    
    // Submit to create a new category
    fetch('{{ route("admin.markdown.categories.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            category_name: categorySlug
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success - reload the page to show new category
            alert('Category created successfully!');
            location.reload();
        } else {
            alert('Error creating category: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating category. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>

<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}
</style>
</x-layout>
