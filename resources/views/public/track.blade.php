<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Track Complaint -GoBEST™ Listens Complaint System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
   <style>
   * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo img {
            width: 32px;
            height: 32px;
        }

        .form-container {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #333;
        }

        .description {
            color: #666;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            background: white;
        }

        .submit-button {
            display: block;
            width: 100%;
            padding: 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .submit-button:hover {
            background: #1d4ed8;
        }

        .result-container {
            display: none;
            margin-top: 24px;
            padding: 24px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .result-container.show {
            display: block;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-submitted {
            background: #fef3c7;
            color: #92400e;
        }

        .status-under_review {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-escalated {
            background: #fde68a;
            color: #92400e;
        }

        .status-resolved {
            background: #dcfce7;
            color: #166534;
        }

        .status-awaiting_signature {
            background: #fef3c7;
            color: #92400e;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-awaiting_signature i {
            font-size: 0.875rem;
        }

        .status-closed {
            background: #e5e7eb;
            color: #374151;
        }

        .complaint-details {
            margin-top: 16px;
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
            gap: 16px;
        }

        .detail-label {
            color: #6b7280;
            font-weight: 500;
            flex-shrink: 0;
        }

        .detail-value {
            font-weight: 400;
            color: #374151;
            text-align: right;
            word-break: break-word;
        }

        .response-section {
            margin-top: 24px;
            padding-top: 16px;
            border-left: 2px solid #e2e8f0;
        }

        .response-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .response-text {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        /* Detailed Response Styles */
        .detailed-responses-section {
            margin-top: 32px;
        }

        .response-card {
            background: white;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 0;
            margin-bottom: 0;
            overflow: visible;
            display: flex;
            gap: 16px;
            padding: 16px 0;
        }

        .response-card:last-child {
            border-bottom: none;
        }

        .response-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .response-header {
            background: transparent;
            padding: 0;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            gap: 12px;
        }

        .respondent-info h4 {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            color: #111827;
        }

        .respondent-info p {
            font-size: 13px;
            color: #6b7280;
            margin: 2px 0 0 0;
        }

        .response-date small {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
        }

        .response-details {
            flex: 1;
            min-width: 0;
            padding: 0;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 12px;
            margin-bottom: 16px;
        }

        .response-content {
            margin-top: 12px;
        }

        .content-section {
            margin-bottom: 16px;
        }

        .content-section h5 {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .content-section p {
            font-size: 14px;
            color: #374151;
            line-height: 1.6;
            margin: 0;
            background: transparent;
            padding: 0;
            border-radius: 0;
        }

        .guest-complaint-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.125rem;
            font-weight: 600;
            text-align: center;
            transition: background-color 0.2s;
        }

        .guest-complaint-btn:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: white;
            text-decoration: none;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 32px;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            position: relative;
        }

        .close-button {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #666;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .close-button:hover {
            background-color: #f5f5f5;
            color: #333;
        }

        .copy-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #666;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
            vertical-align: middle;
            transition: background-color 0.2s;
        }

        .copy-button:hover {
            background-color: #f5f5f5;
            color: #333;
        }

        .success-icon {
            width: 64px;
            height: 64px;
            background: #2ea043;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .success-icon svg {
            width: 32px;
            height: 32px;
            stroke: white;
        }

        .complaint-id {
            margin: 16px 0;
            padding: 12px;
            background: #f5f5f5;
            border-radius: 8px;
            font-weight: 500;
        }

        .submit-another {
            display: inline-block;
            padding: 12px 24px;
            background: #2ea043;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin-top: 16px;
        }

        .submit-another:hover {
            background: #2c974b;
        }

        /* Guest Login Modal Styles */
        .guest-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .guest-modal.show {
            display: flex;
        }

        .guest-modal-content {
            background: white;
            padding: 32px;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            position: relative;
        }

        .guest-modal h2 {
            margin-bottom: 16px;
            font-size: 24px;
            text-align: center;
        }

        .guest-modal .description {
            text-align: center;
            margin-bottom: 32px;
            color: #666;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 24px;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 8px;
        }

        .step.active {
            background: #2563eb;
            color: white;
        }

        .step.completed {
            background: #10b981;
            color: white;
        }

        .step-line {
            width: 40px;
            height: 2px;
            background: #e5e7eb;
        }

        .step-line.active {
            background: #2563eb;
        }

        .modal-step {
            display: none;
        }

        .modal-step.active {
            display: block;
        }

        .otp-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 20px 0;
        }

        .resend-otp {
            background: none;
            border: none;
            color: #2563eb;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .resend-otp:hover {
            color: #1d4ed8;
        }

        .close-modal-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal-btn:hover {
            background: #f3f4f6;
            color: #333;
        }

        .guest-complaints-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .single-complaint-view {
            max-height: 400px;
            overflow-y: auto;
        }

        .complaints-accordion {
            max-height: 400px;
            overflow-y: auto;
        }

        .accordion-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .accordion-header {
            background: #f8fafc;
            padding: 16px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .accordion-header:hover {
            background: #f1f5f9;
        }

        .accordion-header.active {
            background: #e0f2fe;
        }

        .accordion-title {
            font-weight: 600;
            font-size: 16px;
        }

        .accordion-toggle {
            font-size: 20px;
            transition: transform 0.3s;
        }

        .accordion-toggle.active {
            transform: rotate(180deg);
        }

        .accordion-content {
            display: none;
            padding: 0;
        }

        .accordion-content.active {
            display: block;
        }

        .modal-complaint-details {
            padding: 20px;
            background: white;
        }

        .loading {
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2563eb;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Email Thread Timeline Styles */
        .timeline {
            position: relative;
            padding: 0;
            margin-top: 20px;
        }

        .timeline-item {
            position: relative;
            display: flex;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .timeline-content {
            flex: 1;
            min-width: 0;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            gap: 12px;
        }

        .timeline-sender {
            font-weight: 600;
            font-size: 15px;
            color: #111827;
        }

        .timeline-date {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .timeline-body {
            color: #374151;
            font-size: 14px;
            line-height: 1.6;
        }

        .response-text {
            line-height: 1.6;
            margin-bottom: 16px;
            color: #374151;
            font-size: 14px;
        }

        .response-attachments {
            margin-top: 16px;
        }

        .response-attachments h4,
        .response-attachments h5 {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .attachments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .attachment-item {
            text-align: center;
        }

        .attachment-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .attachment-item img:hover {
            transform: scale(1.05);
        }

        .attachment-item .btn {
            width: 100%;
            height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            padding: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }

        .attachment-item .btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .attachment-item .btn i {
            font-size: 24px;
            margin-bottom: 4px;
        }
        .signature-icon svg {
            font-size: 20px;
        }
        .signature_top {
            gap: 10px;
        }
        .signature_top p {
            font-size:12px;
            }
        .image_format {
        font-size: 12px;
        margin-top: 12px;
        }
        .upload_btn {
            /* display: block; */
            width: 100%;
            padding: 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            /* transition: background-color 0.2s; */
            margin-top: 15px;
        }

        /* Quick Actions Buttons */
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            border-top: 1px solid #e5e7eb;
            margin-top: 16px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .timeline-status-section {
            padding: 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            padding: 10px 16px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }

        .action-btn .btn-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .action-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Reply Box */
        .reply-box {
            display: none;
            margin-top: 16px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .reply-box.show {
            display: block;
        }

        .reply-textarea {
            width: 100%;
            min-height: 100px;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            resize: vertical;
            margin-bottom: 12px;
        }

        .reply-textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .reply-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-send {
            padding: 8px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-send:hover {
            background: #1d4ed8;
        }

        .btn-cancel {
            padding: 8px 20px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        /* Quick Action Dropdown */
        .quick-action-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 4px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 100%;
            max-width: 350px;
            z-index: 100;
            max-height: 300px;
            overflow-y: auto;
        }

        .quick-action-dropdown.show {
            display: block;
        }

        .dropdown-item {
            padding: 12px 16px;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #f3f4f6;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background: #f9fafb;
            color: #111827;
        }

        .dropdown-item > div {
            width: 100%;
        }

        .action-wrapper {
            position: relative;
            width: 100%;
        }

        /* Change State Dropdown */
        .state-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 4px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 100%;
            max-width: 280px;
            z-index: 100;
        }

        .state-dropdown.show {
            display: block;
        }
   </style>
</head>
<body>
    <div class="container">
        <a href="{{ url('/') }}" class="back-button">
            ← Back
        </a>

        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo">
                <span>GoBEST™ Listens Complaint System</span>
            </div>
        </div>

        <div class="form-container">
            <h1>Track Your Complaints</h1>
            <p class="description">Verify your identity to access all your complaint information</p>


            @if(session('success'))
            <!-- Success Modal -->
            <div id="successModal" class="modal show">
                <div class="modal-content">
                    <button class="close-button" onclick="closeModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <div class="success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h2>Complaint Submitted</h2>
                    <p>Your complaint has been submitted successfully.</p>
                    <div class="complaint-id">
                        Case Number: <span id="complaintId">{{ session('case_number') }}</span>
                        <button class="copy-button" onclick="copyComplaintId()" title="Copy case number">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                    </div>
                    <a href="{{ route('public.complaints.create') }}" class="submit-another">Submit Again</a>
                </div>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <!-- Track Complaints Button - Hidden as modal auto-opens -->
            @if(session('success'))
            <!-- Hide track button when showing success modal -->
            <div style="text-align: center; color: #666; margin-top: 20px;">
                <p>Close the modal above to access complaint tracking</p>
            </div>
            @endif

            <!-- Complaints Display Area -->
            <div id="complaintsDisplayArea" style="display: none; margin-top: 30px;">
                <!-- Complaints will be displayed here after OTP verification -->
            </div>

            <!-- Guest Login Modal -->
            <div id="guestLoginModal" class="guest-modal" onclick="event.stopPropagation()">
                <div class="guest-modal-content" onclick="event.stopPropagation()">
                    <button class="close-modal-btn" onclick="closeAndGoHome()" title="Go back to homepage">&times;</button>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" id="step1">1</div>
                        <div class="step-line" id="line1"></div>
                        <div class="step" id="step2">2</div>
                    </div>

                    <!-- Step 1: Enter Email -->
                    <div class="modal-step active" id="emailStep">
                        <h2>Access Your Complaints</h2>
                        <p class="description">Enter your email address to receive an OTP</p>
                        
                        <form id="emailForm" onsubmit="sendOTP(event)">
                            @csrf
                            <div class="form-group">
                                <input type="email" id="guestEmail" name="email" placeholder="Enter your email address" required>
                            </div>
                            <button type="submit" class="submit-button">Send OTP</button>
                        </form>
                    </div>

                    <!-- Step 2: Enter OTP -->
                    <div class="modal-step" id="otpStep">
                        <h2>Verify Your Identity</h2>
                        <p class="description">Enter the 6-digit OTP sent to your email</p>
                        
                        <form id="otpForm" onsubmit="verifyOTP(event)">
                            @csrf
                            <div class="form-group">
                                <input type="text" id="otpCode" name="otp" class="otp-input" placeholder="000000" maxlength="6" required>
                                <input type="hidden" id="verifyEmail" name="email">
                            </div>
                            <button type="submit" class="submit-button">Verify OTP</button>
                            <button type="button" class="resend-otp" onclick="resendOTP()">Resend OTP</button>
                        </form>
                    </div>

                    <!-- Error Message Step -->
                    <div class="modal-step" id="errorStep">
                        <div style="text-align: center; padding: 20px;">
                            <div style="color: #dc2626; font-size: 48px; margin-bottom: 16px;">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <h2 style="color: #dc2626; margin-bottom: 16px;">No Complaints Found</h2>
                            <p class="description" id="errorMessage">
                                No complaints are registered with this email address.
                            </p>
                            <div style="margin-top: 24px;">
                                <button type="button" class="submit-button" onclick="resetToEmailStep()">
                                    Try Another Email
                                </button>
                                <button type="button" class="submit-another" onclick="closeAndGoHome()" style="margin-left: 10px;">
                                    Back to Home
                                </button>
                            </div>
                        </div>
                    </div>


                    <!-- Loading State -->
                    <div class="modal-step" id="loadingStep">
                        <div class="loading">
                            <div class="spinner"></div>
                            <p>Processing...</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($complaint))
            <div class="result-container show">
                <div class="status-section">
                    <span class="status-badge status-{{ $complaint->display_status }}">
                        @if($complaint->display_status === 'awaiting_signature')
                            <i class="fas fa-signature"></i>
                            Awaiting Signatures
                        @else
                        {{ ucfirst(str_replace('_', ' ', $complaint->status=='escalated'?'progress':$complaint->status)) }}
                        @endif
                    </span>
                </div>

                <div class="complaint-details">
                    <div class="detail-row">
                        <span class="detail-label">Case Number</span>
                        <span class="detail-value">{{ $complaint->case_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Submitted As</span>
                        <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $complaint->submitted_as)) }}</span>
                    </div>
                    @if($complaint->is_anonymous)
                    <div class="detail-row">
                        <span class="detail-label">Anonymous Complaint</span>
                        <span class="detail-value">Yes</span>
                    </div>
                    @endif
                    @if(!$complaint->is_anonymous && $complaint->name)
                    <div class="detail-row">
                        <span class="detail-label">Name</span>
                        <span class="detail-value">{{ $complaint->name }}</span>
                    </div>
                    @endif
                    @if($complaint->email)
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $complaint->email }}</span>
                    </div>
                    @endif
                    @if($complaint->phone_number)
                    <div class="detail-row">
                        <span class="detail-label">Phone Number</span>
                        <span class="detail-value">{{ $complaint->phone_number }}</span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Issue Type</span>
                        <span class="detail-value">{{ ucfirst($complaint->complaint_type) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Location</span>
                        <span class="detail-value">{{ $complaint->location }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date Submitted</span>
                        <span class="detail-value">{{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($complaint->submitted_at)
                    <div class="detail-row">
                        <span class="detail-label">Date of Experience</span>
                        <span class="detail-value">{{ $complaint->submitted_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Description</span>
                        <span class="detail-value">{{ $complaint->description }}</span>
                    </div>
                    @if($complaint->complaint_about)
                    <div class="detail-row">
                        <span class="detail-label">Complaint About</span>
                        <span class="detail-value">{{ $complaint->complaint_about }}</span>
                    </div>
                    @endif
                    @if($complaint->complainee_name)
                    <div class="detail-row">
                        <span class="detail-label">Complainee Name</span>
                        <span class="detail-value">{{ $complaint->complainee_name }}</span>
                    </div>
                    @endif
                    @if($complaint->complainee_email)
                    <div class="detail-row">
                        <span class="detail-label">Complainee Email</span>
                        <span class="detail-value">{{ $complaint->complainee_email }}</span>
                    </div>
                    @endif
                    @if($complaint->complainee_address)
                    <div class="detail-row">
                        <span class="detail-label">Complainee Address</span>
                        <span class="detail-value">{{ $complaint->complainee_address }}</span>
                    </div>
                    @endif
                    @if($complaint->witnesses)
                    <div class="detail-row">
                        <span class="detail-label">Witnesses</span>
                        <span class="detail-value">{{ $complaint->witnesses }}</span>
                    </div>
                    @endif
                    @if($complaint->evidence_type)
                    <div class="detail-row">
                        <span class="detail-label">Evidence Type</span>
                        <span class="detail-value">{{ ucfirst(str_replace('_', ' / ', $complaint->evidence_type)) }}</span>
                    </div>
                    @endif
                    @if($complaint->evidence_description)
                    <div class="detail-row">
                        <span class="detail-label">Evidence Description</span>
                        <span class="detail-value">{{ $complaint->evidence_description }}</span>
                    </div>
                    @endif
                </div>

                @php
                $guestAttachments = $complaint->attachments->filter(function ($attachment) {
    return is_null($attachment->respondent_response_id) && (is_null($attachment->uploaded_by) || $attachment->uploaded_by == 1);
});                @endphp
                @if($guestAttachments->isNotEmpty())
                <div class="">
                    <span class="detail-label">Attachments</span>
                    <div class="attachments-grid">
                        @foreach ($guestAttachments as $attachment)
                            @php
                                $extension = strtolower($attachment->file_type);
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                            @endphp
                            @if($isImage)
                                <div class="attachment-item">
                                    <img src="{{ asset($attachment->file_path) }}"
                                        alt="Attachment"
                                        class="img-thumbnail"
                                        onclick="openImagePreview('{{ asset($attachment->file_path) }}')">
                                </div>
                            @else
                                <div class="attachment-item">
                                    <a href="{{ asset($attachment->file_path) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        target="_blank">
                                        <i class="bi bi-file-earmark"></i>
                                        {{ strtoupper($extension) }}
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @php
                    // Combine all timeline activities (responses and replies)
                    $timelineActivities = collect();

                    // Add respondent responses
                    foreach($complaint->respondents as $respondent) {
                        foreach($respondent->responses as $response) {
                            $timelineActivities->push((object)[
                                'type' => 'respondent_response',
                                'timestamp' => $response->created_at,
                                'data' => $response,
                                'user' => $respondent->user
                            ]);
                        }
                    }

                    // Add admin replies (only general replies for complainant view)
                    if($complaint->replies) {
                        foreach($complaint->replies as $reply) {
                            // Only show general replies (no specific recipient) to complainants
                            if($reply->recipient_id === null) {
                                $timelineActivities->push((object)[
                                    'type' => 'admin_reply',
                                    'timestamp' => $reply->created_at,
                                    'data' => $reply
                                ]);
                            }
                        }
                    }

                    // Sort by timestamp - newest first
                    $timelineActivities = $timelineActivities->sortByDesc('timestamp');
                @endphp

                @if($timelineActivities->isNotEmpty())
                <h3 class="response-title">Response Timeline</h3>
                <div class="response-section">
                    <div class="timeline">
                        @foreach($timelineActivities as $activity)
                            @if($activity->type === 'respondent_response')
                            <div class="timeline-item">
                                <div class="timeline-avatar">
                                    {{ substr($activity->user->name ?? 'R', 0, 1) }}
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-sender">{{ $activity->user->name ?? 'Respondent' }}</span>
                                        <span class="timeline-date">{{ $activity->timestamp->format('D, M j, Y, g:i A') }}</span>
                                    </div>
                                    <div class="timeline-body">
                                        <p class="response-text">{{ $activity->data->response }}</p>
                                        @if($activity->data->attachments->isNotEmpty())
                                            <div class="response-attachments">
                                                <h4>Attachments</h4>
                                                <div class="attachments-grid">
                                                    @foreach($activity->data->attachments as $attachment)
                                                        @php
                                                            $extension = strtolower($attachment->file_type);
                                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                                        @endphp
                                                        @if($isImage)
                                                            <div class="attachment-item">
                                                                <img src="{{ asset($attachment->file_path) }}"
                                                                    alt="Response Attachment"
                                                                    class="img-thumbnail"
                                                                    onclick="openImagePreview('{{ asset($attachment->file_path) }}')">
                                                            </div>
                                                        @else
                                                            <div class="attachment-item">
                                                                <a href="{{ asset($attachment->file_path) }}"
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    target="_blank">
                                                                    <i class="bi bi-file-earmark"></i>
                                                                    {{ strtoupper($extension) }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @elseif($activity->type === 'admin_reply')
                            <div class="timeline-item">
                                <div class="timeline-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    {{ substr($activity->data->user->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-sender">{{ $activity->data->user->name ?? 'Admin' }}
                                            @if($activity->data->recipient_id)
                                                <span class="badge" style="background: #007bff; color: white; font-size: 11px; padding: 2px 8px; margin-left: 8px;">Reply to {{ $activity->data->recipient->name ?? 'Recipient' }}</span>
                                            @else
                                                <span class="badge" style="background: #007bff; color: white; font-size: 11px; padding: 2px 8px; margin-left: 8px;">Reply</span>
                                            @endif
                                        </span>
                                        <span class="timeline-date">{{ $activity->timestamp->format('D, M j, Y, g:i A') }}</span>
                                    </div>
                                    <div class="timeline-body">
                                        <p class="response-text" style="white-space: pre-wrap;">{{ $activity->data->message }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Detailed Respondent Responses for Guests -->
                    @php
                        $detailedResponses = \App\Models\RespondentResponseDetail::where('complaint_id', $complaint->id)->with(['user', 'attachments'])->get();
                    @endphp
                    @if($detailedResponses->count() > 0)
                    <div class="detailed-responses-section">
                        <h3 class="response-title">Detailed Respondent Responses</h3>
                        @foreach($detailedResponses as $response)
                        <div class="response-card">
                            <div class="response-avatar">
                                {{ substr($response->respondent_name, 0, 1) }}
                            </div>

                            <div class="response-details">
                                <div class="response-header">
                                    <div class="respondent-info">
                                        <h4>{{ $response->respondent_name }}</h4>
                                        <p>{{ $response->venue_legal_name }} - {{ $response->venue_city_state }}</p>
                                    </div>
                                    <div class="response-date">
                                        <small>{{ $response->submitted_at->format('D, M j, Y, g:i A') }}</small>
                                    </div>
                                </div>
                                <div class="detail-grid">
                                    <div class="detail-row">
                                        <span class="detail-label">Email</span>
                                        <span class="detail-value">{{ $response->respondent_email }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Complaint Date</span>
                                        <span class="detail-value">{{ $response->complaint_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Evidence Type</span>
                                        <span class="detail-value">{{ $response->supporting_evidence_type_label }}</span>
                                    </div>
                                </div>
                                
                                <div class="response-content">
                                    <div class="content-section">
                                        <h5>Respondent's Side of the Story</h5>
                                        <p>{{ $response->respondent_side_story }}</p>
                                    </div>
                                    
                                    <div class="content-section">
                                        <h5>Detailed Issue Description</h5>
                                        <p>{{ $response->issue_detail_description }}</p>
                                    </div>
                                    
                                    <div class="content-section">
                                        <h5>Witnesses Information</h5>
                                        <p>{{ $response->witnesses_information }}</p>
                                    </div>
                                    
                                    @if($response->evidence_description)
                                    <div class="content-section">
                                        <h5>Evidence Description</h5>
                                        <p>{{ $response->evidence_description }}</p>
                                    </div>
                                    @endif
                                </div>
                                
                                @if($response->attachments->count() > 0)
                                <div class="response-attachments">
                                    <h5>Supporting Evidence Files</h5>
                                    <div class="attachments-grid">
                                        @foreach($response->attachments as $attachment)
                                            @php
                                                $extension = strtolower($attachment->file_type);
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp
                                            @if($isImage)
                                                <div class="attachment-item">
                                                    <img src="{{ asset($attachment->file_path) }}" 
                                                         alt="Evidence" 
                                                         class="img-thumbnail"
                                                         onclick="openImagePreview('{{ asset($attachment->file_path) }}')">
                                                </div>
                                            @else
                                                <div class="attachment-item">
                                                    <a href="{{ asset($attachment->file_path) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="bi bi-file-earmark"></i>
                                                        {{ strtoupper($extension) }}
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Detailed Lawyer Responses for Guests -->
                    @php
                        $detailedLawyerResponses = \App\Models\LawyerResponseDetail::where('complaint_id', $complaint->id)->with(['user', 'attachments'])->get();
                    @endphp
                    @if($detailedLawyerResponses->count() > 0)
                    <div class="detailed-responses-section">
                        <h3 class="response-title">Legal Review Responses</h3>
                        @foreach($detailedLawyerResponses as $response)
                        <div class="response-card">
                            <div class="response-avatar" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                {{ substr($response->lawyer_name, 0, 1) }}
                            </div>

                            <div class="response-details">
                                <div class="response-header">
                                    <div class="respondent-info">
                                        <h4>{{ $response->lawyer_name }}</h4>
                                        <p>{{ $response->law_firm_name }} - {{ $response->lawyer_city_state }}</p>
                                    </div>
                                    <div class="response-date">
                                        <small>{{ $response->submitted_at->format('D, M j, Y, g:i A') }}</small>
                                    </div>
                                </div>
                                <div class="detail-grid">
                                    <div class="detail-row">
                                        <span class="detail-label">Case Number</span>
                                        <span class="detail-value">{{ $response->case_number }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Review Date</span>
                                        <span class="detail-value">{{ \Carbon\Carbon::parse($response->review_date)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Supporting Evidence</span>
                                        <span class="detail-value">
                                            @if($response->has_supporting_evidence)
                                                <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-size: 12px;">{{ ucfirst(str_replace('_', ' ', $response->supporting_evidence_type)) }}</span>
                                            @else
                                                <span style="background: #f3f4f6; color: #6b7280; padding: 2px 8px; border-radius: 4px; font-size: 12px;">No Evidence</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="response-content">
                                    <div class="content-section">
                                        <h5>Legal Assessment</h5>
                                        <p>{{ $response->legal_assessment }}</p>
                                    </div>
                                    
                                    <div class="content-section">
                                        <h5>Legal Recommendations</h5>
                                        <p>{{ $response->legal_recommendations }}</p>
                                    </div>
                                    
                                    <div class="content-section">
                                        <h5>Compliance & Regulatory Notes</h5>
                                        <p>{{ $response->compliance_notes }}</p>
                                    </div>
                                    
                                    @if($response->evidence_description)
                                    <div class="content-section">
                                        <h5>Evidence Description</h5>
                                        <p>{{ $response->evidence_description }}</p>
                                    </div>
                                    @endif
                                </div>
                                
                                @if($response->attachments && $response->attachments->count() > 0)
                                <div class="response-attachments">
                                    <h5>Supporting Evidence Files</h5>
                                    <div class="attachments-grid">
                                        @foreach($response->attachments as $attachment)
                                            @php
                                                $extension = strtolower($attachment->file_type);
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp
                                            @if($isImage)
                                                <div class="attachment-item">
                                                    <img src="{{ asset($attachment->file_path) }}" 
                                                         alt="Legal Evidence" 
                                                         class="img-thumbnail"
                                                         onclick="openImagePreview('{{ asset($attachment->file_path) }}')">
                                                </div>
                                            @else
                                                <div class="attachment-item">
                                                    <a href="{{ asset($attachment->file_path) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="bi bi-file-earmark"></i>
                                                        {{ strtoupper($extension) }}
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <!-- Timeline/Status Info -->
                        <div class="timeline-status-section">
                            <h4 style="font-size: 13px; font-weight: 600; color: #6b7280; margin: 0 0 12px 0; text-transform: uppercase;">Timeline Status</h4>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                                    <span style="color: #6b7280;">Current Status:</span>
                                    <span class="status-badge status-{{ $complaint->display_status ?? $complaint->status }}" style="font-size: 11px; padding: 3px 10px;">
                                        @if($complaint->display_status === 'awaiting_signature')
                                            Awaiting Signatures
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $complaint->status=='escalated'?'progress':$complaint->status)) }}
                                        @endif
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                                    <span style="color: #6b7280;">Total Responses:</span>
                                    <span style="color: #374151; font-weight: 500;">
                                        @php
                                            $totalResponses = 0;
                                            foreach($complaint->respondents as $respondent) {
                                                $totalResponses += $respondent->responses->count();
                                            }
                                            $detailedResponses = \App\Models\RespondentResponseDetail::where('complaint_id', $complaint->id)->count();
                                            $detailedLawyerResponses = \App\Models\LawyerResponseDetail::where('complaint_id', $complaint->id)->count();
                                            $totalResponses += $detailedResponses + $detailedLawyerResponses;
                                        @endphp
                                        {{ $totalResponses }}
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                                    <span style="color: #6b7280;">Last Updated:</span>
                                    <span style="color: #374151; font-weight: 500;">{{ $complaint->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div style="border-top: 1px solid #e5e7eb; margin: 16px 0;"></div>

                        <!-- Reply Button -->
                        <button class="action-btn" onclick="toggleReplyBox()">
                            <div class="btn-content">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Reply
                            </div>
                        </button>

                        <!-- Reply To Button (Send To) -->
                        <div class="action-wrapper">
                            <button class="action-btn" onclick="toggleSendToDropdown(event)">
                                <div class="btn-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Reply to
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="quick-action-dropdown" id="sendToDropdown">
                                <div style="padding: 8px 16px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #f3f4f6;">Send To</div>
                                @if($complaint->respondents->isNotEmpty())
                                    @foreach($complaint->respondents as $respondent)
                                        <div class="dropdown-item" onclick="selectSendTo('{{ $respondent->user->name ?? 'Respondent' }}', '{{ $respondent->user->email ?? '' }}')">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: 600;">
                                                    {{ substr($respondent->user->name ?? 'R', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight: 500;">{{ $respondent->user->name ?? 'Respondent' }}</div>
                                                    <div style="font-size: 12px; color: #6b7280;">{{ $respondent->user->email ?? '' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                @php
                                    $detailedResponses = \App\Models\RespondentResponseDetail::where('complaint_id', $complaint->id)->with(['user'])->get();
                                @endphp
                                @foreach($detailedResponses as $response)
                                    <div class="dropdown-item" onclick="selectSendTo('{{ $response->respondent_name }}', '{{ $response->respondent_email }}')">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: 600;">
                                                {{ substr($response->respondent_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 500;">{{ $response->respondent_name }}</div>
                                                <div style="font-size: 12px; color: #6b7280;">{{ $response->respondent_email }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @php
                                    $detailedLawyerResponses = \App\Models\LawyerResponseDetail::where('complaint_id', $complaint->id)->with(['user'])->get();
                                @endphp
                                @foreach($detailedLawyerResponses as $response)
                                    <div class="dropdown-item" onclick="selectSendTo('{{ $response->lawyer_name }}', '{{ $response->lawyer_email ?? '' }}')">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: 600;">
                                                {{ substr($response->lawyer_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 500;">{{ $response->lawyer_name }}</div>
                                                <div style="font-size: 12px; color: #6b7280;">Lawyer - {{ $response->lawyer_email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Forward Button (Change State) -->
                        <div class="action-wrapper">
                            <button class="action-btn" onclick="toggleForwardDropdown(event)">
                                <div class="btn-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    Forward
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="state-dropdown" id="forwardDropdown">
                                <div style="padding: 8px 16px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #f3f4f6;">Change State</div>
                                <div class="dropdown-item" onclick="changeComplaintState('submitted')">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="status-badge status-submitted" style="font-size: 10px; padding: 2px 8px;">SUBMITTED</span>
                                        <span>Submitted</span>
                                    </div>
                                </div>
                                <div class="dropdown-item" onclick="changeComplaintState('under_review')">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="status-badge status-under_review" style="font-size: 10px; padding: 2px 8px;">UNDER REVIEW</span>
                                        <span>Under Review</span>
                                    </div>
                                </div>
                                <div class="dropdown-item" onclick="changeComplaintState('escalated')">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="status-badge status-escalated" style="font-size: 10px; padding: 2px 8px;">PROGRESS</span>
                                        <span>In Progress</span>
                                    </div>
                                </div>
                                <div class="dropdown-item" onclick="changeComplaintState('resolved')">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="status-badge status-resolved" style="font-size: 10px; padding: 2px 8px;">RESOLVED</span>
                                        <span>Resolved</span>
                                    </div>
                                </div>
                                <div class="dropdown-item" onclick="changeComplaintState('closed')">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="status-badge status-closed" style="font-size: 10px; padding: 2px 8px;">CLOSED</span>
                                        <span>Closed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Box -->
                    <div class="reply-box" id="replyBox">
                        <textarea class="reply-textarea" id="replyMessage" placeholder="Write your reply..."></textarea>
                        <div class="reply-actions">
                            <button class="btn-cancel" onclick="cancelReply()">Cancel</button>
                            <button class="btn-send" onclick="sendReply()">Send</button>
                        </div>
                    </div>

                    @php
                    // Get the latest resolution first
                    $latestResolution = $complaint->latestResolution;
                    $userSignature = null;
                    $allSignaturesComplete = true;

                    if ($latestResolution) {
                        $userSignature = $latestResolution->signatures()->whereNull('user_id')->first();

                        // Check all required signatures
                        $requiredSignatures = $complaint->signatures()->pluck('signer_role')->toArray();
                        foreach ($requiredSignatures as $signer) {
                            $signature = null;
                            if ($signer === 'complainant') {
                                $signature = $latestResolution->signatures()->whereNull('user_id')->first();
                            } elseif ($signer === 'respondent' && $complaint->respondents->isNotEmpty()) {
                                $signature = $latestResolution->signatures()
                                    ->where('user_id', $complaint->respondents->first()->user_id)
                                    ->first();
                            } elseif ($signer === 'leadership') {
                                $signature = $latestResolution->signatures()
                                    ->whereHas('user', function($query) {
                                        $query->where('role', 'admin');
                                    })
                                    ->first();
                            }

                            if (!$signature || !$signature->signature_path) {
                                $allSignaturesComplete = false;
                                break;
                            }
                        }
                    }
                    @endphp
                    @if($latestResolution && $userSignature)

                        @if(is_null($userSignature->signature_path))
                    <div class="timeline-item signature-section mt-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <div class="d-flex align-items-center mb-4 signature_top">
                                <div class="signature-icon me-3">
                                    <i class="fas fa-file-signature fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="h5 mb-1">Digital Signature Required</h3>
                                    <p class="text-muted small mb-0 image_format">Please provide your signature to complete this process</p>
                                </div>
                            </div>

                        <div class="signature-upload-container p-4 bg-light rounded-3 border-2" style="max-width: 400px; margin: 0 auto;">
                            <div class="text-center mb-3">

                                <h6 class="fw-bold mb-2">Upload Your Signature</h6>
                                <p class="text-muted small mb-0">Click to browse or drag image here</p>
                            </div>

                            <form id="signatureUploadForm" class="mt-3" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                                <input type="hidden" name="email" value="{{ $complaint->email }}">

                                <div class="upload-box position-relative mb-2">
                                    <input type="file"
                                           class="form-control form-control-sm"
                                           name="signature"
                                           accept="image/*"
                                           required
                                           id="signatureInput"
                                           onchange="previewSignature(this)"
                                           style="font-size: 0.875rem;">
                                </div>
                                <div class="text-center">
                                    <div class="form-text mb-3 image_format">Please upload a clear image of your signature (JPEG, PNG, JPG)</div>
                                    <button type="button" class="btn btn-primary btn-sm px-3 upload_btn" onclick="uploadSignature()">
                                        <i class="fas fa-upload me-2"></i>Upload
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="timeline-item signature-section mt-4">
                        <div class="signature-success p-4 bg-white border border-success-subtle rounded-lg shadow-hover">
                            <div class="d-flex align-items-center mb-3">

                                <div>
                                    <h5 class="fw-bold mb-1 text-success">Signature Successfully Uploaded</h5>
                                    <p class="text-muted small mb-0">Your digital signature has been securely stored</p>
                                </div>
                            </div>
                            <div class="signature-preview-container p-3 bg-light rounded-3 border mt-3">
                                <img src="{{ asset($userSignature->signature_path) }}"
                                     alt="Your Signature"
                                     class="signature-preview img-fluid"
                                     style="max-height: 100px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($allSignaturesComplete && $latestResolution && $latestResolution->generated_pdf_path)
                        <div class="timeline-item mt-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-success-subtle">
                                <div class="d-flex align-items-center mb-3">

                                    <div>
                                        <h5 class="fw-bold mb-1">All Signatures Collected</h5>
                                        <p class="text-muted small mb-0">The resolution document is now ready for download</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ asset($latestResolution->generated_pdf_path) }}"
                                       class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                       target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                        Download Resolution PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                </div>
                @endif

                    </div>
                </div>
                @endif

            </div>
            @endif
        </div>
    </div>

    <!-- Add this before the closing body tag -->
    <style>
    .signature-section {
        transition: all 0.3s ease;
        position: relative;
    }
    .signature-section:hover {
        transform: translateY(-1px);
    }
    .signature-icon, .success-icon-circle, .upload-icon-circle {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        transition: all 0.3s ease;
    }
    .success-icon-circle {
        background: rgba(22, 163, 74, 0.1);
        color: #16a34a;
    }
    .upload-icon-circle {
        width: 64px;
        height: 64px;
        margin: 0 auto;
        background: rgba(37, 99, 235, 0.05);
    }
    .timeline-item {
        position: relative;
        padding:20px;
        margin-bottom: 2rem;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 1.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px currentColor;
    }
    .timeline-dot.bg-primary {
        color: #2563eb;
    }
    .timeline-dot.bg-success {
        color: #16a34a;
    }
    .shadow-hover {
        transition: all 0.3s ease;
    }
    .shadow-hover:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }
    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
        border-color: #cbd5e1 !important;
    }
    .signature-upload-container {
        transition: all 0.3s ease;
        max-width: 400px;
        margin: 0 auto;
    }
    .signature-upload-container:hover {
        border-color: #2563eb !important;
        background-color: rgba(37, 99, 235, 0.02) !important;
    }
    .upload-box {
        position: relative;
        transition: all 0.3s ease;
    }
    .upload-box:hover {
        transform: translateY(-1px);
    }
    .form-control-lg {
        padding: 1rem;
        font-size: 1rem;
        border-radius: 0.5rem;
    }
    .bg-success-subtle {
        background-color: rgba(22, 163, 74, 0.1) !important;
    }
    .preview-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100px;
        background: #fff;
        transition: all 0.3s ease;
    }
    .preview-container:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-lg {
        font-weight: 500;
        letter-spacing: 0.025em;
        transition: all 0.2s ease;
    }
    .btn-lg:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .form-text {
        color: #64748b;
    }
    </style>

    <script>
    function previewSignature(input) {
        const preview = document.getElementById('signaturePreview');
        const previewImg = preview.querySelector('img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('d-none');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Auto-open modal when page loads (unless showing success modal)
        $(document).ready(function() {
            @if(session('success'))
                // Complaint just created - success modal will be shown automatically
                // Don't show the complaint access modal
                console.log('Complaint created successfully, showing success modal');
            @else
                // Normal track page - auto-open the modal
                console.log('Track page loaded - auto-opening modal');
                showGuestLoginModal();
            @endif
        });
        
        function uploadSignature() {
            const form = document.getElementById('signatureUploadForm');
            const formData = new FormData(form);

            fetch('{{ route("public.complaints.upload-signature") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Signature uploaded successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Error uploading signature');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error uploading signature');
            });
        }

        function closeModal() {
            document.getElementById('successModal').classList.remove('show');
            // Reload page to clear session and show normal track interface
            window.location.href = '{{ route("public.complaints.track-form") }}';
        }

        function copyComplaintId() {
            const complaintId = document.getElementById('complaintId').textContent;
            navigator.clipboard.writeText(complaintId).then(() => {
                const copyButton = document.querySelector('.copy-button');
                const originalTitle = copyButton.title;
                alert('Complaint ID copied to clipboard!')
            //    copyButton.title = 'Copied!';
            //    setTimeout(() => {
            //        copyButton.title = originalTitle;
            //    }, 2000);
            });
        }

        function openImagePreview(imageSrc) {
            document.getElementById('previewImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
        }

        // Guest Login Modal Functions
        function showGuestLoginModal() {
            document.getElementById('guestLoginModal').classList.add('show');
            resetModal();
        }
        
        // Allow modal to be closed and redirect to homepage
        function closeAndGoHome() {
            window.location.href = '{{ url("/") }}';
        }

        function closeGuestModal() {
            document.getElementById('guestLoginModal').classList.remove('show');
            resetModal();
        }

        function resetModal() {
            // Reset steps
            document.querySelectorAll('.modal-step').forEach(step => step.classList.remove('active'));
            document.getElementById('emailStep').classList.add('active');
            
            // Reset step indicators
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
            });
            document.querySelectorAll('.step-line').forEach(line => {
                line.classList.remove('active');
            });
            document.getElementById('step1').classList.add('active');
            
            // Clear forms
            document.getElementById('guestEmail').value = '';
            document.getElementById('otpCode').value = '';
        }

        function showStep(stepNumber) {
            // Hide all steps
            document.querySelectorAll('.modal-step').forEach(step => step.classList.remove('active'));
            
            // Show target step
            if (stepNumber === 1) {
                document.getElementById('emailStep').classList.add('active');
            } else if (stepNumber === 2) {
                document.getElementById('otpStep').classList.add('active');
            } else if (stepNumber === 'loading') {
                document.getElementById('loadingStep').classList.add('active');
            } else if (stepNumber === 'error') {
                document.getElementById('errorStep').classList.add('active');
            }
            
            // Update step indicators
            document.querySelectorAll('.step').forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 < stepNumber) {
                    step.classList.add('completed');
                } else if (index + 1 === stepNumber) {
                    step.classList.add('active');
                }
            });
            
            // Update step lines
            document.querySelectorAll('.step-line').forEach((line, index) => {
                line.classList.remove('active');
                if (index + 1 < stepNumber) {
                    line.classList.add('active');
                }
            });
        }
        
        function showErrorStep(message) {
            // Hide all steps
            document.querySelectorAll('.modal-step').forEach(step => step.classList.remove('active'));
            
            // Show error step
            document.getElementById('errorStep').classList.add('active');
            
            // Update error message if provided
            if (message) {
                document.getElementById('errorMessage').textContent = message;
            }
            
            // Hide step indicators for error state
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
            });
            document.querySelectorAll('.step-line').forEach(line => {
                line.classList.remove('active');
            });
        }
        
        function resetToEmailStep() {
            // Clear the email input
            document.getElementById('guestEmail').value = '';
            // Go back to step 1
            showStep(1);
        }

        async function sendOTP(event) {
            event.preventDefault();
            const email = document.getElementById('guestEmail').value;
            
            showStep('loading');
            
            try {
                const response = await fetch('{{ url("/guest/send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('verifyEmail').value = email;
                    showStep(2);
                } else {
                    // Check if it's a "no complaints found" error (404 status)
                    if (response.status === 404) {
                        showErrorStep(data.message || 'No complaints found for this email address.');
                    } else {
                        alert(data.message || 'Failed to send OTP');
                        showStep(1);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error sending OTP. Please try again.');
                showStep(1);
            }
        }

        async function verifyOTP(event) {
            event.preventDefault();
            const email = document.getElementById('verifyEmail').value;
            const otp = document.getElementById('otpCode').value;
            
            showStep('loading');
            
            try {
                const response = await fetch('{{ url("/guest/verify-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: email, otp: otp })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Close modal and show complaints on page
                    closeGuestModal();
                    displayComplaintsOnPage(data.complaints);
                } else {
                    alert(data.message || 'Invalid OTP');
                    showStep(2);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error verifying OTP. Please try again.');
                showStep(2);
            }
        }

        async function resendOTP() {
            const email = document.getElementById('verifyEmail').value;
            
            try {
                const response = await fetch('{{ url("/guest/send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('OTP sent successfully!');
                } else {
                    alert(data.message || 'Failed to resend OTP');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error resending OTP. Please try again.');
            }
        }



        function viewFullComplaint(caseNumber) {
            window.location.href = `{{ url("/complaint") }}/${caseNumber}`;
        }

        // Display complaints on the main page after OTP verification
        function displayComplaintsOnPage(complaints) {
            const container = document.getElementById('complaintsDisplayArea');
            
            if (complaints.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">No complaints found for this email address.</p>';
                container.style.display = 'block';
                return;
            }

            let html = '<div class="form-container">';
            
            if (complaints.length === 1) {
                // Single complaint - show detailed view
                const complaint = complaints[0];
                html += `
                    <h2>Your Complaint</h2>
                    <div class="result-container show">
                        <div class="status-section">
                            <span class="status-badge status-${complaint.display_status || complaint.status}">
                                ${complaint.display_status === 'awaiting_signature' ? 'Awaiting Signatures' : 
                                  (complaint.status === 'escalated' ? 'Progress' : complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1))}
                            </span>
                        </div>
                        <div class="complaint-details">
                            <div class="detail-row">
                                <span class="detail-label">Case Number</span>
                                <span class="detail-value">${complaint.case_number}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Issue Type</span>
                                <span class="detail-value">${complaint.complaint_type}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Location</span>
                                <span class="detail-value">${complaint.location}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date Submitted</span>
                                <span class="detail-value">${new Date(complaint.created_at).toLocaleDateString()}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Description</span>
                                <span class="detail-value">${complaint.description}</span>
                            </div>
                        </div>
                        <div style="margin-top: 20px; text-align: center;">
                            <button class="submit-button" onclick="viewFullComplaint('${complaint.case_number}')" style="width: auto; padding: 12px 24px;">
                                View Full Timeline
                            </button>
                        </div>
                    </div>
                `;
            } else {
                // Multiple complaints - show accordion
                html += `
                    <h2>Your Complaints (${complaints.length})</h2>
                    <p class="description">Click on any complaint to view details</p>
                    <div class="complaints-accordion">
                `;
                
                complaints.forEach((complaint, index) => {
                    const statusClass = `status-${complaint.display_status || complaint.status}`;
                    const statusText = complaint.display_status === 'awaiting_signature' ? 'Awaiting Signatures' : 
                        (complaint.status === 'escalated' ? 'Progress' : complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1));
                    
                    html += `
                        <div class="accordion-item">
                            <div class="accordion-header" onclick="togglePageAccordion('${complaint.case_number}', ${index})">
                                <div>
                                    <div class="accordion-title">${complaint.case_number}</div>
                                    <div style="font-size: 12px; color: #666; margin-top: 4px;">
                                        ${complaint.complaint_type} • ${new Date(complaint.created_at).toLocaleDateString()}
                                    </div>
                                </div>
                                <div>
                                    <span class="status-badge ${statusClass}" style="margin-right: 10px;">${statusText}</span>
                                    <span class="accordion-toggle" id="pageToggle-${index}">▼</span>
                                </div>
                            </div>
                            <div class="accordion-content" id="pageContent-${index}">
                                <div class="modal-complaint-details">
                                    <div class="complaint-details">
                                        <div class="detail-row">
                                            <span class="detail-label">Issue Type</span>
                                            <span class="detail-value">${complaint.complaint_type}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Location</span>
                                            <span class="detail-value">${complaint.location}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Date Submitted</span>
                                            <span class="detail-value">${new Date(complaint.created_at).toLocaleDateString()}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Description</span>
                                            <span class="detail-value">${complaint.description.substring(0, 200)}${complaint.description.length > 200 ? '...' : ''}</span>
                                        </div>
                                    </div>
                                    <div style="margin-top: 15px; text-align: center;">
                                        <button class="submit-button" onclick="viewFullComplaint('${complaint.case_number}')" style="width: auto; padding: 10px 20px; font-size: 14px;">
                                            View Full Timeline
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
            }
            
            html += '</div>';
            
            container.innerHTML = html;
            container.style.display = 'block';
        }

        // Toggle accordion for page display
        function togglePageAccordion(caseNumber, index) {
            const content = document.getElementById(`pageContent-${index}`);
            const toggle = document.getElementById(`pageToggle-${index}`);
            const header = content.previousElementSibling;

            // Close all other accordions
            document.querySelectorAll('[id^="pageContent-"]').forEach((item, idx) => {
                if (idx !== index && item.classList.contains('active')) {
                    item.classList.remove('active');
                    document.getElementById(`pageToggle-${idx}`).classList.remove('active');
                    item.previousElementSibling.classList.remove('active');
                }
            });

            // Toggle current accordion
            if (content.classList.contains('active')) {
                content.classList.remove('active');
                toggle.classList.remove('active');
                header.classList.remove('active');
            } else {
                content.classList.add('active');
                toggle.classList.add('active');
                header.classList.add('active');
            }
        }

        // Quick Actions Functions
        let selectedSendTo = null;

        function toggleReplyBox() {
            const replyBox = document.getElementById('replyBox');
            replyBox.classList.toggle('show');
            if (replyBox.classList.contains('show')) {
                document.getElementById('replyMessage').focus();
            }
        }

        function cancelReply() {
            document.getElementById('replyBox').classList.remove('show');
            document.getElementById('replyMessage').value = '';
            selectedSendTo = null;
        }

        function sendReply() {
            const message = document.getElementById('replyMessage').value.trim();
            if (!message) {
                alert('Please enter a message');
                return;
            }

            // Here you can add AJAX call to send the reply
            console.log('Sending reply:', message);
            console.log('Send to:', selectedSendTo);

            alert('Reply sent successfully!');
            cancelReply();
        }

        function toggleSendToDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('sendToDropdown');
            const forwardDropdown = document.getElementById('forwardDropdown');

            // Close forward dropdown
            if (forwardDropdown) {
                forwardDropdown.classList.remove('show');
            }

            dropdown.classList.toggle('show');
        }

        function selectSendTo(name, email) {
            selectedSendTo = { name, email };
            document.getElementById('sendToDropdown').classList.remove('show');
            toggleReplyBox();
            document.getElementById('replyMessage').placeholder = `Reply to ${name}...`;
        }

        function toggleForwardDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('forwardDropdown');
            const sendToDropdown = document.getElementById('sendToDropdown');

            // Close send to dropdown
            if (sendToDropdown) {
                sendToDropdown.classList.remove('show');
            }

            dropdown.classList.toggle('show');
        }

        function changeComplaintState(state) {
            const stateLabels = {
                'submitted': 'Submitted',
                'under_review': 'Under Review',
                'escalated': 'In Progress',
                'resolved': 'Resolved',
                'closed': 'Closed'
            };

            if (confirm(`Are you sure you want to change the complaint state to "${stateLabels[state]}"?`)) {
                // Here you can add AJAX call to change the state
                console.log('Changing state to:', state);

                // For now, just show an alert
                alert(`Complaint state forwarded to: ${stateLabels[state]}`);

                // Reload page to reflect changes
                // location.reload();
            }

            document.getElementById('forwardDropdown').classList.remove('show');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const sendToDropdown = document.getElementById('sendToDropdown');
            const forwardDropdown = document.getElementById('forwardDropdown');

            if (sendToDropdown) {
                sendToDropdown.classList.remove('show');
            }
            if (forwardDropdown) {
                forwardDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
