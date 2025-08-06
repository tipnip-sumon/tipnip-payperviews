<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    
    @section('content')
        <div class="container-fluid my-4">
            <!-- Statistics Cards Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $sponsorStats['total_sponsors'] ?? 0 }}</h3>
                            <p>Total Sponsors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $sponsorStats['active_sponsors'] ?? 0 }}</h3>
                            <p>Active Sponsors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>${{ number_format($sponsorStats['total_business'] ?? 0, 2) }}</h3>
                            <p>Total Business</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>${{ number_format($sponsorStats['total_commission'] ?? 0, 2) }}</h3>
                            <p>Total Commission</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters Row -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-filter mr-2"></i>Filters
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status_filter">Status</label>
                                        <select class="form-control" id="status_filter">
                                            <option value="">All Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                            <option value="2">Banned</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_from">From Date</label>
                                        <input type="date" class="form-control" id="date_from">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_to">To Date</label>
                                        <input type="date" class="form-control" id="date_to">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" class="btn btn-primary" id="apply_filters">
                                                <i class="fas fa-search"></i> Apply Filters
                                            </button>
                                            <button type="button" class="btn btn-secondary ml-2" id="reset_filters">
                                                <i class="fas fa-times"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main DataTable Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users mr-2"></i>{{ $pageTitle }}
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-info" id="refresh_table">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="sponsor_list" class="table table-vcenter text-nowrap table-bordered border-bottom">
                                    <thead>
                                        <tr>
                                            <th>S No.</th>
                                            <th>Joining Date</th>
                                            <th>User ID</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Country</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th>S No.</th>
                                            <th>Joining Date</th>
                                            <th>User ID</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Country</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sponsor Details Modal -->
        <div class="modal fade" id="sponsorDetailsModal" tabindex="-1" aria-labelledby="sponsorDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="sponsorDetailsModalLabel">
                            <i class="fas fa-user me-2"></i>Sponsor Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="sponsor_details_content">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading sponsor details...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Sponsor Modal -->
        <div class="modal fade" id="contactSponsorModal" tabindex="-1" aria-labelledby="contactSponsorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="contactSponsorModalLabel">
                            <i class="fas fa-envelope me-2"></i>Contact Sponsor
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="contactSponsorForm">
                        <div class="modal-body">
                            <input type="hidden" id="contact_sponsor_id" name="sponsor_id">
                            <div class="mb-3">
                                <label for="contact_subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="contact_subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_message" class="form-label">Message</label>
                                <textarea class="form-control" id="contact_message" name="message" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Performance Modal -->
        <div class="modal fade" id="performanceModal" tabindex="-1" aria-labelledby="performanceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="performanceModalLabel">
                            <i class="fas fa-chart-line me-2"></i>Sponsor Performance
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="performance_content">
                            <div class="text-center">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading performance data...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}">
        
        <!-- DataTables JS -->
        <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
        
        <!-- Sweet Alert -->
        <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
        
        <!-- Chart.js for performance charts -->
        <script src="{{ asset('assets/js/chart.min.js') }}"></script>
        
        <script type="text/javascript">
            // Declare variables in global scope
            var table;
            
            // Function to load and populate sponsor data
            function loadSponsorData() {
                // Get filter values
                var statusFilter = $('#status_filter').val();
                var dateFrom = $('#date_from').val();
                var dateTo = $('#date_to').val();
                
                // Build request data
                var requestData = {
                    draw: 1,
                    start: 0,
                    length: 25,
                    order: [{column: 1, dir: 'desc'}],
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'username', name: 'username'},
                        {data: 'name', name: 'name'},
                        {data: 'email', name: 'email'},
                        {data: 'phone', name: 'phone'},
                        {data: 'status', name: 'status'},
                        {data: 'country', name: 'country'},
                        {data: 'actions', name: 'actions'}
                    ]
                };
                
                // Add filters to request
                if (statusFilter) requestData.status_filter = statusFilter;
                if (dateFrom) requestData.date_from = dateFrom;
                if (dateTo) requestData.date_to = dateTo;
                
                // Manual AJAX call to load sponsor data
                $.ajax({
                    url: "{{ route('user.sponsor-list') }}",
                    type: "GET",
                    data: requestData,
                    success: function(data) {
                        console.log('✅ Manual AJAX Success:', data);
                        
                        if (data.data && data.data.length > 0) {
                            console.log('📝 Manually populating table...');
                            
                            // Clear existing table body
                            $('#sponsor_list tbody').empty();
                            
                            // Add each row manually
                            data.data.forEach(function(row, index) {
                                var tableRow = '<tr>' +
                                    '<td class="text-center">' + row.DT_RowIndex + '</td>' +
                                    '<td class="text-center">' + row.created_at + '</td>' +
                                    '<td class="text-center">' + row.username + '</td>' +
                                    '<td>' + row.name + '</td>' +
                                    '<td>' + row.email + '</td>' +
                                    '<td class="text-center">' + row.phone + '</td>' +
                                    '<td class="text-center">' + row.status + '</td>' +
                                    '<td class="text-center">' + row.country + '</td>' +
                                    '<td class="text-center">' + row.actions + '</td>' +
                                '</tr>';
                                
                                $('#sponsor_list tbody').append(tableRow);
                            });
                            
                            console.log('✅ Table manually populated with', data.data.length, 'rows');
                            
                            // Update pagination info manually
                            $('.dataTables_info').html('Showing 1 to ' + data.data.length + ' of ' + data.recordsTotal + ' sponsors');
                            
                            // Reinitialize DataTables on the populated table
                            if (table) {
                                table.destroy();
                            }
                            
                            table = $('#sponsor_list').DataTable({
                                searching: true,
                                paging: true,
                                ordering: true,
                                info: true,
                                pageLength: 25,
                                destroy: true
                            });
                            
                        } else {
                            // No data found
                            $('#sponsor_list tbody').empty();
                            $('#sponsor_list tbody').append('<tr><td colspan="9" class="text-center">No sponsors found</td></tr>');
                            $('.dataTables_info').html('No sponsors to display');
                        }
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ Manual AJAX Error:', error);
                        $('#sponsor_list tbody').empty();
                        $('#sponsor_list tbody').append('<tr><td colspan="9" class="text-center text-danger">Error loading sponsor data</td></tr>');
                    }
                });
            }
            
            $(document).ready(function() {
                console.log('Document ready - testing manual table population');
                
                // Initial load
                loadSponsorData();

                // Apply Filters
                $('#apply_filters').click(function() {
                    loadSponsorData();
                });

                // Reset Filters
                $('#reset_filters').click(function() {
                    $('#status_filter').val('');
                    $('#date_from').val('');
                    $('#date_to').val('');
                    loadSponsorData();
                });

                // Refresh Table
                $('#refresh_table').click(function() {
                    loadSponsorData();
                    Swal.fire({
                        title: 'Refreshed!',
                        text: 'Sponsor list has been refreshed.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });

                // View sponsor details
                $(document).on('click', '.view-sponsor', function() {
                    var sponsorId = $(this).data('id');
                    var sponsorName = $(this).data('name');
                    
                    $('#sponsorDetailsModalLabel').html('<i class="fas fa-user me-2"></i>Sponsor Details - ' + sponsorName);
                    $('#sponsor_details_content').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading sponsor details...</p></div>');
                    
                    // Show modal using Bootstrap 5 syntax
                    var sponsorModal = new bootstrap.Modal(document.getElementById('sponsorDetailsModal'));
                    sponsorModal.show();
                    
                    // Fetch sponsor details via AJAX
                    $.ajax({
                        url: "{{ url('/user/sponsor') }}/" + sponsorId + "/details",
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                var sponsor = response.sponsor;
                                var stats = response.statistics;
                                
                                var detailsHtml = `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Basic Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr><td><strong>Username:</strong></td><td>${sponsor.username}</td></tr>
                                                        <tr><td><strong>Full Name:</strong></td><td>${sponsor.firstname} ${sponsor.lastname}</td></tr>
                                                        <tr><td><strong>Email:</strong></td><td>${sponsor.email || 'N/A'}</td></tr>
                                                        <tr><td><strong>Phone:</strong></td><td>${sponsor.mobile || 'N/A'}</td></tr>
                                                        <tr><td><strong>Country:</strong></td><td>${sponsor.country || 'N/A'}</td></tr>
                                                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-${sponsor.status == 1 ? 'success' : 'warning'}">${sponsor.status == 1 ? 'Active' : 'Inactive'}</span></td></tr>
                                                        <tr><td><strong>Joined:</strong></td><td>${sponsor.created_at}</td></tr>
                                                        <tr><td><strong>Last Login:</strong></td><td>${sponsor.last_login}</td></tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Financial Overview</h5>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr><td><strong>Deposit Wallet:</strong></td><td>$${sponsor.deposit_wallet}</td></tr>
                                                        <tr><td><strong>Interest Wallet:</strong></td><td>$${sponsor.interest_wallet}</td></tr>
                                                        <tr><td><strong>Total Deposits:</strong></td><td>$${stats.total_deposits.toLocaleString()}</td></tr>
                                                        <tr><td><strong>Total Withdrawals:</strong></td><td>$${stats.total_withdrawals.toLocaleString()}</td></tr>
                                                        <tr><td><strong>Total Investments:</strong></td><td>$${stats.total_investments.toLocaleString()}</td></tr>
                                                        <tr><td><strong>Referral Count:</strong></td><td>${stats.referral_count}</td></tr>
                                                        <tr><td><strong>Referral Earnings:</strong></td><td>$${stats.referral_earnings.toLocaleString()}</td></tr>
                                                        <tr><td><strong>Video Earnings:</strong></td><td>$${stats.video_earnings.toLocaleString()}</td></tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                
                                $('#sponsor_details_content').html(detailsHtml);
                            } else {
                                $('#sponsor_details_content').html('<div class="alert alert-danger">' + response.message + '</div>');
                            }
                        },
                        error: function() {
                            $('#sponsor_details_content').html('<div class="alert alert-danger">Failed to load sponsor details.</div>');
                        }
                    });
                });

                // Contact sponsor
                $(document).on('click', '.contact-sponsor', function() {
                    var sponsorId = $(this).data('id');
                    var sponsorName = $(this).data('name') || $(this).data('username') || 'Sponsor';
                    var sponsorEmail = $(this).data('email');
                    
                    console.log('Contact sponsor clicked:', {sponsorId, sponsorName, sponsorEmail});
                    
                    $('#contact_sponsor_id').val(sponsorId);
                    $('#contactSponsorModalLabel').html('<i class="fas fa-envelope me-2"></i>Contact ' + sponsorName);
                    $('#contactSponsorForm')[0].reset();
                    $('#contact_sponsor_id').val(sponsorId); // Set again after reset
                    
                    // Show modal using Bootstrap 5 syntax
                    var contactModal = new bootstrap.Modal(document.getElementById('contactSponsorModal'));
                    contactModal.show();
                });

                // Submit contact form
                $('#contactSponsorForm').submit(function(e) {
                    e.preventDefault();
                    
                    var formData = $(this).serialize();
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalText = submitBtn.html();
                    
                    // Disable submit button
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
                    
                    $.ajax({
                        url: "{{ route('user.sponsor.contact') }}",
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Hide modal using Bootstrap 5 syntax
                            var contactModal = bootstrap.Modal.getInstance(document.getElementById('contactSponsorModal'));
                            contactModal.hide();
                            
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    html: response.message + '<br><br><small class="text-muted">You can view your sent messages in the <a href="{{ route("user.messages.sent") }}" class="text-decoration-underline">Messages Dashboard</a></small>',
                                    icon: 'success',
                                    confirmButtonText: 'View Messages',
                                    showCancelButton: true,
                                    cancelButtonText: 'Close'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '{{ route("user.messages.sent") }}';
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to send message',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Contact error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to send message. Please try again.',
                                icon: 'error'
                            });
                        },
                        complete: function() {
                            // Re-enable submit button
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                // View performance
                $(document).on('click', '.view-performance', function() {
                    var sponsorId = $(this).data('id');
                    var sponsorName = $(this).data('name') || $(this).data('username') || 'Sponsor';
                    
                    $('#performanceModalLabel').html('<i class="fas fa-chart-line me-2"></i>' + sponsorName + ' Performance');
                    $('#performance_content').html('<div class="text-center"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading performance data...</p></div>');
                    
                    // Show modal using Bootstrap 5 syntax
                    var performanceModal = new bootstrap.Modal(document.getElementById('performanceModal'));
                    performanceModal.show();
                    
                    // Fetch performance data via AJAX
                    $.ajax({
                        url: "{{ url('/user/sponsor') }}/" + sponsorId + "/performance",
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                // Create performance charts and data display
                                var performanceHtml = `
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Monthly Performance Chart</h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="performanceChart" height="100"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Referral Network</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Direct Referrals:</strong> ${response.referral_tree.direct_referrals}</p>
                                                    <p><strong>Total Network:</strong> ${response.referral_tree.total_network}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Recent Activity</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p>Performance data for the last 12 months</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                
                                $('#performance_content').html(performanceHtml);
                                
                                // Create chart
                                var ctx = document.getElementById('performanceChart').getContext('2d');
                                var chart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: response.monthly_performance.map(item => item.month),
                                        datasets: [{
                                            label: 'Total Activity',
                                            data: response.monthly_performance.map(item => item.total_activity),
                                            borderColor: 'rgb(75, 192, 192)',
                                            tension: 0.1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            } else {
                                $('#performance_content').html('<div class="alert alert-danger">' + response.message + '</div>');
                            }
                        },
                        error: function() {
                            $('#performance_content').html('<div class="alert alert-danger">Failed to load performance data.</div>');
                        }
                    });
                });

                // Auto-apply filters on change
                $('#status_filter, #date_from, #date_to').change(function() {
                    // Automatically reload data when filters change
                    loadSponsorData();
                });
            });
        </script>
        
        <style>
            .user-info {
                line-height: 1.2;
            }
            .btn-group .btn {
                margin-right: 2px;
            }
            .card-tools .btn-tool {
                color: #495057;
            }
            .dataTables_processing {
                background: rgba(255, 255, 255, 0.8);
            }
            .table th {
                border-top: none;
                font-weight: 600;
                background-color: #f8f9fa;
            }
            .badge {
                font-size: 0.875em;
            }
            .small-box {
                border-radius: 0.375rem;
                box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
                margin-bottom: 20px;
            }
            .small-box .inner {
                padding: 10px;
            }
            .small-box .icon {
                position: absolute;
                top: auto;
                bottom: 10px;
                right: 10px;
                font-size: 40px;
                opacity: 0.3;
            }
            .modal-lg {
                max-width: 900px;
            }
            .modal-xl {
                max-width: 1200px;
            }
        </style>
    @endpush
</x-smart_layout>