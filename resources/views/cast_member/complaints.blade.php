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

    .table td,
    .table th {
        vertical-align: middle;
    }

    .complaints-table {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .pagination {
        margin: 0;
        justify-content: end;
    }

    .pagination li {
        margin: 2px;
    }

    .pagination span {
        background: transparent;
    }

    .pagination .page-item:not(:first-child) .page-link {
        border-radius: 4px;
    }

    .pagination .disabled>.page-link,
    .page-link.disabled {
        background-color: transparent;
    }

    /* Dropdown menu styles */
    .dropdown-menu {
        margin-top: 0.5rem;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>

@extends('layouts.cast_member')

@section('title', 'Complaints')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">All Complaints</h4>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#createCastMemberComplaintModal">
                            <i class="bi bi-plus-lg"></i>
                            New Complaint
                        </button>

                        <!-- Create Complaint Modal -->
                        <div class="modal fade  " id="createCastMemberComplaintModal" data-bs-backdrop="static"
                            tabindex="-1" aria-labelledby="createCastMemberComplaintModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createCastMemberComplaintModalLabel">Create New
                                            Complaint</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="createComplaintForm" action="{{ route('complaints.store') }}"
                                            method="POST" enctype="multipart/form-data" class="needs-validation"
                                            novalidate>
                                            @csrf
                                            <div class="row g-3">

                                                {{-- Name --}}
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        id="name" name="name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Email --}}
                                                <div class="col-md-6">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email" value="{{ old('email') }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Phone Number --}}
                                                <div class="col-md-6">
                                                    <label for="phone" class="form-label">Phone Number</label>
                                                    <input type="tel"
                                                        class="form-control @error('phone_number') is-invalid @enderror"
                                                        id="phone" name="phone_number"
                                                        value="{{ old('phone_number') }}">
                                                    @error('phone_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Location --}}
                                                <div class="col-md-6">
                                                    <label for="location" class="form-label">Location</label>
                                                    <input type="text"
                                                        class="form-control @error('location') is-invalid @enderror"
                                                        id="location" name="location" value="{{ old('location') }}"
                                                        required>
                                                    @error('location')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Complaint Type --}}
                                                <div class="col-md-6">
                                                    <label for="complaint_type" class="form-label">Complaint Type</label>
                                                    <select
                                                        class="form-select @error('complaint_type') is-invalid @enderror"
                                                        id="complaint_type" name="complaint_type" required>
                                                        <option value="">Choose...</option>
                                                        <option value="service"
                                                            {{ old('complaint_type') == 'service' ? 'selected' : '' }}>
                                                            Service Related</option>
                                                        <option value="product"
                                                            {{ old('complaint_type') == 'product' ? 'selected' : '' }}>
                                                            Product Related</option>
                                                        <option value="staff"
                                                            {{ old('complaint_type') == 'staff' ? 'selected' : '' }}>Staff
                                                            Behavior</option>
                                                        <option value="other"
                                                            {{ old('complaint_type') == 'other' ? 'selected' : '' }}>Other
                                                        </option>
                                                    </select>
                                                    @error('complaint_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Anonymity --}}
                                                <div class="col-md-6">
                                                    <div id="other_issue_field" class="form-group" style="display: none;">
                                                        <label for="other_issue">Anonymity</label>
                                                        <input type="text" id="other_issue" name="anonymity"
                                                            class="form-control @error('anonymity') is-invalid @enderror"
                                                            value="{{ old('anonymity') }}">
                                                        @error('anonymity')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Description --}}
                                                <div class="col-12">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                        rows="4" required>{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Attachments --}}
                                                <div class="col-12">
                                                    <label class="form-label">Attachments</label>
                                                    <div class="upload-box @error('attachments.*') is-invalid @enderror"
                                                        id="dropZone">
                                                        <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                                        <p class="mb-1">Drag and drop files here or click to browse</p>
                                                        <p class="text-muted small mb-0">Maximum file size: 50MB per file
                                                        </p>
                                                        <input type="file" id="fileInput" name="attachments[]"
                                                            class="d-none"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mov,.avi,.wmv"
                                                            multiple>
                                                    </div>
                                                    @error('attachments.*')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                    <div id="selectedFiles" class="mt-3"></div>
                                                </div>

                                            </div>

                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Submit Complaint</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item {{ !request('status') ? 'active' : '' }}"
                                        href="{{ route('cast_member.complaints') }}">All ({{ $counts['all'] }})</a></li>
                                <li><a class="dropdown-item {{ request('status') == 'escalated' ? 'active' : '' }}"
                                        href="{{ route('cast_member.complaints', ['status' => 'escalated']) }}">In
                                        Progress
                                        ({{ $counts['escalated'] }})</a></li>
                                <li><a class="dropdown-item {{ request('status') == 'under_review' ? 'active' : '' }}"
                                        href="{{ route('cast_member.complaints', ['status' => 'under_review']) }}">Under
                                        Review ({{ $counts['under_review'] }})</a></li>
                                <li><a class="dropdown-item {{ request('status') == 'resolved' ? 'active' : '' }}"
                                        href="{{ route('cast_member.complaints', ['status' => 'resolved']) }}">Resolved
                                        ({{ $counts['resolved'] }})</a></li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-sort-down"></i> Sort
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item {{ request('sort') == 'newest' || !request('sort') ? 'active' : '' }}"
                                        href="{{ route('cast_member.complaints', array_merge(request()->query(), ['sort' => 'newest'])) }}">Newest
                                        First</a></li>
                                <li><a class="dropdown-item {{ request('sort') == 'oldest' ? 'active' : '' }}"
                                        href="{{ route('cast_member.complaints', array_merge(request()->query(), ['sort' => 'oldest'])) }}">Oldest
                                        First</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="complaints-table p-4">
                    <div class="card-body p-0">
                        <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                            <table class="table mb-0" style="min-width: 950px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Case</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($complaints as $complaint)
                                        <tr>
                                            <td>{{ $complaint->case_number }}</td>
                                            <td>{{ $complaint->name }}</td>
                                            <td>{{ ucfirst($complaint->complaint_type) }}</td>
                                            <td>{{ Str::limit($complaint->description, 50) }}</td>
                                            <td>
                                                @php
                                                    $displayStatus = $complaint->display_status;
                                                    $statusColor = match ($displayStatus) {
                                                        'submitted' => 'primary',
                                                        'under_review' => 'info',
                                                        'escalated' => 'warning',
                                                        'resolved' => 'success',
                                                        'closed' => 'secondary',
                                                        'awaiting_signature' => 'warning',
                                                        default => 'primary',
                                                    };
                                                @endphp
                                                <span
                                                    class="badge-status awaiting_signature bg-{{ $statusColor }} text-white">
                                                    @if ($displayStatus === 'awaiting_signature')
                                                        <i class="fas fa-signature"></i>
                                                        <span>Awaiting Signatures</span>
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $displayStatus == 'escalated' ? 'progress' : $displayStatus)) }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    {{-- <a href="{{ route('cast_member.complaints.show', $complaint->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a> --}}
                                                    <a href="{{ route('cast_member.complaints.show', $complaint['id']) }}"
                                                        class="btn btn-sm btn-outline-primary btn-outline-info"
                                                        title="View" style="padding: 7px;">
                                                        <i class="bi bi-eye"></i>
                                                        {{-- <span class="visually-hidden">View</span> --}}
                                                        <span class="visually-hidden">view</span>
                                                    </a>


                                                    <a href="{{ route('cast_member.complaints.show', $complaint->id) }}#responses"
                                                        class="btn btn-sm btn-success"
                                                        title="{{ $complaint->responses->count() > 0 ? 'View Response' : 'Add Response' }}"
                                                        style="padding: 7px;">
                                                        @if ($complaint->responses->count() > 0)
                                                            <i class="bi bi-chat-square-text"></i>
                                                        @else
                                                            <i class="bi bi-plus-circle"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">No complaints found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($complaints->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-end mt-3">
                                {{ $complaints->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createCastMemberComplaintModal" tabindex="-1"
        aria-labelledby="createCastMemberComplaintModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCastMemberComplaintModal">Create New Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createComplaintForm" action="{{ route('complaints.store') }}" method="POST"
                        enctype="multipart/form-data" class="needs-validation" novalidate>
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
                                <label for="phone" class="form-label">Phone Number(Optional)</label>
                                <input type="tel" class="form-control" id="phone" name="phone_number">
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            <div class="col-12">
                                <label for="complaint_type" class="form-label">Complaint Type</label>
                                <select class="form-select" id="complaint_type" name="complaint_type" required>
                                    <option value="">Choose...</option>
                                    <option value="service">Service Related</option>
                                    <option value="product">Product Related</option>
                                    <option value="staff">Staff Behavior</option>
                                    <option value="other">Other</option>
                                </select>
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
                                    <input type="file" id="fileInput" name="attachments[]" class="d-none"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mov,.avi,.wmv" multiple>
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
    <style>
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
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('createCastMemberComplaintModal'));
                myModal.show();
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });


        $(document).ready(function() {
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
    </script>
@endsection
