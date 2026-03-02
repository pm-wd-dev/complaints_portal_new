<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <title>GoBEST™ Listens Complaint System</title>
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
            padding: 1rem 0;
        }
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0;
        }
        .navbar-brand img {
            width: 80px;
            height: auto;
            margin-right: 1rem;
        }
        .header-title {
            text-align: center;
            flex-grow: 1;
        }
        .login-link {
            color: #1a56db;
            text-decoration: none;
            font-weight: 600;
        }
        .welcome-container {
            max-width: 800px;
            margin: 6rem auto 2rem;
            text-align: center;
            padding: 0 1rem;
        }
        .welcome-title {
            font-size: 3rem;
            margin-bottom: 3rem;
            color: #111827;
            font-weight: 700;
        }
        .role-buttons {
            max-width: 500px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .btn-role {
            display: block;
            width: 100%;
            padding: 1.25rem 2rem;
            background-color: #2563eb;
            border-color: #2563eb;
            font-size: 1.25rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
        }
        .btn-role:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1rem;
                flex-direction: column;
                gap: 0.5rem;
            }
            .navbar-brand img {
                width: 60px;
                margin-right: 0;
            }
            .welcome-container {
                margin: 3rem auto 2rem;
                padding: 0 1rem;
            }
            .welcome-title {
                font-size: 2rem;
                margin-bottom: 2rem;
            }
            .role-buttons {
                max-width: 100%;
                gap: 1rem;
            }
            .btn-role {
                padding: 1rem 1.5rem;
                font-size: 1.125rem;
            }
        }
        
        @media (max-width: 480px) {
            .navbar-brand {
                font-size: 0.9rem;
            }
            .navbar-brand img {
                width: 50px;
            }
            .welcome-container {
                margin: 2rem auto 1rem;
                padding: 0 0.5rem;
            }
            .welcome-title {
                font-size: 1.75rem;
                margin-bottom: 1.5rem;
            }
            .btn-role {
                padding: 0.875rem 1rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid position-relative">
            <a href="{{ route('login') }}" class="login-link position-absolute" style="right: 1rem; top: 50%; transform: translateY(-50%);">Login</a>
            <div class="navbar-brand">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo">
                <div class="header-title">GoBEST™ Listens Complaint Management System</div>
            </div>
        </div>
    </nav>

    <div class="container welcome-container">
        <h1 class="welcome-title">Welcome</h1>
        <div class="role-buttons">
            <a href="{{ route('public.complaints.create') }}" class="btn btn-primary btn-role">Add a new complaint</a>
            <a href="{{ route('public.complaints.track-form') }}" class="btn btn-primary btn-role">Track complaint</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
