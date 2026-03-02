<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - GoBEST™ Listens Complaint System</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
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
            width: 100px;
            margin-right: 0.5rem;
        }
        .login-card {
            max-width: 400px;
            margin: 4rem auto;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-label {
            color: #4b5563;
            font-weight: 500;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .btn-primary {
            padding: 0.875rem;
            font-weight: 600;
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        .back-link {
            color: #6b7280;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #374151;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg p-0">
        <div class="container-fluid">
            <a href="/" class="navbar-brand">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo">
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="card login-card bg-white">
            <div class="card-body">
                <h1 class="h3 text-center mb-4">
                    @if(isset($role))
                        {{ ucfirst(str_replace('_', ' ', $role)) }} Login
                    @else
                        Admin Login
                    @endif
                </h1>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    @if(isset($role))
                        <input type="hidden" name="role" value="{{ $role }}">
                    @endif

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('welcome') }}" class="back-link">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
