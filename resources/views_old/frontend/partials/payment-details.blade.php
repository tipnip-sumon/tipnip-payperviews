@php
    $detail = json_decode($payment->detail, true) ?: [];
    $statusMap = [
        0 => ['class' => 'warning', 'text' => 'Pending'],
        1 => ['class' => 'success', 'text' => 'Successful'],
        2 => ['class' => 'info', 'text' => 'Processing'],
        3 => ['class' => 'danger', 'text' => 'Cancelled']
    ];
    $status = $statusMap[$payment->status] ?? ['class' => 'secondary', 'text' => 'Unknown'];
@endphp

<div class="payment-details">
    <div class="row">
        <div class="col-md-6">
            <div class="detail-group mb-4">
                <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Payment Information</h6>
                <div class="detail-item">
                    <span class="detail-label">Payment ID:</span>
                    <span class="detail-value fw-bold text-primary">{{ $payment->payment_id ?: 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">{{ $payment->trx }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Gateway:</span>
                    <span class="detail-value">
                        <span class="badge bg-info">{{ $payment->gateway->name ?? 'Crypto' }}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="badge bg-{{ $status['class'] }}">{{ $status['text'] }}</span>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="detail-group mb-4">
                <h6 class="text-muted mb-3"><i class="fas fa-dollar-sign me-2"></i>Amount Details</h6>
                <div class="detail-item">
                    <span class="detail-label">Currency:</span>
                    <span class="detail-value">
                        <span class="badge bg-secondary">{{ strtoupper($payment->method_currency) }}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value fw-bold text-success">${{ showAmount($payment->amount) }}</span>
                </div>
                @if($payment->charge && $payment->charge > 0)
                <div class="detail-item">
                    <span class="detail-label">Charge:</span>
                    <span class="detail-value text-warning">${{ showAmount($payment->charge) }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value fw-bold text-primary">${{ showAmount($payment->amount + ($payment->charge ?? 0)) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="detail-group mb-4">
                <h6 class="text-muted mb-3"><i class="fas fa-clock me-2"></i>Timeline</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Created:</span>
                            <span class="detail-value">{{ showDateTime($payment->created_at) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label">Last Updated:</span>
                            <span class="detail-value">{{ showDateTime($payment->updated_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if(!empty($detail['description']))
    <div class="row">
        <div class="col-12">
            <div class="detail-group mb-4">
                <h6 class="text-muted mb-3"><i class="fas fa-comment me-2"></i>Description</h6>
                <div class="alert alert-light">
                    <i class="fas fa-quote-left me-2"></i>
                    {{ $detail['description'] }}
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($payment->admin_feedback && $payment->status == 2)
    <div class="row">
        <div class="col-12">
            <div class="detail-group mb-4">
                <h6 class="text-muted mb-3"><i class="fas fa-user-tie me-2"></i>Admin Feedback</h6>
                <div class="alert alert-primary">
                    @if(filter_var($payment->admin_feedback, FILTER_VALIDATE_URL))
                        <a href="{{ $payment->admin_feedback }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i>Open Payment Link
                        </a>
                    @else
                        <i class="fas fa-comment-dots me-2"></i>
                        {{ $payment->admin_feedback }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($payment->btc_wallet)
    <div class="row">
        <div class="col-12">
            <div class="detail-group mb-4">
                <h6 class="text-muted mb-3"><i class="fab fa-bitcoin me-2"></i>Crypto Details</h6>
                <div class="detail-item">
                    <span class="detail-label">Wallet Address:</span>
                    <span class="detail-value">
                        <div class="d-flex align-items-center">
                            <code class="font-monospace me-2">{{ $payment->btc_wallet }}</code>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('{{ $payment->btc_wallet }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </span>
                </div>
                @if($payment->final_amo && $payment->final_amo != $payment->amount)
                <div class="detail-item">
                    <span class="detail-label">Final Amount:</span>
                    <span class="detail-value">${{ showAmount($payment->final_amo) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    @if(!empty($detail))
    <div class="row">
        <div class="col-12">
            <div class="detail-group">
                <h6 class="text-muted mb-3"><i class="fas fa-cogs me-2"></i>Additional Information</h6>
                @if(isset($detail['created_by']))
                <div class="detail-item">
                    <span class="detail-label">Created By:</span>
                    <span class="detail-value">{{ ucfirst($detail['created_by']) }}</span>
                </div>
                @endif
                @if(isset($detail['manual_entry']) && $detail['manual_entry'])
                <div class="alert alert-info mt-3">
                    <i class="fas fa-hand-paper me-2"></i>
                    <strong>Manual Entry:</strong> This payment was created manually and requires verification.
                </div>
                @endif
                @if(isset($detail['last_updated']))
                <div class="detail-item">
                    <span class="detail-label">Last Modified:</span>
                    <span class="detail-value">{{ $detail['last_updated'] }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.payment-details .detail-group {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 25px;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.payment-details .detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #dee2e6;
    transition: background-color 0.2s ease;
}

.payment-details .detail-item:hover {
    background-color: rgba(102, 126, 234, 0.05);
    border-radius: 6px;
    margin: 0 -10px;
    padding: 12px 10px;
}

.payment-details .detail-item:last-child {
    border-bottom: none;
}

.payment-details .detail-label {
    font-weight: 600;
    color: #495057;
    min-width: 140px;
}

.payment-details .detail-value {
    text-align: right;
    flex: 1;
    font-weight: 500;
}

.payment-details .font-monospace {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    background: #e9ecef;
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid #ced4da;
    word-break: break-all;
}

.payment-details h6 {
    color: #343a40;
    font-weight: 700;
    border-bottom: 2px solid #667eea;
    padding-bottom: 8px;
    margin-bottom: 20px !important;
}

.payment-details .alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create temporary alert
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999;">
                <i class="fas fa-check me-2"></i>Address copied to clipboard!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        
        // Auto-hide after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 3000);
    }).catch(function() {
        alert('Failed to copy to clipboard');
    });
}
</script>
