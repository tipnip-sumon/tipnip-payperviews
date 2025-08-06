<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 300;
        }
        .content {
            padding: 30px;
        }
        .message-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .priority {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority.normal {
            background: #28a745;
            color: white;
        }
        .priority.high {
            background: #ffc107;
            color: #212529;
        }
        .priority.urgent {
            background: #dc3545;
            color: white;
        }
        .message-content {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            line-height: 1.6;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 15px 0;
            font-weight: 500;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .meta-info {
            color: #6c757d;
            font-size: 14px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 New Message Received</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $recipient_name }}</strong>,</p>
            
            <p>You have received a new message from <strong>{{ $sender_name }}</strong>.</p>
            
            <div class="message-info">
                <div class="meta-info">
                    <strong>From:</strong> {{ $sender_name }}<br>
                    <strong>Subject:</strong> {{ $subject }}<br>
                    <strong>Priority:</strong> <span class="priority {{ $priority }}">{{ ucfirst($priority) }}</span><br>
                    <strong>Date:</strong> {{ $sent_date }}
                </div>
            </div>
            
            <div class="message-content">
                <h4>Message:</h4>
                <p>{{ $message_content }}</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ env('APP_URL') }}/user/messages" class="btn">View Message</a>
            </div>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #e9ecef;">
            
            <p><strong>Quick Actions:</strong></p>
            <ul>
                <li>Reply to this message</li>
                <li>View all your messages</li>
                <li>Update notification preferences</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from {{ env('APP_NAME') }}.</p>
            <p>If you don't want to receive these notifications, you can update your preferences in your account settings.</p>
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
