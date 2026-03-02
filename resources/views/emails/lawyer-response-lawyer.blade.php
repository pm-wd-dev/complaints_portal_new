<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Review Submission Confirmed</title>
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
        .confirmation-alert {
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
        .success-notice {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
            <p>Legal Review Portal - Submission Confirmation</p>
        </div>

        <div class="confirmation-alert">
            <h2 style="margin: 0; color: #1976d2;">✅ Legal Review Submitted Successfully</h2>
        </div>

        <p>Dear {{ $response->lawyer_name }},</p>

        <p>Thank you for submitting your comprehensive legal review. Your professional assessment and recommendations have been successfully recorded and will help guide the proper resolution of this complaint case.</p>

        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>

        <div class="details">
            <h3>📋 Your Submission Summary</h3>
            <p><strong>Your Name:</strong> {{ $response->lawyer_name }}</p>
            <p><strong>Law Firm:</strong> {{ $response->law_firm_name }}</p>
            <p><strong>Email:</strong> {{ $response->lawyer_email }}</p>
            <p><strong>Location:</strong> {{ $response->lawyer_city_state }}</p>
            <p><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($response->review_date)->format('F j, Y') }}</p>
            <p><strong>Submitted:</strong> {{ $response->submitted_at->format('F j, Y g:i A') }}</p>
        </div>

        <div class="details">
            <h3>⚖️ Legal Review Components Submitted</h3>
            <ul>
                <li>✅ Legal Assessment</li>
                <li>✅ Legal Recommendations</li>
                <li>✅ Compliance Notes</li>
                @if($response->has_supporting_evidence)
                    <li>✅ Supporting Evidence ({{ ucfirst(str_replace('_', ' ', $response->supporting_evidence_type)) }})</li>
                    @if($response->attachments && $response->attachments->count() > 0)
                        <li>✅ {{ $response->attachments->count() }} Supporting Document(s)</li>
                    @endif
                @endif
            </ul>
        </div>

        <div class="details">
            <h3>📋 Original Complaint Summary</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Stage:</strong> <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">{{ $complaint->stage->name }}</span></p>
            @endif
        </div>

        <div class="success-notice">
            <h3>🎉 What Happens Next</h3>
            <ul style="margin: 10px 0;">
                <li>Your legal review has been forwarded to the administrative team</li>
                <li>All relevant parties have been notified about the completed review</li>
                <li>Your recommendations will guide the case resolution process</li>
                <li>The case will proceed according to your professional guidance</li>
                <li>You will be notified if any additional information is needed</li>
            </ul>
        </div>

        <div class="details">
            <h3>📞 Professional Services</h3>
            <p>Thank you for your professional legal services. Your expertise helps ensure that all complaint cases are handled with appropriate legal oversight and in compliance with applicable laws and regulations.</p>
            <p>If you have any questions about this submission or need to provide additional information, please contact our administrative team.</p>
        </div>

        <div class="footer">
            <p><strong>GoBEST™ Legal Review System</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Submission Confirmed: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated confirmation of your legal review submission.</p>
        </div>
    </div>
</body>
</html>