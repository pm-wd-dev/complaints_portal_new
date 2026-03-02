<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details - {{ $complaint->case_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('respondent.dashboard') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" style="height: 40px;" class="me-2">
                GoBEST™ Listens
            </a>
            
            <div class="navbar-nav ms-auto">
                <a href="{{ route('respondent.dashboard') }}" class="nav-link">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">Complaint Details</h1>
                        <p class="text-muted mb-0">Case Number: <strong>{{ $complaint->case_number }}</strong></p>
                    </div>
                    <div>
                        @if($complaint->stage)
                            <span class="badge fs-6 text-dark" style="background-color: {{ $complaint->stage->color }}">
                                {{ $complaint->stage->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Complaint Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-file-text text-primary me-2"></i>Complaint Information
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Case Number</label>
                                <div class="fw-bold">{{ $complaint->case_number }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Complaint Type</label>
                                <div>{{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Location</label>
                                <div>{{ $complaint->location }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Submitted</label>
                                <div>{{ $complaint->created_at->format('M j, Y g:i A') }}</div>
                            </div>
                            @if($complaint->complaint_about)
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Complaint About</label>
                                    <div>{{ $complaint->complaint_about }}</div>
                                </div>
                            @endif
                            @if($complaint->date_of_experience)
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Date of Experience</label>
                                    <div>{{ \Carbon\Carbon::parse($complaint->date_of_experience)->format('M j, Y') }}</div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label class="form-label text-muted small">Description</label>
                            <div class="bg-light p-3 rounded">
                                {{ $complaint->description }}
                            </div>
                        </div>

                        @if($complaint->complainee_name || $complaint->complainee_email)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Complainee Information</label>
                                <div class="bg-light p-3 rounded">
                                    @if($complaint->complainee_name)
                                        <div><strong>Name:</strong> {{ $complaint->complainee_name }}</div>
                                    @endif
                                    @if($complaint->complainee_email)
                                        <div><strong>Email:</strong> {{ $complaint->complainee_email }}</div>
                                    @endif
                                    @if($complaint->complainee_address)
                                        <div><strong>Address:</strong> {{ $complaint->complainee_address }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($complaint->witnesses)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Witnesses</label>
                                <div class="bg-light p-3 rounded">
                                    {{ $complaint->witnesses }}
                                </div>
                            </div>
                        @endif

                        @if($complaint->evidence_description)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Evidence Description</label>
                                <div class="bg-light p-3 rounded">
                                    <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $complaint->evidence_type)) }}<br>
                                    <strong>Description:</strong> {{ $complaint->evidence_description }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Complainant Attachments -->
                @php
                    $complainantAttachments = $complaint->attachments->whereNull('respondent_response_id');
                @endphp
                @if($complainantAttachments->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-paperclip text-primary me-2"></i>Complainant Attachments
                            </h5>
                            <div class="row">
                                @foreach($complainantAttachments as $attachment)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            @php
                                                $extension = strtolower($attachment->file_type);
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp
                                            
                                            @if($isImage)
                                                <img src="{{ asset($attachment->file_path) }}" 
                                                     alt="Attachment" 
                                                     class="img-fluid rounded mb-2"
                                                     style="max-height: 200px; object-fit: cover;">
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-file-earmark fs-1 text-muted"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ strtoupper($extension) }} File</small>
                                                <a href="{{ asset($attachment->file_path) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download me-1"></i>View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Previous Responses -->
                @php
                    $respondent = $complaint->respondents->where('user_id', session('respondent_user_id'))->first();
                    $responses = $respondent ? $respondent->responses : collect();
                @endphp

                @if($responses->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-chat-dots text-primary me-2"></i>Your Previous Responses
                            </h5>
                            
                            @foreach($responses as $response)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">Response #{{ $loop->iteration }}</h6>
                                        <small class="text-muted">{{ $response->created_at->format('M j, Y g:i A') }}</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        {{ $response->response }}
                                    </div>

                                    @if($response->attachments && $response->attachments->count() > 0)
                                        <div class="border-top pt-2">
                                            <small class="text-muted d-block mb-2">Attachments:</small>
                                            @foreach($response->attachments as $attachment)
                                                <a href="{{ asset($attachment->file_path) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-secondary me-2 mb-1">
                                                    <i class="bi bi-file-earmark me-1"></i>
                                                    {{ strtoupper($attachment->file_type) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Admin Replies Section -->
                @php
                    $currentUserId = session('respondent_user_id');
                    // Filter replies: only show general replies (no recipient) or replies specifically to this respondent
                    $respondentReplies = $complaint->replies->filter(function($reply) use ($currentUserId) {
                        return $reply->recipient_id === null || $reply->recipient_id == $currentUserId;
                    });
                @endphp
                @if($respondentReplies && $respondentReplies->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-chat-square-text text-primary me-2"></i>Admin Replies ({{ $respondentReplies->count() }})
                            </h5>

                            @foreach($respondentReplies->sortByDesc('created_at') as $reply)
                                <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0 text-primary">{{ $reply->user->name ?? 'Admin' }}</h6>
                                            @if($reply->recipient_id)
                                                <span class="badge bg-info" style="font-size: 10px;">
                                                    Reply to {{ $reply->recipient->name ?? 'Recipient' }}
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $reply->created_at->format('M j, Y g:i A') }}</small>
                                    </div>

                                    <div class="mt-2" style="white-space: pre-wrap;">
                                        {{ $reply->message }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Google Form Style Response Section -->
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-reply text-primary me-2"></i>Cast Complaint - Respondent Form
                        </h5>
                        <p class="small text-muted mb-4">
                            This form is used to document complaints, hear all sides of the story, and track investigations and resolutions. Please provide as much detail as possible.
                        </p>

                        @php
                            $existingResponse = \App\Models\RespondentResponseDetail::where('complaint_id', $complaint->id)
                                ->where('user_id', session('respondent_user_id'))
                                ->first();
                        @endphp

                        @if($existingResponse)
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Response Submitted</strong><br>
                                <small>{{ $existingResponse->submitted_at->format('M j, Y g:i A') }}</small>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="#existing-response" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-2"></i>View Your Response
                                </a>
                                <a href="{{ route('respondent.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('respondent.complaint.respond', $complaint) }}" enctype="multipart/form-data" id="respondentForm">
                                @csrf
                                
                                <!-- Required Fields -->
                                <div class="mb-3">
                                    <label for="respondent_email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('respondent_email') is-invalid @enderror" 
                                           id="respondent_email" 
                                           name="respondent_email" 
                                           value="{{ old('respondent_email', session()->has('respondent_user_id') ? \App\Models\User::find(session('respondent_user_id'))->email : '') }}"
                                           required>
                                    @error('respondent_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="venue_legal_name" class="form-label">
                                        Legal Name of the Venue <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('venue_legal_name') is-invalid @enderror" 
                                           id="venue_legal_name" 
                                           name="venue_legal_name" 
                                           value="{{ old('venue_legal_name') }}"
                                           required>
                                    @error('venue_legal_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="venue_city_state" class="form-label">
                                        City and State of the Venue <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('venue_city_state') is-invalid @enderror" 
                                           id="venue_city_state" 
                                           name="venue_city_state" 
                                           value="{{ old('venue_city_state') }}"
                                           placeholder="e.g., Las Vegas, Nevada"
                                           required>
                                    @error('venue_city_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="respondent_name" class="form-label">
                                        Your Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('respondent_name') is-invalid @enderror" 
                                           id="respondent_name" 
                                           name="respondent_name" 
                                           value="{{ old('respondent_name', session()->has('respondent_user_id') ? \App\Models\User::find(session('respondent_user_id'))->name : '') }}"
                                           required>
                                    @error('respondent_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="complaint_date" class="form-label">
                                        Date of Complaint <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('complaint_date') is-invalid @enderror" 
                                           id="complaint_date" 
                                           name="complaint_date" 
                                           value="{{ old('complaint_date', $complaint->created_at->format('Y-m-d')) }}"
                                           max="{{ now()->format('Y-m-d') }}"
                                           required>
                                    @error('complaint_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="respondent_side_story" class="form-label">
                                        Describe your side of the story <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('respondent_side_story') is-invalid @enderror" 
                                              id="respondent_side_story" 
                                              name="respondent_side_story" 
                                              rows="4" 
                                              placeholder="Please provide your perspective on the events that occurred..."
                                              required>{{ old('respondent_side_story') }}</textarea>
                                    @error('respondent_side_story')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="issue_detail_description" class="form-label">
                                        Describe the issue in detail <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('issue_detail_description') is-invalid @enderror" 
                                              id="issue_detail_description" 
                                              name="issue_detail_description" 
                                              rows="4" 
                                              placeholder="Please provide detailed information about the specific issue..."
                                              required>{{ old('issue_detail_description') }}</textarea>
                                    @error('issue_detail_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="witnesses_information" class="form-label">
                                        Witnesses information <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('witnesses_information') is-invalid @enderror" 
                                              id="witnesses_information" 
                                              name="witnesses_information" 
                                              rows="3" 
                                              placeholder="Please list any witnesses and their contact information, or write 'None' if there are no witnesses..."
                                              required>{{ old('witnesses_information') }}</textarea>
                                    @error('witnesses_information')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Do you have any supporting evidence? <span class="text-danger">*</span>
                                    </label>
                                    <div class="border rounded p-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supporting_evidence_type" 
                                                   id="evidence_photos" value="photos" {{ old('supporting_evidence_type') == 'photos' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="evidence_photos">
                                                📸 Photos/Screenshots
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supporting_evidence_type" 
                                                   id="evidence_videos" value="videos" {{ old('supporting_evidence_type') == 'videos' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="evidence_videos">
                                                🎥 Videos
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supporting_evidence_type" 
                                                   id="evidence_messages" value="messages" {{ old('supporting_evidence_type') == 'messages' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="evidence_messages">
                                                📧 Messages/Emails
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supporting_evidence_type" 
                                                   id="evidence_documents" value="documents" {{ old('supporting_evidence_type') == 'documents' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="evidence_documents">
                                                📝 Other Documents
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supporting_evidence_type" 
                                                   id="evidence_none" value="none" {{ old('supporting_evidence_type') == 'none' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="evidence_none">
                                                ❌ No supporting evidence
                                            </label>
                                        </div>
                                    </div>
                                    @error('supporting_evidence_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Upload Section -->
                                <div class="mb-3" id="file-upload-section" style="display: none;">
                                    <label for="attachments" class="form-label">
                                        Supporting Evidence Files <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('attachments.*') is-invalid @enderror" 
                                           id="attachments" 
                                           name="attachments[]" 
                                           multiple
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp4,.mov,.avi,.wmv">
                                    @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Maximum 10 files, 1 GB per file. Supported: PDF, DOC, Images, Videos
                                    </div>
                                    <!-- File preview area -->
                                    <div id="file-preview" class="mt-2" style="display: none;">
                                        <div class="border rounded p-2 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Selected files:</small>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="clear-files">
                                                    <i class="bi bi-trash"></i> Clear
                                                </button>
                                            </div>
                                            <div id="file-list"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3" id="evidence-description-section" style="display: none;">
                                    <label for="evidence_description" class="form-label">
                                        Describe your supporting evidence
                                    </label>
                                    <textarea class="form-control @error('evidence_description') is-invalid @enderror" 
                                              id="evidence_description" 
                                              name="evidence_description" 
                                              rows="3" 
                                              placeholder="Please describe what the evidence shows and how it relates to your response...">{{ old('evidence_description') }}</textarea>
                                    @error('evidence_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg" onclick="console.log('Submit button clicked')">
                                        <i class="bi bi-send me-2"></i>Submit Response
                                    </button>
                                    <a href="{{ route('respondent.dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                                    </a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Complainant Information -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-person text-primary me-2"></i>Complainant Information
                        </h6>
                        
                        <div class="small">
                            @if($complaint->is_anonymous)
                                <div class="text-muted">
                                    <i class="bi bi-eye-slash me-1"></i>Anonymous Complaint
                                </div>
                            @else
                                <div><strong>Name:</strong> {{ $complaint->name ?: 'Not provided' }}</div>
                            @endif
                            
                            @if($complaint->email)
                                <div><strong>Email:</strong> {{ $complaint->email }}</div>
                            @endif
                            @if($complaint->phone_number)
                                <div><strong>Phone:</strong> {{ $complaint->phone_number }}</div>
                            @endif
                            <div><strong>Submitted As:</strong> {{ ucfirst(str_replace('_', ' ', $complaint->submitted_as)) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Status Information -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle text-primary me-2"></i>Status Information
                        </h6>
                        
                        <div class="small">
                            <div class="mb-2">
                                <strong>Current Status:</strong>
                                @php
                                    $statusColor = match($complaint->status) {
                                        'submitted' => 'bg-primary',
                                        'under_review' => 'bg-info',
                                        'escalated' => 'bg-warning text-dark',
                                        'resolved' => 'bg-success',
                                        'closed' => 'bg-secondary',
                                        default => 'bg-primary'
                                    };
                                @endphp
                                <span class="badge {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                </span>
                            </div>
                            
                            @if($complaint->stage)
                                <div class="mb-2">
                                    <strong>Current Stage:</strong>
                                    <span class="badge text-dark" style="background-color: {{ $complaint->stage->color }}">
                                        {{ $complaint->stage->name }}
                                    </span>
                                </div>
                            @endif
                            
                            <div>
                                <strong>Last Updated:</strong> {{ $complaint->updated_at->format('M j, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Existing Response -->
        @if($existingResponse)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm" id="existing-response">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle me-2"></i>Your Submitted Response
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Response Details</h6>
                                <dl class="row">
                                    <dt class="col-sm-5">Respondent Name:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->respondent_name }}</dd>
                                    
                                    <dt class="col-sm-5">Email:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->respondent_email }}</dd>
                                    
                                    <dt class="col-sm-5">Venue:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->venue_legal_name }}</dd>
                                    
                                    <dt class="col-sm-5">Location:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->venue_city_state }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Submission Info</h6>
                                <dl class="row">
                                    <dt class="col-sm-5">Case Number:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->case_number }}</dd>
                                    
                                    <dt class="col-sm-5">Complaint Date:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->complaint_date->format('M j, Y') }}</dd>
                                    
                                    <dt class="col-sm-5">Submitted:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->submitted_at->format('M j, Y g:i A') }}</dd>
                                    
                                    <dt class="col-sm-5">Evidence Type:</dt>
                                    <dd class="col-sm-7">{{ $existingResponse->supporting_evidence_type_label }}</dd>
                                </dl>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-4">
                            <h6 class="text-primary">Your Side of the Story</h6>
                            <div class="bg-light p-3 rounded">{{ $existingResponse->respondent_side_story }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-primary">Detailed Issue Description</h6>
                            <div class="bg-light p-3 rounded">{{ $existingResponse->issue_detail_description }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-primary">Witnesses Information</h6>
                            <div class="bg-light p-3 rounded">{{ $existingResponse->witnesses_information }}</div>
                        </div>
                        
                        @if($existingResponse->evidence_description)
                        <div class="mb-4">
                            <h6 class="text-primary">Evidence Description</h6>
                            <div class="bg-light p-3 rounded">{{ $existingResponse->evidence_description }}</div>
                        </div>
                        @endif
                        
                        @if($existingResponse->attachments->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-primary">Supporting Evidence Files</h6>
                            <div class="row">
                                @foreach($existingResponse->attachments as $attachment)
                                    <div class="col-md-4 mb-3">
                                        @php
                                            $extension = strtolower($attachment->file_type);
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp
                                        
                                        @if($isImage)
                                            <div class="card">
                                                <img src="{{ asset($attachment->file_path) }}" 
                                                     class="card-img-top" 
                                                     style="height: 150px; object-fit: cover; cursor: pointer;"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#responseImageModal{{ $attachment->id }}">
                                                <div class="card-body p-2">
                                                    <small class="text-muted">{{ strtoupper($extension) }} File</small>
                                                </div>
                                            </div>
                                            
                                            <!-- Image Modal -->
                                            <div class="modal fade" id="responseImageModal{{ $attachment->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-body text-center p-0">
                                                            <img src="{{ asset($attachment->file_path) }}" 
                                                                 class="img-fluid" alt="Evidence">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ asset($attachment->file_path) }}" 
                                               class="btn btn-outline-primary w-100" 
                                               target="_blank">
                                                <i class="bi bi-file-earmark me-2"></i>
                                                {{ strtoupper($extension) }} File
                                                <br><small class="text-muted">View/Download</small>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('respondent.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <span class="badge bg-success fs-6 py-2">
                                <i class="bi bi-check-circle me-1"></i>Response Successfully Submitted
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add form submission debugging
            const form = document.getElementById('respondentForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submission started');
                    
                    // Check if all required fields are filled
                    const requiredFields = form.querySelectorAll('[required]');
                    let missingFields = [];
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            missingFields.push(field.name || field.id);
                        }
                    });
                    
                    if (missingFields.length > 0) {
                        console.log('Missing required fields:', missingFields);
                        alert('Please fill in all required fields: ' + missingFields.join(', '));
                        e.preventDefault();
                        return false;
                    }
                    
                    // Check supporting evidence type selection
                    const evidenceType = document.querySelector('input[name="supporting_evidence_type"]:checked');
                    if (!evidenceType) {
                        console.log('No evidence type selected');
                        alert('Please select a supporting evidence type');
                        e.preventDefault();
                        return false;
                    }
                    
                    console.log('Form validation passed, submitting...');
                });
            }
            // Handle evidence type selection
            const evidenceRadios = document.querySelectorAll('input[name="supporting_evidence_type"]');
            const fileUploadSection = document.getElementById('file-upload-section');
            const evidenceDescriptionSection = document.getElementById('evidence-description-section');
            const evidenceDescription = document.getElementById('evidence_description');
            const attachmentsInput = document.getElementById('attachments');
            const filePreview = document.getElementById('file-preview');
            const fileList = document.getElementById('file-list');
            const clearFilesBtn = document.getElementById('clear-files');
            
            function toggleEvidenceSections() {
                const selectedValue = document.querySelector('input[name="supporting_evidence_type"]:checked')?.value;
                
                if (selectedValue && selectedValue !== 'none') {
                    fileUploadSection.style.display = 'block';
                    evidenceDescriptionSection.style.display = 'block';
                    evidenceDescription.setAttribute('required', 'required');
                } else {
                    fileUploadSection.style.display = 'none';
                    evidenceDescriptionSection.style.display = 'none';
                    evidenceDescription.removeAttribute('required');
                    if (attachmentsInput) {
                        attachmentsInput.value = '';
                    }
                    evidenceDescription.value = '';
                    filePreview.style.display = 'none';
                    fileList.innerHTML = '';
                }
            }
            
            // File upload preview functionality
            if (attachmentsInput) {
                attachmentsInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    
                    if (files.length > 0) {
                        filePreview.style.display = 'block';
                        fileList.innerHTML = '';
                        
                        files.forEach((file, index) => {
                            const fileItem = document.createElement('div');
                            fileItem.className = 'bg-white rounded p-2 mb-1 border';
                            
                            const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                            const fileIcon = getFileIcon(file.type);
                            
                            fileItem.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="${fileIcon} me-2 text-primary"></i>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.85rem;">${file.name}</div>
                                            <small class="text-muted">${fileSize} MB</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="${index}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            `;
                            
                            fileList.appendChild(fileItem);
                        });
                        
                        // Add event listeners for individual file removal
                        document.querySelectorAll('.remove-file').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const index = parseInt(this.dataset.index);
                                removeFile(index);
                            });
                        });
                    } else {
                        filePreview.style.display = 'none';
                    }
                });
                
                // Clear all files
                clearFilesBtn.addEventListener('click', function() {
                    attachmentsInput.value = '';
                    filePreview.style.display = 'none';
                    fileList.innerHTML = '';
                });
            }
            
            function getFileIcon(fileType) {
                if (fileType.startsWith('image/')) return 'bi bi-file-earmark-image';
                if (fileType.startsWith('video/')) return 'bi bi-file-earmark-play';
                if (fileType.includes('pdf')) return 'bi bi-file-earmark-pdf';
                if (fileType.includes('doc') || fileType.includes('docx')) return 'bi bi-file-earmark-word';
                return 'bi bi-file-earmark';
            }
            
            function removeFile(indexToRemove) {
                const dt = new DataTransfer();
                const files = Array.from(attachmentsInput.files);
                
                files.forEach((file, index) => {
                    if (index !== indexToRemove) {
                        dt.items.add(file);
                    }
                });
                
                attachmentsInput.files = dt.files;
                attachmentsInput.dispatchEvent(new Event('change'));
            }
            
            // Add event listeners to radio buttons
            evidenceRadios.forEach(radio => {
                radio.addEventListener('change', toggleEvidenceSections);
            });
            
            // Initialize on page load
            toggleEvidenceSections();
        });
    </script>
    
    <style>
        .navbar-brand img {
            filter: brightness(0) invert(1);
        }
        
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-1px);
        }
        
        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 1020;
        }
        
        @media (max-width: 991px) {
            .sticky-top {
                position: relative;
                top: 0;
            }
        }
    </style>
</body>
</html>