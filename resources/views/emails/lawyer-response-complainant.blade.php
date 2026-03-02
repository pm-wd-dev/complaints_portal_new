<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Review Completed</title>
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
            border-bottom: 2px solid #28a745;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .update-alert {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .case-number {
            background: #28a745;
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
            color: #28a745;
            margin-top: 0;
        }
        .good-news {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background: #28a745;
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

        <div class="update-alert">
            <h2 style="margin: 0; color: #28a745;">✅ Legal Review Completed</h2>
        </div>

        <p>Dear {{ $complaint->name ?: 'Valued Customer' }},</p>

        <p>Great news! The legal review of your complaint has been completed by a qualified legal professional. The review process ensures your complaint is handled according to proper legal standards and procedures.</p>

        <div class="case-number">
            Your Case Number: {{ $complaint->case_number }}
        </div>

        <div class="good-news">
            <h3>🎉 What This Means for You</h3>
            <ul style="margin: 10px 0;">
                <li>Your complaint has received professional legal review</li>
                <li>All legal aspects and compliance issues have been assessed</li>
                <li>The legal professional has provided recommendations for resolution</li>
                <li>Your case is now proceeding with expert legal guidance</li>
            </ul>
        </div>

        <div class="details">
            <h3>⚖️ Legal Review Summary</h3>
            <p><strong>Reviewed by:</strong> {{ $response->lawyer_name }}</p>
            <p><strong>Law Firm:</strong> {{ $response->law_firm_name }}</p>
            <p><strong>Review Completed:</strong> {{ \Carbon\Carbon::parse($response->review_date)->format('F j, Y') }}</p>
            <p><strong>Legal Assessment:</strong> Comprehensive legal review completed with recommendations provided to our team</p>
        </div>

        <div class="details">
            <h3>📋 Your Original Complaint</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Status:</strong> <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">{{ $complaint->stage->name }}</span></p>
            @endif
        </div>

        <div class="details">
            <h3>🔍 What Happens Next</h3>
            <ol>
                <li>Our team will review the legal recommendations provided</li>
                <li>Appropriate action will be taken based on the legal guidance</li>
                <li>You will be notified of any significant updates to your case</li>
                <li>The case will proceed according to legal best practices</li>
                <li>Resolution will be handled in compliance with all applicable regulations</li>
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('public.complaints.track-form') }}" class="button">📱 Track Your Complaint Status</a>
            <p style="margin-top: 10px; color: #666; font-size: 14px;">
                Use case number: <strong>{{ $complaint->case_number }}</strong>
            </p>
        </div>

        <div class="good-news">
            <p style="margin: 0;"><strong>🛡️ Legal Protection:</strong> This legal review ensures your complaint is handled with appropriate legal oversight and in accordance with all applicable laws and regulations. Your rights and interests are being protected throughout this process.</p>
        </div>

        @if($complaint->email)
            <p><small><strong>Contact Information:</strong> We will continue to use this email address ({{ $complaint->email }}) for updates regarding your complaint.</small></p>
        @endif

        <div class="footer">
            <p><strong>GoBEST™ Customer Service</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Legal Review Completed: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification about your complaint status.</p>
        </div>
    </div>
</body>
</html>