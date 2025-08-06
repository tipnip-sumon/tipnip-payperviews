<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KYC Verification Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .report-title {
            font-size: 20px;
            margin: 10px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .info-value {
            flex: 1;
        }
        .status {
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 8px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ config('app.name', 'PayPerViews') }}</div>
        <div class="report-title">KYC Verification Report</div>
        <div>Generated on: {{ $generated_at->format('F j, Y \a\t g:i A') }}</div>
    </div>

    <div class="section">
        <div class="section-title">User Information</div>
        <table>
            <tr>
                <td class="info-label">Full Name:</td>
                <td class="info-value">{{ $user->firstname }} {{ $user->lastname }}</td>
            </tr>
            <tr>
                <td class="info-label">Username:</td>
                <td class="info-value">{{ $user->username }}</td>
            </tr>
            <tr>
                <td class="info-label">Email:</td>
                <td class="info-value">{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="info-label">Registration Date:</td>
                <td class="info-value">{{ $user->created_at->format('F j, Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">KYC Verification Details</div>
        <table>
            <tr>
                <td class="info-label">Status:</td>
                <td class="info-value">
                    <span class="status status-{{ $kyc->status }}">{{ ucfirst($kyc->status) }}</span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Document Type:</td>
                <td class="info-value">{{ ucwords(str_replace('_', ' ', $kyc->document_type)) }}</td>
            </tr>
            <tr>
                <td class="info-label">Nationality:</td>
                <td class="info-value">{{ $kyc->nationality }}</td>
            </tr>
            <tr>
                <td class="info-label">Phone Number:</td>
                <td class="info-value">{{ $kyc->phone_number }}</td>
            </tr>
            <tr>
                <td class="info-label">Submitted Date:</td>
                <td class="info-value">{{ $kyc->submitted_at->format('F j, Y \a\t g:i A') }}</td>
            </tr>
            @if($kyc->reviewed_at)
            <tr>
                <td class="info-label">Reviewed Date:</td>
                <td class="info-value">{{ $kyc->reviewed_at->format('F j, Y \a\t g:i A') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Address Information</div>
        <table>
            <tr>
                <td class="info-label">Address:</td>
                <td class="info-value">{{ $kyc->address }}</td>
            </tr>
            <tr>
                <td class="info-label">City:</td>
                <td class="info-value">{{ $kyc->city }}</td>
            </tr>
            <tr>
                <td class="info-label">State:</td>
                <td class="info-value">{{ $kyc->state }}</td>
            </tr>
            <tr>
                <td class="info-label">Postal Code:</td>
                <td class="info-value">{{ $kyc->postal_code }}</td>
            </tr>
            <tr>
                <td class="info-label">Country:</td>
                <td class="info-value">{{ $kyc->country }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Document Information</div>
        <table>
            <tr>
                <td class="info-label">Front Document:</td>
                <td class="info-value">{{ $kyc->document_front ? 'Submitted' : 'Not Submitted' }}</td>
            </tr>
            <tr>
                <td class="info-label">Back Document:</td>
                <td class="info-value">{{ $kyc->document_back ? 'Submitted' : 'Not Submitted' }}</td>
            </tr>
            <tr>
                <td class="info-label">Selfie Image:</td>
                <td class="info-value">{{ $kyc->selfie_image ? 'Submitted' : 'Not Submitted' }}</td>
            </tr>
        </table>
    </div>

    @if($kyc->admin_notes)
    <div class="section">
        <div class="section-title">Admin Notes</div>
        <p>{{ $kyc->admin_notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This is an automated report generated by {{ config('app.name') }} KYC System.</p>
        <p>For any questions, please contact our support team.</p>
    </div>
</body>
</html>