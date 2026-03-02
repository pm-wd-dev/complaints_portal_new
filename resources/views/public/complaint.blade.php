<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Complaint Form - GoBEST™ Listens Complaint System</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            max-width: 700px;
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

        .required {
            color: #dc2626;
        }

        .form-control,
        select,
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            background: white;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 8px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-item input[type="radio"] {
            width: auto;
        }

        .info-text {
            font-size: 14px;
            color: #666;
            font-style: italic;
            margin-top: 4px;
        }

        .hidden {
            display: none;
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            color: #666;
        }

        .file-label:hover {
            border-color: #2ea043;
            color: #2ea043;
        }

        .selected-files {
            margin-top: 8px;
            font-size: 14px;
        }

        .selected-file {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px;
            background: #f5f5f5;
            border-radius: 4px;
            margin-bottom: 8px;
            gap: 12px;
        }

        .selected-file .file-preview {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .selected-file .file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .selected-file .file-info {
            flex: 1;
            min-width: 0;
        }

        .selected-file .file-name {
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
            word-break: break-all;
        }

        .selected-file .file-size {
            font-size: 12px;
            color: #666;
        }

        .remove-file {
            cursor: pointer;
            color: #dc2626;
        }

        .submit-button {
            display: block;
            width: 100%;
            padding: 16px;
            background: #2ea043;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .submit-button:hover {
            background: #2c974b;
        }

        /* Success Modal */
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
        }

        .success-icon {
            width: 64px;
            height: 64px;
            background: #2ea043;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .success-icon svg {
            width: 32px;
            height: 32px;
            color: white;
        }

        .complaint-id {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 8px;
            font-size: 24px;
            font-weight: 600;
            margin: 16px 0;
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

        @media (max-width: 640px) {
            .container {
                padding: 16px;
            }

            .form-container {
                padding: 24px;
            }

            h1 {
                font-size: 24px;
            }
        }

        /* Error styles */
        .error {
            color: #dc2626;
            font-size: 14px;
            margin-top: 4px;
        }

        .is-invalid {
            border-color: #dc2626 !important;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-item input[type="checkbox"] {
            width: auto;
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
            <h1>Complaint Form</h1>
            <p class="description">Please provide details about your complaint</p>

            <form id="complaintForm" method="POST" action="{{ route('public.complaints.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- 1. Select complaint type (Cast Member or Guest) -->
                <div class="form-group">
                    <label for="submitted_as">You are casting this complaint as <span class="required">*</span></label>
                    <select id="submitted_as" name="submitted_as" class="form-control @error('submitted_as') is-invalid @enderror" required>
                        <option value="">Select your role</option>
                        <option value="cast_member" {{ old('submitted_as') == 'cast_member' ? 'selected' : '' }}>Cast Member</option>
                        <option value="guest" {{ old('submitted_as', 'guest') == 'guest' ? 'selected' : '' }}>Guest</option>
                    </select>
                    @error('submitted_as')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 2. Anonymous option -->
                <div class="form-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                        <label for="is_anonymous">Submit this complaint anonymously</label>
                    </div>
                    @error('is_anonymous')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 3. Contact Information (conditional based on anonymous) -->
                <div id="contact-info">
                    <div class="form-group" id="name-group">
                        <label for="name">Your Name (Who is making the complaint) <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter your full name">
                        @error('name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Anonymous contact method selector -->
                    <div class="form-group" id="contact-method-group" style="display: none;">
                        <label for="contact_method">How would you like to receive updates? <span class="required">*</span></label>
                        <select id="contact_method" name="contact_method" class="form-control">
                            <option value="">Select contact method</option>
                            <option value="email" {{ old('contact_method') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('contact_method') == 'phone' ? 'selected' : '' }}>Phone</option>
                        </select>
                    </div>

                    <div class="form-group" id="email-group">
                        <label for="email">Email Address <span id="email-required" class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter your email address">
                        <div class="info-text" id="email-info">We need either email or phone to send you updates regarding your complaint</div>
                        @error('email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" id="phone-group">
                        <label for="phone_number">Phone Number <span id="phone-required" class="required">*</span></label>
                        <input type="tel" id="phone_number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" placeholder="Enter your phone number">
                        <div class="info-text" id="phone-info">We need either email or phone to send you updates regarding your complaint</div>
                        @error('phone_number')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 4. Date of Complaint -->
                <div class="form-group">
                    <label for="date_of_experience">Date of Complaint - When did this occur? <span class="required">*</span></label>
                    <input type="date" id="date_of_experience" name="date_of_experience"
                        class="form-control @error('date_of_experience') is-invalid @enderror"
                        value="{{ old('date_of_experience') }}" required>
                    @error('date_of_experience')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 5. Who is your complaint about? -->
                <div class="form-group">
                    <label for="complaint_about">Who is your complaint about? A specific person, event, or situation? <span class="required">*</span></label>
                    <textarea id="complaint_about" name="complaint_about" class="form-control @error('complaint_about') is-invalid @enderror" required placeholder="Describe who or what your complaint is about">{{ old('complaint_about') }}</textarea>
                    @error('complaint_about')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 6. Complainee Details -->
                <div class="form-group">
                    <label for="complainee_name">If you have their details, please provide their full name</label>
                    <input type="text" id="complainee_name" name="complainee_name" class="form-control @error('complainee_name') is-invalid @enderror" value="{{ old('complainee_name') }}" placeholder="Full name (if known)">
                    @error('complainee_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="complainee_email">Their email address (if you have it)</label>
                    <input type="email" id="complainee_email" name="complainee_email" class="form-control @error('complainee_email') is-invalid @enderror" value="{{ old('complainee_email') }}" placeholder="Email address (if known)">
                    @error('complainee_email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="complainee_address">Their address (if you have it)</label>
                    <textarea id="complainee_address" name="complainee_address" class="form-control @error('complainee_address') is-invalid @enderror" placeholder="Address (if known)">{{ old('complainee_address') }}</textarea>
                    @error('complainee_address')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 7. Location -->
                <div class="form-group">
                    <label for="location">Where did it take place? <span class="required">*</span></label>
                    @if(isset($location))
                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ $location->name }}" readonly>
                        <input type="hidden" name="location_id" value="{{ $location->id }}">
                    @else
                    <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" required placeholder="Enter the location where the issue occurred">
                    @error('location')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    @endif
                </div>

                <!-- 8. Issue Type -->
                <div class="form-group">
                    <label for="issue_type">Issue Type <span class="required">*</span></label>
                    <select id="issue_type" name="issue_type" class="form-control @error('issue_type') is-invalid @enderror" required>
                        <option value="">Select Issue Type</option>
                        <option value="service" {{ old('issue_type') == 'service' ? 'selected' : '' }}>Service Related</option>
                        <option value="product" {{ old('issue_type') == 'product' ? 'selected' : '' }}>Product Related</option>
                        <option value="staff" {{ old('issue_type') == 'staff' ? 'selected' : '' }}>Staff Related</option>
                        <option value="facility" {{ old('issue_type') == 'facility' ? 'selected' : '' }}>Facility Related</option>
                        <option value="other" {{ old('issue_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('issue_type')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 9. Description -->
                <div class="form-group">
                    <label for="description">Describe the issue in details. What happened? Where did it take place? Who was involved? Be as detailed as possible. <span class="required">*</span></label>
                    <textarea id="description" name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        required placeholder="Please provide detailed information about what happened, when it occurred, who was involved, and any other relevant details">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 10. Witnesses -->
                <div class="form-group">
                    <label for="witnesses">Were there witnesses?</label>
                    <textarea id="witnesses" name="witnesses" class="form-control @error('witnesses') is-invalid @enderror" placeholder="Please list any witnesses or indicate 'None' if there were no witnesses">{{ old('witnesses') }}</textarea>
                    @error('witnesses')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 11. Support Evidence Type -->
                <div class="form-group">
                    <label>Do you have support evidence? <span class="required">*</span></label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="evidence_photo" name="evidence_type" value="photo_screenshot" {{ old('evidence_type') == 'photo_screenshot' ? 'checked' : '' }} required>
                            <label for="evidence_photo">Photo/Screenshot</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="evidence_video" name="evidence_type" value="videos" {{ old('evidence_type') == 'videos' ? 'checked' : '' }} required>
                            <label for="evidence_video">Videos</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="evidence_messages" name="evidence_type" value="messages_emails" {{ old('evidence_type') == 'messages_emails' ? 'checked' : '' }} required>
                            <label for="evidence_messages">Messages/Emails</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="evidence_documents" name="evidence_type" value="other_documents" {{ old('evidence_type') == 'other_documents' ? 'checked' : '' }} required>
                            <label for="evidence_documents">Other Documents</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="evidence_none" name="evidence_type" value="no_evidence" {{ old('evidence_type') == 'no_evidence' ? 'checked' : '' }} required>
                            <label for="evidence_none">No Support Evidence</label>
                        </div>
                    </div>
                    @error('evidence_type')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 12. Evidence Description (conditional) -->
                <div class="form-group" id="evidence-description-group" style="display: none;">
                    <label for="evidence_description">If you have evidence, describe it here</label>
                    <textarea id="evidence_description" name="evidence_description" class="form-control @error('evidence_description') is-invalid @enderror" placeholder="Describe your evidence in detail">{{ old('evidence_description') }}</textarea>
                    @error('evidence_description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 14. Upload Evidence Here -->
                <div class="form-group">
                    <label for="additional_attachments" class="form-label fw-medium">Please Upload Evidence Here (Allow multiple files)</label>
                    <label for="additional_attachments_input" class="file-label @error('additional_attachments.*') is-invalid @enderror">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span id="additional-files-name">Choose Files</span>
                    </label>
                    <input type="file" id="additional_attachments_input" name="additional_attachments[]" class="file-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mov,.avi,.wmv" multiple>
                    <div id="additional-selected-files" class="selected-files"></div>
                    @error('additional_attachments.*')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="submit-button">Submit Complaint</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <!-- Success Modal -->
    <div id="successModal" class="modal show">
        <div class="modal-content">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h2>Complaint Submitted</h2>
            <p>{{ session('success') }}</p>
            <div class="complaint-id">Case Number: <span id="complaintId">{{ session('case_number') }}</span></div>
            <a href="{{ route('public.complaints.create') }}" class="submit-another">Submit Again</a>
        </div>
    </div>
    @endif

    <script>
        $(document).ready(function () {
            // Handle anonymous checkbox
            $('#is_anonymous').change(function() {
                const isAnonymous = $(this).is(':checked');
                const nameGroup = $('#name-group');
                const nameInput = $('#name');
                const emailGroup = $('#email-group');
                const phoneGroup = $('#phone-group');
                const contactMethodGroup = $('#contact-method-group');
                const emailInput = $('#email');
                const phoneInput = $('#phone_number');
                const contactMethodSelect = $('#contact_method');

                if (isAnonymous) {
                    // Hide name field for anonymous
                    nameGroup.hide();
                    nameInput.removeAttr('required').val('');

                    // Show contact method selector
                    contactMethodGroup.show();
                    contactMethodSelect.attr('required', true);

                    // Initially hide both email and phone groups
                    emailGroup.hide();
                    phoneGroup.hide();
                    emailInput.removeAttr('required');
                    phoneInput.removeAttr('required');

                    // Reset contact method if previously selected
                    if (!contactMethodSelect.val()) {
                        contactMethodSelect.trigger('change');
                    }
                } else {
                    // Show name field for non-anonymous
                    nameGroup.show();
                    nameInput.attr('required', true);

                    // Hide contact method selector
                    contactMethodGroup.hide();
                    contactMethodSelect.removeAttr('required').val('');

                    // Show both email and phone groups
                    emailGroup.show();
                    phoneGroup.show();
                    emailInput.attr('required', true);
                    phoneInput.removeAttr('required');

                    // Reset labels and info text for non-anonymous
                    $('#email-required').html('<span class="required">*</span>');
                    $('#phone-required').text('');
                    $('#email-info').text('We would not store your personal details. We use it only for sending updates');
                    $('#phone-info').text('Optional contact information');
                }
            });

            // Handle contact method selection for anonymous users
            $('#contact_method').change(function() {
                const isAnonymous = $('#is_anonymous').is(':checked');
                if (!isAnonymous) return;

                const contactMethod = $(this).val();
                const emailGroup = $('#email-group');
                const phoneGroup = $('#phone-group');
                const emailInput = $('#email');
                const phoneInput = $('#phone_number');

                // Hide both groups initially
                emailGroup.hide();
                phoneGroup.hide();
                emailInput.removeAttr('required').val('');
                phoneInput.removeAttr('required').val('');

                if (contactMethod === 'email') {
                    emailGroup.show();
                    emailInput.attr('required', true);
                    $('#email-required').html('<span class="required">*</span>');
                    $('#email-info').text('We would not store your personal details. We use it only for sending updates');
                } else if (contactMethod === 'phone') {
                    phoneGroup.show();
                    phoneInput.attr('required', true);
                    $('#phone-required').html('<span class="required">*</span>');
                    $('#phone-info').text('We would not store your personal details. We use it only for sending updates');
                }
            });

            // Handle evidence type selection
            $('input[name="evidence_type"]').change(function() {
                const evidenceType = $(this).val();
                const descriptionGroup = $('#evidence-description-group');

                if (evidenceType === 'no_evidence') {
                    descriptionGroup.hide();
                    $('#evidence_description').removeAttr('required');
                } else {
                    descriptionGroup.show();
                    $('#evidence_description').attr('required', true);
                }
            });

            // Initial state check
            $('#is_anonymous').trigger('change');
            $('input[name="evidence_type"]:checked').trigger('change');

            // Validate anonymous form
            $('#complaintForm').submit(function(e) {
                const isAnonymous = $('#is_anonymous').is(':checked');
                if (isAnonymous) {
                    const contactMethod = $('#contact_method').val();
                    
                    if (!contactMethod) {
                        e.preventDefault();
                        alert('Please select how you would like to receive updates.');
                        return false;
                    }

                    if (contactMethod === 'email') {
                        const email = $('#email').val().trim();
                        if (!email) {
                            e.preventDefault();
                            alert('Please provide your email address.');
                            return false;
                        }
                    } else if (contactMethod === 'phone') {
                        const phone = $('#phone_number').val().trim();
                        if (!phone) {
                            e.preventDefault();
                            alert('Please provide your phone number.');
                            return false;
                        }
                    }
                }
            });
        });

        // File input handling for additional files (main upload)
        const additionalFileInput = document.getElementById('additional_attachments_input');
        const additionalFilesName = document.getElementById('additional-files-name');
        const additionalSelectedFiles = document.getElementById('additional-selected-files');
        let additionalCurrentFiles = new DataTransfer();


        // Additional file input handling
        additionalFileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    // Check file size (50MB limit)
                    if (file.size > 50 * 1024 * 1024) {
                        alert(`File ${file.name} is larger than 50MB`);
                        return;
                    }

                    // Check if file already exists
                    const existingFile = Array.from(additionalCurrentFiles.files).find(f =>
                        f.name === file.name && f.size === file.size
                    );

                    if (!existingFile) {
                        additionalCurrentFiles.items.add(file);
                    }
                });

                updateAdditionalFilePreview();
            }
        });

        function updateAdditionalFilePreview() {
            additionalSelectedFiles.innerHTML = '';
            const files = additionalCurrentFiles.files;

            if (files.length > 0) {
                additionalFilesName.textContent = `${files.length} file(s) selected`;
                Array.from(files).forEach((file, index) => {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'selected-file';

                    const isImage = file.type.startsWith('image/');
                    const isVideo = file.type.startsWith('video/');
                    const fileSize = (file.size / (1024 * 1024)).toFixed(2); // Convert to MB

                    let preview = '';
                    if (isImage) {
                        preview = `
                            <div class="file-preview">
                                <img src="${URL.createObjectURL(file)}" alt="Preview">
                            </div>
                        `;
                    } else if (isVideo) {
                        preview = `
                            <div class="file-preview video">
                                <i class="bi bi-play-circle-fill"></i>
                            </div>
                        `;
                    }

                    fileDiv.innerHTML = `
                        ${preview}
                        <div class="file-info">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${fileSize} MB</div>
                        </div>
                        <span class="remove-file" data-index="${index}">×</span>
                    `;
                    additionalSelectedFiles.appendChild(fileDiv);

                    // Add click handler for remove button
                    const removeBtn = fileDiv.querySelector('.remove-file');
                    removeBtn.addEventListener('click', function() {
                        const newFiles = new DataTransfer();
                        Array.from(additionalCurrentFiles.files)
                            .filter((_, i) => i !== index)
                            .forEach(f => newFiles.items.add(f));

                        additionalCurrentFiles = newFiles;
                        additionalFileInput.files = additionalCurrentFiles.files;
                        updateAdditionalFilePreview();
                    });
                });
            } else {
                additionalFilesName.textContent = 'Choose Files';
            }

            // Update the file input with current files
            additionalFileInput.files = additionalCurrentFiles.files;
        }

        // Clear validation errors on input
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            });
        });
    </script>
</body>
</html>