<style>
    .badge-status.awaiting_signature {
    background-color: var(--bs-warning) !;
    color: #ffff;
}
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.875rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 500;
    white-space: nowrap;
}
</style>
@extends('layouts.cast_member')

@section('title', 'View Complaint')

@section('content')
@php
            $userSignature = $complaint->signatures->where('user_id', Auth::user()->id)->first();
@endphp
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="">

                @if($complaint->status === 'resolved')
                @if($complaint->resolutions->count() > 0)
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('cast_member.complaints') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Complaints
                    </a>

                        <a href="{{ asset($complaint->resolutions->first()->generated_pdf_path) }}" target="_blank" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>View Resolution Document
                        </a>
                    @endif


                    @if($userSignature && $userSignature->signature_path == null)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadSignatureModal">
                            <i class="bi bi-file-earmark-text me-2"></i>Upload Signature
                        </button>
                    @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div class=" mt-4 mb-3">
        <h4>Complaint Details</h4>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Main Complaint Details -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Case{{ $complaint->case_number }}</h5>
                        @php
                            $displayStatus = $complaint->display_status;
                            $statusColor = match($displayStatus) {
                                'submitted' => 'primary',
                                'under_review' => 'info',
                                'escalated' => 'warning',
                                'resolved' => 'success',
                                'closed' => 'secondary',
                                'awaiting_signature' => 'warning',
                                default => 'primary'
                            };
                        @endphp
                        <span class="badge-status bg-{{ $statusColor }} text-white">
                            @if($displayStatus === 'awaiting_signature')
                                <i class="fas fa-signature"></i>
                                <span>Awaiting Signatures</span>
                            @else
                                {{ ucfirst(str_replace('_', ' ', $displayStatus=='escalated' ? 'progress' : $displayStatus)) }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Name</h6>
                                <p class="mb-0">{{$complaint->name}}</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Email</h6>
                                <p class="mb-0">{{$complaint->email}}</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Complaint Date</h6>
                                <p class="mb-0">{{ $complaint->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Complaint Type</h6>
                                <p class="mb-0">{{ ucfirst($complaint->complaint_type) }}</p>
                            </div>
                            @if ($complaint->anonymity)
                             <div class="mb-4">
                                <h6 class="text-muted mb-2">Anonymity</h6>
                                <p class="mb-0">{{ $complaint->anonymity }}</p>
                            </div>
                            @endif
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Description</h6>
                                <p class="mb-0">{{ $complaint->description }}</p>
                            </div>
                            @if($complaint->location)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Location</h6>
                                <p class="mb-0">{{ $complaint->location }}</p>
                            </div>
                            @endif
                            @if($complaint->submitted_at && is_null($complaint->submited_by_admin_id))
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Date of Experience</h6>
                                <p class="mb-0">{{ $complaint->submitted_at->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            @php
                                $complainantAttachments = $complaint->attachments->whereNull('respondent_response_id');
                            @endphp
                            @if($complainantAttachments->count() > 0)
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Complainant Attachments</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                    @foreach ($complainantAttachments as $attachment)
                        <div class="me-3 mb-3" style="display: inline-block;">
                            @php
                                $extension = strtolower($attachment->file_type);
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                            @endphp

                            @if($isImage)
                                <img src="{{ asset($attachment->file_path) }}"
                                    alt="Attachment" class="img-thumbnail" style="max-width: 150px; cursor: pointer;"
                                    data-bs-toggle="modal" data-bs-target="#imageModal{{ $attachment->id }}">

                                <!-- Modal -->
                                <div class="modal fade" id="imageModal{{ $attachment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-body text-center">
                                                <img src="{{ asset($attachment->file_path) }}"
                                                    alt="Full Attachment" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ asset($attachment->file_path) }}" class="btn btn-outline-primary" target="_blank">
                                    <i class="bi bi-file-earmark"></i> Download {{ strtoupper($attachment->file_type) }} File
                                </a>
                            @endif
                        </div>
                    @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Investigation History</h5>
                                </div>
                                <div class="card-body">
                                    @if($complaint->investigationLogs->count() > 0)
                                        <div class="timeline">
                                            @foreach($complaint->investigationLogs as $log)
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-primary"></div>
                                                <div class="timeline-content">
                                                    <div class="mb-2">
                                                        <small class="text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                    </div>
                                                    <p class="mb-2">{{ $log->note }}</p>
                                                    @if($log->next_steps)
                                                    <div class="bg-light p-3 rounded">
                                                        <h6 class="mb-2">Next Steps:</h6>
                                                        <p class="mb-0">{{ $log->next_steps }}</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No investigation logs yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response History and Form -->
            <div id ='responses' class="card mb-4">
            @if($complaint->status!='resolved')

                <div class="card-header bg-white">
                    <h5 class="mb-0">Your Responses</h5>
                </div>
                <div class="card-body">
                    <!-- Response Form -->
                    <form action="{{ route('cast_member.complaints.respond', $complaint->id) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="response" class="form-label">Add New Response</label>
                            <textarea class="form-control @error('response') is-invalid @enderror"
                                id="response" name="response" rows="4"
                                placeholder="Enter your response here..."
                                required>{{ old('response') }}</textarea>
                            @error('response')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment (optional)</label>
                            <input type="file" class="form-control @error('attachment') is-invalid @enderror"
                                id="attachment" name="attachment">
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max file size: 10MB</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Update Status</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                                <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submit Response</option>
                                <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>Request Review</option>
                                <option value="escalated" {{ old('status') == 'escalated' ? 'selected' : '' }}>Escalate</option>
                                <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>Mark as Resolved</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Submit Response
                            </button>
                        </div>
                    </form>
                @endif
                    <!-- Response History -->
                    <h6 class="mb-3">Response History</h6>
                    @if($responses->count() > 0)
                        <div class="timeline">
                            @foreach($responses as $response)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <div class="mb-2">
                                            <small class="text-muted">{{ $response->responded_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <p class="mb-2">{{ $response->response }}</p>
                                        @if($complaint->attachments->where('created_at', $response->created_at)->count() > 0)
                                            <div class="bg-light p-3 rounded">
                                                <h6 class="mb-2">Attachments:</h6>
                                                <div class="row g-2">
                                                    @foreach($complaint->attachments->where('created_at', $response->created_at) as $attachment)
                                                        <div class="col-auto">
                                                            @php
                                                                $extension = strtolower($attachment->file_type);
                                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                                            @endphp
                                                            @if($isImage)
                                                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal{{ $attachment->id }}">
                                                                    <img src="{{ asset($attachment->file_path) }}"
                                                                        alt="{{ $attachment->original_name }}"
                                                                        class="img-thumbnail" style="height: 60px;">
                                                                </a>
                                                            @else
                                                                <a href="#"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-download me-1"></i>
                                                                    {{ $attachment->original_name }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No responses yet.</p>
                    @endif
                </div>
            </div>

            <!-- Image Modals -->
            @foreach($complaint->attachments as $attachment)
                @php
                    $extension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                @endphp
                @if($isImage)
                <div class="modal fade" id="imageModal{{ $attachment->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $attachment->original_name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <img src="{{ asset($attachment->file_path) }}"
                                    alt="{{ $attachment->original_name }}"
                                    class="img-fluid w-100">
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 3rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
}

.timeline-item:not(:last-child) .timeline-marker::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 1rem;
    bottom: -1.5rem;
    width: 2px;
    margin-left: -1px;
    background-color: #e9ecef;
}

.timeline-content {
    position: relative;
    padding-bottom: 1rem;
}
</style>

<!-- Signature Upload Modal -->
@if($userSignature && $userSignature->signature_path == null)
<div class="modal fade" id="uploadSignatureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Signature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="signatureUploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="signature_id" value="{{ $userSignature ? $userSignature->id : '' }}">
                    <div class="mb-3">
                        <label class="form-label">Upload your signature (Image file)</label>
                        <input type="file" class="form-control" name="signature" accept="image/*" required>
                        <div class="form-text">Please upload a clear image of your signature (JPEG, PNG, JPG)</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadSignature()">Upload</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@section('scripts')
<script>
function uploadSignature() {
    const form = document.getElementById('signatureUploadForm');
    const formData = new FormData(form);

    fetch('{{ route("resolution.upload-signature") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            if (data.path) {
                location.reload();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error uploading signature');
    });
}
</script>
@endsection
