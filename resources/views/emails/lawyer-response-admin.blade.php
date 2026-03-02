<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Review Completed - Admin Notification</title>
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
        .success-alert {
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
        .lawyer-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
        .response-summary {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
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
            <h2 style="margin: 0; color: #28a745;">✅ Legal Review Completed</h2>
        </div>

        <p>Dear Admin,</p>

        <p>A legal review has been completed for the complaint case below. The assigned lawyer has submitted their detailed legal assessment and recommendations.</p>

        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>

        <div class="lawyer-info">
            <h3>⚖️ Legal Review Details</h3>
            <p><strong>Lawyer:</strong> {{ $response->lawyer_name }}</p>
            <p><strong>Law Firm:</strong> {{ $response->law_firm_name }}</p>
            <p><strong>Email:</strong> {{ $response->lawyer_email }}</p>
            <p><strong>Location:</strong> {{ $response->lawyer_city_state }}</p>
            <p><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($response->review_date)->format('F j, Y') }}</p>
            <p><strong>Submitted:</strong> {{ $response->submitted_at->format('F j, Y g:i A') }}</p>
        </div>

        <div class="response-summary">
            <h3>📋 Legal Assessment Summary</h3>
            <h4>Legal Assessment:</h4>
            <p style="background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #ff9800;">
                {{ Str::limit($response->legal_assessment, 200) }}
            </p>

            <h4>Legal Recommendations:</h4>
            <p style="background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #ff9800;">
                {{ Str::limit($response->legal_recommendations, 200) }}
            </p>

            <h4>Compliance Notes:</h4>
            <p style="background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #ff9800;">
                {{ Str::limit($response->compliance_notes, 150) }}
            </p>
        </div>

        <div class="details">
            <h3>📋 Original Complaint Summary</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Complainant:</strong> {{ $complaint->name ?: 'Anonymous' }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Stage:</strong> <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">{{ $complaint->stage->name }}</span></p>
            @endif
        </div>

        @if($response->has_supporting_evidence)
            <div class="details">
                <h3>📎 Supporting Evidence</h3>
                <p><strong>Evidence Type:</strong> {{ ucfirst(str_replace('_', ' ', $response->supporting_evidence_type)) }}</p>
                <p><strong>Description:</strong> {{ $response->evidence_description }}</p>
                @if($response->attachments && $response->attachments->count() > 0)
                    <p><strong>Attachments:</strong> {{ $response->attachments->count() }} file(s) attached to this email</p>
                @endif
            </div>
        @endif

        <div class="details">
            <h3>📊 Next Steps</h3>
            <ul>
                <li>Review the complete legal assessment in the admin dashboard</li>
                <li>Consider the lawyer's recommendations for case resolution</li>
                <li>All parties have been notified about the completed review</li>
                <li>Take appropriate action based on legal guidance provided</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>GoBEST™ Admin Notification</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Legal Review Completed: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification about completed legal review.</p>
        </div>
    </div>
</body>
</html>