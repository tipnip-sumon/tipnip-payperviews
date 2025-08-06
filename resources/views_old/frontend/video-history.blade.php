<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    @section('content')
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <i class="fas fa-history"></i> Video Viewing History
                                </h4>
                                <p class="mb-0">Track all your watched videos and earnings history</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <h5 class="mb-1">
                                        <i class="fas fa-coins"></i> Total Earned: ${{ number_format($totalEarnings, 4) }}
                                    </h5>
                                    <small class="text-light">
                                        From {{ $viewHistory->total() }} video{{ $viewHistory->total() != 1 ? 's' : '' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-white mb-1">Videos Watched</p>
                                <h3 class="text-white mb-0">{{ number_format($totalVideosWatched) }}</h3>
                                <small class="text-light">{{ number_format($uniqueVideosWatched) }} unique videos</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-play-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-white mb-1">Total Earnings</p>
                                <h3 class="text-white mb-0">${{ number_format($totalEarnings, 4) }}</h3>
                                <small class="text-light">From video views</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-white mb-1">Average Per Video</p>
                                <h3 class="text-white mb-0">
                                    ${{ number_format($averagePerVideo, 4) }}
                                </h3>
                                <small class="text-light">Per video earning</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-white mb-1">This Month</p>
                                <h3 class="text-white mb-0">{{ number_format($thisMonthVideos) }}</h3>
                                <small class="text-light">${{ number_format($thisMonthEarnings, 4) }} earned</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        @if($topEarningVideos->count() > 0)
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-trophy text-warning"></i> Top Earning Videos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($topEarningVideos->take(3) as $topVideo)
                                <div class="col-md-4 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 30px;">
                                                <i class="fas fa-video text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 small">{{ Str::limit($topVideo->videoLink->title ?? 'N/A', 25) }}</h6>
                                            <small class="text-success">
                                                ${{ number_format($topVideo->total_earned, 4) }} 
                                                <span class="text-muted">({{ $topVideo->view_count }}x)</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-info"></i> Recent Activity
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Last 7 Days:</span>
                            <span class="badge bg-primary">{{ $recentActivity }} videos</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>This Week:</span>
                            <span class="badge bg-success">${{ number_format($thisWeekEarnings, 4) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Today:</span>
                            <span class="badge bg-info">${{ number_format($todayEarnings, 4) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Filter and Search Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('video.history') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Video</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Search by video title..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video History Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list"></i> Video Viewing History
                                @if($viewHistory->total() > 0)
                                    <span class="badge bg-secondary ms-2">{{ $viewHistory->total() }} records</span>
                                @endif
                            </h5>
                            <div class="btn-group">
                                @if($viewHistory->total() > 0)
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="exportHistory()">
                                        <i class="fas fa-download"></i> Export CSV
                                    </button>
                                @endif
                                <a href="{{ route('user.video-views.gallery') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-video"></i> Watch More Videos
                                </a>
                                <a href="{{ route('video.earnings') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-chart-bar"></i> Earnings Report
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($viewHistory->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Video Details</th>
                                            <th>Watched Date</th>
                                            <th>Earned Amount</th>
                                            <th>IP Address</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($viewHistory as $index => $view)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ ($viewHistory->currentPage() - 1) * $viewHistory->perPage() + $index + 1 }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-3">
                                                            @if($view->videoLink && $view->videoLink->thumbnail)
                                                                <img src="{{ $view->videoLink->thumbnail }}" 
                                                                     alt="Video Thumbnail" 
                                                                     class="rounded" 
                                                                     style="width: 60px; height: 40px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                     style="width: 60px; height: 40px;">
                                                                    <i class="fas fa-video text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">
                                                                {{ $view->videoLink->title ?? 'Video Not Available' }}
                                                            </h6>
                                                            @if($view->videoLink && $view->videoLink->description)
                                                                <small class="text-muted">
                                                                    {{ Str::limit($view->videoLink->description, 50) }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $view->viewed_at->format('M d, Y') }}</strong><br>
                                                        <small class="text-muted">{{ $view->viewed_at->format('h:i A') }}</small><br>
                                                        <small class="text-info">{{ $view->viewed_at->diffForHumans() }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-dollar-sign"></i> {{ number_format($view->earned_amount, 4) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted font-monospace">
                                                        {{ $view->ip_address }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($view->videoLink)
                                                            <a href="{{ route('video.show', $view->videoLink->id) }}" class="btn btn-outline-primary" title="View Video">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-outline-info view-details-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#viewDetailsModal"
                                                                    data-view="{{ json_encode($view) }}"
                                                                    title="View Details">
                                                                <i class="fas fa-info-circle"></i>
                                                            </button>
                                                        @else
                                                            <span class="badge bg-warning">Video Unavailable</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            @if($viewHistory->hasPages())
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <small class="text-muted">
                                            Showing {{ $viewHistory->firstItem() }} to {{ $viewHistory->lastItem() }} 
                                            of {{ $viewHistory->total() }} results
                                        </small>
                                    </div>
                                    <div>
                                        {{ $viewHistory->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-history fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted">No Video History Found</h4>
                                <p class="text-muted mb-4">
                                    @if(request()->hasAny(['date_from', 'date_to', 'search']))
                                        No videos match your current filters. Try adjusting your search criteria.
                                    @else
                                        You haven't watched any videos yet. Start watching to earn money!
                                    @endif
                                </p>
                                <div>
                                    @if(request()->hasAny(['date_from', 'date_to', 'search']))
                                        <a href="{{ route('video.history') }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-times"></i> Clear Filters
                                        </a>
                                    @endif
                                    <a href="{{ route('gallery') }}" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Start Watching Videos
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- View Details Modal -->
        <div class="modal fade" id="viewDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle"></i> Video View Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="modal-video-thumbnail" class="mb-3">
                                    <!-- Video thumbnail will be inserted here -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Video Title:</strong></td>
                                        <td id="modal-video-title">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Watched Date:</strong></td>
                                        <td id="modal-viewed-date">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Earned Amount:</strong></td>
                                        <td id="modal-earned-amount">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>IP Address:</strong></td>
                                        <td id="modal-ip-address">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>User Agent:</strong></td>
                                        <td id="modal-user-agent" class="text-break">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Video Description:</strong>
                                    <p id="modal-video-description" class="mb-0">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="button" class="btn btn-primary" id="modal-watch-again">
                            <i class="fas fa-play"></i> Watch Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Handle view details modal
            $('.view-details-btn').click(function() {
                const viewData = $(this).data('view');
                const videoLink = viewData.video_link;

                console.log('View Data:', viewData);
                // console.log('Video Link:', videoLink);
                
                // Populate modal with data
                $('#modal-video-title').text(videoLink ? videoLink.title : 'N/A');
                $('#modal-viewed-date').text(new Date(viewData.viewed_at).toLocaleString());
                $('#modal-earned-amount').html('<span class="badge bg-success">$' + parseFloat(viewData.earned_amount).toFixed(4) + '</span>');
                $('#modal-ip-address').text(viewData.ip_address);
                $('#modal-user-agent').text(viewData.user_agent || 'N/A');
                $('#modal-video-description').text(videoLink ? (videoLink.description || 'No description available') : 'N/A');
                
                // Set video thumbnail
                if (videoLink && videoLink.thumbnail) {
                    $('#modal-video-thumbnail').html(
                        '<img src="' + videoLink.thumbnail + '" alt="Video Thumbnail" class="img-fluid rounded">'
                    );
                } else {
                    $('#modal-video-thumbnail').html(
                        '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">' +
                        '<i class="fas fa-video fa-3x text-muted"></i>' +
                        '</div>'
                    );
                }
                
                // Set watch again button
                if (videoLink) {
                    $('#modal-watch-again').attr('onclick', 'window.open("{{ route("gallery") }}#video-' + videoLink.id + '", "_blank")');
                } else {
                    $('#modal-watch-again').prop('disabled', true);
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert-dismissible').fadeOut();
            }, 5000);

            // Set max date to today for date inputs
            const today = new Date().toISOString().split('T')[0];
            $('#date_from, #date_to').attr('max', today);
            
            // Validate date range
            $('#date_from, #date_to').change(function() {
                const fromDate = $('#date_from').val();
                const toDate = $('#date_to').val();
                
                if (fromDate && toDate && fromDate > toDate) {
                    alert('From date cannot be greater than To date');
                    $(this).val('');
                }
            });
        });

        // Export history function
        function exportHistory() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            
            // Show loading indicator
            const exportBtn = document.querySelector('button[onclick="exportHistory()"]');
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
            exportBtn.disabled = true;
            
            // Create temporary form to submit the export request
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("video.history") }}';
            
            // Add current filters as hidden inputs
            @if($filters['date_from'])
                const dateFromInput = document.createElement('input');
                dateFromInput.type = 'hidden';
                dateFromInput.name = 'date_from';
                dateFromInput.value = '{{ $filters["date_from"] }}';
                form.appendChild(dateFromInput);
            @endif
            
            @if($filters['date_to'])
                const dateToInput = document.createElement('input');
                dateToInput.type = 'hidden';
                dateToInput.name = 'date_to';
                dateToInput.value = '{{ $filters["date_to"] }}';
                form.appendChild(dateToInput);
            @endif
            
            @if($filters['search'])
                const searchInput = document.createElement('input');
                searchInput.type = 'hidden';
                searchInput.name = 'search';
                searchInput.value = '{{ $filters["search"] }}';
                form.appendChild(searchInput);
            @endif
            
            // Add export parameter
            const exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            exportInput.value = 'csv';
            form.appendChild(exportInput);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Reset button after a short delay
            setTimeout(function() {
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
            }, 2000);
        }
    </script>
    @endpush
</x-smart_layout>
