<x-smart_layout>
    @section('top_title',$pageTitle)
    @section('title',$pageTitle)
    @section('content')
        <div class="row my-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users"></i> My Team Network</h5>
                    </div>
                    <div class="card-body">
                        <!-- Team Statistics -->
                        <div class="row mb-4" id="teamStats" style="display: none;">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 id="totalMembers">0</h3>
                                        <p>Total Members</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 id="activeMembers">0</h3>
                                        <p>Active Members</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3 id="inactiveMembers">0</h3>
                                        <p>Inactive Members</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 id="totalBusiness">$0</h3>
                                        <p>Total Business</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Level Filter Buttons -->
                        <div class="row mb-4" id="levelButtons" style="display: none;">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-layer-group"></i> Filter by Level <span id="levelCount" class="badge bg-info ml-2">0</span></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="btn-group-wrap mb-3" id="dynamicLevelButtons">
                                            <button type="button" class="btn btn-outline-primary level-btn" data-level="all">All Levels</button>
                                            <!-- Dynamic level buttons will be inserted here -->
                                        </div>
                                        <div class="row" id="levelStats" style="display: none;">
                                            <div class="col-md-2">
                                                <div class="card bg-info text-white">
                                                    <div class="card-body text-center p-2">
                                                        <h5 id="levelTotalMembers">0</h5>
                                                        <small>Total Members</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card bg-success text-white">
                                                    <div class="card-body text-center p-2">
                                                        <h5 id="levelActiveMembers">0</h5>
                                                        <small>Active</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card bg-warning text-white">
                                                    <div class="card-body text-center p-2">
                                                        <h5 id="levelInactiveMembers">0</h5>
                                                        <small>Inactive</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card bg-primary text-white">
                                                    <div class="card-body text-center p-2">
                                                        <h5 id="levelTotalBalance">$0</h5>
                                                        <small>Total Balance</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card bg-secondary text-white">
                                                    <div class="card-body text-center p-2">
                                                        <h5 id="levelAvgBalance">$0</h5>
                                                        <small>Avg Balance</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card bg-success text-white">
                                                    <div class="card-body text-center p-2">
                                                        <h5 id="levelTotalReferrals">0</h5>
                                                        <small>Total Referrals</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Loading Indicator -->
                        <div id="loadingIndicator" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Loading team data...</p>
                        </div>
                        
                        <!-- Error Message -->
                        <div id="errorMessage" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> <span id="errorText">Error loading data</span>
                        </div>
                        
                        <!-- Success Message -->
                        <div id="successMessage" class="alert alert-success" style="display: none;">
                            <i class="fas fa-check-circle"></i> <span id="successText">Success</span>
                        </div>
                        
                        <!-- Team List View -->
                        <div id="listContainer" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-list"></i> <span id="listTitle">Team Members</span></h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="teamTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Level</th>
                                                    <th>Status</th>
                                                    <th>Balance</th>
                                                    <th>Joined Date</th>
                                                    <th>Referrals</th>
                                                </tr>
                                            </thead>
                                            <tbody id="teamTableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
      @push('script')
        <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/js/dataTables.bootstrap4.min.js')}}"></script>
        <script src="{{asset('assets/js/dataTables.responsive.min.js')}}"></script>
        <script src="{{asset('assets/js/responsive.bootstrap4.min.js')}}"></script>
        <style>
            .tree-view {
                font-family: Arial, sans-serif;
            }
            .tree-node {
                margin: 10px 0;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                background-color: #f9f9f9;
            }
            .tree-node.level-0 { margin-left: 0px; background-color: #e3f2fd; }
            .tree-node.level-1 { margin-left: 20px; background-color: #f3e5f5; }
            .tree-node.level-2 { margin-left: 40px; background-color: #e8f5e8; }
            .tree-node.level-3 { margin-left: 60px; background-color: #fff3e0; }
            .tree-node.level-4 { margin-left: 80px; background-color: #fce4ec; }
            .tree-node.level-5 { margin-left: 100px; background-color: #f1f8e9; }
            .user-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .user-details {
                flex: 1;
            }
            .user-status {
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: bold;
            }
            .status-active { background-color: #4caf50; color: white; }
            .status-inactive { background-color: #f44336; color: white; }
            .user-balance {
                font-weight: bold;
                color: #2196f3;
            }
            .btn-group .btn {
                margin-right: 2px;
            }
            .btn-group .btn:last-child {
                margin-right: 0;
            }
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
            
            /* Modal close button styles */
            .modal-close-btn {
                background: none;
                border: none;
                font-size: 1.5rem;
                font-weight: 700;
                line-height: 1;
                color: #000;
                text-shadow: 0 1px 0 #fff;
                opacity: 0.5;
                cursor: pointer;
                padding: 0;
                margin: -1rem -1rem -1rem auto;
            }
            
            .modal-close-btn:hover,
            .modal-close-btn:focus {
                color: #000;
                text-decoration: none;
                opacity: 0.75;
                outline: none;
            }
            
            .modal-close-btn span {
                display: block;
                width: 24px;
                height: 24px;
                line-height: 24px;
                text-align: center;
                font-size: 18px;
                font-weight: bold;
            }
            
            /* Ensure proper modal header spacing */
            .modal-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 1rem 1rem;
                border-bottom: 1px solid #dee2e6;
                border-top-left-radius: calc(0.3rem - 1px);
                border-top-right-radius: calc(0.3rem - 1px);
            }
            
            /* Level button styles */
            .level-btn {
                margin: 2px;
                font-size: 0.9rem;
                border-radius: 20px;
                transition: all 0.3s ease;
            }
            
            .level-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            
            .level-btn.active {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            }
            
            /* Level stats cards */
            #levelStats .card {
                transition: all 0.3s ease;
                border-radius: 10px;
            }
            
            #levelStats .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            
            /* Enhanced table styling */
            #teamTable {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                font-size: 14px;
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                font-feature-settings: "liga", "kern";
            }
            
            #teamTable td, #teamTable th {
                vertical-align: middle;
                padding: 12px 8px;
                border: 1px solid #dee2e6;
                font-size: 14px;
                font-weight: normal;
                text-align: left;
                background-color: #fff;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            
            #teamTable th {
                background-color: #f8f9fa;
                font-weight: 600;
                color: #495057;
                border-bottom: 2px solid #dee2e6;
            }
            
            #teamTable tbody tr:hover {
                background-color: #f5f5f5;
            }
            
            #teamTable tbody tr:hover td {
                background-color: #f5f5f5;
            }
            
            /* Ensure text in table body is clear and readable */
            #teamTableBody td {
                font-size: 14px;
                line-height: 1.4;
                color: #212529;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                font-feature-settings: "liga", "kern";
            }
            
            /* Specific cell styling for clarity */
            .username-cell, .name-cell, .email-cell, .date-cell {
                font-weight: 500;
                color: #495057;
            }
            
            .level-cell, .status-cell, .referral-cell {
                text-align: center;
                transform: none !important;
                filter: none !important;
            }
            
            .balance-cell {
                text-align: right;
                font-weight: 600;
            }
            
            /* Fix any transform issues that might cause blur - simplified */
            #teamTable {
                transform: none !important;
                backface-visibility: visible !important;
                perspective: none !important;
            }
            
            #teamTable td, #teamTable th {
                transform: none !important;
                backface-visibility: visible !important;
                perspective: none !important;
            }
            
            /* Reset transforms for badge elements to prevent blur */
            #teamTable .badge {
                transform: none !important;
                backface-visibility: visible !important;
                perspective: none !important;
                filter: none !important;
                will-change: auto !important;
            }
            
            .badge {
                font-size: 13px !important;
                padding: 0.3em 0.6em !important;
                font-weight: 600 !important;
                text-rendering: optimizeLegibility !important;
                -webkit-font-smoothing: antialiased !important;
                -moz-osx-font-smoothing: grayscale !important;
                transform: none !important;
                backface-visibility: visible !important;
                perspective: none !important;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
                line-height: 1.3 !important;
                display: inline-block !important;
                text-align: center !important;
                white-space: nowrap !important;
                vertical-align: baseline !important;
                border-radius: 0.25rem !important;
                letter-spacing: 0.01em !important;
                filter: none !important;
                will-change: auto !important;
            }
            
            /* Force clear rendering for specific badge types */
            .badge-success, .badge-danger, .badge-info, .badge-secondary,
            .bg-success, .bg-danger, .bg-info, .bg-secondary {
                transform: none !important;
                filter: none !important;
                opacity: 1 !important;
                backface-visibility: visible !important;
                perspective: none !important;
                will-change: auto !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                text-rendering: optimizeLegibility !important;
                -webkit-font-smoothing: antialiased !important;
                -moz-osx-font-smoothing: grayscale !important;
            }
            
            /* Responsive improvements */
            @media (max-width: 768px) {
                .level-btn {
                    font-size: 0.8rem;
                    padding: 0.25rem 0.5rem;
                }
                
                #levelStats .col-md-2 {
                    margin-bottom: 10px;
                }
                
                .btn-group .btn {
                    padding: 0.2rem 0.4rem;
                    font-size: 0.75rem;
                }
                
                #teamTable, #teamTable td, #teamTable th {
                    font-size: 13px;
                }
            }
            
            /* DataTables specific styling improvements */
            .dataTables_wrapper {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
            
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                font-size: 14px;
                color: #495057;
            }
            
            /* Fix any potential blur from CSS filters or transforms */
            .table-responsive {
                -webkit-overflow-scrolling: touch;
                overflow-x: auto;
            }
            
            /* Ensure crisp text rendering on all browsers - but exclude transforms */
            * {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-rendering: optimizeLegibility;
            }
            
            /* Specific override for badges to ensure no transforms */
            .badge, span.badge, .status-cell .badge, .level-cell .badge, .referral-cell .badge,
            .badge.bg-success, .badge.bg-danger, .badge.bg-info, .badge.bg-secondary {
                transform: none !important;
                backface-visibility: visible !important;
                perspective: none !important;
                filter: none !important;
                will-change: auto !important;
            }
            
            /* Clear text class for badges */
            .clear-text {
                transform: none !important;
                backface-visibility: visible !important;
                perspective: none !important;
                filter: none !important;
                will-change: auto !important;
                -webkit-font-smoothing: antialiased !important;
                -moz-osx-font-smoothing: grayscale !important;
                text-rendering: optimizeLegibility !important;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
                font-weight: 600 !important;
                font-size: 13px !important;
                line-height: 1.3 !important;
                letter-spacing: 0.01em !important;
            }
        </style>
        
        <script type="text/javascript">
            $(document).ready(function() {
                
                let currentLevel = null; // Start with null, will be set after loading available levels
                let allTeamData = null;
                let availableLevels = [];

                // Level button click handler
                $(document).on('click', '.level-btn', function() {
                    const level = $(this).data('level');
                    
                    // Prevent clicks on disabled buttons
                    if ($(this).prop('disabled')) {
                        return false;
                    }
                    
                    // Prevent multiple clicks on the same level
                    if (currentLevel === level) {
                        return false;
                    }
                    
                    // Disable all buttons during loading
                    $('.level-btn').prop('disabled', true);
                    
                    // Update active button
                    $('.level-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
                    $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
                    
                    currentLevel = level;
                    
                    if (level === 'all') {
                        // Show all team data
                        fetchTeamData();
                    } else {
                        // Fetch specific level data
                        fetchLevelData(level);
                    }
                    
                    // Fallback: Re-enable buttons after 15 seconds in case something goes wrong
                    setTimeout(function() {
                        if ($('.level-btn').prop('disabled')) {
                            $('.level-btn').prop('disabled', false);
                        }
                    }, 15000);
                });

                // Load available levels first, then load the first available level
                loadAvailableLevels();
                
                function loadAvailableLevels() {
                    $('#loadingIndicator').show();
                    $('#levelButtons').hide();
                    
                    $.ajax({
                        url: '{{ route("user.team-tree.available-levels") }}',
                        type: 'GET',
                        dataType: 'json',
                        cache: false,
                        timeout: 10000,
                        success: function(response) {
                            try {
                                if (response.status === 'success') {
                                    availableLevels = response.available_levels;
                                    createDynamicLevelButtons(availableLevels);
                                    
                                    if (availableLevels.length > 0) {
                                        // Load the first available level by default
                                        const firstLevel = availableLevels[0].level;
                                        fetchLevelData(firstLevel);
                                        currentLevel = firstLevel;
                                    } else {
                                        hideLoading();
                                        showError('No team members found at any level');
                                    }
                                    
                                    $('#levelButtons').show();
                                } else {
                                    showError('Failed to load available levels: ' + (response.message || 'Unknown error'));
                                }
                            } catch (e) {
                                showError('Error processing available levels');
                            }
                        },
                        error: function(xhr, status, error) {
                            hideLoading();
                            
                            let errorMessage = 'Failed to load available levels';
                            if (status === 'timeout') {
                                errorMessage = 'Request timed out. Please try again.';
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            showError(errorMessage);
                        }
                    });
                }
                
                function createDynamicLevelButtons(levels) {
                    const container = $('#dynamicLevelButtons');
                    
                    // Keep the "All Levels" button and clear the rest
                    container.find('.level-btn:not([data-level="all"])').remove();
                    
                    // Add dynamic level buttons
                    levels.forEach(function(levelData, index) {
                        const isFirst = index === 0;
                        const buttonClass = isFirst ? 'btn btn-primary level-btn active' : 'btn btn-outline-primary level-btn';
                        
                        const button = `<button type="button" class="${buttonClass}" data-level="${levelData.level}">
                            Level ${levelData.level} <span class="badge badge-light">${levelData.count}</span>
                        </button>`;
                        
                        container.append(button);
                    });
                    
                    // Update level count badge
                    $('#levelCount').text(levels.length);
                }
                
                function fetchTeamData() {
                    // Show loading
                    $('#loadingIndicator').show();
                    $('#listContainer').hide();
                    $('#levelStats').hide();
                    $('#errorMessage').hide();
                    $('#successMessage').hide();
                    
                    // Clear existing table data
                    if ($.fn.DataTable.isDataTable('#teamTable')) {
                        $('#teamTable').DataTable().destroy();
                    }
                    $('#teamTableBody').empty();
                    
                    $.ajax({
                        url: '{{ route("user.team-tree.data") }}',
                        type: 'GET',
                        dataType: 'json',
                        cache: false,
                        timeout: 15000,
                        success: function(response) {
                            try {
                                hideLoading();
                                if (response.status === 'success') {
                                    allTeamData = response;
                                    displayTeamStats(response.statistics);
                                    displayAllTeamList(response.team_tree);
                                    $('#levelStats').hide();
                                    $('#listTitle').text('All Team Members');
                                    showSuccess('All team data loaded successfully');
                                } else {
                                    showError('Failed to load team data: ' + (response.message || 'Unknown error'));
                                }
                            } catch (e) {
                                showError('Error processing team data');
                            } finally {
                                $('.level-btn').prop('disabled', false);
                            }
                        },
                        error: function(xhr, status, error) {
                            hideLoading();
                            
                            let errorMessage = 'Failed to load team data';
                            if (status === 'timeout') {
                                errorMessage = 'Request timed out. Please try again.';
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            showError(errorMessage);
                            $('.level-btn').prop('disabled', false);
                        },
                        complete: function() {
                            hideLoading();
                            $('.level-btn').prop('disabled', false);
                        }
                    });
                }

                function fetchLevelData(level) {
                    // Show loading
                    $('#loadingIndicator').show();
                    $('#listContainer').hide();
                    $('#teamStats').hide();
                    $('#errorMessage').hide();
                    $('#successMessage').hide();
                    
                    // Clear existing table data immediately
                    if ($.fn.DataTable.isDataTable('#teamTable')) {
                        $('#teamTable').DataTable().destroy();
                    }
                    $('#teamTableBody').empty();
                    
                    // Update active button
                    $('.level-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
                    $(`.level-btn[data-level="${level}"]`).removeClass('btn-outline-primary').addClass('btn-primary active');
                    
                    // Show loading for level stats
                    $('#levelStats').show();
                    $('#levelTotalMembers, #levelActiveMembers, #levelInactiveMembers, #levelTotalBalance, #levelAvgBalance, #levelTotalReferrals').html('<i class="fas fa-spinner fa-spin"></i>');
                    
                    $.ajax({
                        url: '{{ route("user.team-tree.level") }}',
                        type: 'GET',
                        data: { level: level },
                        dataType: 'json',
                        cache: false, // Prevent caching
                        timeout: 10000, // 10 second timeout
                        success: function(response) {
                            try {
                                hideLoading();
                                
                                if (response.status === 'success') {
                                    displayLevelStats(response.statistics);
                                    displayLevelMembers(response.members, level);
                                    $('#listTitle').text(`Level ${level} Team Members (${response.members.length} found)`);
                                    
                                    if (response.members.length > 0) {
                                        showSuccess(`Level ${level} data loaded successfully`);
                                    } else {
                                        showSuccess(`Level ${level} has no members`);
                                    }
                                } else {
                                    showError('Failed to load level data: ' + (response.message || 'Unknown error'));
                                }
                            } catch (e) {
                                console.error('Error processing response:', e);
                                showError('Error processing level data');
                            } finally {
                                // Always re-enable buttons
                                $('.level-btn').prop('disabled', false);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error loading level ${level}:`, {xhr, status, error});
                            
                            hideLoading();
                            
                            let errorMessage = 'Failed to load level data';
                            if (status === 'timeout') {
                                errorMessage = 'Request timed out. Please try again.';
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (error) {
                                errorMessage += ': ' + error;
                            }
                            
                            showError(errorMessage);
                            
                            // Always re-enable buttons
                            $('.level-btn').prop('disabled', false);
                        },
                        complete: function() {
                            // This runs whether success or error
                            hideLoading();
                            $('.level-btn').prop('disabled', false);
                            console.log(`Level ${level} request completed`);
                        }
                    });
                }

                function displayLevelStats(stats) {
                    try {
                        if (!stats || typeof stats !== 'object') {
                            console.warn('Invalid stats data:', stats);
                            stats = {
                                total_members: 0,
                                active_members: 0,
                                inactive_members: 0,
                                total_balance: 0,
                                average_balance: 0,
                                total_referrals: 0
                            };
                        }
                        
                        $('#levelTotalMembers').text(stats.total_members || 0);
                        $('#levelActiveMembers').text(stats.active_members || 0);
                        $('#levelInactiveMembers').text(stats.inactive_members || 0);
                        $('#levelTotalBalance').text('$' + parseFloat(stats.total_balance || 0).toFixed(2));
                        $('#levelAvgBalance').text('$' + parseFloat(stats.average_balance || 0).toFixed(2));
                        $('#levelTotalReferrals').text(stats.total_referrals || 0);
                        
                        console.log('Level stats updated successfully');
                    } catch (error) {
                        console.error('Error updating level stats:', error);
                        // Set default values on error
                        $('#levelTotalMembers, #levelActiveMembers, #levelInactiveMembers, #levelTotalReferrals').text('0');
                        $('#levelTotalBalance, #levelAvgBalance').text('$0.00');
                    }
                }

                function displayLevelMembers(members, level) {
                    console.log(`Displaying ${members ? members.length : 0} members for level ${level}`);
                    
                    try {
                        // Destroy existing DataTable first
                        if ($.fn.DataTable.isDataTable('#teamTable')) {
                            $('#teamTable').DataTable().destroy();
                        }
                        
                        // Clear the table body completely
                        $('#teamTableBody').empty();
                        
                        let tableRows = '';
                        
                        if (members && Array.isArray(members) && members.length > 0) {
                            members.forEach(function(member) {
                                // Validate member data
                                if (!member || typeof member !== 'object') {
                                    console.warn('Invalid member data:', member);
                                    return; // Skip this iteration
                                }
                                
                                const statusBadge = (member.status == 1) 
                                    ? '<span class="badge bg-success" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">Active</span>' 
                                    : '<span class="badge bg-danger" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">Inactive</span>';
                                
                                const memberName = member.name || 'N/A';
                                const memberEmail = member.email || 'N/A';
                                const memberUsername = member.username || 'Unknown';
                                const memberBalance = member.total_balance || 0;
                                const memberDate = member.formatted_date || 'N/A';
                                const referralCount = member.referral_count || 0;
                                
                                tableRows += `<tr data-level="${level}" data-username="${memberUsername}">
                                        <td class="username-cell"><strong>${memberUsername}</strong></td>
                                        <td class="name-cell">${memberName}</td>
                                        <td class="email-cell">${memberEmail}</td>
                                        <td class="level-cell"><span class="badge bg-info" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">Level ${level}</span></td>
                                        <td class="status-cell">${statusBadge}</td>
                                        <td class="balance-cell"><strong class="text-primary">$${parseFloat(memberBalance).toFixed(2)}</strong></td>
                                        <td class="date-cell">${memberDate}</td>
                                        <td class="referral-cell"><span class="badge bg-secondary" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">${referralCount}</span></td>
                                    </tr>`;
                            });
                        } else {
                            tableRows = `<tr><td colspan="8" class="text-center">No members found at Level ${level}</td></tr>`;
                        }
                        
                        // Insert new data
                        $('#teamTableBody').html(tableRows);
                        $('#listContainer').show();
                        
                        // Initialize DataTable with new data
                        $('#teamTable').DataTable({
                            "pageLength": 25,
                            "order": [[1, "asc"]], // Sort by name
                            "responsive": true,
                            "destroy": true, // Allow reinitialize
                            "autoWidth": false,
                            "processing": false,
                            "serverSide": false,
                            "searchDelay": 300,
                            "deferRender": true,
                            "language": {
                                "emptyTable": `No members found at Level ${level}`,
                                "info": `Showing _START_ to _END_ of _TOTAL_ Level ${level} members`,
                                "infoEmpty": `No Level ${level} members available`,
                                "infoFiltered": `(filtered from _MAX_ total Level ${level} members)`
                            },
                            "columnDefs": [
                                {
                                    "targets": "_all",
                                    "className": "text-nowrap"
                                }
                            ],
                            "drawCallback": function(settings) {
                                // Ensure text is crisp after DataTable renders
                                $('#teamTable td, #teamTable th').css({
                                    'text-rendering': 'optimizeLegibility',
                                    '-webkit-font-smoothing': 'antialiased',
                                    '-moz-osx-font-smoothing': 'grayscale'
                                });
                                
                                // Force clear rendering for all badges
                                $('#teamTable .badge').css({
                                    'transform': 'none',
                                    'filter': 'none',
                                    '-webkit-font-smoothing': 'antialiased',
                                    '-moz-osx-font-smoothing': 'grayscale',
                                    'text-rendering': 'optimizeLegibility',
                                    'font-size': '13px',
                                    'font-weight': '600',
                                    'backface-visibility': 'visible',
                                    'perspective': 'none',
                                    'will-change': 'auto'
                                });
                            }
                        });
                        
                        console.log(`Level ${level} table updated successfully with ${members ? members.length : 0} members`);
                        
                    } catch (error) {
                        console.error('Error in displayLevelMembers:', error);
                        $('#teamTableBody').html('<tr><td colspan="8" class="text-center text-danger">Error displaying member data</td></tr>');
                        $('#listContainer').show();
                    }
                }
                
                function displayAllTeamList(treeData) {
                    let flatList = [];
                    
                    // If treeData is an array (multiple root nodes), flatten each
                    if (Array.isArray(treeData)) {
                        treeData.forEach(rootNode => {
                            flatList = flatList.concat(flattenTreeData(rootNode));
                        });
                    } else {
                        // If treeData is a single object, flatten it
                        flatList = flattenTreeData(treeData);
                    }
                    
                    let tableRows = '';
                    
                    flatList.forEach(member => {
                        const statusBadge = member.status == 1 
                            ? '<span class="badge bg-success" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">Active</span>' 
                            : '<span class="badge bg-danger" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">Inactive</span>';
                        
                        tableRows += `<tr>
                                <td class="username-cell"><strong>${member.username}</strong></td>
                                <td class="name-cell">${member.name}</td>
                                <td class="email-cell">${member.email || 'N/A'}</td>
                                <td class="level-cell"><span class="badge bg-info" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">Level ${member.level}</span></td>
                                <td class="status-cell">${statusBadge}</td>
                                <td class="balance-cell"><strong class="text-primary">$${parseFloat(member.total_balance).toFixed(2)}</strong></td>
                                <td class="date-cell">${member.created_at}</td>
                                <td class="referral-cell"><span class="badge bg-secondary" style="transform: none !important; filter: none !important; -webkit-font-smoothing: antialiased !important; font-size: 13px !important; font-weight: 600 !important;">${member.referral_count || 0}</span></td>
                            </tr>`;
                    });
                    
                    $('#teamTableBody').html(tableRows);
                    $('#listContainer').show();
                    
                    // Destroy existing DataTable and reinitialize
                    if ($.fn.DataTable.isDataTable('#teamTable')) {
                        $('#teamTable').DataTable().destroy();
                    }
                    
                    // Initialize DataTable
                    $('#teamTable').DataTable({
                        "pageLength": 25,
                        "order": [[3, "asc"]], // Sort by level
                        "responsive": true,
                        "autoWidth": false,
                        "processing": false,
                        "serverSide": false,
                        "searchDelay": 300,
                        "deferRender": true,
                        "columnDefs": [
                            {
                                "targets": "_all",
                                "className": "text-nowrap"
                            }
                        ],
                        "drawCallback": function(settings) {
                            // Ensure text is crisp after DataTable renders
                            $('#teamTable td, #teamTable th').css({
                                'text-rendering': 'optimizeLegibility',
                                '-webkit-font-smoothing': 'antialiased',
                                '-moz-osx-font-smoothing': 'grayscale'
                            });
                            
                            // Force clear rendering for all badges
                            $('#teamTable .badge').css({
                                'transform': 'none',
                                'filter': 'none',
                                '-webkit-font-smoothing': 'antialiased',
                                '-moz-osx-font-smoothing': 'grayscale',
                                'text-rendering': 'optimizeLegibility',
                                'font-size': '13px',
                                'font-weight': '600',
                                'backface-visibility': 'visible',
                                'perspective': 'none',
                                'will-change': 'auto'
                            });
                        }
                    });
                }
                
                function displayTeamStats(stats) {
                    $('#totalMembers').text(stats.total_team_members);
                    $('#activeMembers').text(stats.active_members);
                    $('#inactiveMembers').text(stats.inactive_members);
                    $('#totalBusiness').text('$' + parseFloat(stats.total_team_business).toFixed(2));
                    $('#teamStats').show();
                    $('#levelStats').hide(); // Hide level stats when showing overall stats
                }
                
                function flattenTreeData(node, result = []) {
                    if (!node || !node.id) return result;
                    
                    result.push(node);
                    
                    if (node.children && node.children.length > 0) {
                        node.children.forEach(child => {
                            flattenTreeData(child, result);
                        });
                    }
                    
                    return result;
                }
                
                function hideLoading() {
                    $('#loadingIndicator').hide();
                    
                    // Clear any spinning indicators in level stats
                    $('#levelTotalMembers, #levelActiveMembers, #levelInactiveMembers, #levelTotalBalance, #levelAvgBalance, #levelTotalReferrals').each(function() {
                        if ($(this).html().includes('fa-spinner')) {
                            $(this).text('0');
                        }
                    });
                }
                
                function showError(message) {
                    console.error('Error:', message);
                    $('#errorText').text(message);
                    $('#errorMessage').show();
                    $('#successMessage').hide();
                    
                    // Hide loading indicators
                    hideLoading();
                }
                
                function showSuccess(message) {
                    console.log('Success:', message);
                    $('#successText').text(message);
                    $('#successMessage').show();
                    $('#errorMessage').hide();
                    setTimeout(function() {
                        $('#successMessage').hide();
                    }, 3000);
                }
                
            });
        </script>
        
    @endpush
    
</x-smart_layout>
