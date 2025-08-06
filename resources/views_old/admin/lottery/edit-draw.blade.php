<x-layout>
    @section('top_title', 'Edit Lottery Draw')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Edit Lottery Draw')
            
            <!-- Back Button -->
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.lottery.draws.details', $draw->id) }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-left"></i> Back to Draw Details
                    </a>
                    <div class="text-muted">
                        <small>Draw ID: #{{ $draw->id }} | Status: 
                            <span class="badge bg-{{ $draw->status === 'pending' ? 'warning' : ($draw->status === 'completed' ? 'success' : 'info') }}">
                                {{ ucfirst($draw->status) }}
                            </span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="col-12 mb-3">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h6><i class="fe fe-check"></i> Success</h6>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="col-12 mb-3">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fe fe-alert-triangle"></i> Error</h6>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="col-12 mb-3">
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
            @endif

            <!-- Edit Draw Form -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fe fe-edit-2"></i> Edit Draw Information
                        </h4>
                        <p class="text-muted mb-0">Update draw details and configuration</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.lottery.draws.update', $draw->id) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3 text-primary">Basic Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="draw_number" class="form-label">Draw Number</label>
                                        <input type="text" class="form-control @error('draw_number') is-invalid @enderror" 
                                               id="draw_number" name="draw_number" 
                                               value="{{ old('draw_number', $draw->draw_number) }}"
                                               placeholder="Auto-generated if left empty">
                                        @error('draw_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="draw_date" class="form-label">Draw Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('draw_date') is-invalid @enderror" 
                                                       id="draw_date" name="draw_date" 
                                                       value="{{ old('draw_date', $draw->draw_date ? $draw->draw_date->format('Y-m-d') : '') }}" required>
                                                @error('draw_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="draw_time" class="form-label">Draw Time <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control @error('draw_time') is-invalid @enderror" 
                                                       id="draw_time" name="draw_time" 
                                                       value="{{ old('draw_time', $draw->draw_date ? $draw->draw_date->format('H:i') : '') }}" required>
                                                @error('draw_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="ticket_price" class="form-label">Ticket Price ($) <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0.01" 
                                                       class="form-control @error('ticket_price') is-invalid @enderror" 
                                                       id="ticket_price" name="ticket_price" 
                                                       value="{{ old('ticket_price', $draw->ticket_price) }}" required
                                                       {{ $draw->total_tickets_sold > 0 ? 'readonly' : '' }}>
                                                @if($draw->total_tickets_sold > 0)
                                                    <div class="form-text text-warning">
                                                        <i class="fe fe-info"></i> Cannot change price after tickets are sold
                                                    </div>
                                                @endif
                                                @error('ticket_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_tickets" class="form-label">Max Tickets</label>
                                                <input type="number" min="1" class="form-control @error('max_tickets') is-invalid @enderror" 
                                                       id="max_tickets" name="max_tickets" 
                                                       value="{{ old('max_tickets', $draw->max_tickets) }}"
                                                       placeholder="No limit if empty">
                                                @error('max_tickets')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3"
                                                  placeholder="Optional description for this draw">{{ old('description', $draw->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Prize Configuration -->
                                <div class="col-md-6">
                                    <h5 class="mb-3 text-success">Prize Configuration</h5>
                                    
                                    <div class="mb-3">
                                        <label for="total_prize_pool" class="form-label">Total Prize Pool ($) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('total_prize_pool') is-invalid @enderror" 
                                               id="total_prize_pool" name="total_prize_pool" 
                                               value="{{ old('total_prize_pool', $draw->total_prize_pool) }}" required>
                                        @error('total_prize_pool')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="first_prize" class="form-label">First Prize ($) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('first_prize') is-invalid @enderror" 
                                               id="first_prize" name="first_prize" 
                                               value="{{ old('first_prize', $draw->first_prize) }}" required>
                                        @error('first_prize')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="second_prize" class="form-label">Second Prize ($)</label>
                                                <input type="number" step="0.01" min="0" 
                                                       class="form-control @error('second_prize') is-invalid @enderror" 
                                                       id="second_prize" name="second_prize" 
                                                       value="{{ old('second_prize', $draw->second_prize) }}">
                                                @error('second_prize')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="third_prize" class="form-label">Third Prize ($)</label>
                                                <input type="number" step="0.01" min="0" 
                                                       class="form-control @error('third_prize') is-invalid @enderror" 
                                                       id="third_prize" name="third_prize" 
                                                       value="{{ old('third_prize', $draw->third_prize) }}">
                                                @error('third_prize')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="consolation_prizes" class="form-label">Consolation Prizes Count</label>
                                                <input type="number" min="0" 
                                                       class="form-control @error('consolation_prizes') is-invalid @enderror" 
                                                       id="consolation_prizes" name="consolation_prizes" 
                                                       value="{{ old('consolation_prizes', $draw->consolation_prizes) }}">
                                                @error('consolation_prizes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="consolation_prize_amount" class="form-label">Consolation Prize Amount ($)</label>
                                                <input type="number" step="0.01" min="0" 
                                                       class="form-control @error('consolation_prize_amount') is-invalid @enderror" 
                                                       id="consolation_prize_amount" name="consolation_prize_amount" 
                                                       value="{{ old('consolation_prize_amount', $draw->consolation_prize_amount) }}">
                                                @error('consolation_prize_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3 text-info">Terms and Conditions</h5>
                                    <div class="mb-3">
                                        <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                        <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" 
                                                  id="terms_and_conditions" name="terms_and_conditions" rows="4"
                                                  placeholder="Enter terms and conditions for this draw">{{ old('terms_and_conditions', $draw->terms_and_conditions) }}</textarea>
                                        @error('terms_and_conditions')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Status and Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                   {{ old('is_active', $draw->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                <strong>Active Draw</strong>
                                                <small class="text-muted d-block">Allow ticket purchases for this draw</small>
                                            </label>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.lottery.draws.details', $draw->id) }}" class="btn btn-secondary">
                                                <i class="fe fe-x"></i> Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fe fe-save"></i> Update Draw
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Current Draw Statistics -->
            <div class="col-12 mt-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fe fe-bar-chart-2"></i> Current Draw Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h4 class="text-primary mb-1">{{ $draw->total_tickets_sold ?? 0 }}</h4>
                                    <p class="text-muted mb-0">Tickets Sold</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h4 class="text-success mb-1">${{ number_format($draw->total_revenue ?? 0, 2) }}</h4>
                                    <p class="text-muted mb-0">Revenue Generated</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h4 class="text-warning mb-1">{{ $draw->winners()->count() }}</h4>
                                    <p class="text-muted mb-0">Winners</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h4 class="text-info mb-1">{{ ucfirst($draw->status) }}</h4>
                                    <p class="text-muted mb-0">Status</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

@push('script')
<script>
    // Auto-calculate prize distribution
    function updatePrizeCalculation() {
        const totalPrizePool = parseFloat(document.getElementById('total_prize_pool').value) || 0;
        const firstPrize = parseFloat(document.getElementById('first_prize').value) || 0;
        const secondPrize = parseFloat(document.getElementById('second_prize').value) || 0;
        const thirdPrize = parseFloat(document.getElementById('third_prize').value) || 0;
        const consolationCount = parseInt(document.getElementById('consolation_prizes').value) || 0;
        const consolationAmount = parseFloat(document.getElementById('consolation_prize_amount').value) || 0;
        
        const totalAllocated = firstPrize + secondPrize + thirdPrize + (consolationCount * consolationAmount);
        const remaining = totalPrizePool - totalAllocated;
        
        // Show prize breakdown information
        if (totalPrizePool > 0) {
            let info = `<small class="text-muted">Prize allocation: $${totalAllocated.toFixed(2)} / $${totalPrizePool.toFixed(2)}`;
            if (remaining < 0) {
                info += ` <span class="text-danger">(Over by $${Math.abs(remaining).toFixed(2)})</span>`;
            } else if (remaining > 0) {
                info += ` <span class="text-success">(Remaining: $${remaining.toFixed(2)})</span>`;
            }
            info += `</small>`;
            
            // Find or create info element
            let infoElement = document.getElementById('prize-info');
            if (!infoElement) {
                infoElement = document.createElement('div');
                infoElement.id = 'prize-info';
                infoElement.className = 'form-text';
                document.getElementById('total_prize_pool').parentNode.appendChild(infoElement);
            }
            infoElement.innerHTML = info;
        }
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const prizeInputs = ['total_prize_pool', 'first_prize', 'second_prize', 'third_prize', 
                           'consolation_prizes', 'consolation_prize_amount'];
        
        prizeInputs.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', updatePrizeCalculation);
            }
        });
        
        // Initial calculation
        updatePrizeCalculation();
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const drawDate = new Date(document.getElementById('draw_date').value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (drawDate < today) {
            e.preventDefault();
            alert('Draw date cannot be in the past.');
            return false;
        }
        
        const totalPrizePool = parseFloat(document.getElementById('total_prize_pool').value) || 0;
        const firstPrize = parseFloat(document.getElementById('first_prize').value) || 0;
        
        if (firstPrize > totalPrizePool) {
            e.preventDefault();
            alert('First prize cannot be greater than total prize pool.');
            return false;
        }
    });
</script>
@endpush
</x-layout>
