<x-layout>
    <x-slot name="title">Markdown Statistics</x-slot>
@section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Markdown Statistics</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.markdown.index') }}">Markdown Files</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Statistics</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row">
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-lg bg-primary">
                                <i class="ri-file-text-line fs-18"></i>
                            </span>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0 text-muted">Total Files</p>
                            <h4 class="fw-semibold mb-0">{{ $stats['total_files'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-lg bg-success">
                                <i class="ri-eye-line fs-18"></i>
                            </span>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0 text-muted">Published</p>
                            <h4 class="fw-semibold mb-0">{{ $stats['published_files'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-lg bg-warning">
                                <i class="ri-star-line fs-18"></i>
                            </span>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0 text-muted">Featured</p>
                            <h4 class="fw-semibold mb-0">{{ $stats['featured_files'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-lg bg-info">
                                <i class="ri-bar-chart-line fs-18"></i>
                            </span>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0 text-muted">Total Views</p>
                            <h4 class="fw-semibold mb-0">{{ number_format($stats['total_views']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Files by Category -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Files by Category</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Count</th>
                                    <th>Published</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['by_category'] as $category => $data)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($category) }}</span>
                                    </td>
                                    <td>{{ $data['count'] }}</td>
                                    <td>{{ $data['published'] }}</td>
                                    <td>{{ number_format($data['views']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files by Status -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Files by Status</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['by_status'] as $status => $count)
                                <tr>
                                    <td>
                                        @if($status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($status == 'inactive')
                                            <span class="badge bg-danger">Inactive</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $count }}</td>
                                    <td>
                                        @if($stats['total_files'] > 0)
                                            {{ round(($count / $stats['total_files']) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Most Viewed Files -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Most Viewed Files</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Views</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['most_viewed'] as $file)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.markdown.show', $file->id) }}" 
                                           class="text-decoration-none">
                                            {{ Str::limit($file->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($file->category) }}</span>
                                    </td>
                                    <td>{{ number_format($file->view_count) }}</td>
                                    <td>
                                        <a href="{{ route('admin.markdown.edit', $file->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No files found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Files -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Recently Created Files</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_files'] as $file)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.markdown.show', $file->id) }}" 
                                           class="text-decoration-none">
                                            {{ Str::limit($file->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($file->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($file->status == 'inactive')
                                            <span class="badge bg-danger">Inactive</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $file->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.markdown.edit', $file->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No files found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Additional Statistics</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <h5 class="mb-1">{{ number_format($stats['avg_views']) }}</h5>
                                <p class="text-muted mb-0">Average Views per File</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <h5 class="mb-1">{{ $stats['files_with_physical_files'] }}</h5>
                                <p class="text-muted mb-0">Files with Physical Files</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <h5 class="mb-1">{{ $stats['files_this_month'] }}</h5>
                                <p class="text-muted mb-0">Files Created This Month</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <h5 class="mb-1">{{ $stats['updated_this_week'] }}</h5>
                                <p class="text-muted mb-0">Updated This Week</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
</x-layout>
