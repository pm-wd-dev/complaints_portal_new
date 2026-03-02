<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respondent Access - {{ $complaint->case_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
        .card-body {
            padding: 20px;
        }
        .text-center {
            text-align: center;
        }
        .bg-primary {
            background-color: #0066cc !important;
            color: white;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }
        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
            color: white;
        }
        .bg-primary-badge {
            background-color: #0066cc;
        }
        .bg-warning-badge {
            background-color: #ffc107;
            color: #000;
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            border: 1px solid transparent;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 5px;
        }
        .btn-primary {
            color: #fff;
            background-color: #0066cc;
            border-color: #0066cc;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }
        @media (max-width: 768px) {
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        .border-warning {
            border-color: #ffc107 !important;
        }
        .text-muted {
            color: #6c757d;
        }
        .fs-4 {
            font-size: 1.5rem;
        }
        .me-3 {
            margin-right: 1rem;
        }
        .mb-0, .mb-1, .mb-2, .mb-3, .mb-4 {
            margin-bottom: 0;
        }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }
        .d-flex {
            display: flex;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-center {
            justify-content: center;
        }
        .border-start {
            border-left: 3px solid #0066cc !important;
        }
        .ps-3 {
            padding-left: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center bg-primary text-white">
                    <h1 class="h3 mb-2">🏢 GoBEST™ Listens</h1>
                    <p class="mb-0">Respondent Access Portal</p>
                </div>
            </div>

            <!-- Complaint Assignment Notice -->
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-check-fill fs-4 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">You have been assigned to respond to this complaint</h5>
                        <p class="mb-0">Please review the details below and take appropriate action within the required timeframe.</p>
                    </div>
                </div>
            </div>

            <!-- Case Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Complaint Details</h5>
                        <span class="badge bg-primary-badge">{{ $complaint->case_number }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type:</strong> {{ ucfirst($complaint->complaint_type) }}</p>
                            <p><strong>Location:</strong> {{ $complaint->location }}</p>
                            <p><strong>Submitted:</strong> {{ $complaint->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($complaint->stage)
                                <p><strong>Current Status:</strong>
                                    <span class="badge" style="background-color: {{ $complaint->stage->color }};">
                                        {{ $complaint->stage->name }}
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complaint Description -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📝 Complaint Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $complaint->description }}</p>
                </div>
            </div>

            <!-- Complainant Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">👤 Complainant Information</h5>
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
                            <p><strong>Submitted As:</strong> {{ ucfirst($complaint->submitted_as) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            @if($complaint->complaint_about || $complaint->complainee_name || $complaint->witnesses)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📋 Additional Details</h5>
                </div>
                <div class="card-body">
                    @if($complaint->complaint_about)
                        <p><strong>Complaint About:</strong> {{ $complaint->complaint_about }}</p>
                    @endif

                    @if($complaint->complainee_name)
                        <p><strong>Person/Entity Involved:</strong> {{ $complaint->complainee_name }}</p>
                    @endif

                    @if($complaint->complainee_email)
                        <p><strong>Contact Email:</strong> {{ $complaint->complainee_email }}</p>
                    @endif

                    @if($complaint->witnesses)
                        <p><strong>Witnesses:</strong> {{ $complaint->witnesses }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Evidence/Attachments -->
            @if($complaint->attachments && $complaint->attachments->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📎 Evidence Files ({{ $complaint->attachments->count() }})</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">The following files have been attached to this complaint:</p>
                    <div class="row">
                        @foreach($complaint->attachments as $attachment)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="bi bi-file-earmark fs-4 me-2 text-muted"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted">{{ $attachment->file_type }} file</small>
                                        <br>
                                        <small class="text-muted">Uploaded {{ $attachment->created_at->format('M j, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="alert alert-info mt-3">
                        <small><i class="bi bi-info-circle me-1"></i> To view and download these files, please log in to your cast member account.</small>
                    </div>
                </div>
            </div>
            @endif

            <!-- Response Timeline -->
            @if($complaint->respondents && $complaint->respondents->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">💬 Response History</h5>
                </div>
                <div class="card-body">
                    @php $hasResponses = false; @endphp
                    @foreach($complaint->respondents as $respondent)
                        @if($respondent->responses && $respondent->responses->count() > 0)
                            @php $hasResponses = true; @endphp
                            <div class="mb-3">
                                <h6 class="text-primary">{{ $respondent->user->name }}</h6>
                                @foreach($respondent->responses as $response)
                                    <div class="border-start border-primary ps-3 mb-2">
                                        <small class="text-muted">{{ $response->created_at->format('M j, Y g:i A') }}</small>
                                        <p class="mb-0">{{ $response->response }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach

                    @if(!$hasResponses)
                        <p class="text-muted mb-0">No responses have been submitted yet.</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Action Required -->
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">⚠️ Response Required</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">This complaint requires your response within <strong>48 hours</strong> of assignment.</p>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ $loginUrl }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login to Respond
                        </a>
                    </div>

                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            You will need to log in to your cast member account to submit a response and access all attachments.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow-sm mt-4">
                <div class="card-body text-center">
                    <h6 class="mb-2">Need Help?</h6>
                    <p class="text-muted mb-0">
                        If you have any questions about this assignment, please contact the administration team.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

        </div>
    </div>
</body>
</html>