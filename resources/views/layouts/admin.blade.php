<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRMS Admin') }}</title>
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #4f46e5; /* Indigo 600 */
            --brand-dark: #1e293b; /* Slate 800 */
            --brand-sidebar: #2b3a67;
            --light-bg: #f8fafb;
            --sidebar-width: 260px;
        }
        
        body { 
            background: #f0f4f8;
            overflow-x: hidden;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar { 
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            background: var(--brand-sidebar);
            color: #e2e8f0;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
            z-index: 1030;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-header {
            padding: 25px 20px !important;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.4rem;
            color: #ffffff;
        }
        
        .sidebar a { 
            color: #cbd5e1;
            text-decoration: none;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
            position: relative;
            z-index: 1;
            font-weight: 500;
            margin: 4px 12px;
            border-radius: 8px;
        }
        
        .sidebar a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .sidebar a:hover { 
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        
        .sidebar a.active { 
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-weight: 600;
        }
        
        .content { 
            margin-left: 260px;
            width: calc(100% - 260px);
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .topbar { 
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .topbar .btn {
            border: 1px solid #e2e8f0;
            color: var(--brand-dark);
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .topbar .btn:hover {
            background: #f1f5f9;
            color: var(--brand-dark);
        }
        
        .user-info {
            color: var(--brand-dark);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .wrapper { 
            display: flex;
            align-items: stretch;
        }
        
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--brand-teal), var(--brand-blue));
            color: white;
            font-weight: 600;
            border: none;
            padding: 18px 24px;
        }
        
        .btn-primary {
            background: var(--brand-primary);
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s;
            color: #ffffff;
        }
        
        .btn-primary:hover {
            background: #4338ca; /* Indigo 700 */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            color: #ffffff;
        }
        
        .btn-success {
            background: #10b981; /* Emerald 500 */
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            color: #ffffff;
            transition: all 0.3s;
        }
        
        .btn-success:hover {
            background: #059669; /* Emerald 600 */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: #ffffff;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }
        
        /* Mobile Sidebar Styles */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed !important;
                z-index: 1050;
                left: -260px;
                top: 0;
                height: 100vh;
                width: 260px;
                transition: left 0.3s ease;
            }
            .sidebar.active {
                left: 0;
            }
            .sidebar-overlay.active {
                display: block;
            }
            .content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 10px !important;
            }

            /* Reduced Font Sizes & Spacing for Mobile Screen */
            html, body {
                font-size: 13px !important;
            }
            .topbar {
                padding: 10px 14px !important;
                margin-bottom: 15px !important;
            }
            .topbar h1, .topbar h2, .h1, .h2, .h3 {
                font-size: 1.25rem !important;
            }
            h1 { font-size: 1.35rem !important; }
            h2 { font-size: 1.2rem !important; }
            h3 { font-size: 1.1rem !important; }
            h4 { font-size: 1.0rem !important; }
            h5 { font-size: 0.9rem !important; }
            h6 { font-size: 0.8rem !important; }

            p, span, div, label, input, select, textarea, button {
                font-size: 0.82rem;
            }

            /* Responsive Tables */
            .table {
                font-size: 0.78rem !important;
            }
            .table th, .table td {
                padding: 8px 10px !important;
                white-space: nowrap;
            }
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            /* Buttons & Cards */
            .btn {
                padding: 6px 12px !important;
                font-size: 0.78rem !important;
            }
            .btn-sm {
                padding: 4px 8px !important;
                font-size: 0.72rem !important;
            }
            .card {
                margin-bottom: 12px !important;
            }
            .card-body {
                padding: 12px !important;
            }
            .card-header {
                padding: 10px 12px !important;
            }

            /* Modals */
            .modal-dialog {
                margin: 10px !important;
            }
            .modal-body {
                padding: 14px !important;
            }

            /* Mobile Flex Wrap for Action Headers */
            .d-flex.justify-content-between:not(.topbar):not(.modal-header):not(.modal-footer) {
                flex-wrap: wrap !important;
                gap: 10px !important;
            }
            .d-flex.justify-content-between:not(.topbar):not(.modal-header):not(.modal-footer) > div:first-child {
                flex: 1 1 100%;
            }
            .d-flex.justify-content-between:not(.topbar):not(.modal-header):not(.modal-footer) > button,
            .d-flex.justify-content-between:not(.topbar):not(.modal-header):not(.modal-footer) > a {
                width: 100% !important;
                display: flex;
                justify-content: center;
                align-items: center;
            }
        }
        @media (min-width: 769px) {
            .sidebar.collapsed {
                left: -260px;
                margin-left: 0;
            }
            .sidebar.collapsed ~ .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="wrapper">
    <div class="sidebar-overlay"></div>
    <!-- Sidebar -->
    <nav class="sidebar" style="min-width: 260px; max-width: 260px;">
        <div class="sidebar-header">
            <img src="{{ asset('smec-white.png') }}" alt="Logo" style="max-width: 180px; height: auto;" class="mb-2">
            <div class="mt-2">
                <small class="text-white-50 fw-bold">Admin Dashboard</small>
            </div>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa fa-dashboard"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <i class="fa fa-users"></i> Employees
                </a>
            </li>
            <li>
                <a href="{{ route('admin.team-leaders.index') }}" class="{{ request()->routeIs('admin.team-leaders.*') ? 'active' : '' }}">
                    <i class="fa fa-user-tie"></i> Team Leaders
                </a>
            </li>
            <li>
                <a href="{{ route('admin.organization.index', 'departments') }}" class="{{ request()->is('admin/organization/departments*') ? 'active' : '' }}">
                    <i class="fa fa-building"></i> Departments
                </a>
            </li>
            <!-- Designations Hidden 
            <li>
                <a href="{{ route('admin.organization.index', 'designations') }}" class="{{ request()->is('admin/organization/designations*') ? 'active' : '' }}">
                    <i class="fa fa-id-badge"></i> Designations
                </a>
            </li>
            -->
            <li>
                <a href="{{ route('admin.organization.index', 'branches') }}" class="{{ request()->is('admin/organization/branches*') ? 'active' : '' }}">
                    <i class="fa fa-map-marker-alt"></i> Branches
                </a>
            </li>
            <li>
                <a href="{{ route('admin.leave.index') }}" class="{{ request()->routeIs('admin.leave.*') ? 'active' : '' }}">
                    <i class="fa fa-calendar-alt"></i> Leave Requests
                </a>
            </li>
            <li>
                <a href="{{ route('admin.early-going.index') }}" class="{{ request()->routeIs('admin.early-going.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Early Going
                </a>
            </li>
            <li>
                <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                    <i class="fa fa-clock"></i> Attendance
                </a>
            </li>
            <li>
                <a href="{{ route('admin.daily-reports.index') }}" class="{{ request()->routeIs('admin.daily-reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i> Daily Tasks
                </a>
            </li>
            <li>
                <a href="{{ route('admin.announcements.index') }}" class="{{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i class="fa fa-bullhorn"></i> Announcements
                </a>
            </li>
            <li>
                <a href="{{ route('admin.holidays.index') }}" class="{{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gift"></i> Holidays
                </a>
            </li>
            <li class="mt-4 mb-2 px-4" style="opacity:0.7; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Settings</li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> System Settings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.office-locations.index') }}" class="{{ request()->routeIs('admin.office-locations.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-map-location-dot"></i> Office Locations
                </a>
            </li>
            <li class="mt-4 mb-2 px-4" style="opacity:0.7; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Reports</li>
            <li>
                <a href="{{ route('admin.reports.leaves') }}" class="{{ request()->routeIs('admin.reports.leaves') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Leave Reports
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.attendance') }}" class="{{ request()->routeIs('admin.reports.attendance') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Attendance Reports
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.tasks') }}" class="{{ request()->routeIs('admin.reports.tasks') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i> Task Reports
                </a>
            </li>
            <!--
            <li>
                <a href="{{ route('admin.assets.index') }}" class="{{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">
                    <i class="fa fa-laptop"></i> Assets
                </a>
            </li>
            <li>
                <a href="{{ route('admin.api-tokens.index') }}" class="{{ request()->routeIs('admin.api-tokens.*') ? 'active' : '' }}">
                    <i class="fa fa-key"></i> API Tokens
                </a>
            </li>
            -->
            <li>
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" onclick="document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i> Logout
                    </a>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div class="content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <button class="btn" id="sidebarCollapse">
                <i class="fa fa-bars"></i> <span class="d-none d-md-inline">Menu</span>
            </button>
            <div class="user-info">
                <i class="fa fa-user-circle me-2"></i>
                <span>{{ Auth::user()->name }}</span>
            </div>
        </div>

        <div class="container-fluid px-2 py-3 p-sm-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    <i class="fa-solid fa-circle-check me-2"></i>{!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    <i class="fa-solid fa-circle-check me-2"></i>{!! session('status') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('message'))
                <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    <i class="fa-solid fa-circle-info me-2"></i>{!! session('message') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>{!! session('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{!! session('warning') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    $('#sidebarCollapse').on('click', function () {
        if ($(window).width() <= 768) {
            $('.sidebar').toggleClass('active');
            $('.sidebar-overlay').toggleClass('active');
        } else {
            $('.sidebar').toggleClass('collapsed');
        }
    });

    $('.sidebar-overlay').on('click', function() {
        $('.sidebar').removeClass('active');
        $(this).removeClass('active');
    });
</script>
@stack('scripts')
</body>
</html>
