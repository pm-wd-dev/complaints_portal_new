<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    @yield('meta')
    <title>@yield('title') - GoBEST™ Listens Complaint System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg py-3">
        <div class="container-fluid">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand d-flex align-items-center">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo">
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
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>

            <a href="{{ route('admin.locations') }}" class="nav-link {{ request()->routeIs('admin.locations*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i>
                Locations
            </a>

            <a href="{{ route('admin.stages.index') }}" class="nav-link {{ request()->routeIs('admin.stages*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                Stages
            </a>

            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                All Users
            </a>
            <a href="{{ route('admin.complaints') }}" class="nav-link {{ request()->routeIs('admin.complaints*') ? 'active' : '' }}">
                <i class="bi bi-chat-square-text-fill"></i>
                Complaints
            </a>

            <a href="{{ route('admin.documents') }}" class="nav-link {{ request()->routeIs('admin.documents') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i>
                Documents
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i>
                Reports
            </a>
        </nav>
    </div>
    <main class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>
    <!-- jQuery -->

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
