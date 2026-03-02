@extends('layouts.admin')

@section('title', 'Complaints Management')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')

    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4 complaint-header">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Complaints Management</h1>
                <p class="text-muted mb-0">Track and manage all complaints in the system</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="search-box">
                    <input type="text" id="searchComplaints" class="form-control" placeholder="Search complaints..." aria-label="Search complaints">
                </div>
                <div class="filter-box">
                    <select id="statusFilter" class="form-select" aria-label="Filter by status">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Complaints</option>
                        @foreach($stages as $stage)
                        <option value="{{ $stage->name }}" {{ $status == $stage->name ? 'selected' : '' }}>
                            {{ $stage->name }} ({{ $counts[$stage->name] ?? 0 }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createComplaintModal">
                    <i class="bi bi-plus-lg"></i>
                    New Complaint
                </button>

                <!-- Create Complaint Modal -->
                <div class="modal fade" id="createComplaintModal" tabindex="-1" aria-labelledby="createComplaintModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createComplaintModalLabel">Create New Complaint</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="createComplaintForm" action="{{ route('admin.complaints.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone_number">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control" id="location" name="location" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="complaint_type" class="form-label">Complaint Type</label>
                                            <select class="form-select" id="complaint_type" name="complaint_type" required>
                                                <option value="">Choose...</option>
                                                <option value="service">Service Related</option>
                                                <option value="product">Product Related</option>
                                                <option value="staff">Staff Behavior</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                        <div id="other_issue_field" class="form-group" style="display: none;">
                                                <label for="other_issue">Anonymity</label>
                                                <input type="text" id="other_issue" name="anonymity" class="form-control @error('other_issue') is-invalid @enderror" value="{{ old('other_issue') }}">
                                                @error('other_issue')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                    </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="respondent_id" class="form-label fw-medium">Assign To</label>
                                                <select class="form-select" id="respondent_id" name="respondent_id" required>
                                                    <option value="">Select respondent</option>
                                                    @foreach($respondents as $respondent)
                                                    <option value="{{ $respondent->id }}">{{ $respondent->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Attachments</label>
                                            <div class="upload-box" id="dropZone">
                                                <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                                <p class="mb-1">Drag and drop files here or click to browse</p>
                                                <p class="text-muted small mb-0">Maximum file size: 50MB per file</p>
                                                <input type="file" id="fileInput" name="attachments[]" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mov,.avi,.wmv" multiple>
                                            </div>
                                            <div id="selectedFiles" class="mt-3"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Submit Complaint</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif


        <div class="complaints-table p-4" style="margin-top: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                <table class="table" style="min-width: 1500px;">
                    <thead>
                        <tr>
                            <th>Case #</th>
                            <th>Complainant</th>
                            <th>Submitted As</th>
                            <th>Subject</th>
                            <th>Complaint About</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Evidence</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                        <tr class="complaint-row" data-href="{{ route('admin.complaints.show', $complaint) }}" style="cursor: pointer;">
                            <td class="text-nowrap">{{ $complaint->case_number }}</td>
                            <td>
                                @if($complaint->is_anonymous)
                                    <span class="badge bg-warning text-dark">Anonymous</span>
                                @else
                                    <div>
                                        <strong>{{ $complaint->name ?: 'N/A' }}</strong>
                                        @if($complaint->email)
                                            <br><small class="text-muted">{{ $complaint->email }}</small>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <span class="badge bg-{{ $complaint->submitted_as == 'cast_member' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $complaint->submitted_as)) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($complaint->description, 40) }}</td>
                            <td>{{ Str::limit($complaint->complaint_about ?: 'N/A', 30) }}</td>
                            <td class="text-nowrap">{{ ucwords($complaint->complaint_type) }}</td>
                            <td class="text-nowrap">{{ $complaint->location }}</td>
                            <td>
                                @if($complaint->evidence_type && $complaint->evidence_type !== 'no_evidence')
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst(str_replace('_', ' ', $complaint->evidence_type)) }}
                                    </span>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                    <span class="badge" style="background-color: {{ @$complaint->stage->color }}; color: white;">
                                        {{ @$complaint->stage->name }}
                                    </span>
                            </td>
                            <td class="text-nowrap">{{ $complaint->created_at->format('M d, Y') }}</td>
                            <td class="action-cell">
                                <!-- <div class="btn-group action-btns" role="group" aria-label="Complaint actions">
                                    @if($complaint->investigationLogs->count() > 0)
                                    <a href="{{ route('admin.complaints.investigation-history', $complaint) }}"
                                       class="btn btn-outline-primary btn-sm position-relative"
                                       title="View Investigation History"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-clock-history"></i>
                                        <span>History</span>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                            {{ $complaint->investigationLogs->count() }}
                                            <span class="visually-hidden">investigation updates</span>
                                        </span>
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-info d-flex align-items-center gap-1 justify-content-center">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </a>
                                    @if($complaint->submitted_by_admin_id && $complaint->status !== 'resolved')
                                    <a href="{{ route('admin.complaints.edit', $complaint) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 justify-content-center">
                                        <i class="bi bi-pencil"></i>
                                        <span>Edit</span>
                                    </a>
                                    @endif
                                    @if($complaint->respondents->isEmpty())
                                    <button class="btn btn-primary btn-sm add-respondent-btn" data-complaint-id="{{ $complaint->id }}">
                                        <i class="bi bi-person-plus"></i>
                                        <span>Add Respondent</span>
                                    </button>
                                    @endif
                                    @if($complaint->lawyers->isEmpty())
                                    <button class="btn btn-success btn-sm add-lawyer-btn" data-complaint-id="{{ $complaint->id }}">
                                        <i class="bi bi-person-plus-fill"></i>
                                        <span>Add Lawyer</span>
                                    </button>
                                    @endif
                                    @if($complaint->respondents->isNotEmpty() && $complaint->investigationLogs->count() == 0)
                                    <button type="button" onclick="openInvestigationModal({{ $complaint->id }})" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"style="border-radius:4px;">
                                        <i class="bi bi-search"></i>
                                        <span>Investigate</span>
                                    </button>
                                    @endif
                                    <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                                            <i class="bi bi-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div> -->
                                <div class="btn-group action-btns" role="group" aria-label="Complaint actions">
                                    @if($complaint->investigationLogs->count() > 0)
                                    <a href="{{ route('admin.complaints.investigation-history', $complaint) }}"
                                       class="btn btn-outline-primary btn-sm position-relative"
                                       title="View Investigation History"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-clock-history"></i>
                                        {{-- <span>History</span> --}}
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                            {{ $complaint->investigationLogs->count() }}
                                            <span class="visually-hidden">investigation updates</span>
                                        </span>
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-info d-flex align-items-center gap-1 justify-content-center" title="View"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                        {{-- <span class="visually-hidden">View</span> --}}
                                        <span class="visually-hidden">view</span>
                                    </a>
                                    @if($complaint->submitted_by_admin_id && $complaint->status !== 'resolved')
                                    <a href="{{ route('admin.complaints.edit', $complaint) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 justify-content-center" title="Edit"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>

                                    </a>
                                    <span class="visually-hidden">Edit</span>
                                    @endif
                                    {{-- @if($complaint->respondents->isEmpty())
                                    <button class="btn btn-primary btn-sm add-respondent-btn" data-complaint-id="{{ $complaint->id }}" title="Add Respondent"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-person-plus"></i>

                                    </button>
                                    <span class="visually-hidden">Add Respondent</span>
                                    @endif --}}
                                    @if($complaint->respondents->isNotEmpty() && $complaint->investigationLogs->count() == 0)
                                    <button type="button" onclick="openInvestigationModal({{ $complaint->id }})" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"style="border-radius:4px;" title="Investigate"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-search"></i>

                                    </button>
                                    <span class="visually-hidden">Investigate</span>
                                    @endif
                                    <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" title="Delete"
                                       data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <span class="visually-hidden">Delete</span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox-fill fs-1 mb-3 d-block"></i>
                                    <p class="mb-1">No complaints found</p>
                                    <p class="small text-muted mb-0">{{ $status == 'submitted' ? 'No new complaints have been submitted yet.' :
                                        ($status == 'under_review' ? 'No complaints are currently under review.' :

                                        ($status == 'resolved' ? 'No complaints have been resolved yet.' : 'No complaints have been closed yet.')) }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-4">
                {{ $complaints->links() }}
            </div>
        </div>

    <!-- New Complaint Modal -->
    <div class="modal fade" id="createComplaintModal" tabindex="-1" aria-labelledby="createComplaintModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="createComplaintModalLabel">
                        Create New Complaint
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.complaints.store') }}" method="POST" id="createComplaintForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="submitted_by_admin" value="1">
                        <input type="hidden" name="submitted_by_admin_id" value="{{ auth()->id() }}">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label fw-medium">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="name" name="name" placeholder="Enter complainant name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label fw-medium">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="Enter email address" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="form-label fw-medium">Phone Number(optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-telephone"></i>
                                        </span>
                                        <input type="tel" class="form-control border-start-0 ps-0" id="phone_number" name="phone_number" placeholder="Enter phone number">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location" class="form-label fw-medium">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-geo-alt"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="location" name="location" placeholder="Enter location" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="complaint_type" class="form-label fw-medium">Complaint Type</label>
                                    <select class="form-select" id="complaint_type" name="complaint_type" required>
                                        <option value="">Select complaint type</option>
                                        <option value="service">Service</option>
                                        <option value="facility">Facility</option>
                                        <option value="staff">Staff</option>
                                        <option value="safety">Safety</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-6">
                            <div id="other_issue_field" class="form-label fw-medium" style="display: none;">
                                    <label for="other_issues" class="form-label fw-medium">Anonymity</label>
                                    <input type="text" id="other_issue" name="anonymity" class="form-control @error('anonymity') is-invalid @enderror" value="{{ old('anonymity') }}">
                                @error('anonymity')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="respondent_id" class="form-label fw-medium">Assign To</label>
                                    <select class="form-select" id="respondent_id" name="respondent_id" required>
                                        <option value="">Select respondent</option>
                                        @foreach($respondents as $respondent)
                                        <option value="{{ $respondent->id }}">{{ $respondent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label fw-medium">Description</label>

                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter complaint description" required></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="attachments" class="form-label fw-medium">Photos, Videos, Evidence (Multiple files allowed)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-paperclip"></i>
                                        </span>
                                        <input type="file" class="form-control border-start-0 ps-0 @error('attachments.*') is-invalid @enderror"
                                               id="attachments" name="attachments[]" multiple
                                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.mp4,.mov,.avi,.wmv">
                                    </div>
                                    <div id="selected-files" class="mt-2"></div>
                                    @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light fw-medium" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" form="createComplaintForm" class="btn btn-primary fw-medium">
                        <i class="bi bi-check2 me-1"></i>
                        Create Complaint
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Investigation Modal -->
<div class="modal fade" id="investigationModal" tabindex="-1" aria-labelledby="investigationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="investigationModalLabel">Investigation Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="investigationForm" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" id="investigationComplaintId" name="complaint_id">

                    <div class="mb-3">
                        <label for="subject" class="form-label text-muted">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                        <div class="invalid-feedback">Please enter a subject</div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label text-muted">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select status</option>
                            <option value="escalated">Progress</option>
                            <option value="under_review">Under Review</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                        <div class="invalid-feedback">Please select a status</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label text-muted">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="next_steps" class="form-label text-muted">Next Steps</label>
                        <textarea class="form-control" id="next_steps" name="next_steps" rows="3"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-2"></i>Submit Update
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Respondent Modal -->
    <div class="modal" id="respondentModal" tabindex="-1" role="dialog" aria-labelledby="respondentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="respondentModalLabel">Add Respondent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="respondentForm">
                        @csrf
                        <input type="hidden" id="complaintId" name="complaint_id">
                        <div class="mb-3">
                            <label for="respondentId" class="form-label">Select Respondent</label>
                            <select class="form-select" id="respondentId" name="user_id" required>
                                <option value="">Choose a respondent...</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Select a user from the respondents list to assign them to this complaint.</div>
                        </div>

                        <div class="mb-3">
                            <label for="stageSelect" class="form-label">Change Stage (Required)</label>
                            <select class="form-select" id="stageSelect" name="stage_id" required>
                                <option value="">Select a stage...</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">
                                        Step {{ $stage->step_number }}: {{ $stage->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Optionally change the complaint stage when adding respondent.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveRespondent()">Save Respondent</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lawyer Modal -->
    <div class="modal" id="lawyerModal" tabindex="-1" role="dialog" aria-labelledby="lawyerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lawyerModalLabel">Add Lawyer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="lawyerForm">
                        @csrf
                        <input type="hidden" id="lawyerComplaintId" name="complaint_id">
                        <div class="mb-3">
                            <label for="lawyerId" class="form-label">Select Lawyer</label>
                            <select class="form-select" id="lawyerId" name="user_id" required>
                                <option value="">Choose a lawyer...</option>
                                @foreach($lawyers as $lawyer)
                                <option value="{{ $lawyer->id }}">{{ $lawyer->name }} ({{ $lawyer->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Select a user from the lawyers list to assign them to this complaint.</div>
                        </div>
                        <div class="mb-3">
                            <label for="lawyerStageSelect" class="form-label">Change Stage (Required)</label>
                            <select class="form-select" id="lawyerStageSelect" name="stage_id" required>
                                <option value="">Select a stage...</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}">
                                        Step {{ $stage->step_number }}: {{ $stage->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Optionally change the complaint stage when adding lawyer.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="saveLawyer()">Save Lawyer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery first, then Bootstrap JS Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .complaint-row:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .complaint-row:hover td {
            background-color: transparent;
        }

        .filter-box .form-select {
            min-width: 200px;
        }

        .search-box .form-control {
            min-width: 250px;
        }

        .upload-box {
            border: 2px dashed #e5e7eb;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-box:hover {
            border-color: #2563eb;
            background: #f8fafc;
        }

        .upload-icon {
            font-size: 2rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .selected-file {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            background: white;
        }

        .file-preview {
            width: 40px;
            height: 40px;
            margin-right: 1rem;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
        }

        .file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-preview.video {
            background: #1f2937;
            color: white;
        }

        .file-info {
            flex: 1;
        }

        .file-name {
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .file-size {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .remove-file {
            padding: 0.25rem 0.5rem;
            color: #6b7280;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
        }

        .remove-file:hover {
            color: #ef4444;
        }
    </style>

    <script>
        $(document).ready(function () {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const selectedFiles = document.getElementById('selectedFiles');
            let currentFiles = new DataTransfer();

            // Handle click on drop zone
            dropZone.addEventListener('click', () => fileInput.click());

            // Handle drag and drop
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#2563eb';
                dropZone.style.backgroundColor = '#f8fafc';
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.style.borderColor = '#e5e7eb';
                dropZone.style.backgroundColor = 'white';
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#e5e7eb';
                dropZone.style.backgroundColor = 'white';

                if (e.dataTransfer.files.length) {
                    handleFiles(e.dataTransfer.files);
                }
            });

            // Handle file input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length) {
                    handleFiles(e.target.files);
                }
            });

            function handleFiles(files) {
                Array.from(files).forEach(file => {
                    // Check file size (50MB limit)
                    if (file.size > 50 * 1024 * 1024) {
                        alert(`File ${file.name} is larger than 50MB`);
                        return;
                    }

                    // Check if file already exists
                    const existingFile = Array.from(currentFiles.files).find(f =>
                        f.name === file.name && f.size === file.size
                    );

                    if (!existingFile) {
                        // Add file to current files
                        currentFiles.items.add(file);
                    }
                });

                // Update file input
                fileInput.files = currentFiles.files;

                // Show previews
                updateFilePreview();
            }

            function updateFilePreview() {
                selectedFiles.innerHTML = '';
                const files = currentFiles.files;

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
                    selectedFiles.appendChild(fileDiv);

                    // Add click handler for remove button
                    const removeBtn = fileDiv.querySelector('.remove-file');
                    removeBtn.addEventListener('click', function() {
                        const newFiles = new DataTransfer();
                        Array.from(currentFiles.files)
                            .filter((_, i) => i !== index)
                            .forEach(f => newFiles.items.add(f));

                        currentFiles = newFiles;
                        fileInput.files = currentFiles.files;
                        updateFilePreview();
                    });
                });
            }
            function toggleOtherField() {
                const $otherField = $('#other_issue_field');
                const $otherInput = $('#other_issue');

                if ($('#complaint_type').val() === 'other') {
                    $otherField.show();
                    $otherInput.prop('required', true);
                } else {
                    $otherField.hide();
                    $otherInput.prop('required', false).val('');
                }
            }

            // Initial check
            toggleOtherField();

            // Bind to correct select ID
            $('#complaint_type').on('change', toggleOtherField);
        });
        // File upload preview
        // File input handling
        const fileInput = document.getElementById('attachments');
        const selectedFiles = document.getElementById('selected-files');

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            else return (bytes / 1048576).toFixed(1) + ' MB';
        }

        fileInput.addEventListener('change', function() {
            selectedFiles.innerHTML = '';
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach((file, index) => {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'alert alert-light d-flex align-items-center justify-content-between p-2 mb-2';

                    const isImage = file.type.startsWith('image/');
                    const fileSize = formatFileSize(file.size);

                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'd-flex align-items-center gap-2';

                    if (isImage) {
                        const preview = document.createElement('div');
                        preview.style.width = '40px';
                        preview.style.height = '40px';
                        preview.style.overflow = 'hidden';
                        preview.style.borderRadius = '4px';

                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';

                        preview.appendChild(img);
                        fileInfo.appendChild(preview);
                    } else {
                        const icon = document.createElement('i');
                        icon.className = file.name.endsWith('.pdf') ? 'bi bi-file-pdf text-danger fs-4' :
                                        file.name.endsWith('.doc') || file.name.endsWith('.docx') ? 'bi bi-file-word text-primary fs-4' :
                                        'bi bi-file-text fs-4';
                        fileInfo.appendChild(icon);
                    }

                    const textDiv = document.createElement('div');
                    textDiv.innerHTML = `
                        <div class="text-truncate" style="max-width: 200px;">${file.name}</div>
                        <small class="text-muted">${fileSize}</small>
                    `;
                    fileInfo.appendChild(textDiv);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-link text-danger p-0';
                    removeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
                    removeBtn.onclick = function() {
                        const dt = new DataTransfer();
                        const files = fileInput.files;

                        for (let i = 0; i < files.length; i++) {
                            if (i !== index) {
                                dt.items.add(files[i]);
                            }
                        }

                        fileInput.files = dt.files;
                        if (isImage) {
                            URL.revokeObjectURL(img.src);
                        }
                        fileDiv.remove();
                    };

                    fileDiv.appendChild(fileInfo);
                    fileDiv.appendChild(removeBtn);
                    selectedFiles.appendChild(fileDiv);
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listeners to all Add Respondent buttons
            document.querySelectorAll('.add-respondent-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var complaintId = this.getAttribute('data-complaint-id');
                    openRespondentModal(complaintId);
                });
            });
        });

        function openRespondentModal(complaintId) {
            document.getElementById('complaintId').value = complaintId;
            var modalEl = document.getElementById('respondentModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
            console.log('Opening modal for complaint:', complaintId);
        }

        function closeAlertAndReload() {
            $('.alert').alert('close');
            location.reload();
        }

        function saveRespondent() {
            const complaintId = document.getElementById('complaintId')?.value;
            const respondentId = document.getElementById('respondentId')?.value;
            const stageId = document.getElementById('stageSelect')?.value;

            if (!respondentId || !complaintId) {
                alert('Please select a respondent and ensure complaint ID exists.');
                return;
            }

            if (!stageId) {
                alert('Please select a stage.');
                return;
            }

            // Create a form and submit it to ensure proper email sending and redirects
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/complaints/${complaintId}/add-respondent`;

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            // Add user ID
            const userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'user_id';
            userIdInput.value = respondentId;
            form.appendChild(userIdInput);

            // Add stage ID
            const stageIdInput = document.createElement('input');
            stageIdInput.type = 'hidden';
            stageIdInput.name = 'stage_id';
            stageIdInput.value = stageId;
            form.appendChild(stageIdInput);

            // Submit the form
            document.body.appendChild(form);
            form.submit();
        }

        // Lawyer Functions
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listeners to all Add Lawyer buttons
            document.querySelectorAll('.add-lawyer-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var complaintId = this.getAttribute('data-complaint-id');
                    openLawyerModal(complaintId);
                });
            });
        });

        function openLawyerModal(complaintId) {
            document.getElementById('lawyerComplaintId').value = complaintId;
            var modalEl = document.getElementById('lawyerModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
            console.log('Opening lawyer modal for complaint:', complaintId);
        }

        function saveLawyer() {
            const complaintId = document.getElementById('lawyerComplaintId')?.value;
            const lawyerId = document.getElementById('lawyerId')?.value;
            const stageId = document.getElementById('lawyerStageSelect')?.value;

            if (!lawyerId || !complaintId) {
                alert('Please select a lawyer and ensure complaint ID exists.');
                return;
            }
            if (!stageId) {
                alert('Please select a stage.');
                return;
            }

            // Create a form and submit it to ensure proper redirects
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/complaints/${complaintId}/lawyers`;

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            // Add lawyer IDs (as array for consistency with controller)
            const lawyerIdsInput = document.createElement('input');
            lawyerIdsInput.type = 'hidden';
            lawyerIdsInput.name = 'lawyer_ids[]';
            lawyerIdsInput.value = lawyerId;
            form.appendChild(lawyerIdsInput);

            // Submit the form
            document.body.appendChild(form);
            form.submit();
        }

        // Initialize tooltips and modals
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Debug modal trigger
        document.querySelectorAll('.add-respondent-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                console.log('Add respondent button clicked');
            });
        });

        // Form validation
        (function() {
            'use strict';

            // Fetch all forms with needs-validation class
            var forms = document.querySelectorAll('.needs-validation');

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Make table rows clickable
            document.querySelectorAll('.complaint-row').forEach(function(row) {
                row.addEventListener('click', function(e) {
                    // Don't redirect if clicking on buttons or links in action column
                    if (e.target.closest('.action-cell')) {
                        return;
                    }
                    var url = this.getAttribute('data-href');
                    if (url) {
                        window.location.href = url;
                    }
                });
            });

            // Handle complaint search
            let searchTimeout;
            const searchInput = document.getElementById('searchComplaints');

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchQuery = this.value.trim();
                    const currentUrl = new URL(window.location.href);

                    if (searchQuery) {
                        currentUrl.searchParams.set('search', searchQuery);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }

                    window.location.href = currentUrl.toString();
                }, 500); // Debounce for 500ms
            });

            // Set search input value from URL if exists
            const urlParams = new URLSearchParams(window.location.search);
            const searchValue = urlParams.get('search');
            if (searchValue) {
                searchInput.value = searchValue;
            }
        });

        // Investigation Modal Functions
        function openInvestigationModal(complaintId) {
            document.getElementById('investigationComplaintId').value = complaintId;
            const modal = new bootstrap.Modal(document.getElementById('investigationModal'));
            modal.show();
        }

        document.getElementById('investigationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            $.ajax({
                url: "{{ url('admin/complaints/investigate') }}/" + data.complaint_id,
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>${response.message}</span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>  `;

                            // Add alert to body
                            $(alertHtml).insertAfter('.complaint-header');

                        // Close modal and reset form
                        const modal = bootstrap.Modal.getInstance(document.getElementById('investigationModal'));
                        modal.hide();
                        form.reset();
                        form.classList.remove('was-validated');

                        // Refresh the page to show updated data
                        // location.reload();

                        // Initialize tooltips
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                            new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors || { error: ['An error occurred while saving the update.'] };
                    let errorMessage = '<ul class="mb-0">';
                    Object.values(errors).forEach(error => {
                        errorMessage += `<li>${error}</li>`;
                    });
                    errorMessage += '</ul>';

                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-exclamation-circle-fill fs-4"></i>
                                <strong>Error</strong>
                            </div>
                            ${errorMessage}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    `;
                    $(alertHtml).insertAfter('.complaint-header').delay(5000).fadeOut(function() { $(this).remove(); });
                }
            });
        });
    </script>
    <script>
        // Load signature status for resolved complaints
        function loadSignatureStatus() {
            document.querySelectorAll('.signature-status').forEach(function(element) {
                const complaintId = element.dataset.complaintId;
                const signatureText = element.querySelector('.signature-text');
                const signatureCount = element.querySelector('.signature-count');

                // Add tooltip element if it doesn't exist
                if (!element.querySelector('.signature-tooltip')) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'signature-tooltip';
                    tooltip.textContent = 'Loading signature status...';
                    element.appendChild(tooltip);
                }

                fetch(`{{ route('admin.complaints.signature-status', ['complaint' => '_ID_']) }}`.replace('_ID_', complaintId))
                    .then(response => response.json())
                    .then(data => {
                        signatureText.textContent = `${data.total_signed}/${data.total_required}`;
                        signatureCount.classList.remove('completed', 'pending');
                        signatureCount.classList.add(data.status === 'completed' ? 'completed' : 'pending');

                        // Update tooltip content
                        const tooltip = element.querySelector('.signature-tooltip');
                        if (tooltip) {
                            if (data.status === 'completed') {
                                tooltip.textContent = 'All required signatures are complete';
                            } else {
                                const pendingSigners = data.signatures
                                    .filter(sig => !sig.signed)
                                    .map(sig => `${sig.name} (${sig.role})`)
                                    .join('\n');
                                tooltip.innerHTML = `Pending Signatures:\n${pendingSigners}`;
                                tooltip.style.whiteSpace = 'pre-line';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching signature status:', error);
                        signatureText.textContent = 'Error';
                    });
            });
        }

        // Load signature status when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadSignatureStatus();
        });

        // Handle search and filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchComplaints');
            const statusFilter = document.getElementById('statusFilter');
            let searchTimeout;

            if (searchInput) {
                // Set initial value from URL if exists
                const urlParams = new URLSearchParams(window.location.search);
                const searchValue = urlParams.get('search');
                if (searchValue) {
                    searchInput.value = searchValue;
                }

                // Handle search input
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        updateFilters();
                    }, 500); // Debounce for 500ms
                });
            }

            // Handle status filter change
            if (statusFilter) {
                statusFilter.addEventListener('change', function() {
                    updateFilters();
                });
            }

            function updateFilters() {
                const currentUrl = new URL(window.location.href);
                const searchQuery = searchInput ? searchInput.value.trim() : '';
                const statusValue = statusFilter ? statusFilter.value : 'all';

                // Update URL with parameters
                if (searchQuery) {
                    currentUrl.searchParams.set('search', searchQuery);
                } else {
                    currentUrl.searchParams.delete('search');
                }

                if (statusValue && statusValue !== 'all') {
                    currentUrl.searchParams.set('status', statusValue);
                } else {
                    currentUrl.searchParams.delete('status');
                }

                window.location.href = currentUrl.toString();
            }
        });
    </script>
    @endsection
