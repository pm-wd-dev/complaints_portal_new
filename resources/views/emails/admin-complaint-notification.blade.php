<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Complaint Submitted</title>
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
            border-bottom: 2px solid #dc3545;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
            font-size: 24px;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .case-number {
            background: #dc3545;
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
            color: #dc3545;
            margin-top: 0;
        }
        .complainant-info {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #c82333;
        }
        .urgency {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
            text-align: center;
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
            <h1>🚨 GoBEST™ Admin Alert</h1>
            <p>Complaint Management System</p>
        </div>

        <div class="urgency">
            ⚡ NEW COMPLAINT REQUIRES ATTENTION ⚡
        </div>
        
        <div class="alert">
            <strong>📢 Action Required:</strong> A new complaint has been submitted and requires administrative review.
        </div>
        
        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>
        
        <div class="complainant-info">
            <h3>👤 Complainant Information</h3>
            <p><strong>Name:</strong> {{ $complaint->name }}</p>
            <p><strong>Email:</strong> {{ $complaint->email }}</p>
            @if($complaint->phone_number)
                <p><strong>Phone:</strong> {{ $complaint->phone_number }}</p>
            @endif
            <p><strong>Submission Method:</strong> {{ $complaint->submitted_by_admin ? 'Admin Portal' : 'Public Form' }}</p>
        </div>
        
        <div class="details">
            <h3>📋 Complaint Details</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Current Status:</strong> <span style="background: #ffc107; padding: 3px 8px; border-radius: 3px;">{{ ucfirst($complaint->status) }}</span></p>
            
            <h4>Description:</h4>
            <div style="background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #dc3545;">
                {{ $complaint->description }}
            </div>
        </div>

        @if($complaint->attachments && $complaint->attachments->count() > 0)
            <div class="details">
                <h3>📎 Attachments</h3>
                <p>{{ $complaint->attachments->count() }} file(s) attached to this complaint.</p>
            </div>
        @endif
        
        <div style="text-align: center;">
            <a href="{{ $complaintUrl }}" class="button">🔍 View Complaint Details</a>
            <br>
            <small style="color: #666; margin-top: 10px; display: block;">
                You may need to <a href="{{ $adminLoginUrl }}" style="color: #0066cc;">login first</a> if not already authenticated
            </small>
        </div>
        
        <div class="alert">
            <strong>⏰ Response Time:</strong> Please review and assign this complaint within 24 hours to ensure timely resolution.
        </div>
        
        <div class="details">
            <h3>🚀 Quick Actions Needed</h3>
            <ul>
                <li>Review complaint details thoroughly</li>
                <li>Assign appropriate respondent(s)</li>
                <li>Update complaint stage as needed</li>
                <li>Contact complainant if additional information is required</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>GoBEST™ Listens Admin Notification System</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Submitted: {{ $complaint->created_at->format('M j, Y g:i A') }}</p>
            <p>This is an automated notification. Please log into the admin panel to take action.</p>
        </div>
    </div>
</body>
</html>