<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Submitted</title>
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
        .button {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #218838;
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

        <h2>Thank You for Submitting Your Complaint</h2>
        
        <p>Dear {{ $complaint->name }},</p>
        
        <p>We have successfully received your complaint. Your concerns are important to us, and we are committed to addressing them promptly and thoroughly.</p>
        
        <div class="case-number">
            Case Number: {{ $complaint->case_number }}
        </div>
        
        <div class="details">
            <h3>📋 Complaint Summary</h3>
            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
            <p><strong>Location:</strong> {{ $complaint->location }}</p>
            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Description:</strong> {{ \Str::limit($complaint->description, 200) }}</p>
        </div>
        
        <p><strong>What happens next?</strong></p>
        <ul>
            <li>Your complaint will be reviewed by our team within 2-3 business days</li>
            <li>You will receive updates as we progress through the investigation</li>
            <li>We will contact you if we need additional information</li>
        </ul>
        
        <p>You can track the status of your complaint using the link below:</p>
        
        <div style="text-align: center;">
            <a href="{{ $trackingUrl }}" class="button">🔍 Track Your Complaint</a>
        </div>
        
        <p><strong>Important:</strong> Please save this email and your case number ({{ $complaint->case_number }}) for future reference.</p>
        
        <div class="footer">
            <p>If you have any questions or concerns, please contact our support team.</p>
            <p><strong>GoBEST™ Listens Complaint Management System</strong></p>
            <p>This is an automated message. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>