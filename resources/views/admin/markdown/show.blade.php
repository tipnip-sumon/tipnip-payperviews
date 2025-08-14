<x-layout>
    <x-slot name="title">{{ $markdownFile->title }}</x-slot>
@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Markdown File Details</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.markdown.index') }}">Markdown Files</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $markdownFile->title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">{{ $markdownFile->title }}</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.markdown.edit', $markdownFile->id) }}" 
                           class="btn btn-primary btn-sm">
                            <i class="ri-edit-line me-1"></i>Edit
                        </a>
                        @if($markdownFile->file_path && file_exists(storage_path('app/markdown/' . basename($markdownFile->file_path))))
                            <a href="{{ route('admin.markdown.download', $markdownFile->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="ri-download-line me-1"></i>Download
                            </a>
                        @endif
                        <a href="{{ route('docs.show', [$markdownFile->category, $markdownFile->slug]) }}" 
                           class="btn btn-outline-info btn-sm" target="_blank">
                            <i class="ri-external-link-line me-1"></i>View Public
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="markdown-content">
                        {!! $markdownFile->rendered_content !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">File Information</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $markdownFile->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Slug:</strong></td>
                                <td><code>{{ $markdownFile->slug }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>
                                    <span class="badge bg-primary">{{ ucfirst($markdownFile->category) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($markdownFile->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($markdownFile->status == 'inactive')
                                        <span class="badge bg-danger">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">Draft</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Published:</strong></td>
                                <td>
                                    @if($markdownFile->is_published)
                                        <span class="badge bg-success">Yes</span>
                                        @if($markdownFile->published_at)
                                            <br><small class="text-muted">{{ $markdownFile->published_at->format('M d, Y H:i') }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Featured:</strong></td>
                                <td>
                                    @if($markdownFile->is_featured)
                                        <span class="badge bg-warning">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Views:</strong></td>
                                <td>{{ number_format($markdownFile->view_count) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Word Count:</strong></td>
                                <td>{{ str_word_count(strip_tags($markdownFile->content)) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Character Count:</strong></td>
                                <td>{{ strlen($markdownFile->content) }}</td>
                            </tr>
                            @if($markdownFile->author)
                            <tr>
                                <td><strong>Author:</strong></td>
                                <td>{{ $markdownFile->author->name }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $markdownFile->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated:</strong></td>
                                <td>{{ $markdownFile->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @if($markdownFile->file_path)
                            <tr>
                                <td><strong>File Path:</strong></td>
                                <td><code>{{ basename($markdownFile->file_path) }}</code></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            @if($markdownFile->meta_description || $markdownFile->keywords)
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">SEO Information</div>
                </div>
                <div class="card-body">
                    @if($markdownFile->meta_description)
                    <div class="mb-3">
                        <strong>Meta Description:</strong>
                        <p class="text-muted mb-0">{{ $markdownFile->meta_description }}</p>
                        <small class="text-muted">({{ strlen($markdownFile->meta_description) }} characters)</small>
                    </div>
                    @endif

                    @if($markdownFile->keywords)
                    <div class="mb-0">
                        <strong>Keywords:</strong>
                        <div class="mt-2">
                            @foreach(explode(',', $markdownFile->keywords) as $keyword)
                                <span class="badge bg-light text-dark me-1">{{ trim($keyword) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Quick Actions</div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.markdown.publish', $markdownFile->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-{{ $markdownFile->is_published ? 'warning' : 'success' }} btn-sm w-100 publish-btn">
                                <i class="ri-{{ $markdownFile->is_published ? 'eye-off' : 'eye' }}-line me-1"></i>
                                {{ $markdownFile->is_published ? 'Unpublish' : 'Publish Now' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.markdown.feature', $markdownFile->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-{{ $markdownFile->is_featured ? 'outline-info' : 'info' }} btn-sm w-100 feature-btn">
                                <i class="ri-{{ $markdownFile->is_featured ? 'star-fill' : 'star' }}-line me-1"></i>
                                {{ $markdownFile->is_featured ? 'Remove Featured' : 'Mark as Featured' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.markdown.destroy', $markdownFile->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this markdown file?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="ri-delete-bin-line me-1"></i>Delete File
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .markdown-content {
        line-height: 1.6;
    }
    .markdown-content h1,
    .markdown-content h2,
    .markdown-content h3,
    .markdown-content h4,
    .markdown-content h5,
    .markdown-content h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .markdown-content h1:first-child,
    .markdown-content h2:first-child,
    .markdown-content h3:first-child {
        margin-top: 0;
    }
    .markdown-content p {
        margin-bottom: 1rem;
    }
    .markdown-content ul,
    .markdown-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    .markdown-content code {
        background-color: #f8f9fa;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }
    .markdown-content pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-bottom: 1rem;
    }
    .markdown-content blockquote {
        border-left: 4px solid #dee2e6;
        padding-left: 1rem;
        margin: 1rem 0;
        color: #6c757d;
    }
    .markdown-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }
    .markdown-content table {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }
    .markdown-content table th,
    .markdown-content table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    .markdown-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    </style>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Publish/Unpublish
    $(document).on('click', '.publish-btn', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var url = form.attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload the page to update the UI
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Publish error:', xhr.responseText);
                showAlert('error', 'An error occurred while updating the publication status.');
            }
        });
    });

    // Feature/Unfeature
    $(document).on('click', '.feature-btn', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var url = form.attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload the page to update the UI
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Feature error:', xhr.responseText);
                showAlert('error', 'An error occurred while updating the featured status.');
            }
        });
    });

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alert = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                   message +
                   '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        
        $('body').prepend(alert);
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
@endpush
</x-layout>
