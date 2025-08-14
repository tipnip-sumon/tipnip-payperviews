<x-layout>
    <x-slot name="title">Create Markdown File</x-slot>
@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Create Markdown File</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.markdown.index') }}">Markdown Files</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
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
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Create New Markdown File</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.markdown.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Slug -->
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug') }}" required>
                                    <div class="form-text">URL-friendly version of the title</div>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Content -->
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                                    <div class="form-text">Supports full Markdown syntax</div>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <!-- Category -->
                                <x-category-selector 
                                    :categories="$categories" 
                                    :selected="request('category')" 
                                    name="category" 
                                    id="categorySelect" 
                                    :required="true" 
                                    :allowCustom="true" 
                                    :showRefresh="true" />

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Published -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_published" 
                                               name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">
                                            Publish immediately
                                        </label>
                                    </div>
                                </div>

                                <!-- Featured -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" 
                                               name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Mark as featured
                                        </label>
                                    </div>
                                </div>

                                <!-- Save to file -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="save_to_file" 
                                               name="save_to_file" value="1" {{ old('save_to_file', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="save_to_file">
                                            Save to physical file
                                        </label>
                                    </div>
                                </div>

                                <!-- Meta Description -->
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                                    <div class="form-text">SEO description (max 160 characters)</div>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Keywords -->
                                <div class="mb-3">
                                    <label for="keywords" class="form-label">Keywords</label>
                                    <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                                           id="keywords" name="keywords" value="{{ old('keywords') }}">
                                    <div class="form-text">Comma-separated keywords</div>
                                    @error('keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>Create Markdown File
                            </button>
                            <a href="{{ route('admin.markdown.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Back to List
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-info">
                                <i class="ri-settings-3-line me-1"></i>Manage Categories
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Markdown Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Markdown Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        // Auto-generate slug from title
        $('#title').on('input', function() {
            let title = $(this).val();
            let slug = title.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            $('#slug').val(slug);
        });

        // Character count for meta description
        $('#meta_description').on('input', function() {
            let count = $(this).val().length;
            let color = count > 160 ? 'text-danger' : 'text-muted';
            $(this).next('.form-text').html(`SEO description (${count}/160 characters)`).attr('class', `form-text ${color}`);
        });

        // Form validation and submission with better feedback
        $('form').on('submit', function(e) {
            // Basic client-side validation
            let title = $('#title').val().trim();
            let content = $('#content').val().trim();
            let category = $('#categorySelect_final').val();
            let status = $('#status').val();

            if (!title) {
                showAlert('error', 'Title is required!');
                e.preventDefault();
                return false;
            }

            if (!content) {
                showAlert('error', 'Content is required!');
                e.preventDefault();
                return false;
            }

            if (!category) {
                showAlert('error', 'Please select a category!');
                e.preventDefault();
                return false;
            }

            if (!status) {
                showAlert('error', 'Please select a status!');
                e.preventDefault();
                return false;
            }

            // Show loading state
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ri-loader-2-line me-1 spinner-border spinner-border-sm"></i>Creating...');
        });

        // Show alert function
        function showAlert(type, message) {
            var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            var icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';
            var alert = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                       '<i class="' + icon + ' me-2"></i>' + message +
                       '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            
            $('.page-header-breadcrumb').after(alert);
            
            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
    });
    </script>
@endpush

</x-layout>
