@extends('layouts.admin')

@section('title', 'Admin Dashboard - GoBEST™ Listens Complaint System')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')


    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stats-card open">
                <div class="stats-label">Open Cases</div>
                <div class="stats-number">{{ $openCount }}</div>
                <div class="stats-description">Submitted & Under Review</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stats-card closed">
                <div class="stats-label">Closed Cases</div>
                <div class="stats-number">{{ $closedCount }}</div>
                <div class="stats-description">Closed & Resolved</div>
            </div>
        </div>
    </div>

    <div class="complaint-header" role="alert">
    </div>
    <div class="complaints-table p-4">
        <h2 class="h4 mb-4">Recent Complaints</h2>
        <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
            <table class="table" style="min-width: 950px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                        <tr class="complaint-row" data-href="{{ route('admin.complaints.show', $complaint) }}" style="cursor: pointer;">
                            <td>{{ $complaint->case_number }}</td>
                            <td>{{ Str::limit($complaint->description, 50) }}</td>
                            <td>{{ $complaint->location }}
                            </td>
                            <td>
                                @php
                                    $displayStatus = $complaint->display_status ?? $complaint->status;

                                    // Use stage color if available, otherwise use status-based color
                                    if ($complaint->stage) {
                                        $statusColor = $complaint->stage->color;
                                        $useBootstrapClass = false;
                                    } else {
                                        $statusColor = match ($displayStatus) {
                                            'submitted' => 'primary',
                                            'under_review' => 'info',
                                            'escalated' => 'warning',
                                            'resolved' => 'success',
                                            'closed' => 'secondary',
                                            'awaiting_signature' => 'warning',
                                            default => 'primary',
                                        };
                                        $useBootstrapClass = true;
                                    }

                                    $statusText = $complaint->stage
                                        ? $complaint->stage->name
                                        : ($displayStatus === 'awaiting_signature'
                                            ? 'Awaiting Signatures'
                                            : ucfirst(str_replace('_', ' ', $displayStatus == 'escalated' ? 'progress' : $displayStatus))
                                        );
                                @endphp

                                @if ($useBootstrapClass)
                                    <span class="badge bg-{{ $statusColor }} text-white" style="font-size: 13px; padding: 6px 12px;">
                                        @if ($displayStatus === 'awaiting_signature')
                                            <i class="fas fa-signature"></i>
                                        @endif
                                        <span>{{ $statusText }}</span>
                                    </span>
                                @else
                                    <span class="badge text-white" style="background-color: {{ $statusColor }}; font-size: 13px; padding: 6px 12px;">
                                        {{ $statusText }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ date('M d, Y', strtotime($complaint->created_at)) }}
                            </td>
                            <td class="action-cell">
                                <a href="{{ route('admin.complaints.show', $complaint) }}"
                                    class="btn btn-sm btn-outline-info" title="View" data-bs-toggle="tooltip">
                                    <i class="bi bi-eye"></i>
                                    {{-- <span class="visually-hidden">View</span> --}}
                                    <span class="visually-hidden">view</span>
                                </a>
                                @if ($complaint->respondents->isEmpty())
                                    <button class="btn btn-primary btn-sm add-respondent-btn"
                                        data-complaint-id="{{ $complaint->id }}" title="Add Respondent"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-person-plus"></i>

                                    </button>
                                    <span class="visually-hidden">Add Respondent</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox h4 d-block"></i>
                                    No complaints found
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="respondentModal" tabindex="-1" role="dialog" aria-labelledby="respondentModalLabel"
        aria-hidden="true">
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
                            <select class="form-select" id="respondentId" name="respondent_id" required>
                                <option value="">Choose a respondent...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
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
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .complaint-row:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .complaint-row:hover td {
            background-color: transparent;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
            document.querySelectorAll('.add-respondent-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent row click
                    var complaintId = this.getAttribute('data-complaint-id');
                    openRespondentModal(complaintId);
                });
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
        });

        function openRespondentModal(complaintId) {
            document.getElementById('complaintId').value = complaintId;
            var modalEl = document.getElementById('respondentModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
            console.log('Opening modal for complaint:', complaintId);
        }

        function saveRespondent() {
            const complaintId = document.getElementById('complaintId')?.value;
            console.log('Complaint ID:', complaintId);
            const respondentId = document.getElementById('respondentId')?.value;
            console.log('Respondent ID:', respondentId);

            if (!respondentId || !complaintId) {
                alert('Please select a respondent and ensure complaint ID exists.');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('admin.complaints.respondent') }}",
                method: 'POST',
                data: {
                    respondent_id: respondentId,
                    complaint_id: complaintId
                },
                success: function(response) {
                    if (response.success) {
                        // Close the modal
                        $('#respondentModal').modal('hide');

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
                    } else {
                        alert(response.message || 'Error adding respondent');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX error:', xhr.responseText);
                    alert('Server error while assigning respondent.');
                }
            });
        }
    </script>

@endsection
