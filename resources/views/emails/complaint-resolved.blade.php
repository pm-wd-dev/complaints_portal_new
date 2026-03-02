<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Resolved</title>
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
        .resolution-alert {
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
        .recipient-info {
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
    <div class="email-container">
        <div class="header">
            <h1>✅ GoBEST™ Complaint Portal</h1>
            <p>Complaint Management System</p>
        </div>

        <div class="resolution-alert">
            <h2 style="margin: 0; color: #28a745;">🎉 Complaint Resolved</h2>
        </div>

        <p>Dear 
        @if($recipientType === 'complainant')
            {{ $complaint->name ?: 'Complainant' }},
        @elseif($user)
            {{ $user->name }},
        @else
            Team Member,
        @endif
        </p>

        <p>We are pleased to inform you that the complaint case has been successfully resolved.</p>

        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>

        <div class="details">
            <h3>📋 Case Details</h3>
            <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Original Submission:</strong> {{ $complaint->created_at->format('F j, Y \\a\\t g:i A') }}</p>
            <p><strong>Resolution Date:</strong> {{ now()->format('F j, Y \\a\\t g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Final Stage:</strong> <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">{{ $complaint->stage->name }}</span></p>
            @endif
        </div>

        @if($recipientType === 'complainant')
            <div class="recipient-info">
                <h3>👤 For the Complainant</h3>
                <p>Thank you for bringing this matter to our attention. Your complaint has been thoroughly investigated and resolved. We appreciate your patience throughout this process.</p>
                
                <p><strong>What happens next:</strong></p>
                <ul>
                    <li>This case is now officially closed</li>
                    <li>A resolution document may be generated for your records</li>
                    <li>You may be contacted if any follow-up is required</li>
                </ul>
            </div>
        @elseif($recipientType === 'respondent')
            <div class="recipient-info">
                <h3>👥 For the Respondent</h3>
                <p>The complaint case in which you were involved has been resolved. Thank you for your cooperation and response during the investigation process.</p>
                
                <p><strong>Case Status:</strong></p>
                <ul>
                    <li>All required responses have been reviewed</li>
                    <li>The case has been officially resolved</li>
                    <li>No further action is required from you at this time</li>
                </ul>
            </div>
        @elseif($recipientType === 'lawyer')
            <div class="recipient-info">
                <h3>⚖️ For the Legal Reviewer</h3>
                <p>The complaint case for which you provided legal review has been resolved. Thank you for your professional assessment and recommendations.</p>
                
                <p><strong>Legal Review Status:</strong></p>
                <ul>
                    <li>Your legal assessment has been considered</li>
                    <li>The case resolution is complete</li>
                    <li>Your review contribution is documented in the case file</li>
                </ul>
            </div>
        @endif

        <div class="details">
            <h3>📝 Case Summary</h3>
            <div style="background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #28a745;">
                {{ Str::limit($complaint->description, 300) }}
                @if(strlen($complaint->description) > 300)...@endif
            </div>
        </div>

        <div class="resolution-alert">
            <p style="margin: 0;"><strong>✅ Resolution Status:</strong> This complaint has been successfully resolved and the case is now closed. Thank you for your participation in the GoBEST complaint management process.</p>
        </div>

        <div class="footer">
            <p><strong>GoBEST™ Complaint Portal</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Resolved: {{ now()->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification from the complaint management system.</p>
        </div>
    </div>
</body>
</html>