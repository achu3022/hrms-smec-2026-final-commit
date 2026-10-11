<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Employee Dashboard - {{ config('app.name', 'HRMS') }}</title>
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
        // Request App Runtime Permissions for Location & Camera
        window.addEventListener('DOMContentLoaded', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {}, function(err) {}, { enableHighAccuracy: true, timeout: 5000 });
            }
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        stream.getTracks().forEach(function(track) { track.stop(); });
                    })
                    .catch(function(err) {});
            }
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery & jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f0f4f8; }
        
        /* Desktop Sidebar */
        .desktop-sidebar {
            width: 250px;
            background-color: #2b3a67; /* Dark blue from screenshot */
            color: #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 40;
            overflow-y: auto;
        }

        .desktop-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .desktop-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        .desktop-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            margin: 4px 12px;
            border-radius: 8px;
        }
        
        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 500;
        }
        
        .sidebar-item i {
            width: 24px;
            font-size: 1.1rem;
        }

        /* Topbar */
        .desktop-topbar {
            margin-left: 250px;
            height: 70px;
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        /* Main Layout Content */
        .desktop-content {
            margin-left: 250px;
            padding: 32px;
            min-height: calc(100vh - 70px);
        }

        /* Mobile Adjustments */
        @media (max-width: 1024px) {
            .desktop-sidebar { display: none; }
            .desktop-topbar { margin-left: 0; padding: 0 16px; }
            .desktop-content { margin-left: 0; padding: 16px; padding-bottom: 80px; }
            body { background-color: #f0f3f6; }
        }

        /* Mobile Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 50;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
        }
        
        @media (min-width: 768px) {
            .bottom-nav {
                display: none !important;
            }
        }
        
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #64748b;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 500;
            transition: color 0.2s;
            flex: 1;
            min-width: 0;
        }
        .bottom-nav-item i {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }
        .bottom-nav-item.active {
            color: #4f46e5;
        }
        
    </style>
    @stack('styles')
</head>
<body class="antialiased text-gray-900" x-data="{ mobileMenuOpen: false }" x-effect="document.body.style.overflow = mobileMenuOpen ? 'hidden' : ''">

    <!-- Desktop Sidebar -->
    <aside class="desktop-sidebar hidden lg:block shadow-xl">
        <div class="px-6 py-6 mb-4 flex items-center gap-3">
            <div class="bg-white rounded p-1">
                <img src="{{ asset('logo-new.webp') }}" class="h-6" alt="Logo">
            </div>
            <div>
                <div class="font-bold text-white text-lg leading-none">HRMS</div>
                <div class="text-xs text-indigo-200">Employee</div>
            </div>
        </div>

        <nav class="flex flex-col gap-1">
            <a href="{{ route('employee.dashboard') }}"
               class="sidebar-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="{{ route('employee.attendance.index') }}"
               class="sidebar-item {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock"></i> Attendance
            </a>
            <a href="{{ route('employee.leaves.index') }}"
               class="sidebar-item {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}">
                <i class="fa-regular fa-calendar-minus"></i> Leaves
            </a>
            <a href="{{ route('employee.early-going') }}"
               class="sidebar-item {{ request()->routeIs('employee.early-going*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i> Early Going
            </a>
            <a href="{{ route('employee.profile.index') }}"
               class="sidebar-item {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}">
                <i class="fa-regular fa-user"></i> Profile
            </a>

            @if(Auth::guard('employee')->user()->employee && Auth::guard('employee')->user()->employee->requires_daily_report)
            <a href="{{ route('employee.daily-reports.index') }}"
               class="sidebar-item {{ request()->routeIs('employee.daily-reports.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Daily Report
            </a>
            @endif

            @if(Auth::guard('employee')->user()->employee && Auth::guard('employee')->user()->employee->is_teamleader)
            <div class="mt-4 mb-2 px-6 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Team Management</div>
            <a href="{{ route('employee.team.leaves') }}"
               class="sidebar-item {{ request()->routeIs('employee.team.leaves') ? 'active' : '' }}">
                <i class="fa-solid fa-users-viewfinder"></i> Team Leaves
            </a>
            <a href="{{ route('employee.team.early-going') }}"
               class="sidebar-item {{ request()->routeIs('employee.team.early-going') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i> Team Early Going
            </a>
            <a href="{{ route('employee.team.attendance') }}"
               class="sidebar-item {{ request()->routeIs('employee.team.attendance') ? 'active' : '' }}">
                <i class="fa-solid fa-people-group"></i> Team Attendance
            </a>
            <a href="{{ route('employee.team.daily-reports.index') }}"
               class="sidebar-item {{ request()->routeIs('employee.team.daily-reports.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Team Tasks
            </a>
            @endif
        </nav>
    </aside>

    <!-- Topbar -->
    <header class="desktop-topbar">
        <div class="lg:hidden flex items-center gap-2">
            <img src="{{ asset('logo-new.webp') }}" class="h-8" alt="Logo">
            <span class="font-bold text-gray-800 text-lg">Portal</span>
        </div>
        <div class="hidden lg:block">
            <h1 class="text-xl font-bold text-slate-800">Employee Dashboard</h1>
            <p class="text-xs text-slate-500">Welcome back!</p>
        </div>

        <div class="flex items-center gap-6">
            <div class="hidden sm:flex items-center gap-4 text-slate-400">
                <!-- Notifications -->
                <div x-data="employeeNotifications({{ Auth::guard('employee')->id() }}, {{ json_encode(Auth::guard('employee')->user()->unreadNotifications->map(fn($n) => ['id' => $n->id, 'message' => $n->data['message'], 'time' => $n->created_at->diffForHumans()])) }})">
                    <div class="relative">
                        <button @click="open = !open" class="relative hover:text-indigo-600 transition-colors p-2 text-slate-400">
                            <i class="fa-regular fa-bell text-xl"></i>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border border-white"></span>
                                </span>
                            </template>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl py-0 z-50 ring-1 ring-black ring-opacity-5 overflow-hidden" style="display: none;">
                            <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-800 bg-gray-50/80 flex justify-between items-center sticky top-0 backdrop-blur-sm z-10">
                                <span>Notifications</span>
                                <template x-if="unreadCount > 0">
                                    <span x-text="unreadCount + ' new'" class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700"></span>
                                </template>
                            </div>
                            
                            <div class="max-h-[28rem] overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-8 text-sm text-gray-400 text-center flex flex-col items-center">
                                        <i class="fa-regular fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                                        <p>No new notifications</p>
                                    </div>
                                </template>

                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="px-4 py-3 border-b border-gray-50 hover:bg-indigo-50/50 transition-colors flex justify-between items-start group">
                                        <div class="text-sm text-gray-600 pr-3">
                                            <span x-text="notification.message" class="text-gray-700 font-medium leading-tight"></span>
                                            <div class="text-[11px] text-gray-400 mt-1 flex items-center gap-1 font-medium">
                                                <i class="fa-regular fa-clock"></i>
                                                <span x-text="notification.time"></span>
                                            </div>
                                        </div>
                                        <form method="POST" :action="`/employee/notifications/${notification.id}/mark-as-read`">
                                            @csrf
                                            <button type="submit" class="text-[10px] uppercase font-bold tracking-wider text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity hover:text-indigo-800 shrink-0 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded">Mark Read</button>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(Auth::guard('employee')->user()->employee && !Auth::guard('employee')->user()->employee->checkIsProfileComplete())
                <a href="{{ route('employee.profile.edit') }}" class="bg-amber-100 text-amber-800 border border-amber-300 text-[11px] font-bold px-2.5 py-1 rounded-full hover:bg-amber-200 transition-colors flex items-center gap-1 shadow-sm" title="Missing: {{ implode(', ', Auth::guard('employee')->user()->employee->getMissingProfileFields()) }}">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                    <span class="hidden md:inline">Incomplete Profile</span>
                </a>
            @endif

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-sm font-semibold text-slate-700">{{ Auth::guard('employee')->user()->employee->name ?? 'Employee' }}</div>
                    <div class="text-xs text-slate-400">{{ Auth::guard('employee')->user()->employee->designation ?? 'Staff' }}</div>
                </div>
                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold overflow-hidden shadow-sm">
                    @if(Auth::guard('employee')->user()->employee && Auth::guard('employee')->user()->employee->photo)
                        <img src="{{ asset('storage/' . Auth::guard('employee')->user()->employee->photo) }}" class="h-full w-full object-cover">
                    @else
                        {{ substr(Auth::guard('employee')->user()->employee->name ?? 'E', 0, 1) }}
                    @endif
                </div>
                
                <form method="POST" action="{{ route('employee.logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors text-xl" title="Logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="desktop-content">
        @yield('content')
    </main>

    <!-- Mobile Backdrop Overlay -->
    <div x-cloak
         x-show="mobileMenuOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[99] lg:hidden">
    </div>

    <!-- Mobile Slide-over Drawer Menu -->
    <aside x-cloak
           x-show="mobileMenuOpen"
           x-transition:enter="transform transition ease-in-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transform transition ease-in-out duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           style="background-color: #18223c !important;"
           class="fixed inset-y-0 left-0 z-[100] w-80 max-w-[85vw] text-white shadow-2xl flex flex-col justify-between lg:hidden border-r border-slate-700/60">
        
        <div style="background-color: #18223c !important;">
            <!-- Header -->
            <div class="p-5 border-b border-slate-700/60 flex items-center justify-between" style="background-color: #18223c !important;">
                <div class="flex items-center gap-3">
                    <div class="bg-white rounded p-1">
                        <img src="{{ asset('logo-new.webp') }}" class="h-6" alt="Logo">
                    </div>
                    <div>
                        <div class="font-bold text-white text-base leading-none">Team Management</div>
                        <div class="text-xs text-indigo-200 mt-1">Team Leader Portal</div>
                    </div>
                </div>
                <button type="button" @click="mobileMenuOpen = false" class="text-indigo-200 hover:text-white p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Menu Body (Only Team Management Links) -->
            <nav class="p-4 space-y-2 overflow-y-auto max-h-[calc(100vh-160px)]" style="background-color: #18223c !important;">
                <a href="{{ route('employee.team.leaves') }}"
                   @click="mobileMenuOpen = false"
                   class="sidebar-item {{ request()->routeIs('employee.team.leaves') ? 'active' : '' }} !mx-0">
                    <i class="fa-solid fa-users-viewfinder text-indigo-400"></i> Team Leaves
                </a>
                <a href="{{ route('employee.team.early-going') }}"
                   @click="mobileMenuOpen = false"
                   class="sidebar-item {{ request()->routeIs('employee.team.early-going') ? 'active' : '' }} !mx-0">
                    <i class="fa-solid fa-clock-rotate-left text-amber-400"></i> Team Early Going
                </a>
                <a href="{{ route('employee.team.attendance') }}"
                   @click="mobileMenuOpen = false"
                   class="sidebar-item {{ request()->routeIs('employee.team.attendance') ? 'active' : '' }} !mx-0">
                    <i class="fa-solid fa-people-group text-emerald-400"></i> Team Attendance
                </a>
                <a href="{{ route('employee.team.daily-reports.index') }}"
                   @click="mobileMenuOpen = false"
                   class="sidebar-item {{ request()->routeIs('employee.team.daily-reports.*') ? 'active' : '' }} !mx-0">
                    <i class="fa-solid fa-list-check text-blue-400"></i> Team Tasks
                </a>
            </nav>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-700/60 flex items-center justify-between" style="background-color: #111827 !important;">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="h-9 w-9 shrink-0 rounded-full bg-slate-800 flex items-center justify-center text-indigo-300 font-bold overflow-hidden border border-slate-700">
                    @if(Auth::guard('employee')->user()->employee && Auth::guard('employee')->user()->employee->photo)
                        <img src="{{ asset('storage/' . Auth::guard('employee')->user()->employee->photo) }}" class="h-full w-full object-cover">
                    @else
                        {{ substr(Auth::guard('employee')->user()->employee->name ?? 'E', 0, 1) }}
                    @endif
                </div>
                <div class="truncate">
                    <div class="text-sm font-semibold text-white truncate">{{ Auth::guard('employee')->user()->employee->name ?? 'Employee' }}</div>
                    <div class="text-xs text-indigo-300 truncate">Team Leader</div>
                </div>
            </div>

            <form method="POST" action="{{ route('employee.logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-400 p-2 transition-colors text-lg" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav md:hidden">
        <a href="{{ route('employee.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('employee.attendance.index') }}" class="bottom-nav-item {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i>
            <span>Attendance</span>
        </a>
        <a href="{{ route('employee.leaves.index') }}" class="bottom-nav-item {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar-minus"></i>
            <span>Leaves</span>
        </a>
        <a href="{{ route('employee.early-going') }}" class="bottom-nav-item {{ request()->routeIs('employee.early-going*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Early</span>
        </a>
        <a href="{{ route('employee.profile.index') }}" class="bottom-nav-item {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}">
            <i class="fa-regular fa-user"></i>
            <span>Profile</span>
        </a>
        @if(Auth::guard('employee')->user()->employee && Auth::guard('employee')->user()->employee->is_teamleader)
        <button type="button" @click="mobileMenuOpen = true" class="bottom-nav-item {{ request()->routeIs('employee.team.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear text-indigo-600"></i>
            <span class="font-semibold text-indigo-600">Team</span>
        </button>
        @elseif(Auth::guard('employee')->user()->employee && Auth::guard('employee')->user()->employee->requires_daily_report)
        <a href="{{ route('employee.daily-reports.index') }}" class="bottom-nav-item {{ request()->routeIs('employee.daily-reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Tasks</span>
        </a>
        @endif
    </nav>



    @stack('scripts')
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('employeeNotifications', (userId, initialNotifications) => ({
                open: false,
                notifications: initialNotifications,
                unreadCount: initialNotifications.length,
                init() {
                    if (window.Echo) {
                        window.Echo.private(`App.Models.EmployeeLogin.${userId}`)
                            .notification((notification) => {
                                this.notifications.unshift({
                                    id: notification.id,
                                    message: notification.message,
                                    time: 'Just now'
                                });
                                this.unreadCount++;
                            });
                    }
                }
            }))
        });
    </script>

</body>
</html>
