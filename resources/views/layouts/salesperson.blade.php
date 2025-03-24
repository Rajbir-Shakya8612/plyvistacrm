<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Salesperson Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 280px;
        }

        body {
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            padding: 20px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            transition: all 0.3s ease-in-out;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
            transition: all 0.3s ease-in-out;
        }

        .sidebar-collapsed .sidebar {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-collapsed .main-content {
            margin-left: 0;
        }

        .nav-link {
            border-radius: 5px;
            margin-bottom: 5px;
            color: #6c757d;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #e9ecef;
            color: #0d6efd;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        .profile-section {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            :root {
                --sidebar-width: 100%;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-collapsed .sidebar {
                transform: translateX(0);
                margin-left: 0;
            }
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .stat-card {
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 2px;
            background-color: #dee2e6;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -4px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #0d6efd;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="profile-section">
                <img src="{{ auth()->user()->photo_url ?? asset('images/default-avatar.png') }}" alt="Profile" class="profile-image">
                <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                <small class="text-muted">{{ auth()->user()->designation }}</small>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('salesperson.dashboard') }}" class="nav-link {{ request()->routeIs('salesperson.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('salesperson.leads') }}" class="nav-link {{ request()->routeIs('salesperson.leads*') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill"></i> Leads
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('salesperson.sales') }}" class="nav-link {{ request()->routeIs('salesperson.sales*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check"></i> Sales
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('salesperson.attendance') }}" class="nav-link {{ request()->routeIs('salesperson.attendance*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('salesperson.performance') }}" class="nav-link {{ request()->routeIs('salesperson.performance*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Performance
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('salesperson.profile') }}" class="nav-link {{ request()->routeIs('salesperson.profile*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('salesperson.settings') }}" class="nav-link {{ request()->routeIs('salesperson.settings*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-link sidebar-toggler" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                <li><a class="dropdown-item" href="#">New Lead Assigned</a></li>
                                <li><a class="dropdown-item" href="#">Meeting Reminder</a></li>
                                <li><a class="dropdown-item" href="#">Target Achievement</a></li>
                            </ul>
                        </div>

                        <div class="dropdown ms-3">
                            <button class="btn btn-link" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ auth()->user()->photo_url ?? asset('images/default-avatar.png') }}" alt="Profile" class="rounded-circle" width="32" height="32">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="{{ route('salesperson.profile') }}">My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('salesperson.settings') }}">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggler = document.querySelector('.sidebar-toggler');
            const wrapper = document.querySelector('.wrapper');

            sidebarToggler?.addEventListener('click', function() {
                wrapper.classList.toggle('sidebar-collapsed');
            });

            // Check for saved theme preference
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);

            // API Token Check
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Add token to all API requests
            const originalFetch = window.fetch;
            window.fetch = function() {
                let [resource, config] = arguments;
                if (resource.startsWith('/api/')) {
                    config = config || {};
                    config.headers = config.headers || {};
                    config.headers['Authorization'] = `Bearer ${token}`;
                }
                return originalFetch.apply(this, [resource, config]);
            };
        });

        // Show loading spinner
        function showLoading() {
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Hide loading spinner
        function hideLoading() {
            Swal.close();
        }

        // Show toast notification
        function showToast(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: icon,
                title: title
            });
        }

        // Handle API errors
        function handleApiError(error) {
            console.error('API Error:', error);
            
            if (error.status === 401) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/login';
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Something went wrong!',
                confirmButtonColor: '#3085d6'
            });
        }
    </script>

    @stack('scripts')
</body>
</html> 