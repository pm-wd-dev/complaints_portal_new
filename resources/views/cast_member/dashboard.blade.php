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

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-0">Dashboard</h4>
            </div>
        </div>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
        
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-file-text text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Assigned Complaints</h6>
                            <h3 class="mb-0">{{ $totalAssigned }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-hourglass-split text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">In Progress</h6>
                            <h3 class="mb-0">{{ $inProgress }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Resolved</h6>
                            <h3 class="mb-0">{{ $resolved }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Complaints</h5>
                <a href="{{ route('cast_member.complaints') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Issue Type</th>
                            <th>Subject</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentComplaints as $complaint)
                        <tr>
                            <td>{{ $complaint['case_number'] }}</td>
                            <td>{{ \Illuminate\Support\Str::ucfirst($complaint['issue_type']) }}</td>
                            <td>{{ Str::limit($complaint['description'], 50) }}</td>
                            <td>{{ $complaint['location']}}</td>
                            <td>
                                @php
                                    $displayStatus = $complaint['display_status'];
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
                                <span class="badge-status bg-{{ $statusColor }} text-white  awaiting_signature">
                                    @if($displayStatus === 'awaiting_signature')
                                        <i class="fas fa-signature"></i>
                                        <span>Awaiting Signatures</span>
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $displayStatus=='escalated' ? 'progress' : $displayStatus)) }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($complaint['created_at'])->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('cast_member.complaints.show', $complaint['id']) }}" class="btn btn-sm btn-outline-primary btn-outline-info" title="View"
                                style="padding: 7px;" >
                                <i class="bi bi-eye"></i>
                                {{-- <span class="visually-hidden">View</span> --}}
                                <span class="visually-hidden">view</span> 
                                </a>  
                            </td>
                            
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No complaints assigned yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
