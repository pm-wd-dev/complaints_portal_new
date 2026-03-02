<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Assignment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .email-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0066cc;
        }
        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 24px;
        }
        .success-alert {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .case-number {
            background: #0066cc;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 20px 0;
        }
        .details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .details h3 {
            color: #0066cc;
            margin-top: 0;
        }
        .lawyer-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🏢 GoBEST™ Listens</h1>
            <p>Complaint Management System - Admin Notification</p>
        </div>

        <div class="success-alert">
            <h2 style="margin: 0; color: #28a745;">✅ Lawyer Successfully Assigned</h2>
        </div>

        <p>Dear Admin,</p>

        <p>A lawyer has been successfully assigned to handle legal review for the following complaint case.</p>

        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>

        <div class="lawyer-info">
            <h3>👨‍⚖️ Assigned Lawyer</h3>
            <p><strong>Name:</strong> {{ $lawyer->name }}</p>
            <p><strong>Email:</strong> {{ $lawyer->email }}</p>
            @if($lawyer->phone)
                <p><strong>Phone:</strong> {{ $lawyer->phone }}</p>
            @endif
            <p><strong>Assigned:</strong> {{ now()->format('F j, Y g:i A') }}</p>
        </div>

        <div class="details">
            <h3>📋 Complaint Summary</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Complainant:</strong> {{ $complaint->name ?: 'Anonymous' }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Stage:</strong> <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">{{ $complaint->stage->name }}</span></p>
            @endif
        </div>

        @if($message && is_string($message) && trim($message))
            <div class="details">
                <h3>📝 Assignment Message</h3>
                <p style="font-style: italic;">"{{ trim($message) }}"</p>
            </div>
        @endif

        <div class="details">
            <h3>📊 Next Steps</h3>
            <ul>
                <li>Lawyer will receive a separate notification with case access</li>
                <li>Complainant and respondent will be notified about the legal review</li>
                <li>Legal assessment will be documented in the system</li>
                <li>You can monitor progress through the admin dashboard</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>GoBEST™ Admin Notification</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Assignment Date: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated confirmation of lawyer assignment.</p>
        </div>
    </div>
</body>
</html>