<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - GoBEST™ Legal Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(135deg, #7b1fa2 0%, #4a148c 100%);">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('lawyer.dashboard') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" style="height: 40px;" class="me-2">
                ⚖️ GoBEST™ Legal Review
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row gap-3">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        {{ $user->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('lawyer.dashboard') }}">
                                <i class="bi bi-grid me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item active" href="{{ route('lawyer.profile') }}">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('lawyer.logout') }}" class="d-inline">
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
                        <h1 class="h3 mb-1">⚖️ Lawyer Profile Settings</h1>
                        <p class="text-muted mb-0">Manage your legal professional information and contact details</p>
                    </div>
                    <a href="{{ route('lawyer.dashboard') }}" class="btn btn-outline-secondary">
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
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold; background: linear-gradient(135deg, #7b1fa2 0%, #6a1b9a 100%);">
                                    ⚖️
                                </div>
                            </div>
                            <h4 class="mt-3 mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">Legal Professional</p>
                            <small class="text-muted">Member since {{ $user->created_at->format('M Y') }}</small>
                        </div>

                        <!-- Profile Form -->
                        <form method="POST" action="{{ route('lawyer.profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="name" class="form-label fw-medium">
                                    <i class="bi bi-person me-2 text-warning"></i>Full Name
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

                            <div class="mb-4">
                                <label for="email" class="form-label fw-medium">
                                    <i class="bi bi-envelope me-2 text-warning"></i>Email Address
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
                                    <i class="bi bi-info-circle me-1"></i>
                                    This email will be used for legal case notifications
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="phone_number" class="form-label fw-medium">
                                    <i class="bi bi-telephone me-2 text-warning"></i>Phone Number
                                </label>
                                <input type="tel" 
                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="phone_number" 
                                       name="phone_number" 
                                       value="{{ old('phone_number', $user->phone_number) }}" 
                                       placeholder="Enter your professional phone number">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Professional Information Display -->
                            <div class="mb-4 p-3 bg-light rounded">
                                <h6 class="mb-3 text-muted">
                                    <i class="bi bi-briefcase me-2"></i>Professional Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="small mb-2">
                                            <strong>Role:</strong> Legal Professional
                                        </p>
                                        <p class="small mb-2">
                                            <strong>Access Level:</strong> Legal Review Portal
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="small mb-2">
                                            <strong>Account Status:</strong> 
                                            <span class="badge bg-success">Active</span>
                                        </p>
                                        <p class="small mb-2">
                                            <strong>Verification:</strong> 
                                            <span class="badge bg-warning text-dark">Professional</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg legal-btn">
                                    <i class="bi bi-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </form>
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
            border-color: #7b1fa2;
            box-shadow: 0 0 0 0.2rem rgba(123, 31, 162, 0.25);
        }
        
        .legal-btn {
            background: linear-gradient(135deg, #7b1fa2 0%, #6a1b9a 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .legal-btn:hover {
            background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(123,31,162,0.3);
        }
        
        .dropdown-item.active {
            background-color: rgba(123,31,162,0.1);
            color: #7b1fa2;
        }
    </style>
</body>
</html>