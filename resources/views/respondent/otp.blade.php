<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - GoBEST™ Listens</title>
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
                            <h2 class="h4 text-dark mb-1">Verification Required</h2>
                            <p class="text-muted small mb-0">
                                Complaint: <strong>{{ session('complaint_number') }}</strong>
                            </p>
                        </div>

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

                        <!-- OTP Form -->
                        <form method="POST" action="{{ route('respondent.otp.verify') }}" id="otpForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="otp" class="form-label text-dark fw-medium">
                                    <i class="bi bi-shield-lock me-2 text-primary"></i>Enter OTP
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg text-center @error('otp') is-invalid @enderror" 
                                       id="otp" 
                                       name="otp" 
                                       maxlength="4"
                                       pattern="[0-9]{4}"
                                       placeholder="0000"
                                       style="font-size: 2rem; letter-spacing: 0.5rem; font-weight: bold;"
                                       required 
                                       autofocus>
                                @error('otp')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text text-center">
                                    <i class="bi bi-info-circle me-1"></i>
                                    For testing purposes, use <strong>0000</strong>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Verify & Continue
                                </button>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('respondent.login') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Login
                                </a>
                            </div>
                        </form>

                        <!-- Help Section -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="small text-muted mb-0">
                                <i class="bi bi-shield-check me-1 text-success"></i>
                                This verification ensures secure access to complaint details
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
    
    <script>
        // Auto-submit when 4 digits are entered
        document.getElementById('otp').addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Only allow numbers
            value = value.replace(/[^0-9]/g, '');
            e.target.value = value;
            
            // Auto-submit when 4 digits
            if (value.length === 4) {
                setTimeout(() => {
                    document.getElementById('otpForm').submit();
                }, 500);
            }
        });

        // Focus on input when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('otp').focus();
        });
    </script>
    
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
        
        #otp {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
        }
        
        #otp:focus {
            background: white;
            border-color: #667eea;
        }
    </style>
</body>
</html>