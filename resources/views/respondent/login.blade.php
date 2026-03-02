<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respondent Login - GoBEST™ Listens</title>
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
                            <img src="{{ asset('images/Logo.png') }}" alt="GoBEST Logo" class="mb-3" style="max-height: 80px;">
                            <h2 class="h4 text-dark mb-1">Respondent Access</h2>
                            <p class="text-muted small mb-0">Enter your complaint number to continue</p>
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
                        <form method="POST" action="{{ route('respondent.login.submit') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="complaint_number" class="form-label text-dark fw-medium">
                                    <i class="bi bi-file-text me-2 text-primary"></i>Complaint Number
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('complaint_number') is-invalid @enderror" 
                                       id="complaint_number" 
                                       name="complaint_number" 
                                       value="{{ old('complaint_number') }}"
                                       placeholder="Enter complaint number (e.g., COMP-12345678)"
                                       required 
                                       autofocus>
                                @error('complaint_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    This was provided in your assignment notification email
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-arrow-right-circle me-2"></i>Continue
                                </button>
                            </div>
                        </form>

                        <!-- Help Section -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="small text-muted mb-2">
                                <i class="bi bi-question-circle me-1"></i>Need help?
                            </p>
                            <p class="small text-muted">
                                Contact your administrator if you don't have your complaint number
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-3">
                    <p class="small text-muted">
                        © {{ date('Y') }} GoBEST™ Listens. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .alert {
            border: none;
            border-radius: 12px;
        }
    </style>
</body>
</html>