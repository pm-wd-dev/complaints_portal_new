@extends('layouts.admin')

@section('title', 'Complaint Details')

@section('content')
<style>
/* Hide sidebar and make content full width for email thread */
.sidebar {
    display: none !important;
}

.main-content {
    margin-left: 0 !important;
    padding: 0 !important;
    margin-top: 0 !important;
    width: 100% !important;
    position: relative !important;
}

.main-content .container-fluid {
    padding: 0 !important;
    max-width: none !important;
    margin: 0 !important;
    height: 100vh !important;
    overflow: hidden !important;
}

.email-thread-container {
    max-width: none;
    padding: 0;
    margin: 0;
    width: 100%;
    height: 100vh;
    display: flex;
    flex-direction: column;
    position: relative;
}

.thread-header {
    background: rgba(255, 255, 255, 0.98);
    border-bottom: 2px solid #e9ecef;
    padding: 20px 30px;
    position: fixed;
    top: 100px;
    left: 0;
    right: 0;
    z-index: 1020;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    backdrop-filter: blur(10px);
    height: auto;
    flex-shrink: 0;
}

.thread-body {
    background: #f8f9fa;
    padding: 20px 30px;
    margin-top: 200px;
    flex: 1;
    overflow-y: auto;
    height: calc(100vh - 300px);
}

.email-item {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: visible;
    border: 1px solid #e9ecef;
}

.email-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.email-body {
    padding: 25px;
}

.sender-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #fff;
    font-size: 16px;
}

.complainant-avatar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.admin-avatar { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.respondent-avatar { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.lawyer-avatar { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.thread-actions {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    padding: 15px;
    border: 1px solid #e9ecef;
    position: sticky;
    top: 20px;
    height: fit-content;
}

.case-status {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.case-info-item {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: #f8f9fa;
    border-radius: 20px;
    font-size: 13px;
    color: #495057;
    border: 1px solid #e9ecef;
}

.case-number-badge {
    font-weight: 700;
    font-size: 20px;
    color: #2563eb;
    letter-spacing: 0.5px;
}

.attachments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.attachment-item {
    text-align: center;
    padding: 15px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.attachment-item:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

@media (max-width: 1024px) {
    .thread-body .d-flex {
        flex-direction: column !important;
    }

    .flex-shrink-0 {
        width: 100% !important;
    }

    .thread-actions {
        position: relative;
        top: auto;
        margin: 20px 0;
    }

    .thread-header {
        padding: 15px;
        top: 100px;
        position: fixed;
    }

    .thread-body {
        padding: 15px;
        margin-top: 180px;
        height: calc(100vh - 280px);
    }

    .case-number-badge {
        font-size: 18px;
    }

    .thread-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 15px;
    }

    .case-info-item {
        font-size: 12px;
        padding: 4px 8px;
    }
}

@media (max-width: 768px) {
    .thread-header {
        top: 100px;
        padding: 10px 15px;
        position: fixed;
    }

    .thread-body {
        margin-top: 160px;
        padding: 10px 15px;
        height: calc(100vh - 260px);
    }

    .case-number-badge {
        font-size: 16px;
    }

    .case-info-item, .case-status {
        font-size: 11px;
        padding: 3px 6px;
    }

    .thread-header .d-flex {
        gap: 10px;
    }
}

/* Stage Timeline Styles */
.stage-timeline {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    padding: 10px 0;
}

.stage-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.stage-circle-wrapper {
    display: flex;
    align-items: center;
    width: 100%;
    position: relative;
}

.stage-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    position: relative;
    z-index: 2;
    border: 4px solid;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.stage-circle.completed {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stage-circle.current {
    border-width: 5px;
}

.stage-circle.pending {
    background-color: #ffffff;
    border-color: #d1d5db;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stage-line {
    flex: 1;
    height: 3px;
    margin: 0 12px;
    position: relative;
    z-index: 1;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.stage-line.completed {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stage-line.pending {
    background-color: #e5e7eb;
}

.stage-name {
    margin-top: 14px;
    font-size: 14px;
    text-align: center;
    max-width: 110px;
    line-height: 1.4;
    font-weight: 700;
}

.stage-badge {
    margin-top: 8px;
}

/* Responsive Stage Timeline */
@media (max-width: 1024px) {
    .stage-timeline {
        overflow-x: auto;
        overflow-y: hidden;
        justify-content: flex-start;
        padding-bottom: 15px;
        -webkit-overflow-scrolling: touch;
    }

    .stage-item {
        min-width: 100px;
        flex: 0 0 auto;
    }
}

@media (max-width: 768px) {
    .stage-circle {
        width: 40px;
        height: 40px;
        font-size: 14px;
        border-width: 3px;
    }

    .stage-circle.current {
        border-width: 4px;
    }

    .stage-name {
        font-size: 12px;
        max-width: 90px;
        margin-top: 10px;
        font-weight: 700;
    }

    .stage-line {
        height: 2px;
        margin: 0 8px;
    }

    .stage-item {
        min-width: 85px;
    }
}

/* Action Buttons Container */
.action-buttons-container {
    position: relative;
    z-index: 1;
}

.action-buttons-container .dropdown {
    position: relative;
}

.action-buttons-container .dropdown-menu {
    max-height: 400px;
    overflow-y: auto;
    min-width: 280px;
}

.action-buttons-container .dropdown-menu .dropdown-header {
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
}

.action-buttons-container .dropdown-menu .dropdown-item {
    padding: 10px 16px;
}

.action-buttons-container .dropdown-menu .dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>

<div class="email-thread-container">
    <!-- Thread Header -->
    <div class="thread-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.complaints') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left me-2"></i>Back to Complaints
                </a>
                <div>
                    <div class="case-number-badge">{{ $complaint->case_number }}</div>
                    <div class="d-flex align-items-center gap-3 mt-2">
                        <span class="case-status" style="background-color: {{ $complaint->stage ? $complaint->stage->color : '#6c757d' }}; color: {{ in_array($complaint->stage->color ?? '', ['#ffc107', '#ffeb3b', '#fff3cd', '#FFD700', '#FFFF00', '#FFFFE0']) ? '#000' : '#fff' }};">
                            @if($complaint->stage)
                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                                {{ $complaint->stage->name }}
                            @else
                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                            @endif
                        </span>
                        <span class="case-info-item">
                            <i class="bi bi-tag me-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}
                        </span>
                        <span class="case-info-item">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $complaint->location }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#complaintDetailsModal">
                    <i class="bi bi-file-text me-2"></i>Complaint Details
                </button>
                <span class="text-muted">Last updated {{ $complaint->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    <!-- Thread Body -->
    <div class="thread-body">
        <div class="d-flex gap-4">
            <div class="flex-grow-1" style="min-width: 0;">

                <!-- Stage Change Activities -->
                @php
                    $activities = collect();

                    // Add stage changes
                    if($complaint->stageChangeLogs) {
                        foreach($complaint->stageChangeLogs as $log) {
                            $activities->push((object)[
                                'type' => 'stage_change',
                                'timestamp' => $log->created_at,
                                'data' => $log
                            ]);
                        }
                    }

                    // Add respondent responses
                    if($complaint->respondentResponseDetails) {
                        foreach($complaint->respondentResponseDetails as $response) {
                            // Find the corresponding respondent user
                            $respondentUser = $complaint->respondents->where('user_id', $response->user_id)->first();
                            $activities->push((object)[
                                'type' => 'respondent_response',
                                'timestamp' => $response->submitted_at,
                                'data' => $response,
                                'user' => $respondentUser ? $respondentUser->user : null
                            ]);
                        }
                    }

                    // Add lawyer responses
                    if($complaint->lawyerResponseDetails) {
                        foreach($complaint->lawyerResponseDetails as $response) {
                            // Find the corresponding lawyer user
                            $lawyerUser = $complaint->lawyers->where('user_id', $response->user_id)->first();
                            $activities->push((object)[
                                'type' => 'lawyer_response',
                                'timestamp' => $response->submitted_at,
                                'data' => $response,
                                'user' => $lawyerUser ? $lawyerUser->user : null
                            ]);
                        }
                    }

                    // Add admin replies
                    if($complaint->replies) {
                        foreach($complaint->replies as $reply) {
                            $activities->push((object)[
                                'type' => 'admin_reply',
                                'timestamp' => $reply->created_at,
                                'data' => $reply
                            ]);
                        }
                    }

                    // Sort by timestamp - newest first (descending order)
                    $activities = $activities->sortByDesc('timestamp');

                @endphp

                @foreach($activities as $activity)
                    @if($activity->type === 'stage_change')
                        @php
                            // Skip stage changes that are about respondent/lawyer submissions
                            // because we show the full response instead
                            $skipStageChange = false;
                            $description = strtolower($activity->data->description ?? '');
                            if (
                                stripos($description, 'respondent submitted') !== false ||
                                stripos($description, 'lawyer submitted') !== false ||
                                stripos($description, 'responded') !== false
                            ) {
                                $skipStageChange = true;
                            }
                        @endphp

                        @if(!$skipStageChange)
                        <!-- Stage Change Item -->
                        <div class="email-item">
                            <div class="email-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        @if($activity->data->performer && $activity->data->performer->role === 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @else
                                            <span class="badge bg-secondary">System</span>
                                        @endif
                                        <span>{{ $activity->data->description }}</span>
                                        @if($activity->data->toStage)
                                            <span class="badge" style="background-color: {{ $activity->data->toStage->color }}; color: {{ in_array($activity->data->toStage->color, ['#ffc107', '#ffeb3b', '#fff3cd', '#FFD700', '#FFFF00', '#FFFFE0']) ? '#000' : '#fff' }};">
                                                {{ $activity->data->toStage->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted small text-nowrap ms-3">{{ $activity->timestamp->format('M j, Y g:i A') }}</div>
                                </div>

                                <!-- Show complaint details if this is the initial submission -->
                                @if(stripos($activity->data->description, 'initial complaint submitted') !== false)
                                    @if($complaint->description)
                                    <div class="mt-3 p-3 rounded" style="background: #f8f9fa; border-left: 3px solid #2563eb;">
                                        <div class="small fw-bold text-muted mb-2">Description:</div>
                                        <div style="color: #333; line-height: 1.6;">{{ $complaint->description }}</div>
                                    </div>
                                    @endif

                                    <!-- Additional Quick Info -->
                                    <div class="mt-3 d-flex flex-wrap gap-3 small text-muted">
                                        <div><i class="bi bi-tag me-1"></i><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}</div>
                                        <div><i class="bi bi-geo-alt me-1"></i><strong>Location:</strong> {{ $complaint->location }}</div>
                                        @if($complaint->date_of_experience)
                                        <div><i class="bi bi-calendar me-1"></i><strong>Date of Incident:</strong> {{ \Carbon\Carbon::parse($complaint->date_of_experience)->format('M j, Y') }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                    @elseif($activity->type === 'respondent_response')
                        <!-- Respondent Response -->
                        <div class="email-item">
                            <div class="email-header">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <div class="respondent-avatar me-3">
                                            {{ strtoupper(substr($activity->user->name ?? 'R', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ $activity->user->name ?? 'Respondent' }}</h5>
                                            @if($activity->data->respondent_email)
                                                <div class="text-muted small">{{ $activity->data->respondent_email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-info">Respondent Response</span>
                                        <span class="text-muted small text-nowrap">{{ $activity->timestamp->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="email-body">
                                <h6 class="text-primary mb-3">Response to Complaint: {{ $activity->data->case_number }}</h6>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Venue:</strong> {{ $activity->data->venue_legal_name }}<br>
                                        <strong>Location:</strong> {{ $activity->data->venue_city_state }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Respondent:</strong> {{ $activity->data->respondent_name }}<br>
                                        <strong>Date:</strong> {{ $activity->data->complaint_date->format('M j, Y') }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Respondent's Side of Story:</strong>
                                    <p class="mt-2">{{ $activity->data->respondent_side_story }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Issue Details:</strong>
                                    <p class="mt-2">{{ $activity->data->issue_detail_description }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Witnesses:</strong>
                                    <p class="mt-2">{{ $activity->data->witnesses_information }}</p>
                                </div>

                                @if($activity->data->evidence_description)
                                <div class="mb-3">
                                    <strong>Evidence ({{ ucfirst(str_replace('_', ' ', $activity->data->supporting_evidence_type)) }}):</strong>
                                    <p class="mt-2">{{ $activity->data->evidence_description }}</p>
                                </div>
                                @endif

                                @if($activity->data->attachments && $activity->data->attachments->count() > 0)
                                <div class="attachments-grid">
                                    @foreach($activity->data->attachments as $attachment)
                                        @php
                                            $extension = strtolower($attachment->file_type);
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp
                                        <div class="attachment-item">
                                            @if($isImage)
                                                <img src="{{ asset($attachment->file_path) }}" alt="Evidence" class="img-fluid rounded mb-2 image-preview-trigger" style="max-height: 100px; cursor: pointer;" data-image-src="{{ asset($attachment->file_path) }}">
                                            @else
                                                <i class="bi bi-file-earmark fs-1 text-muted mb-2"></i>
                                            @endif
                                            <div class="small">
                                                <a href="{{ asset($attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                                    {{ strtoupper($extension) }} File
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>

                    @elseif($activity->type === 'lawyer_response')
                        <!-- Lawyer Response -->
                        <div class="email-item">
                            <div class="email-header">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <div class="lawyer-avatar me-3">
                                            {{ strtoupper(substr($activity->user->name ?? 'L', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ $activity->user->name ?? 'Lawyer' }}</h5>
                                            @if($activity->user->email)
                                                <div class="text-muted small">{{ $activity->user->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-success">Legal Assessment</span>
                                        <span class="text-muted small text-nowrap">{{ $activity->timestamp->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="email-body">
                                <h6 class="text-primary mb-3">Legal Review: {{ $activity->data->case_number }}</h6>

                                <div class="mb-3">
                                    <strong>Legal Assessment:</strong>
                                    <p class="mt-2">{{ $activity->data->legal_assessment }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Recommendations:</strong>
                                    <p class="mt-2">{{ $activity->data->legal_recommendations }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Compliance Notes:</strong>
                                    <p class="mt-2">{{ $activity->data->compliance_notes }}</p>
                                </div>

                                @if($activity->data->evidence_description)
                                <div class="mb-3">
                                    <strong>Evidence Review:</strong>
                                    <p class="mt-2">{{ $activity->data->evidence_description }}</p>
                                </div>
                                @endif

                                @if($activity->data->attachments && $activity->data->attachments->count() > 0)
                                <div class="attachments-grid">
                                    @foreach($activity->data->attachments as $attachment)
                                        @php
                                            $extension = strtolower($attachment->file_type);
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp
                                        <div class="attachment-item">
                                            @if($isImage)
                                                <img src="{{ asset($attachment->file_path) }}" alt="Legal Evidence" class="img-fluid rounded mb-2 image-preview-trigger" style="max-height: 100px; cursor: pointer;" data-image-src="{{ asset($attachment->file_path) }}">
                                            @else
                                                <i class="bi bi-file-earmark fs-1 text-muted mb-2"></i>
                                            @endif
                                            <div class="small">
                                                <a href="{{ asset($attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                                    {{ strtoupper($extension) }} File
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>

                    @elseif($activity->type === 'admin_reply')
                        <!-- Admin Reply -->
                        <div class="email-item">
                            <div class="email-header">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <div class="admin-avatar me-3">
                                            {{ strtoupper(substr($activity->data->user->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ $activity->data->user->name ?? 'Admin' }}</h5>
                                            @if($activity->data->user->email)
                                                <div class="text-muted small">{{ $activity->data->user->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-primary">
                                            @if($activity->data->recipient_id)
                                                Reply to {{ $activity->data->recipient->name ?? 'Recipient' }}
                                            @else
                                                Reply
                                            @endif
                                        </span>
                                        <span class="text-muted small text-nowrap">{{ $activity->timestamp->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="email-body">
                                <p style="white-space: pre-wrap;">{{ $activity->data->message }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($activities->isEmpty())
                    <div class="email-item">
                        <div class="email-body text-center py-5">
                            <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No Activity Yet</h5>
                            <p class="text-muted mb-0">Activity will appear here as the complaint progresses.</p>
                        </div>
                    </div>
                @endif

                <!-- Email Thread Action Buttons (Bottom of Chat) -->
                @if ($complaint->status !== 'resolved')
                <div class="email-item" style="background: #f9fafb;">
                    <div class="email-body" style="padding: 20px;">
                        <div class="action-buttons-container">
                            <!-- Reply Button -->
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2" onclick="toggleReplyBox()">
                                <i class="bi bi-reply me-2"></i>Reply
                            </button>

                            <!-- Reply To Button (Send To Dropdown) -->
                            <div class="dropdown mb-2">
                                <button class="btn btn-outline-info btn-sm w-100 dropdown-toggle" type="button" id="replyToDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-send me-2"></i>Reply to
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="replyToDropdown">
                                    <li><h6 class="dropdown-header">SEND TO</h6></li>
                                    @if($complaint->respondents && $complaint->respondents->count() > 0)
                                        @foreach($complaint->respondents as $respondent)
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="selectSendTo(event, '{{ $respondent->user->name ?? 'Respondent' }}', '{{ $respondent->user->email ?? '' }}', {{ $respondent->user->id }}, 'respondent')">
                                                    <div class="d-flex align-items-center">
                                                        <div class="respondent-avatar me-2" style="width: 24px; height: 24px; font-size: 11px;">
                                                            {{ strtoupper(substr($respondent->user->name ?? 'R', 0, 1)) }}
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold" style="font-size: 13px;">{{ $respondent->user->name ?? 'Respondent' }}</div>
                                                            <div class="text-muted" style="font-size: 11px;">{{ $respondent->user->email ?? '' }}</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                    @if($complaint->lawyers && $complaint->lawyers->count() > 0)
                                        @foreach($complaint->lawyers as $lawyer)
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="selectSendTo(event, '{{ $lawyer->user->name ?? 'Lawyer' }}', '{{ $lawyer->user->email ?? '' }}', {{ $lawyer->user->id }}, 'lawyer')">
                                                    <div class="d-flex align-items-center">
                                                        <div class="lawyer-avatar me-2" style="width: 24px; height: 24px; font-size: 11px;">
                                                            {{ strtoupper(substr($lawyer->user->name ?? 'L', 0, 1)) }}
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold" style="font-size: 13px;">{{ $lawyer->user->name ?? 'Lawyer' }}</div>
                                                            <div class="text-muted" style="font-size: 11px;">{{ $lawyer->user->email ?? '' }}</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                    @if((!$complaint->respondents || $complaint->respondents->count() == 0) && (!$complaint->lawyers || $complaint->lawyers->count() == 0))
                                        <li><span class="dropdown-item-text text-muted">
                                            <i class="bi bi-info-circle me-2"></i>Add respondents first
                                        </span></li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Forward Button (Change State Dropdown) -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm w-100 dropdown-toggle" type="button" id="forwardDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-forward me-2"></i>Forward
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="forwardDropdown">
                                    <li><h6 class="dropdown-header">CHANGE STATE</h6></li>
                                    @php
                                        $allStagesBottom = \App\Models\Stage::orderBy('step_number')->get();
                                    @endphp
                                    @foreach ($allStagesBottom as $stage)
                                        <li>
                                            <a class="dropdown-item {{ $complaint->stage_id == $stage->id ? 'active' : '' }}"
                                                href="javascript:void(0);" onclick="updateStage(event, {{ $stage->id }}, 'change', '{{ $stage->name }}')">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge me-2" style="background-color: {{ $stage->color }}; width: 8px; height: 8px; border-radius: 50%; padding: 0;"></span>
                                                        <span>{{ $stage->name }}</span>
                                                    </div>
                                                    @if ($complaint->stage_id == $stage->id)
                                                        <i class="bi bi-check text-success"></i>
                                                    @endif
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Reply Box -->
                        <div id="replyBox" class="mt-3 p-3 rounded" style="display: none; background: white; border: 1px solid #e9ecef;">
                            <label class="form-label small fw-bold">Your Reply</label>
                            <textarea id="replyMessage" class="form-control form-control-sm mb-2" rows="4" placeholder="Write your reply..."></textarea>
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-sm btn-secondary" onclick="cancelReply()">Cancel</button>
                                <button class="btn btn-sm btn-primary" onclick="sendReply()">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Sidebar -->
            <div class="flex-shrink-0" style="width: 320px;">
                <div class="thread-actions">
                    <h6 class="mb-3"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h6>

                    <!-- Timeline Status Section - Vertical -->
                    @php
                        $allStages = \App\Models\Stage::orderBy('step_number')->get();
                        $currentStageId = $complaint->stage_id;

                        // Get all completed stage IDs from stage change logs
                        $completedStageIds = $complaint->stageChangeLogs
                            ->pluck('to_stage_id')
                            ->unique()
                            ->filter()
                            ->toArray();

                        // Also add the current stage as completed
                        if ($currentStageId) {
                            $completedStageIds[] = $currentStageId;
                        }
                        $completedStageIds = array_unique($completedStageIds);

                        // Helper function to convert hex to RGB
                        function hexToRgbSidebar($hex) {
                            $hex = str_replace('#', '', $hex);
                            $r = hexdec(substr($hex, 0, 2));
                            $g = hexdec(substr($hex, 2, 2));
                            $b = hexdec(substr($hex, 4, 2));
                            return "$r, $g, $b";
                        }
                    @endphp

                    <div class="mb-3 p-3 rounded" style="background: white; border: 1px solid #e9ecef;">
                        <div class="small fw-bold text-muted mb-3" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-clock-history me-1"></i> Timeline Status
                        </div>

                        <!-- Vertical Timeline -->
                        <div class="d-flex flex-column gap-3">
                            @foreach($allStages as $index => $stage)
                                @php
                                    $isCompleted = in_array($stage->id, $completedStageIds);
                                    $isCurrent = $stage->id == $currentStageId;
                                    $isPending = !$isCompleted && !$isCurrent;
                                @endphp
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3" style="position: relative;">
                                        <!-- Circle -->
                                        <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                                            background-color: {{ $isCompleted || $isCurrent ? $stage->color : '#e5e7eb' }};
                                            border: 3px solid {{ $isCompleted || $isCurrent ? $stage->color : '#d1d5db' }};
                                            {{ $isCurrent ? 'box-shadow: 0 0 0 4px rgba(' . hexToRgbSidebar($stage->color) . ', 0.2);' : '' }}">
                                            @if($isCompleted && !$isCurrent)
                                                <i class="bi bi-check-lg" style="color: #ffffff; font-size: 14px; font-weight: bold;"></i>
                                            @elseif($isCurrent)
                                                <div style="width: 10px; height: 10px; background: #ffffff; border-radius: 50%;"></div>
                                            @else
                                                <span style="color: #9ca3af; font-size: 12px; font-weight: 600;">{{ $index + 1 }}</span>
                                            @endif
                                        </div>

                                        <!-- Connecting Line -->
                                        @if($index < $allStages->count() - 1)
                                            <div style="position: absolute; left: 50%; top: 32px; width: 2px; height: 24px;
                                                background-color: {{ $isCompleted ? $stage->color : '#e5e7eb' }}; transform: translateX(-50%);"></div>
                                        @endif
                                    </div>

                                    <div class="flex-grow-1">
                                        <div style="font-size: 13px; font-weight: {{ $isCurrent ? '700' : '600' }};
                                            color: {{ $isCurrent ? $stage->color : ($isCompleted ? '#374151' : '#6b7280') }};">
                                            {{ $stage->name }}
                                        </div>
                                        @if($isCurrent)
                                            <span class="badge mt-1" style="background-color: {{ $stage->color }}; color: #ffffff; font-size: 9px; padding: 2px 8px;">Active Now</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Summary Stats -->
                        <div class="mt-3 pt-3" style="border-top: 1px solid #e9ecef;">
                            <div class="d-flex justify-content-between mb-2" style="font-size: 12px;">
                                <span class="text-muted">Total Responses:</span>
                                <span class="fw-bold text-dark">
                                    {{
                                        ($complaint->respondentResponseDetails ? $complaint->respondentResponseDetails->count() : 0) +
                                        ($complaint->lawyerResponseDetails ? $complaint->lawyerResponseDetails->count() : 0)
                                    }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size: 12px;">
                                <span class="text-muted">Last Updated:</span>
                                <span class="fw-bold text-dark">{{ $complaint->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    @if ($complaint->status !== 'resolved')
                        <div class="d-grid gap-2 mb-3">
                            <!-- Add Respondent Button -->
                            @if($availableRespondents && $availableRespondents->count() > 0)
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRespondentModal">
                                    <i class="bi bi-person-plus me-2"></i>Add Respondent
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="No available respondents to add">
                                    <i class="bi bi-person-plus me-2"></i>Add Respondent
                                </button>
                            @endif

                            <!-- Add Lawyer Button - Only show after respondent has responded -->
                            @if($hasRespondentResponses)
                                @if($availableLawyers && $availableLawyers->count() > 0)
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addLawyerModal">
                                        <i class="bi bi-briefcase me-2"></i>Add Lawyer
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="No available lawyers to add">
                                        <i class="bi bi-briefcase me-2"></i>Add Lawyer
                                    </button>
                                @endif
                            @endif
                        </div>
                    @endif

                    <hr>

                    <!-- Case Summary -->
                    <div class="mb-3">
                        <h6><i class="bi bi-info-circle me-2"></i>Case Summary</h6>
                        <div class="small text-muted">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Respondents:</span>
                                <strong>{{ $complaint->respondents ? $complaint->respondents->count() : 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Available to Add:</span>
                                <strong>{{ $availableRespondents ? $availableRespondents->count() : 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Lawyers:</span>
                                <strong>{{ $complaint->lawyers ? $complaint->lawyers->count() : 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Responses:</span>
                                <strong>{{
                                    ($complaint->respondentResponseDetails ? $complaint->respondentResponseDetails->count() : 0) +
                                    ($complaint->lawyerResponseDetails ? $complaint->lawyerResponseDetails->count() : 0)
                                }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Stage Changes:</span>
                                <strong>{{ $complaint->stageChangeLogs ? $complaint->stageChangeLogs->count() : 0 }}</strong>
                            </div>
                        </div>
                    </div>

                    @php
                        $hasLawyerResponses = $complaint->lawyerResponseDetails && $complaint->lawyerResponseDetails->count() > 0;
                        $hasLawyers = $complaint->lawyers && $complaint->lawyers->count() > 0;
                    @endphp

                    @if($complaint->status === 'resolved')
                        <div class="alert alert-success small">
                            <i class="bi bi-check-circle me-1"></i>
                            <strong>Case Resolved</strong><br>
                            This complaint has been successfully resolved and closed.
                        </div>
                    @elseif($hasLawyerResponses)
                        <div class="alert alert-primary small">
                            <i class="bi bi-briefcase me-1"></i>
                            <strong>Under Legal Review</strong><br>
                            Lawyer has responded. Case is being reviewed for resolution.
                        </div>
                    @elseif($hasLawyers)
                        <div class="alert alert-info small">
                            <i class="bi bi-clock me-1"></i>
                            <strong>Awaiting Lawyer Review</strong><br>
                            Lawyer has been assigned. Waiting for legal review and response.
                        </div>
                    @elseif($hasRespondentResponses)
                        <div class="alert alert-success small">
                            <i class="bi bi-check-circle me-1"></i>
                            <strong>Ready for Lawyer Review</strong><br>
                            Respondent has provided their response. You can now add a lawyer to review the case.
                        </div>
                    @elseif($complaint->respondents && $complaint->respondents->count() > 0)
                        <div class="alert alert-info small">
                            <i class="bi bi-clock me-1"></i>
                            <strong>Waiting for Response</strong><br>
                            Complaint sent to {{ $complaint->respondents->count() }} respondent(s). Lawyer option will appear after response.
                        </div>
                    @else
                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Action Required</strong><br>
                            Add a respondent to begin the complaint resolution process.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complaint Details Modal -->
<div class="modal fade" id="complaintDetailsModal" tabindex="-1" aria-labelledby="complaintDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <div>
                    <h5 class="modal-title mb-1" id="complaintDetailsModalLabel">
                        Complaint Form Details
                    </h5>
                    <div class="text-muted small">Case Number: {{ $complaint->case_number }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 32px; background: #f5f5f5;">
                <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 16px; padding: 32px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">

                    <!-- Submitted As -->
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">You are casting this complaint as</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ ucfirst(str_replace('_', ' ', $complaint->submitted_as ?? 'N/A')) }}
                        </div>
                    </div>

                    <!-- Anonymous Status -->
                    @if($complaint->is_anonymous)
                    <div class="mb-4">
                        <div style="padding: 12px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; color: #856404;">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Anonymous Complaint</strong>
                        </div>
                    </div>
                    @endif

                    <!-- Contact Information -->
                    @if(!$complaint->is_anonymous && $complaint->name)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Your Name (Who is making the complaint)</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ $complaint->name }}
                        </div>
                    </div>
                    @endif

                    @if($complaint->email)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Email Address</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ $complaint->email }}
                        </div>
                    </div>
                    @endif

                    @if($complaint->phone_number)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Phone Number</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ $complaint->phone_number }}
                        </div>
                    </div>
                    @endif

                    <!-- Date of Complaint -->
                    @if($complaint->date_of_experience)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Date of Complaint - When did this occur?</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ \Carbon\Carbon::parse($complaint->date_of_experience)->format('M j, Y') }}
                        </div>
                    </div>
                    @endif

                    <!-- Who is your complaint about -->
                    @if($complaint->complaint_about)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Who is your complaint about?</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333; white-space: pre-wrap;">{{ $complaint->complaint_about }}</div>
                    </div>
                    @endif

                    <!-- Complainee Details -->
                    @if($complaint->complainee_name)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Complainee Full Name</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ $complaint->complainee_name }}
                        </div>
                    </div>
                    @endif

                    @if($complaint->complainee_email)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Complainee Email</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ $complaint->complainee_email }}
                        </div>
                    </div>
                    @endif

                    @if($complaint->complainee_address)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Complainee Address</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333; white-space: pre-wrap;">{{ $complaint->complainee_address }}</div>
                    </div>
                    @endif

                    <!-- Location -->
                    @if($complaint->location)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Where did it take place?</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ $complaint->location }}
                        </div>
                    </div>
                    @endif

                    <!-- Issue Type -->
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Issue Type</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Describe the issue in details</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333; min-height: 120px; white-space: pre-wrap;">{{ $complaint->description }}</div>
                    </div>

                    <!-- Witnesses -->
                    @if($complaint->witnesses)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Were there witnesses?</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333; white-space: pre-wrap;">{{ $complaint->witnesses }}</div>
                    </div>
                    @endif

                    <!-- Evidence Type -->
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Do you have support evidence?</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333;">
                            {{ ucfirst(str_replace('_', ' ', $complaint->evidence_type ?? 'No evidence')) }}
                        </div>
                    </div>

                    <!-- Evidence Description -->
                    @if($complaint->evidence_description)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #333;">Evidence Description</label>
                        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #333; white-space: pre-wrap;">{{ $complaint->evidence_description }}</div>
                    </div>
                    @endif

                    <!-- Attachments -->
                    @if($complaint->attachments && $complaint->attachments->where('respondent_response_id', null)->where('lawyer_response_id', null)->count() > 0)
                    <div class="mb-4">
                        <label style="display: block; font-weight: 500; margin-bottom: 12px; color: #333;">Uploaded Evidence Files</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                            @foreach($complaint->attachments->where('respondent_response_id', null)->where('lawyer_response_id', null) as $attachment)
                                @php
                                    $extension = strtolower($attachment->file_type);
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                @endphp
                                <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 15px; text-align: center; transition: all 0.3s;">
                                    @if($isImage)
                                        <img src="{{ asset($attachment->file_path) }}" alt="Evidence" class="image-preview-trigger" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 8px; cursor: pointer;" data-image-src="{{ asset($attachment->file_path) }}">
                                    @else
                                        <i class="bi bi-file-earmark" style="font-size: 48px; color: #999; margin-bottom: 8px;"></i>
                                    @endif
                                    <div style="font-size: 14px;">
                                        <a href="{{ asset($attachment->file_path) }}" target="_blank" style="text-decoration: none; color: #2ea043; font-weight: 500;">
                                            {{ strtoupper($extension) }} File
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            <div class="modal-footer" style="background: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background: rgba(0,0,0,0.9);">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">Image Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 80vh; width: auto; height: auto;">
            </div>
        </div>
    </div>
</div>

<!-- Include all the existing modals -->
@include('admin.complaints.partials.modals')

<script>
// Image Preview Handler
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.image-preview-trigger').forEach(function(img) {
        img.addEventListener('click', function() {
            const imageSrc = this.getAttribute('data-image-src');
            document.getElementById('previewImage').src = imageSrc;
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        });
    });
});

function updateStage(event, stageId, action, stageName) {
    event.preventDefault();
    if (confirm(`Are you sure you want to change the stage to "${stageName}"?`)) {
        fetch(`{{ route('admin.complaints.update-stage', $complaint) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-HTTP-Method-Override': 'PATCH'
            },
            body: JSON.stringify({
                stage_id: stageId,
                action: action
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating stage: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating stage: ' + error.message);
        });
    }
}

// Quick Actions Functions
let selectedSendTo = null;

function toggleReplyBox() {
    const replyBox = document.getElementById('replyBox');
    if (replyBox.style.display === 'none' || !replyBox.style.display) {
        replyBox.style.display = 'block';
        document.getElementById('replyMessage').focus();
    } else {
        replyBox.style.display = 'none';
    }
}

function cancelReply() {
    document.getElementById('replyBox').style.display = 'none';
    document.getElementById('replyMessage').value = '';
    selectedSendTo = null;
}

function sendReply() {
    const message = document.getElementById('replyMessage').value.trim();
    if (!message) {
        alert('Please enter a message');
        return;
    }

    const data = {
        message: message,
        recipient_id: selectedSendTo ? selectedSendTo.id : null,
        recipient_type: selectedSendTo ? selectedSendTo.type : null
    };

    fetch('{{ route('admin.complaints.reply', $complaint) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Reply sent successfully!');
            cancelReply();
            location.reload();
        } else {
            alert('Error sending reply: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending reply: ' + error.message);
    });
}

function selectSendTo(event, name, email, userId, type) {
    event.preventDefault();
    selectedSendTo = { name, email, id: userId, type: type };
    toggleReplyBox();
    document.getElementById('replyMessage').placeholder = `Reply to ${name}...`;
}
</script>
@endsection
