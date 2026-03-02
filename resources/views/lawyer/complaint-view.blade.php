<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Case Review - {{ $complaint->case_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('lawyer.dashboard') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" style="height: 40px;" class="me-2">
                ⚖️ GoBEST™ Legal Review
            </a>
            
            <div class="navbar-nav ms-auto">
                <a href="{{ route('lawyer.dashboard') }}" class="nav-link">
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
                        <h1 class="h3 mb-1">⚖️ Legal Case Review</h1>
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Column - Case Information -->
            <div class="col-lg-8">
                <!-- Case Overview Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0">
                            <i class="bi bi-file-text me-2 text-primary"></i>
                            Case Overview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Case Type:</strong> {{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}</p>
                                <p><strong>Location:</strong> {{ $complaint->location }}</p>
                                <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y g:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Current Status:</strong> 
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</span>
                                </p>
                                <p><strong>Priority:</strong> 
                                    <span class="badge bg-warning text-dark">Legal Review Required</span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <strong>Case Description:</strong>
                            <div class="bg-light p-3 rounded mt-2 border-start border-4 border-primary">
                                {{ $complaint->description }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complainant Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2 text-primary"></i>
                            Complainant Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $complaint->name ?: 'Anonymous' }}</p>
                                @if($complaint->email)
                                    <p><strong>Email:</strong> {{ $complaint->email }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($complaint->phone_number)
                                    <p><strong>Phone:</strong> {{ $complaint->phone_number }}</p>
                                @endif
                                <p><strong>Submission Date:</strong> {{ $complaint->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evidence Files -->
                @if($complaint->attachments && $complaint->attachments->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0">
                                <i class="bi bi-paperclip me-2 text-success"></i>
                                Evidence Files ({{ $complaint->attachments->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($complaint->attachments as $attachment)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            <i class="bi bi-file-earmark text-primary me-3 fs-4"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ basename($attachment->file_path) }}</h6>
                                                <small class="text-muted">{{ $attachment->description ?: 'Evidence file' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Respondent Information -->
                @if($complaint->respondents && $complaint->respondents->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0">
                                <i class="bi bi-people me-2 text-info"></i>
                                Assigned Respondents ({{ $complaint->respondents->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($complaint->respondents as $respondent)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-info">
                                            <div class="card-body">
                                                <h6 class="card-title text-info mb-2">
                                                    <i class="bi bi-person-circle me-1"></i>
                                                    {{ $respondent->user->name }}
                                                </h6>
                                                <p class="mb-1"><strong>Email:</strong> {{ $respondent->user->email }}</p>
                                                @if($respondent->user->phone_number)
                                                    <p class="mb-1"><strong>Phone:</strong> {{ $respondent->user->phone_number }}</p>
                                                @endif
                                                <p class="mb-1"><strong>Assigned:</strong> {{ $respondent->created_at->format('M j, Y g:i A') }}</p>
                                                @if($respondent->responded_at)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Responded on {{ $respondent->responded_at->format('M j, Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-clock me-1"></i>Response Pending
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Respondent Detailed Responses -->
                @if($respondentResponses && $respondentResponses->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0">
                                <i class="bi bi-chat-left-text me-2 text-primary"></i>
                                Respondent Responses ({{ $respondentResponses->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($respondentResponses as $response)
                                <div class="border rounded p-4 mb-4 bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1 text-primary">
                                                <i class="bi bi-person-badge me-1"></i>
                                                {{ $response->respondent_name }}
                                            </h6>
                                            <small class="text-muted">
                                                Responded on {{ $response->submitted_at->format('F j, Y g:i A') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-primary">Response #{{ $loop->iteration }}</span>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Email:</strong> {{ $response->respondent_email }}</p>
                                            <p><strong>Venue:</strong> {{ $response->venue_legal_name }}</p>
                                            <p><strong>Location:</strong> {{ $response->venue_city_state }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Complaint Date:</strong> {{ $response->complaint_date->format('M j, Y') }}</p>
                                            <p><strong>Evidence Type:</strong> {{ ucfirst(str_replace('_', ' ', $response->supporting_evidence_type)) }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="text-primary">Respondent's Side of Story:</strong>
                                        <div class="bg-white p-3 rounded mt-2 border-start border-4 border-primary">
                                            {{ $response->respondent_side_story }}
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="text-primary">Issue Details:</strong>
                                        <div class="bg-white p-3 rounded mt-2 border-start border-4 border-info">
                                            {{ $response->issue_detail_description }}
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="text-primary">Witnesses Information:</strong>
                                        <div class="bg-white p-3 rounded mt-2 border-start border-4 border-secondary">
                                            {{ $response->witnesses_information }}
                                        </div>
                                    </div>

                                    @if($response->evidence_description)
                                        <div class="mb-3">
                                            <strong class="text-primary">Evidence Description:</strong>
                                            <div class="bg-white p-3 rounded mt-2 border-start border-4 border-success">
                                                {{ $response->evidence_description }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($response->attachments && $response->attachments->count() > 0)
                                        <div class="mt-3">
                                            <strong class="text-primary">Response Attachments ({{ $response->attachments->count() }}):</strong>
                                            <div class="row mt-2">
                                                @foreach($response->attachments as $attachment)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="d-flex align-items-center p-2 bg-white rounded border">
                                                            <i class="bi bi-file-earmark-text text-success me-2"></i>
                                                            <div class="flex-grow-1">
                                                                <small class="d-block">{{ basename($attachment->file_path) }}</small>
                                                                <small class="text-muted">{{ $attachment->description ?: 'Respondent evidence' }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Admin Replies Section -->
                @php
                    $currentUserId = session('lawyer_user_id');
                    // Filter replies: only show general replies (no recipient) or replies specifically to this lawyer
                    $lawyerReplies = $complaint->replies->filter(function($reply) use ($currentUserId) {
                        return $reply->recipient_id === null || $reply->recipient_id == $currentUserId;
                    });
                @endphp
                @if($lawyerReplies && $lawyerReplies->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0">
                                <i class="bi bi-chat-square-text me-2 text-primary"></i>
                                Admin Replies ({{ $lawyerReplies->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($lawyerReplies->sortByDesc('created_at') as $reply)
                                <div class="border rounded p-4 mb-3" style="background-color: #f8f9fa; border-left: 4px solid #0d6efd !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1 text-primary">
                                                <i class="bi bi-person-circle me-1"></i>
                                                {{ $reply->user->name ?? 'Admin' }}
                                            </h6>
                                            @if($reply->recipient_id)
                                                <span class="badge bg-info" style="font-size: 11px;">
                                                    Reply to {{ $reply->recipient->name ?? 'Recipient' }}
                                                </span>
                                            @else
                                                <span class="badge bg-primary" style="font-size: 11px;">
                                                    General Reply
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $reply->created_at->format('M j, Y g:i A') }}
                                        </small>
                                    </div>

                                    <div class="bg-white p-3 rounded mt-2" style="white-space: pre-wrap;">
                                        {{ $reply->message }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Legal Review Form -->
                @if(!$existingResponse)
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0">
                                <i class="bi bi-scale me-2 text-primary"></i>
                                Submit Legal Review
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Legal Review Required:</strong> Please provide your professional assessment of this case.
                            </div>

                            <form method="POST" action="{{ route('lawyer.complaint.respond', $complaint) }}" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="lawyer_name" class="form-label fw-medium">Lawyer Name</label>
                                        <input type="text" class="form-control @error('lawyer_name') is-invalid @enderror" id="lawyer_name" name="lawyer_name" value="{{ old('lawyer_name') }}" required>
                                        @error('lawyer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lawyer_email" class="form-label fw-medium">Professional Email</label>
                                        <input type="email" class="form-control @error('lawyer_email') is-invalid @enderror" id="lawyer_email" name="lawyer_email" value="{{ old('lawyer_email') }}" required>
                                        @error('lawyer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="law_firm_name" class="form-label fw-medium">Law Firm/Organization</label>
                                        <input type="text" class="form-control @error('law_firm_name') is-invalid @enderror" id="law_firm_name" name="law_firm_name" value="{{ old('law_firm_name') }}" required>
                                        @error('law_firm_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="review_date" class="form-label fw-medium">Review Date</label>
                                        <input type="date" class="form-control @error('review_date') is-invalid @enderror" id="review_date" name="review_date" value="{{ old('review_date', date('Y-m-d')) }}" required>
                                        @error('review_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="lawyer_city_state" class="form-label fw-medium">City, State</label>
                                    <input type="text" class="form-control @error('lawyer_city_state') is-invalid @enderror" id="lawyer_city_state" name="lawyer_city_state" value="{{ old('lawyer_city_state') }}" required>
                                    @error('lawyer_city_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="legal_assessment" class="form-label fw-medium">Legal Assessment</label>
                                    <textarea class="form-control @error('legal_assessment') is-invalid @enderror" id="legal_assessment" name="legal_assessment" rows="4" 
                                              placeholder="Provide your professional legal assessment of this case..." required>{{ old('legal_assessment') }}</textarea>
                                    @error('legal_assessment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="legal_recommendations" class="form-label fw-medium">Legal Recommendations</label>
                                    <textarea class="form-control @error('legal_recommendations') is-invalid @enderror" id="legal_recommendations" name="legal_recommendations" rows="4" 
                                              placeholder="Provide recommendations for handling this case..." required>{{ old('legal_recommendations') }}</textarea>
                                    @error('legal_recommendations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="compliance_notes" class="form-label fw-medium">Compliance & Regulatory Notes</label>
                                    <textarea class="form-control @error('compliance_notes') is-invalid @enderror" id="compliance_notes" name="compliance_notes" rows="3" 
                                              placeholder="Note any compliance or regulatory considerations..." required>{{ old('compliance_notes') }}</textarea>
                                    @error('compliance_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="supporting_evidence_type" class="form-label fw-medium">Supporting Documentation Type</label>
                                    <select class="form-select" id="supporting_evidence_type" name="supporting_evidence_type" required>
                                        <option value="">Select documentation type...</option>
                                        <option value="legal_docs">Legal Documents</option>
                                        <option value="case_law">Case Law References</option>
                                        <option value="regulations">Regulatory Guidelines</option>
                                        <option value="correspondence">Legal Correspondence</option>
                                        <option value="none">No Supporting Documentation</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="evidence_description_section" style="display: none;">
                                    <label for="evidence_description" class="form-label fw-medium">Documentation Description</label>
                                    <textarea class="form-control @error('evidence_description') is-invalid @enderror" id="evidence_description" name="evidence_description" rows="2" 
                                              placeholder="Describe the supporting documentation...">{{ old('evidence_description') }}</textarea>
                                    @error('evidence_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4" id="attachments_section" style="display: none;">
                                    <label for="attachments" class="form-label fw-medium">Upload Documentation</label>
                                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                                    <div class="form-text">
                                        You can upload multiple files. Supported formats: PDF, DOC, DOCX, JPG, PNG (Max: 1GB per file)
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn legal-btn btn-lg">
                                        <i class="bi bi-send me-2"></i>Submit Legal Review
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Existing Review Display -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                Legal Review Completed
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Submitted:</strong> {{ $existingResponse->submitted_at->format('F j, Y g:i A') }}</p>
                            <p><strong>Reviewer:</strong> {{ $existingResponse->lawyer_name }}</p>
                            <p><strong>Law Firm:</strong> {{ $existingResponse->law_firm_name }}</p>
                            
                            <div class="mt-3">
                                <strong>Legal Assessment:</strong>
                                <div class="bg-light p-3 rounded mt-2">
                                    {{ $existingResponse->legal_assessment }}
                                </div>
                            </div>

                            <div class="mt-3">
                                <strong>Recommendations:</strong>
                                <div class="bg-light p-3 rounded mt-2">
                                    {{ $existingResponse->legal_recommendations }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Case Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white border-0 pt-3">
                        <h6 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Case Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Case Number</small>
                            <strong class="text-primary">{{ $complaint->case_number }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Type</small>
                            <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Submitted</small>
                            {{ $complaint->created_at->format('M j, Y') }}
                            <small class="text-muted">({{ $complaint->created_at->diffForHumans() }})</small>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Location</small>
                            {{ $complaint->location }}
                        </div>
                        @if($complaint->stage)
                        <div class="mb-0">
                            <small class="text-muted d-block">Current Stage</small>
                            <span class="badge" style="background-color: {{ $complaint->stage->color }}; color: {{ in_array($complaint->stage->color, ['#ffc107', '#ffeb3b', '#fff3cd']) ? '#000' : '#fff' }};">
                                {{ $complaint->stage->name }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Assigned Team -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="mb-0">
                            <i class="bi bi-people me-2 text-primary"></i>
                            Assigned Team
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Respondents -->
                        @if($complaint->respondents && $complaint->respondents->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Respondents ({{ $complaint->respondents->count() }})</small>
                                @foreach($complaint->respondents as $respondent)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person-circle text-info me-2"></i>
                                        <div class="flex-grow-1">
                                            <small class="d-block">{{ $respondent->user->name }}</small>
                                            <small class="text-muted">{{ $respondent->user->email }}</small>
                                        </div>
                                        <span class="badge bg-{{ $respondent->responded_at ? 'success' : 'warning' }} badge-sm">
                                            {{ $respondent->responded_at ? 'Responded' : 'Pending' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="mb-0">
                            <i class="bi bi-lightning me-2 text-warning"></i>
                            Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('lawyer.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <button class="btn btn-outline-info btn-sm" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i>Print Case Details
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="window.scrollTo(0,0)">
                                <i class="bi bi-arrow-up me-1"></i>Go to Top
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Case Timeline -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="mb-0">
                            <i class="bi bi-clock-history me-2 text-info"></i>
                            Legal Review Status
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($existingResponse)
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Legal review completed on {{ $existingResponse->submitted_at->format('M j, Y') }}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-clock me-2"></i>
                                Legal review pending - please complete your assessment
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Show/hide evidence sections based on selection
        document.getElementById('supporting_evidence_type').addEventListener('change', function() {
            const evidenceDesc = document.getElementById('evidence_description_section');
            const attachments = document.getElementById('attachments_section');
            
            if (this.value === 'none' || this.value === '') {
                evidenceDesc.style.display = 'none';
                attachments.style.display = 'none';
                document.getElementById('evidence_description').required = false;
            } else {
                evidenceDesc.style.display = 'block';
                attachments.style.display = 'block';
                document.getElementById('evidence_description').required = true;
            }
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
        
        .legal-btn {
            background: #0d6efd;
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .legal-btn:hover {
            background: #0b5ed7;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
    </style>
</body>
</html>