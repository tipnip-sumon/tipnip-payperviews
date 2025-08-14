<x-layout>
    <x-slot name="title">Edit Markdown File</x-slot>
@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Edit Markdown File</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.markdown.index') }}">Markdown Files</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Edit: {{ $markdownFile->title }}</div>
                    <div class="d-flex gap-2">
                        @if($markdownFile->file_path && file_exists(storage_path('app/markdown/' . basename($markdownFile->file_path))))
                            <a href="{{ route('admin.markdown.download', $markdownFile->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="ri-download-line me-1"></i>Download
                            </a>
                        @endif
                        <a href="{{ route('docs.show', [$markdownFile->category, $markdownFile->slug]) }}" 
                           class="btn btn-outline-info btn-sm" target="_blank">
                            <i class="ri-external-link-line me-1"></i>Preview
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.markdown.update', $markdownFile->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $markdownFile->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Slug -->
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $markdownFile->slug) }}" required>
                                    <div class="form-text">URL-friendly version of the title</div>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Content -->
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="15" required>{{ old('content', $markdownFile->content) }}</textarea>
                                    <div class="form-text">Supports full Markdown syntax</div>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <!-- File Info -->
                                <div class="mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="card-title mb-2">File Information</h6>
                                            <small class="text-muted">
                                                <strong>ID:</strong> {{ $markdownFile->id }}<br>
                                                <strong>Created:</strong> {{ $markdownFile->created_at->format('M d, Y H:i') }}<br>
                                                <strong>Updated:</strong> {{ $markdownFile->updated_at->format('M d, Y H:i') }}<br>
                                                <strong>Views:</strong> {{ number_format($markdownFile->view_count) }}<br>
                                                @if($markdownFile->author)
                                                    <strong>Author:</strong> {{ $markdownFile->author->name }}<br>
                                                @endif
                                                @if($markdownFile->file_path)
                                                    <strong>File Path:</strong> {{ basename($markdownFile->file_path) }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="general" {{ old('category', $markdownFile->category) == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="documentation" {{ old('category', $markdownFile->category) == 'documentation' ? 'selected' : '' }}>Documentation</option>
                                        <option value="help" {{ old('category', $markdownFile->category) == 'help' ? 'selected' : '' }}>Help</option>
                                        <option value="tutorial" {{ old('category', $markdownFile->category) == 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                                        <option value="announcement" {{ old('category', $markdownFile->category) == 'announcement' ? 'selected' : '' }}>Announcement</option>
                                        <option value="policy" {{ old('category', $markdownFile->category) == 'policy' ? 'selected' : '' }}>Policy</option>
                                        <option value="terms" {{ old('category', $markdownFile->category) == 'terms' ? 'selected' : '' }}>Terms</option>
                                        <option value="privacy" {{ old('category', $markdownFile->category) == 'privacy' ? 'selected' : '' }}>Privacy</option>
                                        <option value="faq" {{ old('category', $markdownFile->category) == 'faq' ? 'selected' : '' }}>FAQ</option>
                                        <option value="guide" {{ old('category', $markdownFile->category) == 'guide' ? 'selected' : '' }}>Guide</option>
                                        <option value="api" {{ old('category', $markdownFile->category) == 'api' ? 'selected' : '' }}>API</option>
                                        <option value="changelog" {{ old('category', $markdownFile->category) == 'changelog' ? 'selected' : '' }}>Changelog</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="draft" {{ old('status', $markdownFile->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ old('status', $markdownFile->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $markdownFile->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Published -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_published" 
                                               name="is_published" value="1" {{ old('is_published', $markdownFile->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">
                                            Published
                                        </label>
                                    </div>
                                    @if($markdownFile->published_at)
                                        <small class="text-muted">Published: {{ $markdownFile->published_at->format('M d, Y H:i') }}</small>
                                    @endif
                                </div>

                                <!-- Featured -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" 
                                               name="is_featured" value="1" {{ old('is_featured', $markdownFile->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Featured
                                        </label>
                                    </div>
                                </div>

                                <!-- Save to file -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="save_to_file" 
                                               name="save_to_file" value="1" {{ old('save_to_file', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="save_to_file">
                                            Update physical file
                                        </label>
                                    </div>
                                </div>

                                <!-- Meta Description -->
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $markdownFile->meta_description) }}</textarea>
                                    <div class="form-text">SEO description (max 160 characters)</div>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Keywords -->
                                <div class="mb-3">
                                    <label for="keywords" class="form-label">Keywords</label>
                                    <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                                           id="keywords" name="keywords" value="{{ old('keywords', $markdownFile->keywords) }}">
                                    <div class="form-text">Comma-separated keywords</div>
                                    @error('keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>Update Markdown File
                            </button>
                            <a href="{{ route('admin.markdown.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Back to List
                            </a>
                            <button type="button" class="btn btn-info" onclick="showPreview()">
                                <i class="ri-eye-line me-1"></i>Preview
                            </button>
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
        // Character count for meta description
        function updateCharCount() {
            let count = $('#meta_description').val().length;
            let color = count > 160 ? 'text-danger' : 'text-muted';
            $('#meta_description').next('.form-text').html(`SEO description (${count}/160 characters)`).attr('class', `form-text ${color}`);
        }
        
        $('#meta_description').on('input', updateCharCount);
        updateCharCount(); // Initial count
        
        // Debug form submission
        $('form').on('submit', function(e) {
            console.log('Form is being submitted...');
            console.log('Form action:', $(this).attr('action'));
            console.log('Form method:', $(this).attr('method'));
            
            // Show loading state
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.html();
            submitBtn.html('<i class="ri-loader-2-line me-1"></i>Updating...').prop('disabled', true);
            
            // Re-enable button after 10 seconds in case something goes wrong
            setTimeout(function() {
                submitBtn.html(originalText).prop('disabled', false);
            }, 10000);
        });
    });

    function showPreview() {
        let content = $('#content').val();
        let title = $('#title').val();
        
        // Simple markdown to HTML conversion for preview
        let html = content
            .replace(/^# (.*$)/gim, '<h1>$1</h1>')
            .replace(/^## (.*$)/gim, '<h2>$1</h2>')
            .replace(/^### (.*$)/gim, '<h3>$1</h3>')
            .replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>')
            .replace(/\*(.*)\*/gim, '<em>$1</em>')
            .replace(/!\[([^\]]*)\]\(([^\)]*)\)/gim, '<img alt="$1" src="$2" class="img-fluid">')
            .replace(/\[([^\]]*)\]\(([^\)]*)\)/gim, '<a href="$2">$1</a>')
            .replace(/\n/gim, '<br>');
        
        $('#previewModal .modal-title').text('Preview: ' + title);
        $('#preview-content').html(html);
        $('#previewModal').modal('show');
    }
    </script>
@endpush
</x-layout>
