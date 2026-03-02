<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - GoBEST™ Listens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('respondent.dashboard') }}">
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
                            <a class="dropdown-item" href="{{ route('respondent.dashboard') }}">
                                <i class="bi bi-grid me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item active" href="{{ route('respondent.profile') }}">
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

    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">Profile Settings</h1>
                        <p class="text-muted mb-0">Manage your personal information and contact details</p>
                    </div>
                    <a href="{{ route('respondent.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <!-- Profile Picture Section -->
                        <div class="text-center mb-4 pb-4 border-bottom">
                            <div class="position-relative d-inline-block">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <h4 class="mt-3 mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">{{ ucfirst($user->role) }}</p>
                            <small class="text-muted">Member since {{ $user->created_at->format('M Y') }}</small>
                        </div>

                        <!-- Profile Form -->
                        <form method="POST" action="{{ route('respondent.profile.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-person me-1 text-primary"></i>Full Name
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-1 text-primary"></i>Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        This email will be used for complaint notifications
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">
                                        <i class="bi bi-telephone me-1 text-primary"></i>Phone Number
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="{{ old('phone_number', $user->phone_number) }}"
                                           placeholder="e.g., +1 (555) 123-4567">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Optional - for urgent contact purposes
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Account Information</h6>
                                            <small class="text-muted">Read-only information about your account</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">User ID</label>
                                    <div class="form-control-plaintext">{{ $user->id }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Account Type</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Account Created</label>
                                    <div class="form-control-plaintext">{{ $user->created_at->format('M j, Y g:i A') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Last Updated</label>
                                    <div class="form-control-plaintext">{{ $user->updated_at->format('M j, Y g:i A') }}</div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Your information is kept secure and confidential
                                </small>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('respondent.dashboard') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Update Profile
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Additional Information Card -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle text-primary me-2"></i>Important Notes
                        </h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-envelope-check text-success me-2 mt-1"></i>
                                    <div>
                                        <small class="fw-bold">Email Notifications</small>
                                        <div class="small text-muted">
                                            You'll receive notifications when new complaints are assigned to you
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-shield-lock text-primary me-2 mt-1"></i>
                                    <div>
                                        <small class="fw-bold">Data Privacy</small>
                                        <div class="small text-muted">
                                            Your personal information is protected and only used for complaint management
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-clock text-info me-2 mt-1"></i>
                                    <div>
                                        <small class="fw-bold">Response Time</small>
                                        <div class="small text-muted">
                                            Please respond to complaints within 48 hours of assignment
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-question-circle text-secondary me-2 mt-1"></i>
                                    <div>
                                        <small class="fw-bold">Need Help?</small>
                                        <div class="small text-muted">
                                            Contact your administrator for technical support
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .navbar-brand img {
            filter: brightness(0) invert(1);
        }
        
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-1px);
        }
        
        .form-control-plaintext {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }
        
        .dropdown-item.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
    </style>
</body>
</html>