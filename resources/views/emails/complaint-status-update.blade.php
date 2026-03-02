<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Status Update</title>
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
            border-bottom: 2px solid #4caf50;
        }
        .header h1 {
            color: #4caf50;
            margin: 0;
            font-size: 24px;
        }
        .update-alert {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .case-number {
            background: #4caf50;
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
            color: #4caf50;
            margin-top: 0;
        }
        .status-progress {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #388e3c;
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
            <h2 style="margin: 0;">📢 Complaint Status Update</h2>
        </div>
        
        <p>Dear {{ $complaint->name }},</p>
        
        <p>We have an important update regarding your complaint. Thank you for your patience as we work to address your concerns.</p>
        
        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>
        
        <div class="status-progress">
            <h3 style="margin-top: 0; color: #1976d2;">🔄 Status Update</h3>
            <p style="font-size: 16px; font-weight: bold; margin: 0;">{{ $updateMessage }}</p>
        </div>
        
        <div class="details">
            <h3>📋 Current Complaint Status</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Last Updated:</strong> {{ $complaint->updated_at->format('F j, Y \a\t g:i A') }}</p>
            @if($complaint->stage)
                <p><strong>Current Stage:</strong> 
                    <span style="background: {{ $complaint->stage->color }}; color: white; padding: 3px 8px; border-radius: 3px;">
                        {{ $complaint->stage->name }}
                    </span>
                </p>
            @endif
        </div>
        
        <div class="status-progress">
            <h3 style="margin-top: 0; color: #1976d2;">⏭️ What's Next?</h3>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Your complaint is now being actively reviewed by our team</li>
                <li>You will receive updates as we progress through the resolution process</li>
                <li>We may contact you if additional information is needed</li>
                <li>Expected response timeframe: 2-5 business days</li>
            </ul>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ $trackingUrl }}" class="button">🔍 Check Current Status</a>
        </div>
        
        <div class="details">
            <h3>📞 Need to Contact Us?</h3>
            <p>If you have any questions or need to provide additional information regarding your complaint, please:</p>
            <ul>
                <li>Use the tracking link above to view the latest status</li>
                <li>Reference your case number: <strong>{{ $complaint->case_number }}</strong></li>
                <li>Contact our support team if urgent</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>GoBEST™ Listens Complaint Management System</strong></p>
            <p>Case ID: {{ $complaint->case_number }} | Updated: {{ $complaint->updated_at->format('M j, Y g:i A') }}</p>
            <p>This is an automated update. Please use the tracking link to view current status.</p>
        </div>
    </div>
</body>
</html>