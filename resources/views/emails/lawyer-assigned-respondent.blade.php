<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Review Assignment Notification</title>
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
            border-bottom: 2px solid #ff9800;
        }
        .header h1 {
            color: #ff9800;
            margin: 0;
            font-size: 24px;
        }
        .notification-alert {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .case-number {
            background: #ff9800;
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
            color: #ff9800;
            margin-top: 0;
        }
        .important-notice {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background: #ff9800;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            font-size: 16px;
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

        <div class="notification-alert">
            <h2 style="margin: 0; color: #f57c00;">⚖️ Legal Review Assignment Notice</h2>
        </div>

        <p>Dear Respondent,</p>

        <p>We are writing to inform you that a legal professional has been assigned to review a complaint case. This is part of our standard process to ensure all matters are handled properly and fairly.</p>

        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>

        <div class="details">
            <h3>📋 Case Information</h3>
            <p><strong>Complaint Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Case Submitted:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
            <p><strong>Legal Review Assigned:</strong> {{ now()->format('F j, Y g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Stage:</strong> <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">{{ $complaint->stage->name }}</span></p>
            @endif
        </div>

        <div class="important-notice">
            <h3>📌 What This Means</h3>
            <ul style="margin: 10px 0;">
                <li>A qualified legal professional will review this complaint</li>
                <li>All parties will be treated fairly and according to established procedures</li>
                <li>The legal review ensures compliance with relevant laws and regulations</li>
                <li>This is a standard part of our complaint resolution process</li>
            </ul>
        </div>

        <div class="details">
            <h3>🔍 Review Process</h3>
            <ol>
                <li>The assigned legal professional will review all available information</li>
                <li>They will assess the case according to applicable laws and policies</li>
                <li>All relevant parties and evidence will be considered</li>
                <li>Legal recommendations will be provided to guide resolution</li>
                <li>The case will proceed according to established protocols</li>
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('public.complaints.track-form') }}" class="button">📋 View Case Status</a>
            <p style="margin-top: 10px; color: #666; font-size: 14px;">
                Case number: <strong>{{ $complaint->case_number }}</strong>
            </p>
        </div>

        <div class="important-notice">
            <p style="margin: 0;"><strong>📞 Questions?</strong> If you have any questions about this legal review assignment or the complaint process, please contact our customer service team. We are committed to handling all matters fairly and transparently.</p>
        </div>

        <div class="footer">
            <p><strong>GoBEST™ Legal Affairs</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Legal Review Assigned: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification regarding legal review assignment.</p>
        </div>
    </div>
</body>
</html>