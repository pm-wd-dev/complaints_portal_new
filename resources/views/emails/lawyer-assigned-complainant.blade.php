<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Review Initiated</title>
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
        .update-alert {
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
        .reassurance {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background: #0066cc;
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
            <h2 style="margin: 0; color: #1976d2;">⚖️ Legal Review Initiated</h2>
        </div>

        <p>Dear {{ $complaint->name ?: 'Valued Customer' }},</p>

        <p>We want to update you on the status of your complaint. We have assigned a legal professional to review your case to ensure it is handled with the appropriate expertise and care.</p>

        <div class="case-number">
            Your Case Number: {{ $complaint->case_number }}
        </div>

        <div class="reassurance">
            <h3>✨ What This Means for You</h3>
            <ul style="margin: 10px 0;">
                <li>Your complaint is being taken seriously and receiving professional attention</li>
                <li>A qualified legal expert is now reviewing your case</li>
                <li>This ensures compliance with all relevant regulations and procedures</li>
                <li>Your case will receive thorough and proper legal consideration</li>
            </ul>
        </div>

        <div class="details">
            <h3>📋 Your Complaint Details</h3>
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
                <li>The legal professional will thoroughly review your complaint</li>
                <li>They will assess all relevant legal and regulatory aspects</li>
                <li>They will ensure proper procedures are followed</li>
                <li>You will be notified of any significant updates</li>
                <li>The case will proceed according to established protocols</li>
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('public.complaints.track-form') }}" class="button">📱 Track Your Complaint Status</a>
            <p style="margin-top: 10px; color: #666; font-size: 14px;">
                Use case number: <strong>{{ $complaint->case_number }}</strong>
            </p>
        </div>

        <div class="reassurance">
            <p style="margin: 0;"><strong>🛡️ Your Rights:</strong> This legal review ensures your complaint is handled fairly and in accordance with all applicable laws and regulations. No additional action is required from you at this time.</p>
        </div>

        @if($complaint->email)
            <p><small><strong>Contact Information:</strong> If you have any questions, please contact us. We will continue to use this email address ({{ $complaint->email }}) for updates.</small></p>
        @endif

        <div class="footer">
            <p><strong>GoBEST™ Customer Service</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Legal Review Initiated: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification about your complaint status.</p>
        </div>
    </div>
</body>
</html>