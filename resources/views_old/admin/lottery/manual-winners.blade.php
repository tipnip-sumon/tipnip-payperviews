@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header">
                <h5 class="card-title">Manual Winner Selection - Draw #{{ $draw->draw_number }}</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge badge--{{ $draw->status == 'pending' ? 'warning' : 'success' }}">
                            {{ ucfirst($draw->status) }}
                        </span>
                    </div>
                    <a href="{{ route('admin.lottery.draws.details', $draw->id) }}" class="btn btn--dark btn--sm">
                        <i class="las la-arrow-left"></i> Back to Draw Details
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Draw Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6>Draw Information</h6>
                            <p><strong>Draw Number:</strong> {{ $draw->draw_number }}</p>
                            <p><strong>Draw Date:</strong> {{ showDateTime($draw->draw_date) }}</p>
                            <p><strong>Total Tickets:</strong> {{ $tickets->count() }}</p>
                            <p><strong>Ticket Price:</strong> {{ getAmount($draw->ticket_price) }} {{ __($general->cur_text) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6>Prize Structure</h6>
                            @foreach($prizeStructure as $position => $prize)
                                <p><strong>{{ ordinal($position) }} Place:</strong> 
                                    @if($draw->lotterySetting->prize_distribution_type == 'fixed')
                                        {{ getAmount($prize['amount']) }} {{ __($general->cur_text) }}
                                    @else
                                        {{ $prize['percentage'] }}% of total collection
                                    @endif
                                    @if($draw->lotterySetting->allow_multiple_winners_per_place)
                                        (Max {{ $prize['max_winners'] ?? 1 }} winners)
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($tickets->isEmpty())
                    <div class="alert alert-warning">
                        <i class="las la-exclamation-triangle"></i>
                        No tickets have been sold for this draw yet.
                    </div>
                @else
                    <!-- Winner Selection Form -->
                    <form id="manualWinnerForm">
                        @csrf
                        <div id="winnersContainer">
                            @foreach($prizeStructure as $position => $prize)
                                <div class="prize-position mb-4" data-position="{{ $position }}">
                                    <h6 class="text-primary">{{ ordinal($position) }} Place Winners</h6>
                                    
                                    <div class="winners-for-position" data-max-winners="{{ $prize['max_winners'] ?? 1 }}">
                                        <div class="winner-selection mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Select Ticket/User</label>
                                                    <select name="winners[{{ $position }}][0][ticket_id]" class="form-control ticket-select" required>
                                                        <option value="">-- Select Ticket --</option>
                                                        @foreach($tickets as $ticket)
                                                            <option value="{{ $ticket->id }}" data-user="{{ $ticket->user->firstname }} {{ $ticket->user->lastname }}" data-ticket="{{ $ticket->ticket_number }}">
                                                                {{ $ticket->ticket_number }} - {{ $ticket->user->firstname }} {{ $ticket->user->lastname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Prize Amount</label>
                                                    <div class="input-group">
                                                        <input type="number" name="winners[{{ $position }}][0][prize_amount]" 
                                                               class="form-control prize-amount" 
                                                               value="{{ $prize['amount'] ?? 0 }}" 
                                                               step="0.01" min="0" required>
                                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                    </div>
                                                    <input type="hidden" name="winners[{{ $position }}][0][position]" value="{{ $position }}">
                                                </div>
                                                <div class="col-md-2">
                                                    @if(($prize['max_winners'] ?? 1) > 1)
                                                        <label class="form-label">&nbsp;</label>
                                                        <button type="button" class="btn btn--success btn--sm add-winner" style="display: block; width: 100%;">
                                                            <i class="las la-plus"></i> Add Winner
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn--primary btn-lg">
                                <i class="las la-check-circle"></i> Select Winners & Complete Draw
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .info-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }
    .info-card h6 {
        color: #007bff;
        margin-bottom: 10px;
        font-weight: 600;
    }
    .info-card p {
        margin-bottom: 5px;
        font-size: 14px;
    }
    .prize-position {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        background: #ffffff;
    }
    .winner-selection {
        border: 1px dashed #dee2e6;
        padding: 15px;
        border-radius: 6px;
        background: #f8f9fa;
    }
    .remove-winner {
        color: #dc3545;
    }
    .selected-tickets {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        let winnerCount = {};
        
        // Initialize winner counters
        $('.prize-position').each(function() {
            let position = $(this).data('position');
            winnerCount[position] = 1;
        });

        // Add winner functionality
        $(document).on('click', '.add-winner', function() {
            let prizePosition = $(this).closest('.prize-position');
            let position = prizePosition.data('position');
            let winnersContainer = prizePosition.find('.winners-for-position');
            let maxWinners = winnersContainer.data('max-winners');
            
            if (winnerCount[position] < maxWinners) {
                let newWinnerHtml = `
                    <div class="winner-selection mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Select Ticket/User</label>
                                <select name="winners[${position}][${winnerCount[position]}][ticket_id]" class="form-control ticket-select" required>
                                    <option value="">-- Select Ticket --</option>
                                    @foreach($tickets as $ticket)
                                        <option value="{{ $ticket->id }}" data-user="{{ $ticket->user->firstname }} {{ $ticket->user->lastname }}" data-ticket="{{ $ticket->ticket_number }}">
                                            {{ $ticket->ticket_number }} - {{ $ticket->user->firstname }} {{ $ticket->user->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prize Amount</label>
                                <div class="input-group">
                                    <input type="number" name="winners[${position}][${winnerCount[position]}][prize_amount]" 
                                           class="form-control prize-amount" 
                                           value="${prizePosition.find('.prize-amount:first').val()}" 
                                           step="0.01" min="0" required>
                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                </div>
                                <input type="hidden" name="winners[${position}][${winnerCount[position]}][position]" value="${position}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn--danger btn--sm remove-winner" style="display: block; width: 100%;">
                                    <i class="las la-minus"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                winnersContainer.append(newWinnerHtml);
                winnerCount[position]++;
                
                // Hide add button if max reached
                if (winnerCount[position] >= maxWinners) {
                    prizePosition.find('.add-winner').hide();
                }
                
                updateTicketOptions();
            }
        });

        // Remove winner functionality
        $(document).on('click', '.remove-winner', function() {
            let prizePosition = $(this).closest('.prize-position');
            let position = prizePosition.data('position');
            
            $(this).closest('.winner-selection').remove();
            winnerCount[position]--;
            
            // Show add button again
            prizePosition.find('.add-winner').show();
            
            updateTicketOptions();
        });

        // Update ticket options to prevent duplicate selections
        function updateTicketOptions() {
            let selectedTickets = [];
            $('.ticket-select').each(function() {
                if ($(this).val()) {
                    selectedTickets.push($(this).val());
                }
            });

            $('.ticket-select').each(function() {
                let currentValue = $(this).val();
                $(this).find('option').each(function() {
                    let optionValue = $(this).val();
                    if (optionValue && selectedTickets.includes(optionValue) && optionValue !== currentValue) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });
        }

        // Handle ticket selection change
        $(document).on('change', '.ticket-select', function() {
            updateTicketOptions();
        });

        // Handle form submission
        $('#manualWinnerForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = $(this).serializeArray();
            let winners = [];
            
            // Process form data
            formData.forEach(function(field) {
                if (field.name.includes('winners[')) {
                    let match = field.name.match(/winners\[(\d+)\]\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        let position = parseInt(match[1]);
                        let index = parseInt(match[2]);
                        let property = match[3];
                        
                        if (!winners[position]) winners[position] = [];
                        if (!winners[position][index]) winners[position][index] = {};
                        
                        winners[position][index][property] = field.value;
                    }
                }
            });
            
            // Flatten winners array
            let flatWinners = [];
            winners.forEach(function(positionWinners, position) {
                if (positionWinners) {
                    positionWinners.forEach(function(winner) {
                        if (winner && winner.ticket_id) {
                            flatWinners.push(winner);
                        }
                    });
                }
            });
            
            if (flatWinners.length === 0) {
                Toast.fire({
                    icon: 'error',
                    title: 'Please select at least one winner'
                });
                return;
            }
            
            // Show confirmation
            Swal.fire({
                title: 'Confirm Winner Selection',
                text: `Are you sure you want to select ${flatWinners.length} winner(s) and complete this draw?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Select Winners!'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitWinners(flatWinners);
                }
            });
        });

        function submitWinners(winners) {
            $.ajax({
                url: '{{ route("admin.lottery.draws.store-manual-winners", $draw->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    winners: winners
                },
                beforeSend: function() {
                    $('#manualWinnerForm button[type="submit"]').prop('disabled', true)
                        .html('<i class="las la-spinner la-spin"></i> Processing...');
                },
                success: function(response) {
                    if (response.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while selecting winners.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Toast.fire({
                        icon: 'error',
                        title: errorMessage
                    });
                },
                complete: function() {
                    $('#manualWinnerForm button[type="submit"]').prop('disabled', false)
                        .html('<i class="las la-check-circle"></i> Select Winners & Complete Draw');
                }
            });
        }

        // Initialize ticket options
        updateTicketOptions();
    });

    // Helper function to convert number to ordinal
    function ordinal(num) {
        const suffixes = ["th", "st", "nd", "rd"];
        const v = num % 100;
        return num + (suffixes[(v - 20) % 10] || suffixes[v] || suffixes[0]);
    }
</script>
@endpush
