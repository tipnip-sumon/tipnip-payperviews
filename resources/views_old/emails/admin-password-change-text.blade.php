ADMIN PASSWORD CHANGED - SECURITY ALERT
{{ $appName }}

Dear {{ $admin->name }},

This email confirms that your admin account password was recently changed. If you initiated this change, no further action is required.

CHANGE DETAILS:
- Admin Account: {{ $admin->username }} ({{ $admin->email }})
- Change Time: {{ $changeTime->format('F j, Y \a\t g:i A (T)') }}
- IP Address: {{ $ipAddress }}
- Device/Browser: {{ $userAgent }}
- Admin Role: {{ $admin->role ?? 'Administrator' }}

SECURITY BEST PRACTICES:
- Use a unique, strong password that's at least 12 characters long
- Enable two-factor authentication if available
- Never share your admin credentials with anyone
- Log out completely when finished with admin tasks
- Regularly monitor your account activity

WARNING: If you did NOT change your password:
- Your account may have been compromised
- Contact support immediately: {{ $supportEmail }}
- Change your password again as soon as possible
- Review all recent admin activities

NEED HELP?
If you have any concerns about this password change or need assistance, please contact our support team immediately at {{ $supportEmail }}.

This is an automated security notification from {{ $appName }}.
For your security, please do not reply to this email.

© {{ date('Y') }} {{ $appName }}. All rights reserved.
