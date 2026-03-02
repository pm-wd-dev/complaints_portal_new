<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Investigate Complaint - GoBEST™ Listens Complaint System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Custom Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Firefox Scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
            overflow-y: auto;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: #000;
        }
        .navbar-brand img {
            width: 30px;
            margin-right: 0.5rem;
        }
        .sidebar {
            width: 280px;
            background: white;
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
            height: calc(100vh - 72px);
            position: fixed;
            top: 72px;
            left: 0;
            padding: 1.5rem 1rem;
            overflow-y: auto;
        }
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            /* height: calc(100vh - 72px); */
            margin-top: 72px;
            overflow-y: auto;
        }
        .nav-link {
            color: #4b5563;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #f3f4f6;
            color: #2563eb;
        }
        .nav-link i {
            font-size: 1.25rem;
        }
        .complaint-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 500;
        }
        .status-badge.open {
            background-color: #dbeafe;
            color: #2563eb;
        }
        .status-badge.in-progress {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-badge.closed {
            background-color: #d1fae5;
            color: #059669;
        }
        .btn-back {
            color: #4b5563;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        .btn-back:hover {
            color: #2563eb;
        }
        .complaint-details {
            border-bottom: 1px solid #e5e7eb;
        }
        .complaint-details dt {
            color: #6b7280;
            font-weight: 500;
        }
        .complaint-details dd {
            color: #111827;
            font-weight: 500;
        }
        .attachment-box {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .attachment-box i {
            font-size: 2rem;
            color: #2563eb;
        }
        .form-label {
            font-weight: 500;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg py-3 fixed-top">
        <div class="container-fluid px-4">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand d-flex align-items-center">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'><circle cx='12' cy='12' r='10'/></svg>" alt="Logo">
                GoBEST™ Listens Complaint System
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, Admin</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn btn-link text-dark text-decoration-none">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <nav class="nav flex-column gap-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="nav-link">
                <i class="bi bi-people-fill"></i>
                All Users
            </a>
            <a href="{{ route('admin.complaints') }}" class="nav-link active">
                <i class="bi bi-chat-square-text-fill"></i>
                Complaints
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark-text-fill"></i>
                Documents
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-bar-chart-fill"></i>
                Reports
            </a>
            <a href="#" class="nav-link">
                <i class="bi bi-gear-fill"></i>
                Settings
            </a>
        </nav>
    </div>

    <main class="main-content">
        <div class="container-fluid">
            <a href="{{ route('admin.complaints') }}" class="btn-back mb-4">
                <i class="bi bi-arrow-left"></i>
                Back to Complaints
            </a>

            <div class="complaint-card p-4">
                <div class="complaint-details pb-4 mb-4">
                    <h1 class="h3 mb-4">Complaint #{{ $complaint->id }}</h1>

                    <dl class="row">
                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            <span class="status-badge {{ str_replace('_', '-', $complaint->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Subject</dt>
                        <dd class="col-sm-9">{{ $complaint->subject }}</dd>

                        <dt class="col-sm-3">Submitted By</dt>
                        <dd class="col-sm-9">{{ $complaint->user->name }}</dd>

                        <dt class="col-sm-3">Date Submitted</dt>
                        <dd class="col-sm-9">{{ $complaint->created_at->format('F d, Y \a\t h:i A') }}</dd>

                        <dt class="col-sm-3">Description</dt>
                        <dd class="col-sm-9">{{ $complaint->description }}</dd>

                        @if($complaint->attachment_path)
                        <dt class="col-sm-3">Attachment</dt>
                        <dd class="col-sm-9">
                            <div class="attachment-box d-inline-flex align-items-center gap-3">
                                <i class="bi bi-file-earmark"></i>
                                <div>
                                    <p class="mb-1 fw-500">Attachment</p>
                                    <a href="{{ Storage::url($complaint->attachment_path) }}" target="_blank" class="text-primary">View File</a>
                                </div>
                            </div>
                        </dd>
                        @endif
                    </dl>
                </div>

                <form action="{{ route('admin.resolve', $complaint) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="status" class="form-label">Update Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="closed" {{ old('status', $complaint->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="admin_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control @error('admin_notes') is-invalid @enderror"
                                  id="admin_notes" name="admin_notes" rows="4"
                                  placeholder="Add your notes here" required>{{ old('admin_notes', $complaint->admin_notes) }}</textarea>
                        @error('admin_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            Update Complaint
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
