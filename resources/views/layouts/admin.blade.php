<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EIMS Admin') }}</title>
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
            --brand-teal: #00aa9e;
            --brand-blue: #0070bc;
            --brand-purple: #2f3092;
            --light-bg: #f8fafb;
            --sidebar-width: 260px;
        }
        
        body { 
            background: linear-gradient(135deg, #f8fafb 0%, #e8f4f8 100%);
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar { 
            min-height: 100vh;
            background: white;
            color: var(--brand-teal);
            transition: all 0.3s;
            box-shadow: 4px 0 20px rgba(0, 170, 158, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(0,170,158,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.8;
        }
        
        .sidebar-header {
            padding: 25px 20px !important;
            background: transparent;
            border-bottom: 1px solid rgba(0,170,158,0.2) !important;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--brand-teal);
        }
        
        .sidebar a { 
            color: var(--brand-teal);
            text-decoration: none;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            font-weight: 600;
            opacity: 0.85;
        }
        
        .sidebar a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .sidebar a:hover { 
            background: rgba(0,170,158,0.05);
            color: var(--brand-teal);
            border-left-color: var(--brand-teal);
            padding-left: 25px;
            opacity: 1;
        }
        
        .sidebar a.active { 
            background: rgba(0,170,158,0.1);
            color: var(--brand-teal);
            border-left-color: var(--brand-teal);
            font-weight: 700;
            opacity: 1;
        }
        
        .content { 
            width: 100%;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        .topbar { 
            background: white;
            box-shadow: 0 4px 20px rgba(0, 170, 158, 0.08);
            padding: 15px 30px;
            border-bottom: 1px solid rgba(0, 170, 158, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .topbar .btn {
            border: 2px solid var(--brand-teal);
            color: var(--brand-teal);
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .topbar .btn:hover {
            background: var(--brand-teal);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,170,158,0.3);
        }
        
        .user-info {
            color: var(--brand-purple);
            font-weight: 600;
            font-size: 1rem;
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
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-purple));
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,112,188,0.4);
        }
        
        .btn-success {
            background: var(--brand-teal);
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 600;
        }
        
        .btn-success:hover {
            background: #008f85;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,170,158,0.4);
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
                height: 100vh;
                transition: left 0.3s ease;
            }
            .sidebar.active {
                left: 0;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }
        @media (min-width: 769px) {
            .sidebar.collapsed {
                margin-left: -260px;
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
            <img src="{{ asset('logo-new.webp') }}" alt="Logo" style="max-width: 180px; height: auto;" class="mb-2">
            <div class="mt-2">
                <small class="text-muted fw-bold">Admin Dashboard</small>
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
            <!-- Uncomment these features when ready to use them -->
            <!--
            <li>
                <a href="{{ route('admin.leave-types.index') }}" class="{{ request()->routeIs('admin.leave-types.*') ? 'active' : '' }}">
                    <i class="fa fa-cogs"></i> Leave Types
                </a>
            </li>
            <li>
                <a href="{{ route('admin.leave-requests.index') }}" class="{{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">
                    <i class="fa fa-calendar-alt"></i> Leave Requests
                </a>
            </li>
            <li>
                <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                    <i class="fa fa-clock"></i> Attendance
                </a>
            </li>
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

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
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
