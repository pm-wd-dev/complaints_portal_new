<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Review Assignment</title>
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
        .assignment-alert {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
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
        .urgency {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background: #0066cc;
            color: white !important;
            padding: 15px 40px;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            font-size: 16px;
            border: 2px solid #0066cc;
            box-shadow: 0 4px 8px rgba(0,102,204,0.3);
            transition: all 0.3s ease;
        }
        .button:hover {
            background: #004499;
            border-color: #004499;
            box-shadow: 0 6px 12px rgba(0,102,204,0.4);
            transform: translateY(-1px);
        }
        .complainant-info {
            background: #f1f8e9;
            border-left: 4px solid #4caf50;
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
            <p>Complaint Management System</p>
        </div>

        <div class="assignment-alert">
            <h2 style="margin: 0; color: #1976d2;">📋 Legal Review Assignment</h2>
        </div>

        <p>Dear {{ $lawyer->name }},</p>

        <p>You have been assigned to provide legal review for a complaint case. Please review the details below and provide your professional legal assessment.</p>

        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>

        <div class="urgency">
            ⚖️ LEGAL REVIEW REQUESTED ⚖️
        </div>

        <div class="details">
            <h3>📋 Complaint Details</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Stage:</strong> <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">{{ $complaint->stage->name }}</span></p>
            @endif

            <h4>Description:</h4>
            <div style="background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #0066cc;">
                {{ $complaint->description }}
            </div>
        </div>

        <div class="complainant-info">
            <h3>👤 Complainant Information</h3>
            <p><strong>Name:</strong> {{ $complaint->name ?: 'Anonymous' }}</p>
            @if($complaint->email)
                <p><strong>Email:</strong> {{ $complaint->email }}</p>
            @endif
            @if($complaint->phone_number)
                <p><strong>Phone:</strong> {{ $complaint->phone_number }}</p>
            @endif
            <p><strong>Submission Date:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
        </div>

        @if($complaint->attachments && $complaint->attachments->count() > 0)
            <div class="details">
                <h3>📎 Evidence Files</h3>
                <p>{{ $complaint->attachments->count() }} file(s) are attached to this complaint. You can view them after logging in.</p>
            </div>
        @endif
        <div style="text-align: center;">
            <h3><a href="{{ route('lawyer.login') }}?case_number={{ $complaint->case_number }}&email={{ urlencode($lawyer->email) }}" class="button">⚖️ Access Legal Review Portal</a></h3>
            <br>
            <small style="color: #666; margin-top: 10px; display: block;">
                Use case number <strong>{{ $complaint->case_number }}</strong> to log in
            </small>
            <small style="color: #666; margin-top: 5px; display: block;">
                OTP: <strong>0000</strong> (for testing purposes)
            </small>
        </div>

        <div class="details">
            <h3>📌 Your Legal Review Tasks</h3>
            <ol>
                <li>Review all complaint details and evidence</li>
                <li>Assess legal implications and potential risks</li>
                <li>Review respondent responses (if available)</li>
                <li>Provide legal recommendations</li>
                <li>Advise on compliance and regulatory matters</li>
                <li>Document your legal assessment in the system</li>
            </ol>
        </div>

        <div class="urgency">
            <p style="margin: 0;"><strong>⚖️ Legal Notice:</strong> This case has been assigned to you for professional legal review. Your expertise is needed to ensure proper handling and compliance.</p>
        </div>

        <div class="footer">
            <p><strong>GoBEST™ Legal Review System</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Assigned: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification. Please access the legal review portal to complete your assessment.</p>
        </div>
    </div>
</body>
</html>