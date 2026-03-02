@extends('layouts.admin')

@section('title', 'Investigation History')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.complaints') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i>
                    Back to Complaints
                </a>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#investigationModal">
                <i class="bi bi-plus-lg"></i>
                <span>New Investigation Update</span>
            </button>
        </div>
    </div>
    <div class="mb-3">
        <h1 class="h3 mb-0 text-gray-800">Investigation History</h1>
        <p class="text-muted mb-0">Case Number: {{ $complaint->case_number }}</p>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="timeline">
                @forelse($complaint->investigationLogs()->with('creator')->latest()->get() as $log)
                    <div class="timeline-item">
                        <div class="timeline-content card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ explode("\n\n", $log->note)[0] }}</h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                                        <div class="dropdown">
                                            <button class="btn btn-link btn-sm p-0 text-muted" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button type="button" class="dropdown-item" onclick="editInvestigation({{ $log->id }})">
                                                        <i class="bi bi-pencil me-2"></i>Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger" onclick="deleteInvestigation({{ $log->id }})">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @if(isset(explode("\n\n", $log->note)[1]))
                                    <p class="card-text mb-3">{{ explode("\n\n", $log->note)[1] }}</p>
                                @endif
                                @if($log->next_steps)
                                    <div class="next-steps mt-3">
                                        <h6 class="text-primary mb-2">Next Steps:</h6>
                                        <p class="card-text mb-0">{{ $log->next_steps }}</p>
                                    </div>
                                @endif
                                <div class="mt-3">
                                    <span class="text-muted small">
                                        Updated by: {{ $log->creator->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-x text-muted mb-3" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">No investigation records found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Investigation Modal -->
<div class="modal fade" id="investigationModal" tabindex="-1" aria-labelledby="investigationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="investigationModalLabel">New Investigation Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="investigationForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                        <div class="invalid-feedback">
                            Please provide a subject.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Investigation Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="next_steps" class="form-label">Next Steps</label>
                        <textarea class="form-control" id="next_steps" name="next_steps" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Update Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select status...</option>
                            <option value="under_review">Under Review</option>
                            <option value="escalated">Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a status.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding: 1rem 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 2.5rem;
        margin-bottom: 2rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: -2rem;
        width: 2px;
        background-color: #e5e7eb;
    }

    .timeline-item:last-child::before {
        bottom: 0;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: #3b82f6;
        border: 2px solid #fff;
    }

    .timeline-content {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .next-steps {
        background-color: #f8fafc;
        border-radius: 0.5rem;
        padding: 1rem;
    }
</style>
@endsection

@section('scripts')
<script>
function editInvestigation(logId) {
    // Fetch investigation log data
    $.ajax({
        url: `{{ route('admin.complaints.investigation.show', ['complaint' => $complaint->id, 'log' => '_ID_']) }}`.replace('_ID_', logId),
        method: 'GET',
        success: function(response) {
            // Populate form with existing data
            $('#subject').val(response.subject);
            $('#notes').val(response.note);
            $('#next_steps').val(response.next_steps);
            $('#status').val(response.status);
            
            // Add log ID to form for update
            $('#investigationForm').append(`<input type="hidden" name="log_id" value="${logId}">`);
            
            // Update modal title and button
            $('#investigationModalLabel').text('Edit Investigation Update');
            $('#investigationForm button[type="submit"]').text('Save Changes');
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('investigationModal'));
            modal.show();
        },
        error: function(xhr) {
            const alertHtml = `
                <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
                    <div class="alert alert-danger alert-dismissible fade show shadow" role="alert" style="min-width: 300px;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-exclamation-circle-fill fs-4"></i>
                            <strong>Error</strong>
                        </div>
                        <p class="mb-0">Failed to load investigation details.</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            `;
            $(alertHtml).appendTo('body').delay(5000).fadeOut(function() { $(this).remove(); });
        }
    });
}

function deleteInvestigation(logId) {
    if (confirm('Are you sure you want to delete this investigation update? This action cannot be undone.')) {
        $.ajax({
            url: `{{ route('admin.complaints.investigation.delete', ['complaint' => $complaint->id, 'log' => '_ID_']) }}`.replace('_ID_', logId),
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const alertHtml = `
                    <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
                        <div class="alert alert-success alert-dismissible fade show shadow" role="alert" style="min-width: 300px; text-align: center;">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                                <strong>Success!</strong>
                            </div>
                            <p class="mb-0">Investigation update has been deleted.</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                $(alertHtml).appendTo('body').delay(3000).fadeOut(function() { $(this).remove(); });
                
                // Refresh the page to show updated data
                location.reload();
            },
            error: function(xhr) {
                const alertHtml = `
                    <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
                        <div class="alert alert-danger alert-dismissible fade show shadow" role="alert" style="min-width: 300px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-exclamation-circle-fill fs-4"></i>
                                <strong>Error</strong>
                            </div>
                            <p class="mb-0">Failed to delete investigation update.</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                $(alertHtml).appendTo('body').delay(5000).fadeOut(function() { $(this).remove(); });
            }
        });
    }
}

// Reset form when modal is closed
$('#investigationModal').on('hidden.bs.modal', function() {
    const form = document.getElementById('investigationForm');
    form.reset();
    form.classList.remove('was-validated');
    $('input[name="log_id"]').remove();
    $('#investigationModalLabel').text('New Investigation Update');
    $('#investigationForm button[type="submit"]').text('Save Update');
});


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

    const logId = form.querySelector('input[name="log_id"]')?.value;
    const isUpdate = !!logId;

    $.ajax({
        url: isUpdate 
            ? `{{ route('admin.complaints.investigation.update', ['complaint' => $complaint->id, 'log' => '_ID_']) }}`.replace('_ID_', logId)
            : "{{ route('admin.complaints.investigate', $complaint) }}",
        method: isUpdate ? 'PUT' : 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Show success message
                const alertHtml = `
                    <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
                        <div class="alert alert-success alert-dismissible fade show shadow" role="alert" style="min-width: 300px; text-align: center;">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                                <strong>Success!</strong>
                            </div>
                            <p class="mb-0">Investigation update has been saved.</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                $(alertHtml).appendTo('body').delay(3000).fadeOut(function() { $(this).remove(); });

                // Close modal and reset form
                const modal = bootstrap.Modal.getInstance(document.getElementById('investigationModal'));
                modal.hide();
                form.reset();
                form.classList.remove('was-validated');

                // Refresh the page to show updated data
                location.reload();
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
                <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
                    <div class="alert alert-danger alert-dismissible fade show shadow" role="alert" style="min-width: 300px;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-exclamation-circle-fill fs-4"></i>
                            <strong>Error</strong>
                        </div>
                        ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            `;
            $(alertHtml).appendTo('body').delay(5000).fadeOut(function() { $(this).remove(); });
        }
    });
});
</script>
@endsection
