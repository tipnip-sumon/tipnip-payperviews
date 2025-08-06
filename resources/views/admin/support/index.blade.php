<x-layout>

@section('title', 'Support Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Support Dashboard</h1>
            <p class="text-muted">Manage and respond to customer support tickets</p>
        </div>
        <div>
            <a href="{{ route('admin.support.tickets') }}" class="btn btn-primary">
                <i class="fas fa-tickets-alt me-2"></i>View All Tickets
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 my-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Open Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['open_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Closed Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['closed_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Today's Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Avg Response Time
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['avg_response_time'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-stopwatch fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Satisfaction Rating
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['satisfaction_rating'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Tickets</h6>
                    <a href="{{ route('admin.support.tickets') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div id="recent-tickets-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.support.tickets') }}?status=open" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-folder-open text-success me-2"></i>
                                View Open Tickets
                            </div>
                            <span class="badge bg-success rounded-pill">{{ $stats['open_tickets'] }}</span>
                        </a>
                        <a href="{{ route('admin.support.tickets') }}?status=pending" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-clock text-warning me-2"></i>
                                View Pending Tickets
                            </div>
                            <span class="badge bg-warning rounded-pill">{{ $stats['pending_tickets'] }}</span>
                        </a>
                        <a href="{{ route('admin.support.tickets') }}?priority=high" class="list-group-item list-group-item-action">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                            High Priority Tickets
                        </a>
                        <a href="{{ route('admin.support.tickets') }}?starred=1" class="list-group-item list-group-item-action">
                            <i class="fas fa-star text-warning me-2"></i>
                            Starred Tickets
                        </a>
                        <a href="{{ route('admin.support.export') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-download text-info me-2"></i>
                            Export Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Load recent tickets
    loadRecentTickets();
    
    function loadRecentTickets() {
        $.get('{{ route("admin.support.tickets") }}', {
            ajax: true,
            length: 5,
            order: [[5, 'desc']] // Order by created_at desc
        })
        .done(function(response) {
            if (response.data && response.data.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Subject</th><th>User</th><th>Status</th><th>Created</th></tr></thead><tbody>';
                
                response.data.forEach(function(ticket) {
                    html += `<tr>
                        <td><a href="#" class="view-ticket" data-id="${ticket.id}">${ticket.subject}</a></td>
                        <td>${ticket.user}</td>
                        <td>${ticket.status_badge}</td>
                        <td>${new Date(ticket.created_at).toLocaleDateString()}</td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                $('#recent-tickets-container').html(html);
            } else {
                $('#recent-tickets-container').html('<div class="text-center py-4 text-muted">No recent tickets found</div>');
            }
        })
        .fail(function() {
            $('#recent-tickets-container').html('<div class="text-center py-4 text-danger">Failed to load recent tickets</div>');
        });
    }
    
    // Handle view ticket clicks
    $(document).on('click', '.view-ticket', function(e) {
        e.preventDefault();
        window.location.href = '{{ route("admin.support.tickets") }}';
    });
});
</script>
@endpush
@endsection
</x-layout>
