<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cast Member Dashboard - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 145;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #2563eb;
            color: white;
            padding: 1rem;
            transition: all 0.3s ease;
            z-index: 1000;
            border-radius: 0px 8px 0px 0px;
        }

        .sidebar-header {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand {
            color: white;
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }

        .navbar-brand img {
            width: 90px;
            height: auto;
            margin-right: 0.5rem;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            font-size: 1.25rem;
        }

        .main-content {
            margin-left: 265px;
            padding: 2rem;
            min-height: 100vh;
            background: #ffffff;
            margin-top: 26px;
            border-radius: 8px;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            right: 0;
            left: 0;
            z-index: 999;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: #000;
        }

        .user-dropdown {
            background: none;
            border: none;
            padding: 0.5rem;
            color: #333;
            transition: all 0.2s;
        }

        .user-dropdown:hover,
        .user-dropdown:focus {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #4b5563;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 500;
            color: #6b7280;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-resolved {
            background: #dcfce7;
            color: #166534;
        }

        .sidebar-header img {
            width: 70px;
            height: auto;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white">
        <div class="container-fluid">
            <a href="{{ route('cast_member.dashboard') }}" class="navbar-brand d-flex align-items-center">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo">
            </a>
            <div class="navbar-nav ms-auto">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted">Welcome, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-link text-dark text-decoration-none">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <!-- Sidebar -->
    <div class="sidebar">

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('cast_member.dashboard') }}"
                    class="nav-link {{ request()->routeIs('cast_member.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cast_member.complaints') }}"
                    class="nav-link {{ request()->routeIs('cast_member.complaints') ? 'active' : '' }}">
                    <i class="bi bi-file-text"></i>
                    Complaints
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link {{ request()->routeIs('cast_member.documents') ? 'active' : '' }}">
                    <i class="bi bi-folder"></i>
                    Documents
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link {{ request()->routeIs('cast_member.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        @yield('content')
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdowns = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdowns.map(function(dropdown) {
                return new bootstrap.Dropdown(dropdown);
            });
        });
    </script>
    @yield('scripts')
</body>

</html>
