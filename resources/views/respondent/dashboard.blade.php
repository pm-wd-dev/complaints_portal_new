<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respondent Dashboard - GoBEST™ Listens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" style="height: 40px;" class="me-2">
                GoBEST™ Listens
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row gap-3">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        {{ $user->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('respondent.profile') }}">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('respondent.logout') }}" class="d-inline">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">Welcome back, {{ $user->name }}</h1>
                        <p class="text-muted mb-0">Manage your assigned complaints and responses</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">{{ $complaints->count() }}</h5>
                                <p class="card-text small mb-0">Total Assigned</p>
                            </div>
                            <i class="bi bi-file-text fs-2 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">{{ $complaints->where('respondents.0.responded_at', '!=', null)->count() }}</h5>
                                <p class="card-text small mb-0">Responded</p>
                            </div>
                            <i class="bi bi-check-circle fs-2 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">{{ $complaints->where('respondents.0.responded_at', null)->count() }}</h5>
                                <p class="card-text small mb-0">Pending</p>
                            </div>
                            <i class="bi bi-clock fs-2 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">{{ $complaints->where('status', 'resolved')->count() }}</h5>
                                <p class="card-text small mb-0">Resolved</p>
                            </div>
                            <i class="bi bi-award fs-2 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaints Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0">
                    <i class="bi bi-list-task me-2 text-primary"></i>
                    Assigned Complaints
                </h5>
            </div>
            <div class="card-body p-0">
                @if($complaints->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Case Number</th>
                                    <th class="border-0">Type</th>
                                    <th class="border-0">Stage</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Assigned</th>
                                    <th class="border-0">Response Status</th>
                                    <th class="border-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaints as $complaint)
                                    @php
                                        $respondent = $complaint->respondents->where('user_id', $user->id)->first();
                                        $hasResponded = $respondent && $respondent->responded_at;
                                    @endphp
                                    <tr class="clickable-row" data-href="{{ route('respondent.complaint.view', $complaint) }}">
                                        <td class="ps-4">
                                            <strong class="text-primary">{{ $complaint->case_number }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($complaint->stage)
                                                <span class="badge text-dark" style="background-color: {{ $complaint->stage->color }}">
                                                    {{ $complaint->stage->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No Stage</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColor = match($complaint->status) {
                                                    'submitted' => 'bg-primary',
                                                    'under_review' => 'bg-info',
                                                    'escalated' => 'bg-warning text-dark',
                                                    'resolved' => 'bg-success',
                                                    'closed' => 'bg-secondary',
                                                    default => 'bg-primary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $complaint->created_at->format('M j, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($hasResponded)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Responded
                                                </span>
                                                <div class="small text-muted mt-1">
                                                    {{ $respondent->responded_at->format('M j, g:i A') }}
                                                </div>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('respondent.complaint.view', $complaint) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               onclick="event.stopPropagation();">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No Complaints Assigned</h5>
                        <p class="text-muted mb-0">You don't have any complaints assigned to you at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make table rows clickable
            const clickableRows = document.querySelectorAll('.clickable-row');
            clickableRows.forEach(row => {
                row.addEventListener('click', function() {
                    window.location.href = this.dataset.href;
                });
            });
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

        .table th {
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.04);
        }

        .clickable-row {
            cursor: pointer;
        }

        .btn-outline-primary:hover {
            transform: translateY(-1px);
        }

        .badge {
            font-weight: 500;
        }
    </style>
</body>
</html>