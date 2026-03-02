<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Legal Review Submitted</title>
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
        .notification-alert {
            background: #d4edda;
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
            background: #e8f5e8;
            border-left: 4px solid #28a745;
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
        .button {
            display: inline-block;
            background: #28a745;
            color: white !important;
            padding: 15px 40px;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            font-size: 16px;
        }
        .button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class=\"email-container\">
        <div class=\"header\">
            <h1>⚖️ GoBEST™ Legal Review</h1>
            <p>Complaint Management System</p>
        </div>

        <div class=\"notification-alert\">
            <h2 style=\"margin: 0; color: #28a745;\">✅ Legal Review Submitted</h2>
        </div>

        <p>Dear Administrator,</p>

        <p>A legal review has been submitted for complaint case. Please review the details below.</p>

        <div class=\"case-number\">
            Case Number: {{ $complaint->case_number }}
        </div>

        <div class=\"lawyer-info\">
            <h3>👨‍💼 Legal Review Submitted By</h3>
            <p><strong>Lawyer:</strong> {{ $response->lawyer_name }}</p>
            <p><strong>Law Firm:</strong> {{ $response->law_firm_name }}</p>
            <p><strong>Email:</strong> {{ $response->lawyer_email }}</p>
            <p><strong>Location:</strong> {{ $response->lawyer_city_state }}</p>
            <p><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($response->review_date)->format('F j, Y') }}</p>
            <p><strong>Submitted:</strong> {{ $response->submitted_at->format('F j, Y \\a\\t g:i A') }}</p>
        </div>

        <div class=\"details\">
            <h3>📋 Case Details</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Original Submission:</strong> {{ $complaint->created_at->format('F j, Y \\a\\t g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Stage:</strong> <span style=\"background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;\">{{ $complaint->stage->name }}</span></p>
            @endif
        </div>

        <div class=\"details\">
            <h3>⚖️ Legal Review Summary</h3>
            <p><strong>Supporting Evidence:</strong> 
                @if($response->has_supporting_evidence)
                    <span style=\"color: #28a745;\">✅ {{ ucfirst(str_replace('_', ' ', $response->supporting_evidence_type)) }}</span>
                @else
                    <span style=\"color: #6c757d;\">❌ No Evidence Submitted</span>
                @endif
            </p>
            
            <div class=\"mt-3\">
                <strong>Legal Assessment:</strong>
                <div style=\"background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #28a745; margin-top: 10px;\">
                    {{ Str::limit($response->legal_assessment, 200) }}
                    @if(strlen($response->legal_assessment) > 200)...@endif
                </div>
            </div>
            
            <div class=\"mt-3\">
                <strong>Key Recommendations:</strong>
                <div style=\"background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #28a745; margin-top: 10px;\">
                    {{ Str::limit($response->legal_recommendations, 200) }}
                    @if(strlen($response->legal_recommendations) > 200)...@endif
                </div>
            </div>
        </div>

        <div style=\"text-align: center; margin: 30px 0;\">
            <a href=\"{{ route('admin.complaints.show', $complaint->id) }}\" class=\"button\">
                📋 View Full Legal Review
            </a>
        </div>

        <div class=\"footer\">
            <p><strong>GoBEST™ Legal Review System</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Legal Review Completed: {{ $response->submitted_at->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification from the complaint management system.</p>
        </div>
    </div>
</body>
</html>