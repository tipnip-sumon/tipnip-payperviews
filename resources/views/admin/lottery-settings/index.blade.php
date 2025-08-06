<x-layout>
    @section('top_title', 'Lottery Settings Management')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Lottery Settings Management')
            
            <!-- Statistics Cards -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Total Settings</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ isset($stats['total_settings']) ? $stats['total_settings'] : 0 }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-primary my-auto float-end">
                                    <i class="fe fe-settings"></i> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Active Lottery</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ isset($stats['is_active']) && $stats['is_active'] ? 'Yes' : 'No' }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-success my-auto float-end">
                                    <i class="fe fe-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Ticket Price</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">${{ isset($stats['ticket_price']) ? number_format($stats['ticket_price'], 2) : '0.00' }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-warning my-auto float-end">
                                    <i class="fe fe-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Commission Rate</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ isset($stats['admin_commission_percentage']) ? number_format($stats['admin_commission_percentage'], 2) : '0.00' }}%</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-info my-auto float-end">
                                    <i class="fe fe-percent"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Update Results -->
        @if(session('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h6><i class="fe fe-check"></i> Success</h6>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fe fe-alert-triangle"></i> Validation Errors</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fe fe-alert-triangle"></i> Error</h6>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Lottery Settings Management -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Lottery Settings Management</h4>
                            <p class="text-muted mb-0">Configure your lottery system settings and parameters</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.lottery.index') }}" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Back to Lottery Dashboard
                            </a>
                            <button type="button" class="btn btn-success" onclick="resetToDefaults()">
                                <i class="fe fe-refresh-cw"></i> Reset to Defaults
                            </button>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#backupModal">
                                <i class="fe fe-download"></i> Backup Settings
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.lottery-settings.update') }}" id="settingsForm">
                            @csrf
                            
                            <!-- Basic Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-settings me-2"></i>Basic Settings</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Lottery System Status</label>
                                                <select name="is_active" class="form-select" required>
                                                    <option value="1" {{ (isset($settingsArray['is_active']) && $settingsArray['is_active']) ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ (isset($settingsArray['is_active']) && !$settingsArray['is_active']) ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                <small class="text-muted">Enable or disable the entire lottery system</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Ticket Price ($)</label>
                                                <input type="number" name="ticket_price" class="form-control" 
                                                       value="{{ old('ticket_price', $settingsArray['ticket_price'] ?? 2.00) }}" 
                                                       step="0.01" min="0.01" max="1000" required>
                                                <small class="text-muted">Price per lottery ticket</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Max Tickets Per User Per Draw</label>
                                                <input type="number" name="max_tickets_per_user" class="form-control" 
                                                       value="{{ old('max_tickets_per_user', $settingsArray['max_tickets_per_user'] ?? 10) }}" 
                                                       step="1" min="1" max="1000" required>
                                                <small class="text-muted">Maximum tickets a user can buy per draw</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Minimum Tickets for Draw</label>
                                                <input type="number" name="min_tickets_for_draw" class="form-control" 
                                                       value="{{ old('min_tickets_for_draw', $settingsArray['min_tickets_for_draw'] ?? 5) }}" 
                                                       step="1" min="1" max="1000" required>
                                                <small class="text-muted">Minimum tickets required to proceed with draw</small>
                                            </div>
                                            <!-- Virtual User ID for Lottery Display Enhancement -->
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Virtual User ID 
                                                    <i class="fe fe-info" data-bs-toggle="tooltip" 
                                                       title="This user ID will be used to generate virtual lottery tickets for display purposes. Virtual tickets make the lottery appear more active but don't affect real winners."></i>
                                                </label>
                                                <input type="number" name="virtual_user_id" class="form-control" 
                                                       value="{{ old('virtual_user_id', $settingsArray['virtual_user_id'] ?? 1) }}" 
                                                       step="1" min="1" max="999999" required>
                                                <small class="text-muted">User ID for generating virtual lottery tickets (Display only)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Draw Schedule Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-calendar me-2"></i>Draw Schedule</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Draw Day</label>
                                                <select name="draw_day" class="form-select" required>
                                                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                                                        <option value="{{ $index }}" {{ old('draw_day', $settingsArray['draw_day'] ?? 0) == $index ? 'selected' : '' }}>
                                                            {{ $day }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Day of the week for draws</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Draw Hour (24-hour format)</label>
                                                <input type="number" name="draw_hour" class="form-control" 
                                                       value="{{ old('draw_hour', intval($settingsArray['draw_hour'] ?? 20)) }}" 
                                                       step="1" min="0" max="23" pattern="[0-9]*" inputmode="numeric" required>
                                                <small class="text-muted">Hour of the day for draws (0-23)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Draw Minute</label>
                                                <input type="number" name="draw_minute" class="form-control" 
                                                       value="{{ old('draw_minute', intval($settingsArray['draw_minute'] ?? 0)) }}" 
                                                       step="1" min="0" max="59" pattern="[0-9]*" inputmode="numeric" required>
                                                <small class="text-muted">Minute of the hour for draws (0-59)</small>
                                            </div>
                                        </div>
                                        <div class="mt-3 p-3 bg-info text-white rounded">
                                            <strong>Next Draw Schedule:</strong> 
                                            <span id="nextDrawTime">Calculating...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prize Configuration -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-award me-2"></i>Prize Configuration</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Admin Commission (%)</label>
                                                <input type="number" name="admin_commission_percentage" class="form-control" 
                                                       value="{{ old('admin_commission_percentage', $settingsArray['admin_commission_percentage'] ?? 10) }}" 
                                                       step="0.01" min="0" max="50" required>
                                                <small class="text-muted">Percentage kept by admin (remaining goes to prize pool)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Number of Winners</label>
                                                <input type="number" name="number_of_winners" class="form-control" 
                                                       value="{{ old('number_of_winners', $settingsArray['number_of_winners'] ?? 1) }}" 
                                                       step="1" min="1" max="10" required>
                                                <small class="text-muted">Number of winners per draw</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Auto Claim Days</label>
                                                <input type="number" name="auto_claim_days" class="form-control" 
                                                       value="{{ old('auto_claim_days', $settingsArray['auto_claim_days'] ?? 30) }}" 
                                                       step="1" min="1" max="365" required>
                                                <small class="text-muted">Days after which unclaimed prizes are automatically claimed</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Prize Distribution -->
                                        <div class="mt-4">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Prize Distribution Type</label>
                                                    <select name="prize_distribution_type" class="form-select" id="prizeDistributionType" required>
                                                        <option value="percentage" {{ old('prize_distribution_type', $settingsArray['prize_distribution_type'] ?? 'percentage') == 'percentage' ? 'selected' : '' }}>
                                                            Percentage-based (% of prize pool)
                                                        </option>
                                                        <option value="fixed_amount" {{ old('prize_distribution_type', $settingsArray['prize_distribution_type'] ?? 'percentage') == 'fixed_amount' ? 'selected' : '' }}>
                                                            Fixed Amount (specific dollar amounts)
                                                        </option>
                                                    </select>
                                                    <small class="text-muted">Choose how prizes are calculated</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Prize Distribution <span id="prizeDistributionUnit">(%)</span></label>
                                                    <div class="alert alert-info" id="prizeDistributionHelper">
                                                        <small><strong>Percentage Mode:</strong> Total must equal 100%</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div id="prizeDistribution" class="row g-2">
                                                @php
                                                    $distribution = $settingsArray['prize_distribution'] ?? [100];
                                                    if (is_string($distribution)) {
                                                        $distribution = json_decode($distribution, true) ?: [100];
                                                    }
                                                    // Ensure we have a proper array structure
                                                    if (!is_array($distribution)) {
                                                        $distribution = [100];
                                                    }
                                                    // Convert associative array to indexed array if needed
                                                    if (isset($distribution[1]) && is_array($distribution[1])) {
                                                        $tempDistribution = [];
                                                        foreach ($distribution as $prize) {
                                                            $tempDistribution[] = $prize['percentage'] ?? 0;
                                                        }
                                                        $distribution = $tempDistribution;
                                                    }
                                                @endphp
                                                
                                                @if(is_array($distribution) && !empty($distribution))
                                                    @foreach($distribution as $index => $percentage)
                                                        @php
                                                            $isFixedAmount = ($settingsArray['prize_distribution_type'] ?? 'percentage') === 'fixed_amount';
                                                            $maxValue = $isFixedAmount ? 100000 : 100;
                                                            $unit = $isFixedAmount ? '$' : '%';
                                                        @endphp
                                                        <div class="col-md-6 col-lg-4 prize-row">
                                                            <div class="input-group">
                                                                <span class="input-group-text">{{ $index + 1 }}{{ $index == 0 ? 'st' : ($index == 1 ? 'nd' : ($index == 2 ? 'rd' : 'th')) }} Place</span>
                                                                <input type="number" name="prize_distribution[]" 
                                                                       class="form-control prize-value-input" 
                                                                       step="0.01" min="0" max="{{ $maxValue }}" 
                                                                       value="{{ is_numeric($percentage) ? min($percentage, $maxValue) : 0 }}" required>
                                                                <span class="input-group-text prize-unit">{{ $unit }}</span>
                                                                @if($index > 0)
                                                                    <button type="button" class="btn btn-outline-danger" onclick="removePrizeRow(this)">
                                                                        <i class="fe fe-x"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                            <!-- Multiple Winners Configuration -->
                                                            <div class="multiple-winners-config mt-2" style="display: {{ ($settingsArray['allow_multiple_winners_per_place'] ?? false) ? 'block' : 'none' }};">
                                                                <div class="card card-body border-info">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                        <small class="text-info fw-bold">Multiple Winners Distribution</small>
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWinnerSlot(this, {{ $index + 1 }})">
                                                                            <i class="fe fe-plus"></i> Add Winner
                                                                        </button>
                                                                    </div>
                                                                    <div class="winner-slots">
                                                                        @php
                                                                            // Check if there are multiple winners for this position in the prize structure
                                                                            $prizeStructure = $settingsArray['prize_structure'] ?? [];
                                                                            $currentPrize = $prizeStructure[$index + 1] ?? null;
                                                                            $multipleWinners = [];
                                                                            
                                                                            if (is_array($currentPrize) && isset($currentPrize['multiple_winners']) && is_array($currentPrize['multiple_winners'])) {
                                                                                $multipleWinners = $currentPrize['multiple_winners'];
                                                                            }
                                                                            
                                                                            // If no multiple winners data, create default single winner
                                                                            if (empty($multipleWinners)) {
                                                                                $multipleWinners = [['winner' => 1, ($isFixedAmount ? 'amount' : 'percentage') => ($isFixedAmount ? '0' : '100')]];
                                                                            }
                                                                        @endphp
                                                                        
                                                                        @foreach($multipleWinners as $winnerIndex => $winnerData)
                                                                            <div class="winner-slot mb-2">
                                                                                <div class="input-group input-group-sm">
                                                                                    <span class="input-group-text">Winner {{ $winnerIndex + 1 }}</span>
                                                                                    <input type="number" name="multiple_winners[{{ $index }}][]" 
                                                                                           class="form-control winner-value-input" 
                                                                                           step="0.01" min="0" max="{{ $maxValue }}" 
                                                                                           value="{{ floatval($winnerData[$isFixedAmount ? 'amount' : 'percentage'] ?? ($isFixedAmount ? '0' : '100')) }}" 
                                                                                           placeholder="Amount/Percentage">
                                                                                    <span class="input-group-text winner-unit">{{ $unit }}</span>
                                                                                    @if($winnerIndex > 0)
                                                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeWinnerSlot(this)">
                                                                                            <i class="fe fe-x"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            Total for this position: <span class="position-total">{{ array_sum(array_map('floatval', array_column($multipleWinners, $isFixedAmount ? 'amount' : 'percentage'))) }}</span><span class="position-unit">{{ $unit }}</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <!-- Default single winner -->
                                                    @php
                                                        $isFixedAmount = ($settingsArray['prize_distribution_type'] ?? 'percentage') === 'fixed_amount';
                                                        $maxValue = $isFixedAmount ? 100000 : 100;
                                                        $unit = $isFixedAmount ? '$' : '%';
                                                        $defaultValue = $isFixedAmount ? '0' : '100';
                                                    @endphp
                                                    <div class="col-md-6 col-lg-4 prize-row">
                                                        <div class="input-group">
                                                            <span class="input-group-text">1st Place</span>
                                                            <input type="number" name="prize_distribution[]" 
                                                                   class="form-control prize-value-input" 
                                                                   step="0.01" min="0" max="{{ $maxValue }}" 
                                                                   value="{{ $defaultValue }}" required>
                                                            <span class="input-group-text prize-unit">{{ $unit }}</span>
                                                        </div>
                                                        <!-- Multiple Winners Configuration -->
                                                        <div class="multiple-winners-config mt-2" style="display: {{ ($settingsArray['allow_multiple_winners_per_place'] ?? false) ? 'block' : 'none' }};">
                                                            <div class="card card-body border-info">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <small class="text-info fw-bold">Multiple Winners Distribution</small>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWinnerSlot(this, 1)">
                                                                        <i class="fe fe-plus"></i> Add Winner
                                                                    </button>
                                                                </div>
                                                                <div class="winner-slots">
                                                                    <div class="winner-slot mb-2">
                                                                        <div class="input-group input-group-sm">
                                                                            <span class="input-group-text">Winner 1</span>
                                                                            <input type="number" name="multiple_winners[0][]" 
                                                                                   class="form-control winner-value-input" 
                                                                                   step="0.01" min="0" max="{{ $maxValue }}" 
                                                                                   value="{{ $defaultValue }}" placeholder="Amount/Percentage">
                                                                            <span class="input-group-text winner-unit">{{ $unit }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <small class="text-muted">
                                                                        Total for this position: <span class="position-total">{{ $defaultValue }}</span><span class="position-unit">{{ $unit }}</span>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPrizeRow()">
                                                    <i class="fe fe-plus"></i> Add Prize Level
                                                </button>
                                                <div class="text-muted">
                                                    <strong>Total:</strong> <span id="totalPercentage">{{ is_array($distribution) ? array_sum($distribution) : 100 }}</span><span id="totalUnit">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Automation Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-zap me-2"></i>Automation Settings</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="auto_draw" value="0">
                                                    <input class="form-check-input" type="checkbox" name="auto_draw" value="1"
                                                           {{ old('auto_draw', $settingsArray['auto_draw'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>Auto Draw</strong>
                                                        <br><small class="text-muted">Automatically perform draws at scheduled times</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="auto_prize_distribution" value="0">
                                                    <input class="form-check-input" type="checkbox" name="auto_prize_distribution" value="1"
                                                           {{ old('auto_prize_distribution', $settingsArray['auto_prize_distribution'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>Auto Prize Distribution</strong>
                                                        <br><small class="text-muted">Automatically distribute prizes to winners</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="auto_refund_cancelled" value="0">
                                                    <input class="form-check-input" type="checkbox" name="auto_refund_cancelled" value="1"
                                                           {{ old('auto_refund_cancelled', $settingsArray['auto_refund_cancelled'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>Auto Refund Cancelled Draws</strong>
                                                        <br><small class="text-muted">Automatically refund tickets when draws are cancelled</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Expiration Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-clock me-2"></i>Ticket Expiration</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Ticket Expiration Hours</label>
                                                <input type="number" name="ticket_expiry_hours" class="form-control" 
                                                       value="{{ old('ticket_expiry_hours', $settingsArray['ticket_expiry_hours'] ?? 24) }}" 
                                                       step="1" min="1" max="8760" required>
                                                <small class="text-muted">Hours after draw when tickets expire</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Prize Claim Deadline (Days)</label>
                                                <input type="number" name="prize_claim_deadline" class="form-control" 
                                                       value="{{ old('prize_claim_deadline', $settingsArray['prize_claim_deadline'] ?? 30) }}" 
                                                       step="1" min="1" max="365" required>
                                                <small class="text-muted">Days winners have to claim their prizes</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Lottery Features -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-star me-2"></i>Advanced Lottery Features</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="allow_multiple_winners_per_place" value="0">
                                                    <input class="form-check-input" type="checkbox" name="allow_multiple_winners_per_place" value="1" id="allowMultipleWinners"
                                                           {{ old('allow_multiple_winners_per_place', $settingsArray['allow_multiple_winners_per_place'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>Multiple Winners Per Place</strong>
                                                        <br><small class="text-muted">Allow multiple users to win the same prize position</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="manual_winner_selection" value="0">
                                                    <input class="form-check-input" type="checkbox" name="manual_winner_selection" value="1"
                                                           {{ old('manual_winner_selection', $settingsArray['manual_winner_selection'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>Manual Winner Selection</strong>
                                                        <br><small class="text-muted">Allow manual selection of winners instead of random draw</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Virtual Ticket Display Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fe fe-eye me-2"></i>Virtual Ticket Display</h5>
                                    <div class="border rounded p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="show_virtual_tickets" value="0">
                                                    <input class="form-check-input" type="checkbox" name="show_virtual_tickets" value="1" id="showVirtualTicketsCheckbox"
                                                           {{ old('show_virtual_tickets', $settingsArray['show_virtual_tickets'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>Show Virtual Tickets</strong>
                                                        <br><small class="text-muted">Display inflated ticket sales numbers to users (real sales remain tracked separately)</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 virtual-ticket-settings" style="display: {{ old('show_virtual_tickets', $settingsArray['show_virtual_tickets'] ?? false) ? 'block' : 'none' }};">
                                                <label class="form-label">Virtual Ticket Multiplier (%)</label>
                                                <input type="number" name="virtual_ticket_multiplier" class="form-control virtual-ticket-input" 
                                                       value="{{ old('virtual_ticket_multiplier', $settingsArray['virtual_ticket_multiplier'] ?? 200) }}" 
                                                       step="1" min="100" max="1000">
                                                <small class="text-muted">Multiply real tickets by this percentage (100% = same as real)</small>
                                            </div>
                                            <div class="col-md-6 virtual-ticket-settings" style="display: {{ old('show_virtual_tickets', $settingsArray['show_virtual_tickets'] ?? false) ? 'block' : 'none' }};">
                                                <label class="form-label">Virtual Ticket Base</label>
                                                <input type="number" name="virtual_ticket_base" class="form-control virtual-ticket-input" 
                                                       value="{{ old('virtual_ticket_base', $settingsArray['virtual_ticket_base'] ?? 0) }}" 
                                                       step="1" min="0" max="10000">
                                                <small class="text-muted">Base number of virtual tickets to always add</small>
                                            </div>
                                            <div class="col-md-12 virtual-ticket-settings" style="display: {{ old('show_virtual_tickets', $settingsArray['show_virtual_tickets'] ?? false) ? 'block' : 'none' }};">
                                                <div class="alert alert-info">
                                                    <strong>Example:</strong> If 10 real tickets are sold, multiplier is 200%, and base is 50:<br>
                                                    <strong>Display:</strong> (10 × 200%) + 50 = 70 tickets shown to users<br>
                                                    <strong>Reality:</strong> Only 10 tickets actually sold and eligible to win
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fe fe-x"></i> Reset Changes
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe fe-save"></i> Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Configuration Changes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Recent Configuration Changes</h4>
                            <p class="text-muted mb-0">Track recent updates to lottery settings</p>
                        </div>
                        <div class="badge bg-info">
                            <i class="fe fe-activity me-1"></i>
                            Live Monitoring
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fe fe-settings me-1"></i>Setting Changed</th>
                                        <th><i class="fe fe-arrow-left me-1"></i>Previous Value</th>
                                        <th><i class="fe fe-arrow-right me-1"></i>New Value</th>
                                        <th><i class="fe fe-user me-1"></i>Changed By</th>
                                        <th><i class="fe fe-clock me-1"></i>Date</th>
                                        <th><i class="fe fe-more-horizontal me-1"></i>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentChanges as $change)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">{{ $change->setting_name }}</strong>
                                                <span class="badge {{ $change->getChangeTypeBadgeClass() }} ms-1">{{ $change->getChangeTypeText() }}</span>
                                            </td>
                                            <td>
                                                <code class="bg-light text-danger border rounded px-2 py-1">{!! $change->formatted_old_value !!}</code>
                                            </td>
                                            <td>
                                                <code class="bg-light text-success border rounded px-2 py-1">{!! $change->formatted_new_value !!}</code>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                                        {{ substr($change->changed_by_name, 0, 1) }}
                                                    </div>
                                                    {{ $change->changed_by_name }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $change->created_at->format('M d, Y') }}</span>
                                                <br>
                                                <small class="text-muted">{{ $change->created_at->format('H:i A') }}</small>
                                                <br>
                                                <small class="badge bg-light text-muted">{{ $change->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fe fe-more-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="#" onclick="viewChangeDetails({{ $change->id }})"><i class="fe fe-eye me-1"></i>View Details</a></li>
                                                        @if($change->created_at->diffInHours() < 24)
                                                            <li><a class="dropdown-item" href="#" onclick="confirmRevertChange({{ $change->id }})"><i class="fe fe-rotate-ccw me-1"></i>Revert Change</a></li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDeleteLog({{ $change->id }})"><i class="fe fe-trash me-1"></i>Delete Log</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-img">
                                                        <i class="fe fe-clock display-1 text-muted"></i>
                                                    </div>
                                                    <h4 class="mt-3 text-muted">No Recent Changes</h4>
                                                    <p class="text-muted mb-3">Configuration changes will appear here when settings are modified.</p>
                                                    <div class="alert alert-info">
                                                        <i class="fe fe-info me-2"></i>
                                                        <strong>Tip:</strong> Make any setting change above to see it logged here!
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Summary Footer -->
                        <div class="card-footer bg-light">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="text-muted">
                                        <strong class="text-primary display-6">{{ App\Models\ConfigurationChange::getChangesCount('week') }}</strong>
                                        <br>
                                        <small>Changes This Week</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted">
                                        <strong class="text-success display-6">{{ $recentChanges->first()->changed_by_name ?? 'No Changes' }}</strong>
                                        <br>
                                        <small>Last Modified By</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted">
                                        <strong class="text-warning display-6">{{ $recentChanges->first() ? $recentChanges->first()->created_at->format('M d') : 'N/A' }}</strong>
                                        <br>
                                        <small>Last Activity</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted">
                                        <strong class="text-info display-6">{{ isset($settingsArray['is_active']) && $settingsArray['is_active'] ? 'Active' : 'Inactive' }}</strong>
                                        <br>
                                        <small>System Status</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Backup Modal -->
    <div class="modal fade" id="backupModal" tabindex="-1" aria-labelledby="backupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backupModalLabel">Backup Lottery Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="backup_name" class="form-label">Backup Name</label>
                        <input type="text" class="form-control" id="backup_name" 
                               value="Lottery_Settings_{{ date('Y-m-d_H-i-s') }}" required>
                        <div class="form-text">Give your backup a descriptive name</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6>Backup Contents:</h6>
                        <ul class="mb-0">
                            <li>Current lottery settings configuration</li>
                            <li>Prize distribution setup</li>
                            <li>Automation preferences</li>
                            <li>Draw schedule settings</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createBackup()">
                        <i class="fe fe-download"></i> Create Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('styles')
    <style>
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        .prize-row {
            margin-bottom: 0.5rem;
        }
        .input-group-text {
            min-width: 80px;
        }
        .border {
            border-color: #dee2e6 !important;
        }
        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
    @endpush

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
    // Update next draw time when schedule changes
    function updateNextDrawTime() {
        const day = document.querySelector('[name="draw_day"]').value;
        const hour = document.querySelector('[name="draw_hour"]').value;
        const minute = document.querySelector('[name="draw_minute"]').value;
        
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const nextDraw = `${days[day]} at ${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
        
        document.getElementById('nextDrawTime').textContent = nextDraw;
    }

    // Add prize distribution row
    function addPrizeRow() {
        const container = document.getElementById('prizeDistribution');
        if (!container) {
            console.warn('prizeDistribution container not found');
            return;
        }
        
        const rows = container.children.length;
        const places = ['st', 'nd', 'rd', 'th'];
        const place = places[rows < 4 ? rows : 3];
        
        const typeElement = document.getElementById('prizeDistributionType');
        const isFixedAmount = typeElement ? typeElement.value === 'fixed_amount' : false;
        const unit = isFixedAmount ? '$' : '%';
        const max = isFixedAmount ? '100000' : '100';
        
        const div = document.createElement('div');
        div.className = 'col-md-6 col-lg-4 prize-row';
        div.innerHTML = `
            <div class="input-group">
                <span class="input-group-text">${rows + 1}${place} Place</span>
                <input type="number" name="prize_distribution[]" class="form-control prize-value-input" 
                       step="0.01" min="0" max="${max}" value="0" required>
                <span class="input-group-text prize-unit">${unit}</span>
                <button type="button" class="btn btn-outline-danger" onclick="removePrizeRow(this)">
                    <i class="fe fe-x"></i>
                </button>
            </div>
            <!-- Multiple Winners Configuration -->
            <div class="multiple-winners-config mt-2" style="display: none;">
                <div class="card card-body border-info">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-info fw-bold">Multiple Winners Distribution</small>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWinnerSlot(this, ${rows + 1})">
                            <i class="fe fe-plus"></i> Add Winner
                        </button>
                    </div>
                    <div class="winner-slots">
                        <div class="winner-slot mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Winner 1</span>
                                <input type="number" name="multiple_winners[${rows}][]" 
                                       class="form-control winner-value-input" 
                                       step="0.01" min="0" max="${max}" value="${isFixedAmount ? '0' : '100'}" placeholder="Amount/Percentage">
                                <span class="input-group-text winner-unit">${unit}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            Total for this position: <span class="position-total">${isFixedAmount ? '0' : '100'}</span><span class="position-unit">${unit}</span>
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(div);
        
        // Add event listener for the new prize input
        const newPrizeInput = div.querySelector('.prize-value-input');
        newPrizeInput.addEventListener('input', updateTotalPercentage);
        
        updatePrizeLabels();
        updateTotalPercentage();
        updateNumberOfWinners();
        updateMultipleWinnersVisibility();
    }

    // Add winner slot for multiple winners per place
    function addWinnerSlot(button, position) {
        const winnerSlots = button.closest('.multiple-winners-config').querySelector('.winner-slots');
        if (!winnerSlots) {
            console.warn('winner-slots container not found');
            return;
        }
        
        const winnerCount = winnerSlots.children.length + 1;
        const typeElement = document.getElementById('prizeDistributionType');
        const isFixedAmount = typeElement ? typeElement.value === 'fixed_amount' : false;
        const unit = isFixedAmount ? '$' : '%';
        const max = isFixedAmount ? '100000' : '100';
        
        const winnerSlot = document.createElement('div');
        winnerSlot.className = 'winner-slot mb-2';
        winnerSlot.innerHTML = `
            <div class="input-group input-group-sm">
                <span class="input-group-text">Winner ${winnerCount}</span>
                <input type="number" name="multiple_winners[${position - 1}][]" 
                       class="form-control winner-value-input" 
                       step="0.01" min="0" max="${max}" value="0" placeholder="Amount/Percentage">
                <span class="input-group-text winner-unit">${unit}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeWinnerSlot(this)">
                    <i class="fe fe-x"></i>
                </button>
            </div>
        `;
        
        winnerSlots.appendChild(winnerSlot);
        
        // Add event listener for the new input
        winnerSlot.querySelector('.winner-value-input').addEventListener('input', updatePositionTotals);
        
        updatePositionTotals();
    }

    // Remove winner slot
    function removeWinnerSlot(button) {
        const winnerSlots = button.closest('.winner-slots');
        if (winnerSlots.children.length > 1) {
            button.closest('.winner-slot').remove();
            updateWinnerLabels(winnerSlots);
            updatePositionTotals();
        }
    }

    // Update winner labels after removal
    function updateWinnerLabels(winnerSlots) {
        const slots = winnerSlots.querySelectorAll('.winner-slot');
        slots.forEach((slot, index) => {
            slot.querySelector('.input-group-text').textContent = `Winner ${index + 1}`;
        });
    }

    // Update position totals for multiple winners
    function updatePositionTotals() {
        document.querySelectorAll('.multiple-winners-config').forEach(config => {
            const inputs = config.querySelectorAll('.winner-value-input');
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value || 0);
            });
            
            const totalSpan = config.querySelector('.position-total');
            if (totalSpan) {
                totalSpan.textContent = total.toFixed(2);
            }
        });
    }

    // Toggle prize distribution type (percentage vs fixed amount)
    function togglePrizeDistributionType() {
        const typeElement = document.getElementById('prizeDistributionType');
        if (!typeElement) {
            console.warn('prizeDistributionType element not found');
            return;
        }
        
        const type = typeElement.value;
        const isFixedAmount = type === 'fixed_amount';
        const unit = isFixedAmount ? '$' : '%';
        const helperText = isFixedAmount ? 
            '<strong>Fixed Amount Mode:</strong> Enter specific dollar amounts' : 
            '<strong>Percentage Mode:</strong> Total must equal 100%';
        const maxValue = isFixedAmount ? '100000' : '100';
        
        // Update unit display
        const unitElement = document.getElementById('prizeDistributionUnit');
        const helperElement = document.getElementById('prizeDistributionHelper');
        const totalUnitElement = document.getElementById('totalUnit');
        
        if (unitElement) unitElement.textContent = `(${unit})`;
        if (helperElement) helperElement.innerHTML = `<small>${helperText}</small>`;
        if (totalUnitElement) totalUnitElement.textContent = unit;
        
        // Get the original prize structure data to maintain proper values
        const prizeStructure = @json($settingsArray['prize_structure'] ?? []);
        const currentDistributionType = '{{ $settingsArray['prize_distribution_type'] ?? 'percentage' }}';
        
        // Update all prize input units and max values
        document.querySelectorAll('.prize-unit').forEach(el => el.textContent = unit);
        document.querySelectorAll('.prize-value-input').forEach((el, index) => {
            el.max = maxValue;
            el.step = '0.01';
            
            // Get the correct value from prize structure based on type
            const position = index + 1;
            let correctValue = 0;
            
            if (prizeStructure && prizeStructure[position]) {
                const prizeData = prizeStructure[position];
                if (isFixedAmount && prizeData.amount !== undefined) {
                    correctValue = prizeData.amount;
                } else if (!isFixedAmount && prizeData.percentage !== undefined) {
                    correctValue = prizeData.percentage;
                } else if (isFixedAmount && prizeData.percentage !== undefined) {
                    // Converting from percentage to fixed amount - use a default amount
                    correctValue = prizeData.percentage > 0 ? 100 : 0;
                } else if (!isFixedAmount && prizeData.amount !== undefined) {
                    // Converting from fixed amount to percentage - use proportional percentage
                    correctValue = prizeData.amount > 0 ? 50 : 0;
                }
            }
            
            // Only set the value if it's within the valid range
            if (correctValue >= 0 && correctValue <= parseFloat(maxValue)) {
                el.value = correctValue;
            } else {
                el.value = '0';
            }
        });
        
        // Update winner input units and values
        document.querySelectorAll('.winner-unit').forEach(el => el.textContent = unit);
        document.querySelectorAll('.winner-value-input').forEach(el => {
            el.max = maxValue;
            el.step = '0.01';
            
            // Get position and winner index from the element's name attribute
            const nameAttr = el.getAttribute('name');
            if (nameAttr) {
                const matches = nameAttr.match(/multiple_winners\[(\d+)\]\[(\d+)\]/);
                if (matches) {
                    const position = parseInt(matches[1]) + 1;
                    const winnerIndex = parseInt(matches[2]);
                    
                    let correctValue = 0;
                    if (prizeStructure && prizeStructure[position] && prizeStructure[position].multiple_winners) {
                        const winnerData = prizeStructure[position].multiple_winners[winnerIndex];
                        if (winnerData) {
                            if (isFixedAmount && winnerData.amount !== undefined) {
                                correctValue = winnerData.amount;
                            } else if (!isFixedAmount && winnerData.percentage !== undefined) {
                                correctValue = winnerData.percentage;
                            } else if (isFixedAmount && winnerData.percentage !== undefined) {
                                // Converting from percentage to fixed amount
                                correctValue = winnerData.percentage > 0 ? 100 : 0;
                            } else if (!isFixedAmount && winnerData.amount !== undefined) {
                                // Converting from fixed amount to percentage
                                correctValue = winnerData.amount > 0 ? 25 : 0;
                            }
                        }
                    }
                    
                    // Only set the value if it's within the valid range
                    if (correctValue >= 0 && correctValue <= parseFloat(maxValue)) {
                        el.value = correctValue;
                    } else {
                        el.value = '0';
                    }
                }
            }
        });
        
        // Update position unit displays
        document.querySelectorAll('.position-unit').forEach(el => el.textContent = unit);
        
        updateTotalPercentage();
        updatePositionTotals();
    }

    // Toggle multiple winners visibility
    function updateMultipleWinnersVisibility() {
        const allowMultipleElement = document.getElementById('allowMultipleWinners');
        if (!allowMultipleElement) {
            console.warn('allowMultipleWinners element not found');
            return;
        }
        
        const allowMultiple = allowMultipleElement.checked;
        const configs = document.querySelectorAll('.multiple-winners-config');
        
        configs.forEach(config => {
            config.style.display = allowMultiple ? 'block' : 'none';
        });
    }

    // Remove prize distribution row
    function removePrizeRow(button) {
        if (document.querySelectorAll('.prize-row').length > 1) {
            button.closest('.prize-row').remove();
            updatePrizeLabels();
            updateTotalPercentage();
            updateNumberOfWinners();
        }
    }

    // Update prize row labels
    function updatePrizeLabels() {
        const rows = document.querySelectorAll('.prize-row');
        const places = ['st', 'nd', 'rd', 'th'];
        
        rows.forEach((row, index) => {
            const label = row.querySelector('.input-group-text');
            const place = places[index < 4 ? index : 3];
            label.textContent = `${index + 1}${place} Place`;
        });
    }

    // Update total percentage
    function updateTotalPercentage() {
        const inputs = document.querySelectorAll('[name="prize_distribution[]"]');
        const typeElement = document.getElementById('prizeDistributionType');
        const isFixedAmount = typeElement ? typeElement.value === 'fixed_amount' : false;
        let total = 0;
        
        inputs.forEach(input => {
            total += parseFloat(input.value || 0);
        });
        
        const totalSpan = document.getElementById('totalPercentage');
        totalSpan.textContent = total.toFixed(2);
        
        // Color code the total based on type
        if (isFixedAmount) {
            // For fixed amounts, just show the total
            totalSpan.className = 'text-info';
        } else {
            // For percentages, validate that total equals 100%
            if (total === 100) {
                totalSpan.className = 'text-success';
            } else if (total > 100) {
                totalSpan.className = 'text-danger';
            } else {
                totalSpan.className = 'text-warning';
            }
        }
    }

    // Update number of winners to match distribution rows
    function updateNumberOfWinners() {
        const rows = document.querySelectorAll('.prize-row').length;
        document.querySelector('[name="number_of_winners"]').value = rows;
    }

    // Reset form to original values
    function resetForm() {
        if (confirm('Are you sure you want to reset all changes?')) {
            document.getElementById('settingsForm').reset();
            updateNextDrawTime();
            updateTotalPercentage();
        }
    }

    // Reset to default settings
    function resetToDefaults() {
        if (confirm('Are you sure you want to reset all settings to default values? This cannot be undone.')) {
            // Set default values
            document.querySelector('[name="is_active"]').value = '1';
            document.querySelector('[name="ticket_price"]').value = '2.00';
            document.querySelector('[name="max_tickets_per_user"]').value = '10';
            document.querySelector('[name="min_tickets_for_draw"]').value = '5';
            document.querySelector('[name="draw_day"]').value = '0';
            document.querySelector('[name="draw_hour"]').value = '20';
            document.querySelector('[name="draw_minute"]').value = '0';
            document.querySelector('[name="admin_commission_percentage"]').value = '10';
            document.querySelector('[name="number_of_winners"]').value = '1';
            document.querySelector('[name="auto_claim_days"]').value = '30';
            document.querySelector('[name="auto_draw"]').checked = true;
            document.querySelector('[name="auto_prize_distribution"]').checked = true;
            document.querySelector('[name="auto_refund_cancelled"]').checked = true;
            document.querySelector('[name="ticket_expiry_hours"]').value = '24';
            document.querySelector('[name="prize_claim_deadline"]').value = '30';
            
            // Reset prize distribution to single winner
            const container = document.getElementById('prizeDistribution');
            container.innerHTML = `
                <div class="col-md-6 col-lg-4 prize-row">
                    <div class="input-group">
                        <span class="input-group-text">1st Place</span>
                        <input type="number" name="prize_distribution[]" class="form-control" 
                               step="0.01" min="0" max="100" value="100" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            `;
            
            updateNextDrawTime();
            updateTotalPercentage();
        }
    }

    // Create backup
    function createBackup() {
        const backupName = document.getElementById('backup_name').value;
        if (!backupName.trim()) {
            alert('Please enter a backup name.');
            return;
        }
        
        // Here you would make an AJAX call to create the backup
        // For now, we'll just show a success message and close modal
        alert('Backup created successfully: ' + backupName);
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('backupModal'));
        modal.hide();
    }

    // Form validation before submit
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
        const isFixedAmount = document.getElementById('prizeDistributionType').value === 'fixed_amount';
        const totalPercentage = parseFloat(document.getElementById('totalPercentage').textContent);
        
        console.log('Form submission - Total:', totalPercentage, 'Type:', isFixedAmount ? 'Fixed Amount' : 'Percentage');
        
        // Validate prize distribution based on type
        if (!isFixedAmount && Math.abs(totalPercentage - 100) > 0.01) {
            e.preventDefault();
            alert('Prize distribution percentages must total exactly 100%. Current total: ' + totalPercentage + '%');
            return false;
        }
        
        if (isFixedAmount && totalPercentage <= 0) {
            e.preventDefault();
            alert('Prize distribution amounts must be greater than 0. Current total: $' + totalPercentage);
            return false;
        }
        
        // Validate multiple winners distribution if enabled
        const allowMultipleWinners = document.getElementById('allowMultipleWinners').checked;
        if (allowMultipleWinners) {
            const multipleWinnersConfigs = document.querySelectorAll('.multiple-winners-config[style*="block"]');
            for (let config of multipleWinnersConfigs) {
                const positionTotal = parseFloat(config.querySelector('.position-total').textContent);
                const prizeRowInput = config.closest('.prize-row').querySelector('.prize-value-input');
                const mainPrizeValue = parseFloat(prizeRowInput.value || 0);
                
                if (!isFixedAmount && Math.abs(positionTotal - mainPrizeValue) > 0.01) {
                    e.preventDefault();
                    alert(`Multiple winners distribution for a position must equal the main prize value. Expected: ${mainPrizeValue}%, Got: ${positionTotal}%`);
                    return false;
                }
                
                if (isFixedAmount && Math.abs(positionTotal - mainPrizeValue) > 0.01) {
                    e.preventDefault();
                    alert(`Multiple winners distribution for a position must equal the main prize value. Expected: $${mainPrizeValue}, Got: $${positionTotal}`);
                    return false;
                }
            }
        }
        
        // Validate integer fields
        const integerFields = [
            'draw_minute', 'draw_hour', 'max_tickets_per_user', 'min_tickets_for_draw',
            'number_of_winners', 'auto_claim_days', 'ticket_expiry_hours', 
            'prize_claim_deadline', 'virtual_ticket_multiplier', 'virtual_ticket_base'
        ];
        
        for (const fieldName of integerFields) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && field.value) {
                const value = parseFloat(field.value);
                if (!Number.isInteger(value)) {
                    e.preventDefault();
                    alert(`${fieldName.replace(/_/g, ' ')} must be a whole number (integer).`);
                    field.focus();
                    return false;
                }
                // Force the value to be an integer
                field.value = Math.floor(Math.abs(value)).toString();
            }
        }
        
        console.log('Form validation passed, submitting...');
        return true;
    });

    // Fix existing values that exceed current mode limits
    function fixExistingValues() {
        const typeElement = document.getElementById('prizeDistributionType');
        const isFixedAmount = typeElement ? typeElement.value === 'fixed_amount' : false;
        const maxValue = isFixedAmount ? 100000 : 100;
        
        // Fix prize distribution values
        document.querySelectorAll('.prize-value-input').forEach(input => {
            const currentValue = parseFloat(input.value || 0);
            if (currentValue > maxValue) {
                input.value = isFixedAmount ? '0' : '0';
                console.log(`Fixed prize value from ${currentValue} to ${input.value} (max: ${maxValue})`);
            }
        });
        
        // Fix winner values
        document.querySelectorAll('.winner-value-input').forEach(input => {
            const currentValue = parseFloat(input.value || 0);
            if (currentValue > maxValue) {
                input.value = isFixedAmount ? '0' : '0';
                console.log(`Fixed winner value from ${currentValue} to ${input.value} (max: ${maxValue})`);
            }
        });
        
        // Update totals after fixing values
        updateTotalPercentage();
        updatePositionTotals();
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        updateNextDrawTime();
        updateTotalPercentage();
        
        // Update next draw time when schedule changes
        document.querySelector('[name="draw_day"]').addEventListener('change', updateNextDrawTime);
        document.querySelector('[name="draw_hour"]').addEventListener('change', updateNextDrawTime);
        document.querySelector('[name="draw_minute"]').addEventListener('change', updateNextDrawTime);
        
        // Update total percentage when distribution changes
        document.querySelectorAll('[name="prize_distribution[]"]').forEach(input => {
            input.addEventListener('input', updateTotalPercentage);
        });
        
        // Handle prize distribution type changes
        const prizeDistributionTypeElement = document.getElementById('prizeDistributionType');
        if (prizeDistributionTypeElement) {
            prizeDistributionTypeElement.addEventListener('change', togglePrizeDistributionType);
        }
        
        // Handle multiple winners per place toggle
        const allowMultipleWinnersElement = document.getElementById('allowMultipleWinners');
        if (allowMultipleWinnersElement) {
            allowMultipleWinnersElement.addEventListener('change', updateMultipleWinnersVisibility);
        }
        
        // Add event listeners for existing winner inputs
        document.querySelectorAll('.winner-value-input').forEach(input => {
            input.addEventListener('input', updatePositionTotals);
        });
        
        // Initialize the display based on current settings
        togglePrizeDistributionType();
        updateMultipleWinnersVisibility();
        updatePositionTotals();
        
        // Fix any existing values that exceed limits
        fixExistingValues();
        
        // Prevent decimal input for integer fields
        const integerFields = [
            'draw_minute', 'draw_hour', 'max_tickets_per_user', 'min_tickets_for_draw',
            'number_of_winners', 'auto_claim_days', 'ticket_expiry_hours', 
            'prize_claim_deadline', 'virtual_ticket_multiplier', 'virtual_ticket_base'
        ];
        
        integerFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.addEventListener('input', function(e) {
                    // Remove any decimal points and non-numeric characters except digits
                    let value = this.value.replace(/[^0-9]/g, '');
                    
                    // Apply field-specific constraints
                    if (fieldName === 'draw_minute') {
                        value = Math.min(parseInt(value) || 0, 59).toString();
                    } else if (fieldName === 'draw_hour') {
                        value = Math.min(parseInt(value) || 0, 23).toString();
                    }
                    
                    this.value = value;
                });
                
                field.addEventListener('keypress', function(e) {
                    // Only allow numeric input
                    const char = String.fromCharCode(e.which);
                    if (!/[0-9]/.test(char)) {
                        e.preventDefault();
                    }
                });
                
                field.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const numericValue = paste.replace(/[^0-9]/g, '');
                    this.value = numericValue;
                    this.dispatchEvent(new Event('input'));
                });
            }
        });
    });

    // Virtual User ID validation
    document.addEventListener('DOMContentLoaded', function() {
        const virtualUserIdField = document.querySelector('input[name="virtual_user_id"]');
        if (virtualUserIdField) {
            // Add validation styling and feedback
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.style.display = 'none';
            virtualUserIdField.parentNode.appendChild(feedback);

            virtualUserIdField.addEventListener('input', function() {
                const value = parseInt(this.value);
                let isValid = true;
                let message = '';

                if (isNaN(value) || value < 1) {
                    isValid = false;
                    message = 'Virtual User ID must be at least 1';
                } else if (value > 999999) {
                    isValid = false;
                    message = 'Virtual User ID cannot exceed 999,999';
                }

                if (isValid) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    feedback.style.display = 'none';
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    feedback.textContent = message;
                    feedback.style.display = 'block';
                }
            });

            // Trigger validation on page load
            virtualUserIdField.dispatchEvent(new Event('input'));
        }
    });

    // ===========================================
    // VIRTUAL TICKET SETTINGS FUNCTIONALITY
    // ===========================================
    
    // Handle Show Virtual Tickets checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const showVirtualTicketsCheckbox = document.getElementById('showVirtualTicketsCheckbox');
        const virtualTicketSettings = document.querySelectorAll('.virtual-ticket-settings');
        const virtualTicketInputs = document.querySelectorAll('.virtual-ticket-input');
        
        if (!showVirtualTicketsCheckbox) {
            console.error('Virtual tickets checkbox not found!');
            return;
        }
        
        
        function toggleVirtualTicketSettings() {
            const isChecked = showVirtualTicketsCheckbox.checked;
            
            virtualTicketSettings.forEach(setting => {
                setting.style.display = isChecked ? 'block' : 'none';
            });
            
            // Handle required attribute and validation
            virtualTicketInputs.forEach(input => {
                if (isChecked) {
                    input.setAttribute('required', 'required');
                    // Set default values if empty or invalid
                    if (input.name === 'virtual_ticket_multiplier' && (input.value === '' || parseInt(input.value) < 100)) {
                        input.value = '200';
                    }
                    if (input.name === 'virtual_ticket_base' && input.value === '') {
                        input.value = '0';
                    }
                } else {
                    input.removeAttribute('required');
                    // Remove validation classes when disabled
                    input.classList.remove('is-invalid', 'is-valid');
                }
            });
        }
        
        // Initial state
        toggleVirtualTicketSettings();
        
        // Handle checkbox change
        showVirtualTicketsCheckbox.addEventListener('change', function() {
            toggleVirtualTicketSettings();
        });
        
        // Form submission validation
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            const isVirtualTicketsEnabled = showVirtualTicketsCheckbox.checked;
            
            if (isVirtualTicketsEnabled) {
                let isValid = true;
                
                virtualTicketInputs.forEach(input => {
                    const value = parseInt(input.value);
                    
                    if (input.name === 'virtual_ticket_multiplier') {
                        if (isNaN(value) || value < 100 || value > 1000) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        }
                    }
                    
                    if (input.name === 'virtual_ticket_base') {
                        if (isNaN(value) || value < 0 || value > 10000) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        }
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Scroll to first invalid input
                    const firstInvalid = document.querySelector('.virtual-ticket-input.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please fix the errors in virtual ticket settings before saving.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert('Please fix the errors in virtual ticket settings before saving.');
                    }
                }
            }
        });
    });
</script>
@endpush
</x-layout>
