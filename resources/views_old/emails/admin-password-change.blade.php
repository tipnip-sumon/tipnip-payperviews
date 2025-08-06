<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Password Changed - Security Alert</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e74c3c;
        }
        .security-icon {
            font-size: 48px;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .title {
            color: #e74c3c;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .alert-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .details-section {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
            min-width: 120px;
        }
        .detail-value {
            color: #6c757d;
            flex: 1;
            text-align: right;
        }
        .security-tips {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .tips-title {
            color: #0c5460;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .tips-list {
            color: #0c5460;
            margin: 0;
            padding-left: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-info {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .danger-text {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="security-icon">🔒</div>
            <h1 class="title">Password Changed Successfully</h1>
            <p style="margin: 0; color: #6c757d;">Security Alert for {{ $appName }} Admin Panel</p>
        </div>

        <div class="alert-box">
            <strong>⚠️ Security Notice:</strong> Your admin account password has been successfully changed.
        </div>

        <p>Dear <strong>{{ $admin->name }}</strong>,</p>

        <p>This email confirms that your admin account password was recently changed. If you initiated this change, no further action is required.</p>

        <div class="details-section">
            <h3 style="margin-top: 0; color: #495057;">Change Details:</h3>
            
            <div class="detail-row">
                <span class="detail-label">Admin Account:</span>
                <span class="detail-value">{{ $admin->username }} ({{ $admin->email }})</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Change Time:</span>
                <span class="detail-value">{{ $changeTime->format('F j, Y \a\t g:i A (T)') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">IP Address:</span>
                <span class="detail-value">{{ $ipAddress }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Device/Browser:</span>
                <span class="detail-value">{{ $userAgent }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Admin Role:</span>
                <span class="detail-value">{{ $admin->role ?? 'Administrator' }}</span>
            </div>
        </div>

        <div class="security-tips">
            <div class="tips-title">🛡️ Security Best Practices:</div>
            <ul class="tips-list">
                <li>Use a unique, strong password that's at least 12 characters long</li>
                <li>Enable two-factor authentication if available</li>
                <li>Never share your admin credentials with anyone</li>
                <li>Log out completely when finished with admin tasks</li>
                <li>Regularly monitor your account activity</li>
            </ul>
        </div>

        <p class="danger-text">⚠️ If you did NOT change your password:</p>
        <ul>
            <li>Your account may have been compromised</li>
            <li>Contact support immediately using the information below</li>
            <li>Change your password again as soon as possible</li>
            <li>Review all recent admin activities</li>
        </ul>

        <div class="contact-info">
            <strong>Need Help?</strong><br>
            If you have any concerns about this password change or need assistance, please contact our support team immediately:
            <br><br>
            <a href="mailto:{{ $supportEmail }}" class="btn">Contact Support</a>
            <br>
            Email: {{ $supportEmail }}
        </div>

        <div class="footer">
            <p>This is an automated security notification from {{ $appName }}.</p>
            <p>For your security, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
