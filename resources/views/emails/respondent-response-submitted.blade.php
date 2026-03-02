<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Respondent Response Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e9ecef;
        }
        .complaint-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .response-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .response-section h3 {
            color: #667eea;
            margin-top: 0;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        .field-group {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .evidence-type {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 5px;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #5a67d8;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #b6d4da;
            color: #0c5460;
        }
        .attachments-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $isForAdmin ? '🔔 New Respondent Response' : '📋 Response Received for Your Complaint' }}</h1>
        <p>Case Number: <strong>{{ $complaint->case_number }}</strong></p>
    </div>

    <div class="content">
        @if($isForAdmin)
            <div class="alert alert-info">
                <strong>📌 Admin Notification:</strong> A respondent has submitted a detailed response to complaint {{ $complaint->case_number }}. Please review the response and take appropriate action.
            </div>
        @else
            <div class="alert alert-info">
                <strong>📢 Good News:</strong> The respondent for your complaint has submitted their detailed response. You can review their response and supporting evidence below.
            </div>
        @endif

        <div class="complaint-info">
            <h3>📋 Complaint Information</h3>
            <div class="field-group">
                <div class="field-label">Case Number:</div>
                <div class="field-value"><strong>{{ $complaint->case_number }}</strong></div>
            </div>
            <div class="field-group">
                <div class="field-label">Original Complaint Date:</div>
                <div class="field-value">{{ $complaint->created_at->format('F j, Y \a\t g:i A') }}</div>
            </div>
            @if(!$isForAdmin && $complaint->complainant_name)
            <div class="field-group">
                <div class="field-label">Complainant:</div>
                <div class="field-value">{{ $complaint->complainant_name }}</div>
            </div>
            @endif
        </div>

        <div class="response-section">
            <h3>🏢 Venue & Respondent Information</h3>
            <div class="field-group">
                <div class="field-label">Respondent Name:</div>
                <div class="field-value">{{ $response->respondent_name }}</div>
            </div>
            <div class="field-group">
                <div class="field-label">Respondent Email:</div>
                <div class="field-value">{{ $response->respondent_email }}</div>
            </div>
            <div class="field-group">
                <div class="field-label">Venue Legal Name:</div>
                <div class="field-value">{{ $response->venue_legal_name }}</div>
            </div>
            <div class="field-group">
                <div class="field-label">Venue City & State:</div>
                <div class="field-value">{{ $response->venue_city_state }}</div>
            </div>
        </div>

        <div class="response-section">
            <h3>📅 Incident Information</h3>
            <div class="field-group">
                <div class="field-label">Date of Complaint Incident:</div>
                <div class="field-value">{{ $response->complaint_date->format('F j, Y') }}</div>
            </div>
        </div>

        <div class="response-section">
            <h3>📝 Respondent's Side of the Story</h3>
            <div class="field-group">
                <div class="field-label">Respondent's Version of Events:</div>
                <div class="field-value">{{ $response->respondent_side_story }}</div>
            </div>
        </div>

        <div class="response-section">
            <h3>🔍 Detailed Issue Description</h3>
            <div class="field-group">
                <div class="field-label">Issue Detail Description:</div>
                <div class="field-value">{{ $response->issue_detail_description }}</div>
            </div>
        </div>

        <div class="response-section">
            <h3>👥 Witnesses Information</h3>
            <div class="field-group">
                <div class="field-label">Witnesses Details:</div>
                <div class="field-value">{{ $response->witnesses_information }}</div>
            </div>
        </div>

        <div class="response-section">
            <h3>📎 Supporting Evidence</h3>
            <div class="field-group">
                <div class="field-label">Evidence Type:</div>
                <div class="field-value">
                    {{ $response->supporting_evidence_type_label }}
                    <span class="evidence-type">{{ ucfirst($response->supporting_evidence_type) }}</span>
                </div>
            </div>
            @if($response->supporting_evidence_type !== 'none' && $response->evidence_description)
            <div class="field-group">
                <div class="field-label">Evidence Description:</div>
                <div class="field-value">{{ $response->evidence_description }}</div>
            </div>
            @endif
            @if($response->has_supporting_evidence && $response->attachments->count() > 0)
                <div class="attachments-info">
                    <strong>📁 Attachments:</strong> {{ $response->attachments->count() }} file(s) uploaded
                    <br><small>Note: Attachments are available in the web interface for review.</small>
                </div>
            @endif
        </div>

        <div class="response-section">
            <h3>⏰ Response Details</h3>
            <div class="field-group">
                <div class="field-label">Response Submitted:</div>
                <div class="field-value">{{ $response->submitted_at->format('F j, Y \a\t g:i A') }}</div>
            </div>
            <div class="field-group">
                <div class="field-label">Current Status:</div>
                <div class="field-value">
                    <span style="background: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                        ✅ Response Received
                    </span>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ $complaintUrl }}" class="btn">
                {{ $isForAdmin ? '🔍 Review Full Details in Admin Panel' : '👀 View Complete Complaint Details' }}
            </a>
        </div>

        @if($isForAdmin)
        <div style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <h4 style="margin-top: 0; color: #0056b3;">💼 Next Steps for Admin:</h4>
            <ul style="margin-bottom: 0;">
                <li>Review the respondent's detailed response and any supporting evidence</li>
                <li>Compare with the original complaint details</li>
                <li>Determine if additional information is needed from either party</li>
                <li>Update the complaint status as appropriate</li>
                <li>Consider scheduling mediation or further investigation if needed</li>
            </ul>
        </div>
        @else
        <div style="background: #f0f9ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <h4 style="margin-top: 0; color: #1e40af;">📋 What Happens Next:</h4>
            <ul style="margin-bottom: 0;">
                <li>The administration team will review both your complaint and the respondent's response</li>
                <li>You may be contacted if additional information is needed</li>
                <li>The case will proceed according to our standard complaint resolution process</li>
                <li>You will be notified of any status updates or resolution</li>
            </ul>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>This is an automated notification from the Complaint Management System.</p>
        <p><small>Please do not reply to this email. For questions, contact our support team.</small></p>
    </div>
</body>
</html>