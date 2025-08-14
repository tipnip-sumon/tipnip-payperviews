<x-layout>
    <x-slot name="title">Markdown Files Management</x-slot>
@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Markdown Files</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Markdown Files</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <i class="ri-check-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title">Markdown Files Management</div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.markdown.statistics') }}" class="btn btn-info btn-sm">
                        <i class="ri-bar-chart-line me-1"></i>Statistics
                    </a>
                    <a href="{{ route('admin.markdown.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i>Create New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            <!-- Categories will be loaded dynamically -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="publishedFilter">
                            <option value="">All</option>
                            <option value="1">Published</option>
                            <option value="0">Draft</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-secondary" id="clearFilters">Clear Filters</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap w-100" id="markdownTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Author</th>
                                <th>Views</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
@endpush

@push('script')
<!-- DataTables JavaScript -->
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(function() {
    console.log('jQuery loaded:', typeof $);
    console.log('DataTable function:', typeof $.fn.DataTable);
    
    // Load categories dynamically
    loadCategories();
    
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTable is not loaded!');
        return;
    }
    
    var table = $('#markdownTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.markdown.index') }}",
            data: function (d) {
                d.category = $('#categoryFilter').val();
                d.status = $('#statusFilter').val();
                d.published = $('#publishedFilter').val();
                console.log('AJAX data being sent:', d);
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', xhr.responseText);
                console.error('Error:', error);
                console.error('Thrown:', thrown);
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'title', name: 'title'},
            {data: 'category', name: 'category'},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'published_badge', name: 'is_published', orderable: false, searchable: false},
            {data: 'author_name', name: 'author.name'},
            {data: 'view_count', name: 'view_count'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            search: "Search files:",
            lengthMenu: "Show _MENU_ files per page",
            info: "Showing _START_ to _END_ of _TOTAL_ files",
            infoEmpty: "No files found",
            infoFiltered: "(filtered from _MAX_ total files)"
        }
    });

    // Filter functionality
    $('#categoryFilter, #statusFilter, #publishedFilter').change(function() {
        table.draw();
    });

    $('#clearFilters').click(function() {
        $('#categoryFilter, #statusFilter, #publishedFilter').val('');
        table.draw();
    });

    // Auto-refresh DataTable if there's a success message (after update/create/delete)
    @if(session('success'))
        // Small delay to ensure DataTable is fully initialized
        setTimeout(function() {
            table.ajax.reload(null, false); // false = keep current page
            console.log('DataTable refreshed due to success message');
        }, 500);
        
        // Auto-hide success message after 5 seconds
        setTimeout(function() {
            $('#successAlert').fadeOut('slow');
        }, 5000);
    @endif

    @if(session('error'))
        // Auto-hide error message after 8 seconds
        setTimeout(function() {
            $('#errorAlert').fadeOut('slow');
        }, 8000);
    @endif

    // Delete confirmation
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var url = form.attr('action');
        var title = $(this).data('title');
        
        if (confirm('Are you sure you want to delete "' + title + '"?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', xhr.responseText);
                    showAlert('error', 'An error occurred while deleting the file.');
                }
            });
        }
    });

    // Toggle status
    $(document).on('click', '.toggle-status', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    table.ajax.reload();
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the status.');
            }
        });
    });

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
                    table.ajax.reload();
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
                    table.ajax.reload();
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

    function loadCategories() {
        $.ajax({
            url: "{{ route('admin.markdown.categories') }}",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.categories) {
                    var categorySelect = $('#categoryFilter');
                    categorySelect.find('option:not(:first)').remove(); // Keep "All Categories" option
                    
                    response.categories.forEach(function(category) {
                        categorySelect.append(
                            '<option value="' + category.value + '">' + 
                            category.label + ' (' + category.count + ')</option>'
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading categories:', {
                    url: "{{ route('admin.markdown.categories') }}",
                    referrer: window.location.href,
                    timestamp: new Date().toISOString()
                });
                console.log('404 Error occurred:', {
                    url: "{{ route('admin.markdown.categories') }}",
                    referrer: window.location.href,
                    timestamp: new Date().toISOString()
                });
            }
        });
    }
});
</script>


<!-- DataTables CSS -->
<style>
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.375rem 0.75rem;
    margin-left: 2px;
    border-radius: 0.25rem;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}
</style>
@endpush
</x-layout>
