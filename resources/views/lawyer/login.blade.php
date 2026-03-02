<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Login - GoBEST™ Legal Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <div class="logo-container">
                                <img src="{{ asset('images/Logo.png') }}" alt="GoBEST Logo" class="mb-3" style="max-height: 90px;">
                            </div>
                            <h2 class="h4 text-primary-custom mb-2">⚖️ Legal Review Portal</h2>
                            <p class="small mb-0" style="color: #666;">Enter your case details to access the legal review system</p>
                        </div>

                        <!-- Success Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                @foreach($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('lawyer.login.submit') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="complaint_number" class="form-label">
                                    <i class="bi bi-folder-fill me-2 text-primary-custom"></i>Case Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-color: #e0e0e0;">
                                        <i class="bi bi-briefcase-fill text-primary-custom"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control form-control-lg border-start-0 @error('complaint_number') is-invalid @enderror" 
                                           id="complaint_number" 
                                           name="complaint_number" 
                                           value="{{ old('complaint_number') }}"
                                           placeholder="Enter case number (e.g., COMP-12345678)"
                                           required 
                                           autofocus>
                                </div>
                                @error('complaint_number')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Hidden email field -->
                            <input type="hidden" 
                                   name="lawyer_email" 
                                   value="{{ $email ?? old('lawyer_email') }}">
                            
                            @if($email)
                                <div class="mb-4">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Legal review access for: <strong>{{ $email }}</strong>
                                    </div>
                                </div>
                            @else
                                <div class="mb-4">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Please use the login link from your assignment email to access this portal.
                                    </div>
                                </div>
                            @endif

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg shadow">
                                    <i class="bi bi-shield-check me-2"></i>Access Legal Review Portal
                                </button>
                            </div>
                        </form>

                        <!-- Help Section -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="small text-muted mb-2">
                                <i class="bi bi-question-circle me-1"></i>Need help?
                            </p>
                            <p class="small text-muted">
                                Contact the administrator if you don't have your case number
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-3">
                    <p class="small text-white-50">
                        © {{ date('Y') }} GoBEST™ Legal Review Portal. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #c41e3a 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(30, 60, 114, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(196, 30, 58, 0.3) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            border: 2px solid rgba(30, 60, 114, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 60, 114, 0.3);
            color: #fff !important;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1565c0;
            border-left: 4px solid #1e3c72;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            color: #e65100;
            border-left: 4px solid #ff6f00;
        }
        
        .text-primary-custom {
            color: #1e3c72 !important;
        }
        
        .text-accent {
            color: #c41e3a !important;
        }
        
        .form-label {
            color: #1e3c72 !important;
            font-weight: 600;
        }
        
        .border-top {
            border-color: rgba(30, 60, 114, 0.2) !important;
        }
        
        /* Logo glow effect */
        .logo-container {
            position: relative;
            display: inline-block;
        }
        
        .logo-container::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 120%;
            height: 120%;
            background: radial-gradient(circle, rgba(42, 82, 152, 0.2) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
        
        /* Animated background particles */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 0.6; }
        }
        
        .bg-particle {
            position: fixed;
            pointer-events: none;
            opacity: 0.3;
            animation: float 6s ease-in-out infinite;
        }
    </style>
</body>
</html>