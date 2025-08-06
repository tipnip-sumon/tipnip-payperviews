<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .transfer-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #e74c3c;
        }
        .detail-label {
            font-weight: 600;
            color: #34495e;
        }
        .detail-value {
            color: #2c3e50;
        }
        .success-badge {
            background-color: #27ae60;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin: 15px 0;
        }
        .note-section {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .note-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }
        .note-content {
            color: #856404;
            font-style: italic;
        }
        .footer {
            background-color: #ecf0f1;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
        }
        .transaction-id {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .warning {
            background-color: #ffe6e6;
            border: 1px solid #ffb3b3;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #d63031;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 15px 0;
        }
        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
                text-align: left;
            }
            .detail-value {
                margin-top: 5px;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">💸</div>
            <h1>Transfer Sent Successfully</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello {{ $sender_name }},
            </div>
            
            <div class="success-badge">
                ✅ Transfer Completed
            </div>
            
            <p>Your money transfer has been processed successfully. Here are the details:</p>
            
            <div class="transfer-details">
                <h3 style="margin-top: 0; color: #667eea;">📋 Transfer Summary</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Recipient:</span>
                    <span class="detail-value">{{ $receiver_name }} ({{ $receiver_username }})</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Transfer Amount:</span>
                    <span class="detail-value" style="color: #27ae60; font-weight: bold;">${{ $amount }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Transfer Fee (5%):</span>
                    <span class="detail-value">${{ $charge }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Source Wallet:</span>
                    <span class="detail-value">{{ $wallet_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Deducted:</span>
                    <span class="detail-value">${{ $total_deducted }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Remaining Balance:</span>
                    <span class="detail-value">${{ $remaining_balance }}</span>
                </div>
            </div>
            
            <div style="text-align: center; margin: 20px 0;">
                <strong>Transaction ID:</strong><br>
                <span class="transaction-id">{{ $transaction_id }}</span>
            </div>
            
            @if($note)
            <div class="note-section">
                <div class="note-title">📝 Transfer Note:</div>
                <div class="note-content">"{{ $note }}"</div>
            </div>
            @endif
            
            <div class="warning">
                <strong>⚠️ Important:</strong> This transfer is final and cannot be reversed. Please ensure you've sent money to the correct recipient.
            </div>
            
            <p><strong>Transfer Date:</strong> {{ $transfer_date }}</p>
            
            <div style="text-align: center;">
                <a href="{{ route('user.transfer_history') }}" class="btn">View Transfer History</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME', 'ViewCash') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
