<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message from {{ $sender_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .message-box {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin: 10px 0;
        }
        .info-table td {
            padding: 5px 0;
        }
        .info-table .label {
            font-weight: bold;
            width: 100px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 New Message from Your Network</h1>
    </div>
    
    <div class="content">
        <h2>Hello {{ $sponsor_name }},</h2>
        
        <p>You have received a new message from <strong>{{ $sender_name }}</strong> ({{ $sender_username }}) in your referral network.</p>
        
        <table class="info-table">
            <tr>
                <td class="label">From:</td>
                <td>{{ $sender_name }} ({{ $sender_username }})</td>
            </tr>
            <tr>
                <td class="label">Subject:</td>
                <td><strong>{{ $subject }}</strong></td>
            </tr>
            <tr>
                <td class="label">Date:</td>
                <td>{{ $sent_date }}</td>
            </tr>
        </table>
        
        <div class="message-box">
            <h3>Message:</h3>
            <p>{{ $message_content }}</p>
        </div>
        
        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>Log in to your account to view and respond to this message</li>
            <li>Check your messages dashboard for more details</li>
            <li>Reply directly through the platform to maintain conversation history</li>
        </ul>
    </div>
    
    <div class="footer">
        <p>This email was sent automatically by the system. Please do not reply to this email.</p>
        <p>If you have any issues, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Your Platform') }}. All rights reserved.</p>
    </div>
</body>
</html>
