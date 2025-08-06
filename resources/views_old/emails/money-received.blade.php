<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Received</title>
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
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
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
        .amount-highlight {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-highlight .amount {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .amount-highlight .label {
            font-size: 14px;
            opacity: 0.9;
        }
        .transfer-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #00b894;
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
            background-color: #e8f5e8;
            border: 1px solid #c3e6c3;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .note-title {
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 8px;
        }
        .note-content {
            color: #2d5016;
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
        .celebration {
            text-align: center;
            font-size: 48px;
            margin: 20px 0;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 15px 0;
        }
        .info-box {
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #1565c0;
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
            .amount-highlight .amount {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">💰</div>
            <h1>Money Received!</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello {{ $receiver_name }},
            </div>
            
            <div class="celebration">🎉</div>
            
            <div class="success-badge">
                ✅ Payment Received
            </div>
            
            <p>Great news! You've received money from {{ $sender_name }}. The amount has been added to your account.</p>
            
            <div class="amount-highlight">
                <div class="label">Amount Received</div>
                <div class="amount">${{ $amount }}</div>
                <div class="label">Added to your {{ $wallet_name }}</div>
            </div>
            
            <div class="transfer-details">
                <h3 style="margin-top: 0; color: #00b894;">📋 Transfer Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">From:</span>
                    <span class="detail-value">{{ $sender_name }} ({{ $sender_username }})</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Amount Received:</span>
                    <span class="detail-value" style="color: #27ae60; font-weight: bold;">${{ $amount }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Credited to:</span>
                    <span class="detail-value">{{ $wallet_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">New Balance:</span>
                    <span class="detail-value" style="color: #27ae60; font-weight: bold;">${{ $new_balance }}</span>
                </div>
            </div>
            
            <div style="text-align: center; margin: 20px 0;">
                <strong>Transaction ID:</strong><br>
                <span class="transaction-id">{{ $transaction_id }}</span>
            </div>
            
            @if($note)
            <div class="note-section">
                <div class="note-title">💬 Message from sender:</div>
                <div class="note-content">"{{ $note }}"</div>
            </div>
            @endif
            
            <div class="info-box">
                <strong>ℹ️ Good to know:</strong> This transfer was processed securely and the funds are immediately available in your account.
            </div>
            
            <p><strong>Received Date:</strong> {{ $transfer_date }}</p>
            
            <div style="text-align: center;">
                <a href="{{ route('user.dashboard') }}" class="btn">View Dashboard</a>
                <a href="{{ route('user.transfer_history') }}" class="btn" style="margin-left: 10px;">View History</a>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 8px; text-align: center;">
                <h4 style="margin-top: 0; color: #00b894;">💡 What's Next?</h4>
                <p style="margin-bottom: 0;">You can use this money for transfers, purchases, or withdraw to your bank account.</p>
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
