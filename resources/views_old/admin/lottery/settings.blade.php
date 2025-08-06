<x-layout>

@section('content')
<div class="row my-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-cogs mr-2"></i>
                    Lottery Settings
                </h4>
                <p class="card-text">Configure lottery system parameters and rules</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card-body">
                <form method="POST" action="{{ route('admin.lottery-settings.update') }}" id="lotterySettingsForm">
                    @csrf

                    <!-- Basic Settings -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lottery_enabled">
                                    <i class="fas fa-power-off mr-1"></i>
                                    Lottery System Status
                                </label>
                                <select name="lottery_enabled" id="lottery_enabled" class="form-control">
                                    <option value="1" {{ old('lottery_enabled', $settings->lottery_enabled ?? 1) == 1 ? 'selected' : '' }}>
                                        Enabled
                                    </option>
                                    <option value="0" {{ old('lottery_enabled', $settings->lottery_enabled ?? 1) == 0 ? 'selected' : '' }}>
                                        Disabled
                                    </option>
                                </select>
                                <small class="form-text text-muted">Enable or disable the entire lottery system</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ticket_price">
                                    <i class="fas fa-tag mr-1"></i>
                                    Ticket Price ($)
                                </label>
                                <input type="number" 
                                        name="ticket_price" 
                                        id="ticket_price" 
                                        class="form-control" 
                                        value="{{ old('ticket_price', $settings->ticket_price ?? 1) }}" 
                                        min="0.01" 
                                        step="0.01" 
                                        required>
                                <small class="form-text text-muted">Price per lottery ticket</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_tickets_per_user">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Max Tickets Per User
                                </label>
                                <input type="number" 
                                        name="max_tickets_per_user" 
                                        id="max_tickets_per_user" 
                                        class="form-control" 
                                        value="{{ old('max_tickets_per_user', $settings->max_tickets_per_user ?? 10) }}" 
                                        min="1" 
                                        required>
                                <small class="form-text text-muted">Maximum tickets a user can buy per draw</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_tickets_per_draw">
                                    <i class="fas fa-ticket-alt mr-1"></i>
                                    Max Tickets Per Draw
                                </label>
                                <input type="number" 
                                        name="max_tickets_per_draw" 
                                        id="max_tickets_per_draw" 
                                        class="form-control" 
                                        value="{{ old('max_tickets_per_draw', $settings->max_tickets_per_draw ?? 1000) }}" 
                                        min="1" 
                                        required>
                                <small class="form-text text-muted">Maximum total tickets allowed per draw</small>
                            </div>
                        </div>
                    </div>

                    <!-- Prize Distribution -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy mr-2"></i>
                                Prize Distribution Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="first_prize_percentage">
                                            <i class="fas fa-medal mr-1" style="color: gold;"></i>
                                            1st Prize (%)
                                        </label>
                                        <input type="number" 
                                                name="first_prize_percentage" 
                                                id="first_prize_percentage" 
                                                class="form-control" 
                                                value="{{ old('first_prize_percentage', $settings->first_prize_percentage ?? 50) }}" 
                                                min="0" 
                                                max="100" 
                                                step="0.1" 
                                                required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="second_prize_percentage">
                                            <i class="fas fa-medal mr-1" style="color: silver;"></i>
                                            2nd Prize (%)
                                        </label>
                                        <input type="number" 
                                                name="second_prize_percentage" 
                                                id="second_prize_percentage" 
                                                class="form-control" 
                                                value="{{ old('second_prize_percentage', $settings->second_prize_percentage ?? 30) }}" 
                                                min="0" 
                                                max="100" 
                                                step="0.1" 
                                                required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="third_prize_percentage">
                                            <i class="fas fa-medal mr-1" style="color: #cd7f32;"></i>
                                            3rd Prize (%)
                                        </label>
                                        <input type="number" 
                                                name="third_prize_percentage" 
                                                id="third_prize_percentage" 
                                                class="form-control" 
                                                value="{{ old('third_prize_percentage', $settings->third_prize_percentage ?? 20) }}" 
                                                min="0" 
                                                max="100" 
                                                step="0.1" 
                                                required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="admin_commission_percentage">
                                            <i class="fas fa-percentage mr-1"></i>
                                            Admin Commission (%)
                                        </label>
                                        <input type="number" 
                                                name="admin_commission_percentage" 
                                                id="admin_commission_percentage" 
                                                class="form-control" 
                                                value="{{ old('admin_commission_percentage', $settings->admin_commission_percentage ?? 10) }}" 
                                                min="0" 
                                                max="50" 
                                                step="0.1" 
                                                required>
                                        <small class="form-text text-muted">Commission taken from total sales</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="min_tickets_for_draw">
                                            <i class="fas fa-users mr-1"></i>
                                            Minimum Tickets for Draw
                                        </label>
                                        <input type="number" 
                                                name="min_tickets_for_draw" 
                                                id="min_tickets_for_draw" 
                                                class="form-control" 
                                                value="{{ old('min_tickets_for_draw', $settings->min_tickets_for_draw ?? 10) }}" 
                                                min="1" 
                                                required>
                                        <small class="form-text text-muted">Minimum tickets sold to proceed with draw</small>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Prize Pool Calculation:</strong> Total prize pool = (Ticket Sales - Admin Commission). 
                                Prize percentages are calculated from this pool.
                            </div>
                        </div>
                    </div>

                    <!-- Draw Settings -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Draw Schedule Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="auto_draw_enabled">
                                            <i class="fas fa-robot mr-1"></i>
                                            Auto Draw
                                        </label>
                                        <select name="auto_draw_enabled" id="auto_draw_enabled" class="form-control">
                                            <option value="1" {{ old('auto_draw_enabled', $settings->auto_draw_enabled ?? 0) == 1 ? 'selected' : '' }}>
                                                Enabled
                                            </option>
                                            <option value="0" {{ old('auto_draw_enabled', $settings->auto_draw_enabled ?? 0) == 0 ? 'selected' : '' }}>
                                                Manual Only
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">Automatically perform draws at scheduled times</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="draw_frequency">
                                            <i class="fas fa-clock mr-1"></i>
                                            Draw Frequency
                                        </label>
                                        <select name="draw_frequency" id="draw_frequency" class="form-control">
                                            <option value="daily" {{ old('draw_frequency', $settings->draw_frequency ?? 'weekly') == 'daily' ? 'selected' : '' }}>
                                                Daily
                                            </option>
                                            <option value="weekly" {{ old('draw_frequency', $settings->draw_frequency ?? 'weekly') == 'weekly' ? 'selected' : '' }}>
                                                Weekly
                                            </option>
                                            <option value="monthly" {{ old('draw_frequency', $settings->draw_frequency ?? 'weekly') == 'monthly' ? 'selected' : '' }}>
                                                Monthly
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ticket_sale_hours">
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                            Ticket Sale Duration (Hours)
                                        </label>
                                        <input type="number" 
                                                name="ticket_sale_hours" 
                                                id="ticket_sale_hours" 
                                                class="form-control" 
                                                value="{{ old('ticket_sale_hours', $settings->ticket_sale_hours ?? 24) }}" 
                                                min="1" 
                                                max="168" 
                                                required>
                                        <small class="form-text text-muted">How long tickets are sold before draw</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="auto_distribute_prizes">
                                            <i class="fas fa-coins mr-1"></i>
                                            Auto Distribute Prizes
                                        </label>
                                        <select name="auto_distribute_prizes" id="auto_distribute_prizes" class="form-control">
                                            <option value="1" {{ old('auto_distribute_prizes', $settings->auto_distribute_prizes ?? 1) == 1 ? 'selected' : '' }}>
                                                Enabled
                                            </option>
                                            <option value="0" {{ old('auto_distribute_prizes', $settings->auto_distribute_prizes ?? 1) == 0 ? 'selected' : '' }}>
                                                Manual Only
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">Automatically distribute prizes after draw</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bell mr-2"></i>
                                Notification Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notify_winners">
                                            <i class="fas fa-envelope mr-1"></i>
                                            Notify Winners
                                        </label>
                                        <select name="notify_winners" id="notify_winners" class="form-control">
                                            <option value="1" {{ old('notify_winners', $settings->notify_winners ?? 1) == 1 ? 'selected' : '' }}>
                                                Enabled
                                            </option>
                                            <option value="0" {{ old('notify_winners', $settings->notify_winners ?? 1) == 0 ? 'selected' : '' }}>
                                                Disabled
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notify_draw_results">
                                            <i class="fas fa-bullhorn mr-1"></i>
                                            Notify Draw Results
                                        </label>
                                        <select name="notify_draw_results" id="notify_draw_results" class="form-control">
                                            <option value="1" {{ old('notify_draw_results', $settings->notify_draw_results ?? 1) == 1 ? 'selected' : '' }}>
                                                Enabled
                                            </option>
                                            <option value="0" {{ old('notify_draw_results', $settings->notify_draw_results ?? 1) == 0 ? 'selected' : '' }}>
                                                Disabled
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save mr-2"></i>
                                        Save Settings
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="resetForm()">
                                        <i class="fas fa-undo mr-2"></i>
                                        Reset
                                    </button>
                                </div>

                                <div>
                                    <button type="button" class="btn btn-info btn-lg" onclick="validatePrizePercentages()">
                                        <i class="fas fa-calculator mr-2"></i>
                                        Validate Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Settings Summary Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Current Settings Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary">${{ $settings->ticket_price ?? 1 }}</h4>
                            <small class="text-muted">Ticket Price</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success">{{ $settings->max_tickets_per_user ?? 10 }}</h4>
                            <small class="text-muted">Max Tickets/User</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning">{{ $settings->admin_commission_percentage ?? 10 }}%</h4>
                            <small class="text-muted">Admin Commission</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info">{{ ucfirst($settings->draw_frequency ?? 'weekly') }}</h4>
                            <small class="text-muted">Draw Frequency</small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-light">
                            <strong>Prize Distribution:</strong>
                            1st Prize: {{ $settings->first_prize_percentage ?? 50 }}% |
                            2nd Prize: {{ $settings->second_prize_percentage ?? 30 }}% |
                            3rd Prize: {{ $settings->third_prize_percentage ?? 20 }}%
                            <br>
                            <strong>Total Prize Pool:</strong> {{ ($settings->first_prize_percentage ?? 50) + ($settings->second_prize_percentage ?? 30) + ($settings->third_prize_percentage ?? 20) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time validation of prize percentages
    const prizeInputs = ['first_prize_percentage', 'second_prize_percentage', 'third_prize_percentage'];
    
    prizeInputs.forEach(inputId => {
        document.getElementById(inputId).addEventListener('input', function() {
            validatePrizePercentages();
        });
    });
});

function validatePrizePercentages() {
    const first = parseFloat(document.getElementById('first_prize_percentage').value) || 0;
    const second = parseFloat(document.getElementById('second_prize_percentage').value) || 0;
    const third = parseFloat(document.getElementById('third_prize_percentage').value) || 0;
    
    const total = first + second + third;
    const alertDiv = document.querySelector('.prize-validation-alert');
    
    // Remove existing alert
    if (alertDiv) {
        alertDiv.remove();
    }
    
    // Create new alert
    const prizeCard = document.querySelector('.card-body');
    let alertClass = 'alert-success';
    let alertText = `Prize percentages total: ${total.toFixed(1)}% ✓`;
    
    if (total > 100) {
        alertClass = 'alert-danger';
        alertText = `Prize percentages total: ${total.toFixed(1)}% - Cannot exceed 100%!`;
    } else if (total < 90) {
        alertClass = 'alert-warning';
        alertText = `Prize percentages total: ${total.toFixed(1)}% - Consider increasing for better user engagement`;
    }
    
    const newAlert = document.createElement('div');
    newAlert.className = `alert ${alertClass} prize-validation-alert mt-3`;
    newAlert.innerHTML = `<i class="fas fa-calculator mr-2"></i>${alertText}`;
    prizeCard.appendChild(newAlert);
}

function resetForm() {
    if (confirm('Are you sure you want to reset all settings to their current saved values?')) {
        document.getElementById('lotterySettingsForm').reset();
    }
}

// Form validation before submit
document.getElementById('lotterySettingsForm').addEventListener('submit', function(e) {
    const first = parseFloat(document.getElementById('first_prize_percentage').value) || 0;
    const second = parseFloat(document.getElementById('second_prize_percentage').value) || 0;
    const third = parseFloat(document.getElementById('third_prize_percentage').value) || 0;
    
    if (first + second + third > 100) {
        e.preventDefault();
        alert('Prize percentages cannot exceed 100% total!');
        return false;
    }
    
    if (first + second + third < 50) {
        if (!confirm('Prize percentages total less than 50%. This may result in low user engagement. Continue?')) {
            e.preventDefault();
            return false;
        }
    }
});
</script>
@endsection
</x-layout>
