<x-layout>
    <x-slot name="title">Category Management</x-slot>

@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Category Management</h4>
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

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">All Categories</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-sm">
                            <i class="ri-add-line me-1"></i>Create New Category
                        </a>
                        <a href="{{ route('admin.markdown.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i>Back to Files
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($categories->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category Name</th>
                                        <th>Display Name</th>
                                        <th>Files Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $index => $category)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <code class="bg-light px-2 py-1 rounded">{{ $category->category }}</code>
                                            </td>
                                            <td>
                                                <strong>{{ ucfirst(str_replace('-', ' ', $category->category)) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $category->count }} files</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.markdown.index', ['category' => $category->category]) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View Files">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a href="{{ route('admin.categories.edit', $category->category) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit Category">
                                                        <i class="ri-edit-line"></i>
                                                    </a>
                                                    <a href="{{ route('admin.markdown.create', ['category' => $category->category]) }}" 
                                                       class="btn btn-sm btn-outline-success" title="Add File">
                                                        <i class="ri-add-line"></i>
                                                    </a>
                                                    @if($category->count == 0)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteCategory('{{ $category->category }}')" title="Delete Category">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                onclick="showMoveFilesModal('{{ $category->category }}', {{ $category->count }})" title="Move Files">
                                                            <i class="ri-file-transfer-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-folder-open-line fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Categories Found</h4>
                            <p class="text-muted mb-4">Create your first category to organize your markdown files.</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
                                <i class="ri-add-line me-2"></i>Create First Category
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Move Files Modal -->
    <div class="modal fade" id="moveFilesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Move Files to Another Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="moveFilesForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p id="moveFilesText"></p>
                        <div class="mb-3">
                            <label for="target_category" class="form-label">Target Category</label>
                            <select class="form-select" id="target_category" name="target_category" required>
                                <option value="">Select target category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category }}">{{ ucfirst(str_replace('-', ' ', $cat->category)) }} ({{ $cat->count }} files)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Move Files</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Category Form -->
    <form id="deleteCategoryForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteCategory(categoryName) {
    Swal.fire({
        title: 'Delete Category?',
        text: `Are you sure you want to delete the category "${categoryName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteCategoryForm');
            form.action = `{{ route('admin.categories.index') }}/${categoryName}`;
            form.submit();
        }
    });
}

function showMoveFilesModal(categoryName, filesCount) {
    const modal = new bootstrap.Modal(document.getElementById('moveFilesModal'));
    const form = document.getElementById('moveFilesForm');
    const text = document.getElementById('moveFilesText');
    const targetSelect = document.getElementById('target_category');
    
    form.action = `{{ route('admin.categories.index') }}/${categoryName}/move-files`;
    text.textContent = `Move ${filesCount} files from "${categoryName}" to:`;
    
    // Remove the current category from target options
    Array.from(targetSelect.options).forEach(option => {
        option.style.display = option.value === categoryName ? 'none' : 'block';
    });
    
    modal.show();
}
</script>
@endpush
</x-layout>
