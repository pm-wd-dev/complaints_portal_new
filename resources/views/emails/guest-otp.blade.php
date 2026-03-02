<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP - GoBEST™ Listens</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .header p {
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .otp-container {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 15px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #2563eb;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin-bottom: 15px;
        }
        .expires-text {
            font-size: 14px;
            color: #64748b;
        }
        .warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            color: #92400e;
        }
        .warning strong {
            color: #92400e;
        }
        .footer {
            background: #f8fafc;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .logo {
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h1>GoBEST™ Listens</h1>
            </div>
            <p>Complaint Tracking System</p>
        </div>
        
        <div class="content">
            <h2>Your One-Time Password (OTP)</h2>
            <p>Use this OTP to access your complaint tracking information:</p>
            
            <div class="otp-container">
                <div class="otp-label">Your OTP Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="expires-text">
                    Valid until: {{ $expiresAt->format('M d, Y h:i A') }}
                </div>
            </div>
            
            <div class="warning">
                <strong>Security Notice:</strong><br>
                • This OTP is valid for 10 minutes only<br>
                • Do not share this code with anyone<br>
                • If you didn't request this, please ignore this email
            </div>
            
            <p>Enter this OTP on the complaint tracking page to view your complaint history.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from GoBEST™ Listens Complaint System</p>
            <p>If you have any questions, please contact our support team</p>
        </div>
    </div>
</body>
</html>