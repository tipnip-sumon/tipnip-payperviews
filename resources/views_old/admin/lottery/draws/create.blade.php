<x-layout>
    @section('top_title', 'Create New Draw')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Create New Draw')
            
            <!-- Quick Access to Manual Winner Selection -->
            <div class="col-12 mb-4">
                <div class="alert alert-info border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="alert-heading mb-2">
                                <i class="fe fe-users me-2"></i>🎯 Manual Winner Selection Available
                            </h6>
                            <p class="mb-0">
                                <small>Looking for manual winner selection? Check existing draws that have manual selection enabled.</small>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-info btn-sm" onclick="showAvailableDrawsModal()">
                                <i class="fe fe-list"></i> View Available Draws
                            </button>
                            <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fe fe-refresh-cw"></i> All Draws
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Create Draw Form -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Create New Lottery Draw</h4>
                            <p class="text-muted mb-0">Set up a new lottery draw with custom parameters</p>
                        </div>
                        <div class="d-flex gap-2"> 
                            <a href="{{ route('admin.lottery.draws') }}" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Back to Draws
                            </a>
                        </div>
                    </div> 
                    
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6><i class="fe fe-alert-triangle"></i> Error</h6>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6><i class="fe fe-alert-triangle"></i> Validation Errors</h6>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.lottery.draws.store') }}" id="createDrawForm">
                            @csrf
                            
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-info me-2"></i>Basic Information</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Draw Date <span class="text-danger">*</span></label>
                                                <input type="date" name="draw_date" class="form-control" 
                                                       value="{{ old('draw_date', date('Y-m-d', strtotime('+1 day'))) }}" 
                                                       min="{{ date('Y-m-d') }}" required>
                                                <small class="text-muted">Date when the draw will be performed</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Draw Time <span class="text-danger">*</span></label>
                                                <input type="time" name="draw_time" class="form-control" 
                                                       value="{{ old('draw_time', '20:00') }}" required>
                                                <small class="text-muted">Time when the draw will be performed</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Ticket Price ($) <span class="text-danger">*</span></label>
                                                <input type="number" name="ticket_price" class="form-control" 
                                                       value="{{ old('ticket_price', $settings['ticket_price'] ?? 2.00) }}" 
                                                       step="0.01" min="0.01" max="1000" required>
                                                <small class="text-muted">Price per ticket for this draw</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Maximum Tickets <span class="text-danger">*</span></label>
                                                <input type="number" name="max_tickets" class="form-control" 
                                                       value="{{ old('max_tickets', 1000) }}" 
                                                       min="1" max="10000" required>
                                                <small class="text-muted">Maximum number of tickets that can be sold</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prize Configuration -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-award me-2"></i>Prize & Winner Configuration</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <!-- Auto-inherited settings display -->
                                        <div class="alert alert-success mb-0">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fe fe-users me-2"></i>
                                                        <div>
                                                            <strong>Winner Selection:</strong><br>
                                                            <span class="text-muted">Auto (from lottery settings)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fe fe-award me-2"></i>
                                                        <div>
                                                            <strong>Prize Distribution:</strong><br>
                                                            <span class="text-muted">Auto (from lottery settings)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fe fe-percent me-2"></i>
                                                        <div>
                                                            <strong>Commission:</strong><br>
                                                            <span class="text-muted">Auto (from lottery settings)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Hidden fields for auto-inherited values -->
                                        <input type="hidden" name="winner_selection_mode" value="auto">
                        <input type="hidden" name="number_of_winners" value="{{ $settings['number_of_winners'] ?? 1 }}">
                        <input type="hidden" name="admin_commission" value="{{ $settings['admin_commission_percentage'] ?? 10 }}">
                        <input type="hidden" name="prize_distribution_type" value="fixed">                                        <!-- Prize distribution as detailed winner array -->
                                        @php
                                            $prizeStructure = $settings['prize_structure'] ?? [];
                                            $winnerArray = [];
                                            
                                            // Generate detailed winner structure from lottery settings
                                            if (empty($prizeStructure)) {
                                                // Default structure if no prize structure exists
                                                $winnerArray[] = [
                                                    'position' => 1,
                                                    'winner_index' => 1,
                                                    'amount' => 100,
                                                    'name' => '1st Prize (Winner 1)',
                                                    'type' => 'fixed_amount'
                                                ];
                                            } else {
                                                foreach ($prizeStructure as $position => $prize) {
                                                    $prizeAmount = $prize['amount'] ?? $prize['percentage'] ?? 0;
                                                    $prizeName = $prize['name'] ?? ($position . ' Prize');
                                                    $prizeType = $prize['type'] ?? 'fixed_amount';
                                                    
                                                    // Check if there are multiple winners for this position
                                                    if (isset($prize['multiple_winners']) && is_array($prize['multiple_winners'])) {
                                                        foreach ($prize['multiple_winners'] as $winnerIndex => $winner) {
                                                            $winnerArray[] = [
                                                                'position' => (int)$position,
                                                                'winner_index' => $winnerIndex + 1,
                                                                'amount' => $winner['amount'] ?? ($prizeAmount / count($prize['multiple_winners'])),
                                                                'name' => $prizeName . ' (Winner ' . ($winnerIndex + 1) . ')',
                                                                'type' => $prizeType
                                                            ];
                                                        }
                                                    } else {
                                                        // Single winner for this position
                                                        $winnerArray[] = [
                                                            'position' => (int)$position,
                                                            'winner_index' => 1,
                                                            'amount' => $prizeAmount,
                                                            'name' => $prizeName . ' (Winner 1)',
                                                            'type' => $prizeType
                                                        ];
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        <!-- Send prize distribution as array fields -->
                                        @foreach($winnerArray as $index => $winner)
                                            <input type="hidden" name="prize_distribution[{{ $index }}][position]" value="{{ $winner['position'] }}">
                                            <input type="hidden" name="prize_distribution[{{ $index }}][winner_index]" value="{{ $winner['winner_index'] }}">
                                            <input type="hidden" name="prize_distribution[{{ $index }}][amount]" value="{{ $winner['amount'] }}">
                                            <input type="hidden" name="prize_distribution[{{ $index }}][name]" value="{{ $winner['name'] }}">
                                            <input type="hidden" name="prize_distribution[{{ $index }}][type]" value="{{ $winner['type'] }}">
                                        @endforeach
                                        
                                        <!-- Keep JSON field for JavaScript updates -->
                                        <input type="hidden" name="prize_distribution_json" value="{{ json_encode($winnerArray) }}" id="prizeDistributionField">
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-settings me-2"></i>Advanced Settings</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <!-- Hidden field to ensure a value is always sent -->
                                                    <input type="hidden" name="auto_draw" value="false">
                                                    <input class="form-check-input" type="checkbox" name="auto_draw" value="true" id="auto_draw"
                                                           {{ old('auto_draw', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="auto_draw">
                                                        <strong>Auto Draw</strong>
                                                        <br><small class="text-muted">Automatically perform draw at scheduled time</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <!-- Hidden field to ensure a value is always sent -->
                                                    <input type="hidden" name="auto_prize_distribution" value="false">
                                                    <input class="form-check-input" type="checkbox" name="auto_prize_distribution" value="true" id="auto_prize_distribution"
                                                           {{ old('auto_prize_distribution', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="auto_prize_distribution">
                                                        <strong>Auto Prize Distribution</strong>
                                                        <br><small class="text-muted">Automatically distribute prizes to winners</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual Winner Selection Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-users me-2"></i>🎯 Manual Winner Selection</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <div class="form-check form-switch">
                                                    <!-- Hidden field to ensure a value is always sent -->
                                                    <input type="hidden" name="manual_winner_selection" value="false">
                                                    <input class="form-check-input" type="checkbox" name="manual_winner_selection" value="true" id="manual_winner_selection"
                                                           {{ old('manual_winner_selection', false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manual_winner_selection">
                                                        <strong>🎯 Enable Manual Winner Selection</strong>
                                                        <br><small class="text-muted">Allow manual selection of winners from purchased tickets after creating the draw</small>
                                                    </label>
                                                </div>
                                                
                                                <div class="mt-3" id="manual_selection_note" style="display: none;">
                                                    <div class="alert alert-info mb-0">
                                                        <i class="fe fe-info me-2"></i>
                                                        <strong>How Manual Winner Selection Works:</strong>
                                                        <ul class="mb-0 mt-2">
                                                            <li>Create the draw with this option enabled</li>
                                                            <li>Users can purchase tickets for the draw</li>
                                                            <li>Access manual winner selection from the draw details page</li>
                                                            <li>Select specific tickets as winners for each prize position</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-warning text-white">
                                                    <div class="card-body text-center">
                                                        <i class="fe fe-users" style="font-size: 2rem;"></i>
                                                        <h6 class="mt-2">Manual Control</h6>
                                                        <p class="small mb-0">Perfect for special events or when you want full control over winner selection</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-bar-chart me-2"></i>Draw Summary</h5>
                                    <div class="border rounded p-4 bg-info text-white">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Estimated Revenue:</strong><br>
                                                <span id="estimatedRevenue">$0.00</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Admin Commission:</strong><br>
                                                <span id="adminCommission">$0.00</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Prize Pool:</strong><br>
                                                <span id="prizePool">$0.00</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Draw Status:</strong><br>
                                                <span class="badge bg-warning">Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-secondary">
                                            <i class="fe fe-x"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe fe-save"></i> Create Draw
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endsection

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
    // Simple summary calculations only
    function updateSummary() {
        const maxTickets = parseFloat(document.querySelector('[name="max_tickets"]').value || 0);
        const ticketPrice = parseFloat(document.querySelector('[name="ticket_price"]').value || 0);
        const commission = {{ $settings['admin_commission_percentage'] ?? 10 }}; // From lottery settings
        
        const estimatedRevenue = maxTickets * ticketPrice;
        const adminCommissionAmount = estimatedRevenue * (commission / 100);
        const prizePoolAmount = estimatedRevenue - adminCommissionAmount;
        
        document.getElementById('estimatedRevenue').textContent = '$' + estimatedRevenue.toFixed(2);
        document.getElementById('adminCommission').textContent = '$' + adminCommissionAmount.toFixed(2);
        document.getElementById('prizePool').textContent = '$' + prizePoolAmount.toFixed(2);
        
        // Update prize distribution with calculated amounts
        const prizeDistributionField = document.getElementById('prizeDistributionField');
        if (prizeDistributionField && prizePoolAmount > 0) {
            try {
                const currentDistribution = JSON.parse(prizeDistributionField.value);
                const distributionType = 'fixed';
                
                // Calculate total percentage/amount for proportional distribution
                let totalValue = 0;
                currentDistribution.forEach(winner => {
                    totalValue += parseFloat(winner.amount || 0);
                });
                
                // Update each winner's amount based on their proportion of the prize pool
                const updatedDistribution = currentDistribution.map((winner, index) => {
                    let calculatedAmount;
                    
                    if (distributionType === 'percentage') {
                        // For percentage-based, calculate amount from percentage of prize pool
                        const percentage = parseFloat(winner.amount || 0);
                        calculatedAmount = (prizePoolAmount * percentage) / 100;
                    } else {
                        // For fixed amounts, calculate proportional amount based on original values
                        if (totalValue > 0) {
                            const proportion = parseFloat(winner.amount || 0) / totalValue;
                            calculatedAmount = prizePoolAmount * proportion;
                        } else {
                            calculatedAmount = prizePoolAmount / currentDistribution.length;
                        }
                    }
                    
                    // Update the individual hidden fields
                    const amountField = document.querySelector(`input[name="prize_distribution[${index}][amount]"]`);
                    if (amountField) {
                        amountField.value = calculatedAmount.toFixed(2);
                    }
                    
                    return {
                        ...winner,
                        amount: calculatedAmount.toFixed(2)
                    };
                });
                
                prizeDistributionField.value = JSON.stringify(updatedDistribution);
            } catch (error) {
                console.warn('Error updating prize distribution:', error);
            }
        }
    }

    // Simple form validation - no complex checks needed
    document.getElementById('createDrawForm').addEventListener('submit', function(e) {
        // All settings are auto-inherited, no validation needed
        return true;
    });

    // Event listeners - simplified
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize summary with default settings
        updateSummary();
        
        // Update summary when basic values change
        document.querySelector('[name="max_tickets"]').addEventListener('input', updateSummary);
        document.querySelector('[name="ticket_price"]').addEventListener('input', updateSummary);

        // Handle checkbox states properly
        const autoDrawCheckbox = document.getElementById('auto_draw');
        const autoPrizeCheckbox = document.getElementById('auto_prize_distribution');
        const manualWinnerCheckbox = document.getElementById('manual_winner_selection');

        // Override hidden field values when checkboxes are checked
        autoDrawCheckbox.addEventListener('change', function() {
            const hiddenField = document.querySelector('input[type="hidden"][name="auto_draw"]');
            if (this.checked) {
                hiddenField.disabled = true;
            } else {
                hiddenField.disabled = false;
            }
        });

        autoPrizeCheckbox.addEventListener('change', function() {
            const hiddenField = document.querySelector('input[type="hidden"][name="auto_prize_distribution"]');
            if (this.checked) {
                hiddenField.disabled = true;
            } else {
                hiddenField.disabled = false;
            }
        });

        // Handle manual winner selection checkbox
        manualWinnerCheckbox.addEventListener('change', function() {
            const hiddenField = document.querySelector('input[type="hidden"][name="manual_winner_selection"]');
            const noteDiv = document.getElementById('manual_selection_note');
            
            if (this.checked) {
                hiddenField.disabled = true;
                noteDiv.style.display = 'block';
            } else {
                hiddenField.disabled = false;
                noteDiv.style.display = 'none';
            }
        });

        // Initialize checkbox states
        if (autoDrawCheckbox.checked) {
            document.querySelector('input[type="hidden"][name="auto_draw"]').disabled = true;
        }
        if (autoPrizeCheckbox.checked) {
            document.querySelector('input[type="hidden"][name="auto_prize_distribution"]').disabled = true;
        }
        if (manualWinnerCheckbox.checked) {
            document.querySelector('input[type="hidden"][name="manual_winner_selection"]').disabled = true;
            document.getElementById('manual_selection_note').style.display = 'block';
        }
    });
    
    // Function to show available draws for manual winner selection
    function showAvailableDrawsModal() {
        // Create modal for available draws
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fe fe-users me-2"></i>🎯 Draws Available for Manual Winner Selection
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fe fe-info me-2"></i>
                            <strong>How to access manual winner selection:</strong><br>
                            1. Find a draw with "Manual Selection Enabled" status<br>
                            2. Click "Manual Winners" to select specific ticket winners<br>
                            3. Click "Manual Selection" for advanced winner selection options
                        </div>
                        <div id="available-draws-content">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading available draws...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-primary">
                            <i class="fe fe-external-link"></i> View All Draws
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        // Load draws with manual selection enabled
        fetch('{{ route("admin.lottery.draws") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.text())
        .then(html => {
            // Since we're getting HTML, we'll parse it to extract draw information
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const content = document.getElementById('available-draws-content');
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Manual Winner Selection Access</h6>
                                <p class="card-text">
                                    To access manual winner selection features:
                                </p>
                                <div class="d-grid">
                                    <a href="{{ route('admin.lottery.draws') }}" class="btn btn-primary">
                                        <i class="fe fe-list me-2"></i>View All Draws
                                    </a>
                                </div>
                                <hr>
                                <small class="text-muted">
                                    Look for draws with "Manual Selection" option enabled in the draws list.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">Quick Access URLs</h6>
                                <p class="card-text small">
                                    Direct access to manual winner features:
                                </p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-light btn-sm" onclick="window.open('{{ route('admin.lottery.draws') }}/[DRAW_ID]/manual-winners', '_blank')">
                                        <i class="fe fe-users"></i> Manual Winners
                                    </button>
                                    <button class="btn btn-light btn-sm" onclick="window.open('{{ route('admin.lottery.draws') }}/[DRAW_ID]/manual-selection', '_blank')">
                                        <i class="fe fe-edit"></i> Manual Selection
                                    </button>
                                </div>
                                <small class="d-block mt-2">Replace [DRAW_ID] with actual draw ID</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            const content = document.getElementById('available-draws-content');
            content.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fe fe-alert-triangle me-2"></i>
                    Unable to load draw information. Please visit the main draws page to access manual winner selection.
                </div>
                <div class="text-center">
                    <a href="{{ route('admin.lottery.draws') }}" class="btn btn-primary">
                        <i class="fe fe-list me-2"></i>View All Draws
                    </a>
                </div>
            `;
        });
        
        // Clean up modal when closed
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
    }

    // Function to show auto-generate draw modal
    function showAutoGenerateModal() {
        // Create modal for auto-generate draw
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fe fe-zap me-2"></i>⚡ Auto Generate Draw
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="fe fe-zap me-2"></i>
                            <strong>Quick Auto-Generate:</strong> Instantly create a new lottery draw with automatic settings from your lottery configuration.
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <i class="fe fe-clock" style="font-size: 2rem; color: #0d6efd;"></i>
                                        <h6 class="mt-2">Standard Auto-Draw</h6>
                                        <p class="text-muted small">Uses default lottery settings</p>
                                        <button class="btn btn-primary btn-sm" onclick="generateAutoDraw('standard')">
                                            <i class="fe fe-plus me-1"></i>Generate Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="fe fe-settings" style="font-size: 2rem; color: #198754;"></i>
                                        <h6 class="mt-2">Quick Custom</h6>
                                        <p class="text-muted small">Set basic parameters</p>
                                        <button class="btn btn-success btn-sm" onclick="showQuickCustomForm()">
                                            <i class="fe fe-edit me-1"></i>Customize
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Recent Activity:</h6>
                            <div id="recent-draws-info">
                                <div class="d-flex align-items-center text-muted">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading recent draws...
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-primary">
                            <i class="fe fe-list me-1"></i>Manage All Draws
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        // Load recent draws info
        setTimeout(() => {
            document.getElementById('recent-draws-info').innerHTML = `
                <div class="small text-muted">
                    <i class="fe fe-info me-1"></i>
                    Auto-generate will create draws with your current lottery settings. 
                    You can modify them later from the draws management page.
                </div>
            `;
        }, 1000);
        
        // Clean up modal when closed
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
    }

    // Function to generate auto draw
    function generateAutoDraw(type) {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fe fe-loader me-1"></i>Generating...';
        button.disabled = true;
        
        fetch('{{ route("admin.lottery.auto-generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                type: type,
                auto_draw: true,
                auto_prize_distribution: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and show success
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) modalInstance.hide();
                });
                
                // Show success message
                alert(`✅ Auto-draw generated successfully! Draw ID: #${data.draw_id || 'Generated'}`);
                
                // Redirect to draws page
                setTimeout(() => {
                    window.location.href = '{{ route("admin.lottery.draws") }}';
                }, 1500);
            } else {
                alert('❌ Error: ' + (data.message || 'Failed to generate auto-draw'));
            }
        })
        .catch(error => {
            console.error('Auto-generate error:', error);
            alert('❌ An error occurred while generating the auto-draw');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    // Function to show quick custom form
    function showQuickCustomForm() {
        alert('🚧 Quick custom form will be available in the next update. For now, please use the standard auto-generate or create a draw manually.');
    }
</script>
@endpush
</x-layout>
