@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.complaints') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i>
                        Back to Complaints
                    </a>
                    <h1 class="h3 mb-0">Edit Complaint</h1>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Case Information -->
                        <div class="p-4 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-folder text-primary fs-4 me-2"></i>
                                <h5 class="card-title mb-0">Case Information</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Case Number</label>
                                    <input type="text" class="form-control" value="{{ $complaint->case_number }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Complainant Details -->
                        <div class="p-4 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-person text-primary fs-4 me-2"></i>
                                <h5 class="card-title mb-0">Complainant Details</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $complaint->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $complaint->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                           value="{{ old('phone_number', $complaint->phone_number) }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Location</label>
                                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                           value="{{ old('location', $complaint->location) }}" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Complaint Details -->
                        <div class="p-4 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-chat-square-text text-primary fs-4 me-2"></i>
                                <h5 class="card-title mb-0">Complaint Details</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Complaint Type</label>
                                    <select name="complaint_type" class="form-select @error('complaint_type') is-invalid @enderror" required>
                                        <option value="service" {{ $complaint->complaint_type === 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="facility" {{ $complaint->complaint_type === 'facility' ? 'selected' : '' }}>Facility</option>
                                        <option value="staff" {{ $complaint->complaint_type === 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="safety" {{ $complaint->complaint_type === 'safety' ? 'selected' : '' }}>Safety</option>
                                        <option value="other" {{ $complaint->complaint_type === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('complaint_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $complaint->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>  

                        <!-- Current Attachments -->
                        @if($complaint->attachments->count() > 0)
                        <div class="p-4 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-paperclip text-primary fs-4 me-2"></i>
                                <h5 class="card-title mb-0">Current Attachments</h5>
                            </div>
                            <div class="row g-3">
                            @foreach($complaint->attachments as $attachment)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-2 border rounded">
                                            @php
                                                $extension = strtolower($attachment->file_type);
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp

                                            @if($isImage)
                                                <img src="{{ asset($attachment->file_path) }}"  
                                                    alt="Attachment" class="img-thumbnail me-2" style="max-width: 50px; max-height: 50px">
                                            @else
                                                <i class="bi bi-file-earmark-text fs-3 me-2"></i>
                                            @endif

                                            <div class="flex-grow-1">
                                                <div class="small fw-medium">{{ basename($attachment->file_path) }}</div>
                                                <div class="small text-muted">{{ strtoupper($attachment->file_type) }} file</div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/attachments/' . basename($attachment->file_path)) }}" 
                                                class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.attachments.delete', $attachment->id) }}"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this attachment?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <form id="delete-attachment-{{ $attachment->id }}" 
                                        action="{{ route('admin.attachments.delete', $attachment->id) }}" 
                                        method="POST" style="display: block;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                                    </form> -->
                                @endforeach

                            </div>
                        </div>
                        @endif

                        <!-- New Attachments -->
                        <div class="p-4 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-upload text-primary fs-4 me-2"></i>
                                <h5 class="card-title mb-0">Add New Attachments</h5>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="attachments[]" multiple 
                                       class="form-control @error('attachments.*') is-invalid @enderror" 
                                       accept=".jpg,.jpeg,.png,.gif,.mp4,.mov,.avi,.wmv,.pdf,.doc,.docx">
                                <div class="form-text">Supported files: Images (JPG, PNG, GIF), Videos (MP4, MOV, AVI, WMV), Documents (PDF, DOC, DOCX)</div>
                                <div class="form-text text-muted">Maximum file size: 50MB per file</div>
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Assigned Respondents -->
                        <div class="p-4 bg-light rounded-3 border mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-people text-primary fs-4 me-2"></i>
                                <h5 class="card-title mb-0">Assigned Respondents</h5>
                            </div>
                            <div class="mb-3">
                                <select name="respondent_ids[]" class="form-select @error('respondent_ids') is-invalid @enderror" multiple>
                                    @foreach($respondents as $respondent)
                                        <option value="{{ $respondent->id }}" 
                                            {{ $complaint->respondents->where('user_id', $respondent->id)->count() > 0 ? 'selected' : '' }}>
                                            {{ $respondent->name }} ({{ $respondent->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('respondent_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Hold Ctrl (Windows) or Command (Mac) to select multiple respondents</div>
                            </div>
                            
                            @if($complaint->respondents->count() > 0)
                                <div class="mt-3">
                                    <h6 class="mb-2">Currently Assigned:</h6>
                                    <div class="list-group">
                                        @foreach($complaint->respondents as $complaintRespondent)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $complaintRespondent->user->name }}</h6>
                                                    <small class="text-muted">{{ $complaintRespondent->user->email }}</small>
                                                </div>
                                                @if($complaintRespondent->responded_at)
                                                    <span class="badge bg-success">Responded</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Attachments -->
                        

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.complaints') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Complaint</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(attachmentId) {
    alert(attachmentId);
    if (confirm('Are you sure you want to delete this attachment?')) {
        const form = document.getElementById('delete-attachment-' + 33);
        if (form) {
            form.submit();
        } else {
            alert('Error: Could not find delete form');
        }
    }
}
</script>

<style>
/* Form styles */
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control:focus, .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.1);
}

.form-control:read-only {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

/* Card styles */
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.card-body {
    padding: 2rem;
}

/* Section styles */
.bg-light {
    background-color: #f8f9fa !important;
}

.border {
    border-color: #dee2e6 !important;
}

/* Text styles */
.text-muted {
    color: #6c757d !important;
}

.fw-medium {
    font-weight: 500 !important;
}

/* Button styles */
.btn-primary {
    background-color: #2563eb;
    border-color: #2563eb;
}

.btn-primary:hover {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
}

.btn-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.btn-light:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
@endsection
