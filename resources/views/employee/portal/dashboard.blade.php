@extends('employee.portal.layout')

@section('content')

@php
    $attendanceState = 'unpunched';
    $punchInTime = null;
    $punchOutTime = null;
    $totalWorkHoursStr = null;
    $isHalfDay = false;

    if ($todayAttendance) {
        if ($todayAttendance->check_in && !$todayAttendance->check_out) {
            $attendanceState = 'punched_in';
            $punchInTime = \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A');
        } elseif ($todayAttendance->check_in && $todayAttendance->check_out) {
            $attendanceState = 'punched_out';
            $punchInTime = \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A');
            $punchOutTime = \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A');
            
            $checkIn = \Carbon\Carbon::parse($todayAttendance->check_in);
            $checkOut = \Carbon\Carbon::parse($todayAttendance->check_out);
            $diffInMinutes = $checkIn->diffInMinutes($checkOut);
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            $totalWorkHoursStr = "{$hours}h {$minutes}m";
        }
        
        if ($todayAttendance->status === 'Half-Day' || $todayAttendance->status === 'Late') {
            $isHalfDay = true;
        }
    }
@endphp

@push('styles')
<style>
    .ep-dash-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(30, 27, 75, 0.25);
        position: relative;
        overflow: hidden;
    }
    .ep-dash-hero::before {
        content: '';
        position: absolute;
        top: -40%; right: -15%;
        width: 340px; height: 340px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }
    .ep-stat-card {
        background: #ffffff;
        border: 1px solid #e0e7ff;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .ep-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.12);
        border-color: #c7d2fe;
    }
    .ep-stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        transition: all 0.25s ease;
    }

    /* ── Celebrations ───────────────────────────────── */
    .ep-celebrations-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .ep-celebration-tabs {
        display: flex;
        gap: 6px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 4px;
        border-radius: 12px;
    }
    .ep-celebration-tab-btn {
        background: none;
        border: none;
        font-size: 0.78rem;
        font-weight: 800;
        color: #64748b;
        padding: 6px 14px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .ep-celebration-tab-btn.active {
        color: #4f46e5;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .ep-celebration-list { display: flex; flex-direction: column; gap: 12px; }
    .ep-celebration-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    .ep-celebration-item:hover { background: #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,0.06); transform: scale(1.01); }
    .ep-celebration-profile { display:flex; align-items:center; gap:12px; }
    .ep-celebration-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.9rem;
    }
    .ep-celebration-info { display:flex; flex-direction:column; }
    .ep-celebration-name { font-size:0.88rem; font-weight:800; color:#1e293b; }
    .ep-celebration-sub  { font-size:0.75rem; color:#64748b; font-weight: 500; }
    .ep-badge {
        font-size: 0.78rem; font-weight: 800;
        padding: 6px 12px; border-radius: 10px;
        display: flex; align-items: center; gap: 6px;
    }
    .ep-badge.birthday    { background: linear-gradient(135deg, #fff0f6 0%, #ffdeeb 100%); color: #e03131; border: 1px solid #fcc2d7; }
    .ep-badge.birthday.is-today { background: linear-gradient(135deg, #fff9db 0%, #ffe066 100%); color: #854d0e; border: 1px solid #fab005; }
    .ep-badge.anniversary { background: linear-gradient(135deg, #e6fcf5 0%, #c3fae8 100%); color: #0ca678; border: 1px solid #96f2d7; }

    /* ── Calendar ───────────────────────────────────── */
    .ep-calendar-day-cell {
        aspect-ratio: 1;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        border-radius: 50%;
        font-size: 0.82rem; font-weight: 700;
        cursor: pointer; position: relative;
        transition: all 0.2s ease;
        color: #475569;
    }
    .ep-calendar-day-cell:hover   { background-color: #e0e7ff; color: #4338ca; }
    .ep-calendar-day-cell.other-month { color: #cbd5e1; pointer-events:none; }
    .ep-calendar-day-cell.today   { border: 2.5px solid #4f46e5; font-weight: 900; }
    .ep-calendar-day-cell.has-holiday  { background:#fff5f5!important; color:#e03131!important; border:1px solid #ffc9c9; }
    .ep-calendar-day-cell.has-birthday { background:#fff9db!important; color:#f08c00!important; border:1px solid #ffe066; }
    .ep-calendar-day-cell.has-anniversary { background:#ebfbee!important; color:#2b8a3e!important; border:1px solid #b2f2bb; }
    .ep-legend-dot { display:inline-block; width:8px; height:8px; border-radius:50%; }

    /* ── Popover ────────────────────────────────────── */
    .ep-cal-popover {
        position: absolute;
        width: 270px; background:#fff;
        border-radius: 16px;
        box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        border: 1px solid #e2e8f0;
        z-index: 100; padding: 16px;
        display: none; box-sizing: border-box;
    }
    .ep-popover-header {
        display: flex; justify-content:space-between; align-items:center;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 8px; margin-bottom: 10px;
    }
    .ep-popover-body { max-height: 160px; overflow-y:auto; }
    .ep-event-item {
        display:flex; align-items:center; justify-content:space-between;
        padding: 8px 10px; border-radius:10px;
        background:#f8fafc; border:1px solid #f1f5f9;
        margin-bottom: 6px;
    }
    .ep-event-item:last-child { margin-bottom:0; }
    .ep-event-left  { display:flex; align-items:center; gap:8px; min-width:0; }
    .ep-event-icon  {
        width:28px; height:28px; border-radius:50%;
        display:flex; align-items:center; justify-content:center; font-size:0.8rem;
    }
    .ep-event-info  { display:flex; flex-direction:column; min-width:0; }
    .ep-event-title { font-size:0.78rem; font-weight:700; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .ep-event-sub   { font-size:0.68rem; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .ep-event-badge { font-size:0.68rem; font-weight:700; padding:2px 7px; border-radius:5px; }

    /* ── Punch Button Fallback Styles ── */
    .bg-slate-400 { background-color: #94a3b8 !important; }
    .bg-emerald-500 { background-color: #10b981 !important; }
    .hover\:bg-emerald-600:hover { background-color: #059669 !important; }
    .bg-red-500 { background-color: #ef4444 !important; }
    .hover\:bg-red-600:hover { background-color: #dc2626 !important; }
    .text-white { color: #ffffff !important; }
    .opacity-70 { opacity: 0.7 !important; }
    .cursor-not-allowed { cursor: not-allowed !important; }
    .bg-green-100 { background-color: #dcfce7 !important; }
    .text-green-700 { color: #15803d !important; }
    .bg-red-100 { background-color: #fee2e2 !important; }
    .text-red-700 { color: #b91c1c !important; }

    /* ── News Ticker Bar for Mobile & Desktop Banner ── */
    .ep-desktop-banner { display: block !important; }
    .ep-mobile-ticker  { display: none !important; }

    @media (max-width: 767px) {
        .ep-desktop-banner { display: none !important; }
        .ep-mobile-ticker  { display: block !important; }
    }

    .ep-news-ticker-container {
        background: linear-gradient(90deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        border: 1px solid #4338ca;
        border-radius: 14px;
        padding: 10px 14px;
        color: #ffffff;
        display: flex;
        align-items: center;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
        box-shadow: 0 4px 14px rgba(49, 46, 129, 0.2);
        cursor: pointer;
    }
    .ep-ticker-badge {
        background: #ef4444;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 900;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        flex-shrink: 0;
        margin-right: 10px;
        letter-spacing: 0.5px;
    }
    .ep-ticker-wrapper {
        flex: 1;
        overflow: hidden;
        position: relative;
    }
    .ep-ticker-text {
        display: inline-block;
        padding-left: 100%;
        animation: ep-marquee 20s linear infinite;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .ep-news-ticker-container:hover .ep-ticker-text {
        animation-play-state: paused;
    }
    @keyframes ep-marquee {
        0%   { transform: translate(0, 0); }
        100% { transform: translate(-100%, 0); }
    }
</style>
@endpush

@if(isset($activeAnnouncements) && $activeAnnouncements->count() > 0)
    <!-- Mobile View: News Ticker -->
    <div class="ep-mobile-ticker mb-4" x-data="{ selectedAnnouncement: null }">
        @foreach($activeAnnouncements as $announcement)
            <div @click="selectedAnnouncement = {{ json_encode($announcement) }}" class="ep-news-ticker-container">
                <span class="ep-ticker-badge">
                    @if($announcement->type === 'urgent')
                        🚨 URGENT
                    @elseif($announcement->type === 'warning')
                        ⚠️ ALERT
                    @else
                        📢 NOTICE
                    @endif
                </span>
                <div class="ep-ticker-wrapper">
                    <span class="ep-ticker-text">
                        <strong class="text-amber-300 me-1">{{ $announcement->title }}:</strong> {{ $announcement->message }}
                    </span>
                </div>
                <span class="text-xs text-indigo-200 shrink-0 ml-2 font-bold bg-white/10 px-2 py-0.5 rounded">Details &rsaquo;</span>
            </div>
        @endforeach

        <!-- Mobile Announcement Detail Modal -->
        <template x-if="selectedAnnouncement">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm" @click.self="selectedAnnouncement = null">
                <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border border-slate-100">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between"
                         :class="{
                             'bg-red-50 text-red-700': selectedAnnouncement.type === 'urgent',
                             'bg-amber-50 text-amber-800': selectedAnnouncement.type === 'warning',
                             'bg-indigo-50 text-indigo-800': selectedAnnouncement.type === 'info'
                         }">
                        <div class="flex items-center gap-2 font-bold text-sm">
                            <i class="fa-solid" :class="{
                                'fa-circle-exclamation text-red-600': selectedAnnouncement.type === 'urgent',
                                'fa-triangle-exclamation text-amber-600': selectedAnnouncement.type === 'warning',
                                'fa-bullhorn text-indigo-600': selectedAnnouncement.type === 'info'
                            }"></i>
                            <span x-text="selectedAnnouncement.type.toUpperCase() + ' ANNOUNCEMENT'"></span>
                        </div>
                        <button type="button" @click="selectedAnnouncement = null" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                    </div>
                    <div class="p-5 space-y-3">
                        <h3 class="text-base font-bold text-slate-800 leading-snug" x-text="selectedAnnouncement.title"></h3>
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line" x-text="selectedAnnouncement.message"></p>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                            <span x-text="selectedAnnouncement.target_type === 'all' ? 'Target: All Employees' : (selectedAnnouncement.target_type === 'individual' ? 'Target: Direct Notice' : ('Target: ' + (selectedAnnouncement.department ? selectedAnnouncement.department.name : 'Team')))"></span>
                            <span x-text="new Date(selectedAnnouncement.created_at).toLocaleDateString()"></span>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 border-t border-slate-100 text-right">
                        <button type="button" @click="selectedAnnouncement = null" class="px-4 py-1.5 bg-slate-800 text-white rounded-xl font-semibold text-xs shadow-sm">Got it</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Desktop View: Normal Banner -->
    <div class="ep-desktop-banner mb-6 space-y-3">
        @foreach($activeAnnouncements as $announcement)
            <div class="rounded-2xl p-4 shadow-sm border flex items-start justify-between gap-4 transition-all hover:shadow-md
                @if($announcement->type === 'urgent')
                    bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white border-red-500
                @elseif($announcement->type === 'warning')
                    bg-gradient-to-r from-amber-600 via-amber-700 to-orange-600 text-white border-amber-500
                @else
                    bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 text-white border-indigo-800
                @endif
            " style="background: {{ $announcement->type === 'urgent' ? 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)' : ($announcement->type === 'warning' ? 'linear-gradient(135deg, #d97706 0%, #b45309 100%)' : 'linear-gradient(135deg, #1e1b4b 0%, #312e81 100%)') }};">
                <div class="flex items-start gap-3.5">
                    <div class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center text-xl bg-white/15 backdrop-blur-md border border-white/20">
                        @if($announcement->type === 'urgent')
                            🚨
                        @elseif($announcement->type === 'warning')
                            ⚠️
                        @else
                            📢
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-white/20 text-white backdrop-blur-sm">
                                {{ $announcement->target_type === 'all' ? 'All Employees' : ($announcement->target_type === 'individual' ? 'Direct Notice' : ($announcement->department->name ?? 'Team Notice')) }}
                            </span>
                            <span class="text-xs text-white/70 font-medium">&bull; {{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                        <h4 class="text-base font-bold leading-snug text-white" style="margin:0;">{{ $announcement->title }}</h4>
                        <p class="text-xs text-white/90 font-medium mt-1 leading-relaxed max-w-3xl mb-0">{{ $announcement->message }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Top Welcome Hero Banner --}}
<div class="ep-dash-hero">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-indigo-200 mb-2">
                <i class="fa-solid fa-sparkles text-amber-300"></i> Employee Portal &bull; {{ now()->format('l, d F Y') }}
            </div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">
                Welcome back, {{ explode(' ', $employee->name)[0] }} 👋
            </h1>
            <p class="text-indigo-200 text-sm font-medium mt-1">
                {{ $employee->designationRel->name ?? $employee->designation ?? 'Staff Member' }} &bull; {{ $employee->departmentRel->name ?? $employee->department ?? 'General' }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('employee.qr.scan') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white backdrop-blur-md border border-indigo-400/40 px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-indigo-600/30">
                <i class="fa-solid fa-qrcode text-sm"></i> Scan Office QR
            </a>
            <a href="{{ route('employee.profile.edit') }}" class="bg-white/15 hover:bg-white/25 text-white backdrop-blur-md border border-white/20 px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-user-pen"></i> Edit Profile
            </a>
        </div>
    </div>
</div>

<div class="mb-6">
    <!-- My Stats Horizontal Grid Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Leaves Month -->
        <div class="ep-stat-card group">
            <div class="ep-stat-icon bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-2xl md:text-3xl font-black text-slate-800 leading-none mb-1">{{ $leavesMonth }}</p>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Leaves (This Month)</p>
            </div>
        </div>

        <!-- Leaves Year -->
        <div class="ep-stat-card group">
            <div class="ep-stat-icon bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <p class="text-2xl md:text-3xl font-black text-slate-800 leading-none mb-1">{{ $leavesYear }}</p>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Leaves (This Year)</p>
            </div>
        </div>

        <!-- Late Time Month -->
        <div class="ep-stat-card group">
            <div class="ep-stat-icon bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-xl md:text-2xl font-black text-slate-800 leading-none mb-1">{{ $statsLateTime }}</p>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Late (This Month)</p>
            </div>
        </div>

        <!-- Early Going Month -->
        <div class="ep-stat-card group">
            <div class="ep-stat-icon bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white">
                <i class="fa-solid fa-person-running"></i>
            </div>
            <div>
                <p class="text-xl md:text-2xl font-black text-slate-800 leading-none mb-1">{{ $statsEarlyTime }}</p>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Early (This Month)</p>
            </div>
        </div>
    </div>
</div>

@php
    $missingFields = $employee->getMissingProfileFields();
@endphp

@if(count($missingFields) > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-4 rounded-xl shadow-sm mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-lg mt-0.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-amber-900">Complete Your Profile Information</h4>
                <p class="text-xs text-amber-800 mt-0.5">
                    Your profile is incomplete. Missing required fields: 
                    <strong class="font-bold text-amber-900 underline">{{ implode(', ', $missingFields) }}</strong>. 
                    Please complete your profile.
                </p>
            </div>
        </div>
        <a href="{{ route('employee.profile.edit') }}" class="shrink-0 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-user-pen"></i> Complete Profile
        </a>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Column 1 -->
    <div class="space-y-6">
        
        <!-- Attendance Widget -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-rose-500 text-lg"></i>
                    <div>
                        <h3 class="font-bold text-slate-800 leading-tight">Attendance</h3>
                        <p class="text-xs text-slate-500">Punch in/out for today</p>
                    </div>
                </div>
                <div id="geofenceStatus" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                    <i class="fa-solid fa-spinner fa-spin"></i> Locating...
                </div>
            </div>
            
            <div class="p-5">
                
                <!-- Map Container -->
                <div id="mapContainer" class="mb-4 w-full bg-slate-100 rounded-lg overflow-hidden relative" style="height: 160px; border: 1px solid #e2e8f0;">
                    <div id="punchMap" style="width: 100%; height: 100%;"></div>
                    <div id="mapOverlay" class="absolute inset-0 bg-white/80 flex items-center justify-center flex-col z-10">
                        <i class="fa-solid fa-location-dot text-3xl text-indigo-500 mb-2 animate-bounce"></i>
                        <p class="text-sm font-semibold text-slate-700">Fetching Location...</p>
                        <p class="text-xs text-slate-500 text-center px-4 mt-1">Please allow browser location access</p>
                    </div>
                </div>
                
                @if($attendanceState === 'unpunched')
                    <!-- Unpunched State -->
                    <div class="text-center py-2" id="unpunched-view">
                        <p class="text-xs font-semibold text-slate-700 mb-1">Punch Machine Time</p>
                        <div class="border border-slate-200 rounded-lg py-2 px-4 mb-4 flex justify-between items-center bg-white shadow-inner">
                            <span class="font-mono text-lg tracking-wider font-semibold text-slate-800" id="live-clock">--:--:--</span>
                            <i class="fa-regular fa-clock text-slate-400"></i>
                        </div>
                        
                        <button onclick="handlePunch('in')" id="punchInBtn" disabled class="w-full bg-slate-400 text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2 opacity-70 cursor-not-allowed mb-2.5">
                            <i class="fa-solid fa-right-to-bracket"></i> Punch In (GPS)
                        </button>

                        <a href="{{ route('employee.qr.scan') }}" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-indigo-500/20 transition flex justify-center items-center gap-2 text-sm">
                            <i class="fa-solid fa-qrcode text-base"></i> Scan Office QR (No GPS Needed)
                        </a>
                    </div>
                @endif
                
                <div id="punched-view" class="{{ $attendanceState === 'unpunched' ? 'hidden' : '' }}">
                    <!-- Status Cards -->
                    <div class="space-y-3 mb-4">
                        <!-- Punched In Card -->
                        <div class="border border-emerald-200 bg-emerald-50 rounded-lg p-3 flex justify-between items-center" id="card-punched-in" style="display: {{ $attendanceState !== 'unpunched' ? 'flex' : 'none' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-800 leading-tight">Punched In</p>
                                    <p class="text-xs text-emerald-600" id="txt-punch-in">{{ $punchInTime ?? '--:--' }}</p>
                                </div>
                            </div>
                            <span class="bg-emerald-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">Active</span>
                        </div>

                        <!-- Half Day Warning (if applicable) -->
                        @if($isHalfDay)
                        <div class="border border-orange-200 bg-orange-50 rounded-lg p-3">
                            <div class="flex items-start gap-2">
                                <i class="fa-solid fa-triangle-exclamation text-orange-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold text-orange-800 leading-tight">Marked as Half Day / Late</p>
                                    <p class="text-xs text-orange-700 mt-1">Punch time after allowed grace period.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Punched Out Card -->
                        <div class="border border-red-200 bg-red-50 rounded-lg p-3 flex justify-between items-center" id="card-punched-out" style="display: {{ $attendanceState === 'punched_out' ? 'flex' : 'none' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-red-800 leading-tight">Punched Out</p>
                                    <p class="text-xs text-red-600" id="txt-punch-out">{{ $punchOutTime ?? '--:--' }}</p>
                                </div>
                            </div>
                            <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">Completed</span>
                        </div>

                        <!-- Working Time / Total Hours -->
                        <div class="border border-blue-200 bg-blue-50 rounded-lg p-3 text-center" id="card-working-time">
                            <p class="text-xs font-bold text-blue-800 mb-1" id="lbl-working-time">
                                {{ $attendanceState === 'punched_out' ? 'Total Work Hours' : 'Working Time' }}
                            </p>
                            <p class="text-2xl font-bold text-blue-600 font-mono tracking-wider" id="val-working-time">
                                {{ $attendanceState === 'punched_out' ? $totalWorkHoursStr : '00:00:00' }}
                            </p>
                        </div>
                    </div>

                    @if($attendanceState !== 'punched_out')
                        <button onclick="handlePunch('out')" id="punchOutBtn" disabled class="w-full bg-slate-400 text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2 opacity-70 cursor-not-allowed mb-2.5">
                            <i class="fa-solid fa-right-from-bracket"></i> Punch Out (GPS)
                        </button>

                        <a href="{{ route('employee.qr.scan') }}" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-indigo-500/20 transition flex justify-center items-center gap-2 text-sm">
                            <i class="fa-solid fa-qrcode text-base"></i> Scan Office QR (No GPS Needed)
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <!-- Column 2 -->
    <div class="space-y-6">
        


        <!-- Upcoming Celebrations (DB-driven) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <div class="ep-celebrations-header">
                <h3 class="font-bold text-slate-800 text-sm">Upcoming Celebrations</h3>
                <div class="ep-celebration-tabs">
                    <button id="epBtnBdays" class="ep-celebration-tab-btn active" onclick="epSwitchCeleb('birthdays')">Birthdays</button>
                    <button id="epBtnAnnivs" class="ep-celebration-tab-btn" onclick="epSwitchCeleb('anniversaries')">Anniversaries</button>
                </div>
            </div>

            <!-- Birthdays -->
            <div id="epCelebBirthdays" class="ep-celebration-list">
                @forelse($upcomingBirthdays as $emp)
                    @php
                        $initials = collect(explode(' ', $emp->name))->map(fn($n)=>mb_substr($n,0,1))->take(2)->join('');
                        $dobCarbon = \Carbon\Carbon::parse($emp->dob);
                        $isToday = $dobCarbon->isBirthday();
                        $bdayFormatted = $dobCarbon->format('d M');
                    @endphp
                    <div class="ep-celebration-item">
                        <div class="ep-celebration-profile">
                            @if($emp->photo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($emp->photo) }}" alt="{{ $emp->name }}" class="w-9 h-9 rounded-full object-cover border border-pink-200 shadow-sm">
                            @else
                                <div class="ep-celebration-avatar" style="background: linear-gradient(135deg, #fff0f6, #ffe3e3); color: #d6336c; border: 1px solid #fcc2d7;">{{ $initials }}</div>
                            @endif
                            <div class="ep-celebration-info">
                                <span class="ep-celebration-name">{{ $emp->name }}</span>
                                <span class="ep-celebration-sub">{{ $emp->designationRel->name ?? $emp->designation ?? 'Staff' }} &bull; {{ $emp->departmentRel->name ?? $emp->department ?? 'General' }}</span>
                            </div>
                        </div>
                        <span class="ep-badge birthday {{ $isToday ? 'is-today' : '' }}">
                            <i class="fa-solid fa-cake-candles {{ $isToday ? 'fa-bounce' : '' }}"></i>
                            <span>{{ $isToday ? 'Today 🎉 ' . $bdayFormatted : $bdayFormatted }}</span>
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">No upcoming birthdays this month.</p>
                @endforelse
            </div>

            <!-- Anniversaries -->
            <div id="epCelebAnnivs" class="ep-celebration-list" style="display:none;">
                @forelse($workAnniversaries as $emp)
                    @php
                        $initials = collect(explode(' ', $emp->name))->map(fn($n)=>mb_substr($n,0,1))->take(2)->join('');
                        $years = $emp->anniversary_years;
                    @endphp
                    <div class="ep-celebration-item">
                        <div class="ep-celebration-profile">
                            @if($emp->photo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($emp->photo) }}" alt="{{ $emp->name }}" class="w-9 h-9 rounded-full object-cover border border-emerald-200 shadow-sm">
                            @else
                                <div class="ep-celebration-avatar" style="background:#e6fcf5;color:#0ca678;">{{ $initials }}</div>
                            @endif
                            <div class="ep-celebration-info">
                                <span class="ep-celebration-name">{{ $emp->name }}</span>
                                <span class="ep-celebration-sub">Joined {{ \Carbon\Carbon::parse($emp->joining_date)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                        <span class="ep-badge anniversary"><i class="fa-solid fa-award"></i> {{ $years > 0 ? $years.' Yr'.($years>1?'s':'') : 'New' }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">No anniversaries in the next 7 days.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Column 3: Events Calendar -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5" style="position:relative;">
            <!-- Header -->
            <div class="flex justify-between items-center mb-3 pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-calendar-days text-indigo-500 mr-1"></i> Events Calendar</h3>
                <div class="flex gap-1 items-center">
                    <button onclick="epPrevMonth()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs flex items-center justify-center transition">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <span id="epCalMonthYear" class="font-bold text-slate-700 text-xs min-w-[88px] text-center"></span>
                    <button onclick="epNextMonth()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs flex items-center justify-center transition">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Weekday headers -->
            <div class="grid grid-cols-7 text-center text-slate-400 font-bold mb-1" style="font-size:0.7rem;letter-spacing:0.5px;">
                <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
            </div>

            <!-- Day grid -->
            <div id="epCalGrid" class="grid grid-cols-7 text-center gap-1 mb-3">
                <!-- injected by JS -->
            </div>

            <!-- Legend -->
            <div class="flex justify-center gap-4 pt-2 border-t border-slate-100" style="font-size:0.68rem;font-weight:600;color:#64748b;">
                <span class="flex items-center gap-1"><span class="ep-legend-dot" style="background:#f08c00;"></span>Birthday</span>
                <span class="flex items-center gap-1"><span class="ep-legend-dot" style="background:#2b8a3e;"></span>Anniversary</span>
                <span class="flex items-center gap-1"><span class="ep-legend-dot" style="background:#e03131;"></span>Holiday</span>
            </div>

            <!-- Popover -->
            <div id="epCalPopover" class="ep-cal-popover" style="display:none;">
                <div class="ep-popover-header">
                    <span id="epPopoverTitle" style="font-size:0.8rem;font-weight:700;color:#1e293b;">Events</span>
                    <button onclick="epClosePopover()" style="background:none;border:none;font-size:1.1rem;color:#94a3b8;cursor:pointer;">&times;</button>
                </div>
                <div id="epPopoverBody" class="ep-popover-body"></div>
            </div>
        </div>
    </div>


</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry&loading=async&callback=initMap" async defer></script>

<script>
    // ── Time Sync (Asia/Kolkata local device fallback) ──────────────
    let internetNow = null;
    let syncedAt   = null;

    function getInternetNow() {
        if (!internetNow) return null;
        const elapsedMs = performance.now() - syncedAt;
        return new Date(internetNow.getTime() + elapsedMs);
    }

    function syncInternetTime(callback) {
        // Use local device time formatted to Asia/Kolkata as a reliable fallback
        const now = new Date();
        const kolkataTimeStr = now.toLocaleString("en-US", {timeZone: "Asia/Kolkata"});
        internetNow = new Date(kolkataTimeStr);
        syncedAt = performance.now();
        if (callback) callback();
    }

    // Live Clock for Punch Machine Time
    function updateClock() {
        const now = getInternetNow();
        if (!now) return;
        let hours   = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
        const ampm  = hours >= 12 ? 'PM' : 'AM';
        hours   = hours % 12 || 12;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        const strTime = hours + ':' + minutes + ':' + seconds + ' ' + ampm;

        const clockEl = document.getElementById('live-clock');
        if (clockEl) clockEl.innerText = strTime;
    }

    // Start clock after first sync
    syncInternetTime(() => {
        setInterval(updateClock, 1000);
        updateClock();
    });

    // Re-sync every 10 minutes to correct any drift
    setInterval(() => syncInternetTime(), 10 * 60 * 1000);

    // Working Time counter logic (if punched in but not out)
    @if($attendanceState === 'punched_in')
        const punchInTimeString = "{{ $todayAttendance->check_in ?? '' }}";
        if (punchInTimeString) {
            // Build punch-in Date in Asia/Kolkata context using today's date
            const punchInDate = (() => {
                const d = new Date();
                const [h, m, s] = punchInTimeString.split(':');
                return new Date(d.getFullYear(), d.getMonth(), d.getDate(),
                                parseInt(h), parseInt(m), parseInt(s || 0));
            })();

            function updateWorkingTime() {
                const now  = getInternetNow();
                if (!now) return;
                let diff = Math.floor((now - punchInDate) / 1000);
                if (diff < 0) diff = 0;

                const h = Math.floor(diff / 3600).toString().padStart(2, '0');
                const m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
                const s = (diff % 60).toString().padStart(2, '0');

                document.getElementById('val-working-time').innerText = `${h}:${m}:${s}`;
            }
            setInterval(updateWorkingTime, 1000);
            updateWorkingTime();
        }
    @endif


    let employeeLat = null;
    let employeeLng = null;
    let employeeAccuracy = null;
    let map = null;
    let empMarker = null;
    let isInsideGeofence = false;

    const officeData = {
        lat: {{ \App\Models\OfficeLocation::first()->latitude ?? 9.99419969778416 }},
        lng: {{ \App\Models\OfficeLocation::first()->longitude ?? 76.2922585260837 }},
        radius: {{ \App\Models\OfficeLocation::first()->radius ?? 100 }}
    };

    function initMap() {
        if (!navigator.geolocation) {
            document.getElementById('mapOverlay').innerHTML = '<p class="text-sm font-bold text-red-500">Geolocation not supported</p>';
            return;
        }

        const officePos = { lat: officeData.lat, lng: officeData.lng };
        
        map = new google.maps.Map(document.getElementById("punchMap"), {
            zoom: 18,
            center: officePos,
            mapTypeId: 'satellite',
            disableDefaultUI: true,
            zoomControl: true
        });

        const officeMarker = new google.maps.Marker({
            position: officePos,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
            title: 'Office'
        });

        const circle = new google.maps.Circle({
            map: map,
            radius: officeData.radius,
            fillColor: '#00AA00',
            fillOpacity: 0.1,
            strokeColor: '#00AA00',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            center: officePos
        });

        // Watch position continuously
        navigator.geolocation.watchPosition(
            (position) => {
                employeeLat = position.coords.latitude;
                employeeLng = position.coords.longitude;
                employeeAccuracy = position.coords.accuracy;
                
                const empPos = { lat: employeeLat, lng: employeeLng };
                
                if (!empMarker) {
                    empMarker = new google.maps.Marker({
                        position: empPos,
                        map: map,
                        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        title: 'You are here'
                    });
                    document.getElementById('mapOverlay').style.display = 'none';
                } else {
                    empMarker.setPosition(empPos);
                }

                checkGeofenceStatus(empPos, officePos);
            },
            (error) => {
                let msg = "Location Access Denied";
                if(error.code === 2) msg = "Location Unavailable";
                if(error.code === 3) msg = "Location Timeout";
                
                document.getElementById('mapOverlay').innerHTML = `<i class="fa-solid fa-triangle-exclamation text-3xl text-red-500 mb-2"></i><p class="text-sm font-bold text-slate-700">${msg}</p><p class="text-xs text-slate-500 mt-1 px-4 text-center">Check device location settings and permissions.</p>`;
                
                updateGeofenceUI(false, "Locating Error");
            },
            { enableHighAccuracy: true, maximumAge: 10000, timeout: 30000 }
        );
    }
    
    function checkGeofenceStatus(empPos, officePos) {
        if (!google.maps.geometry) return;
        
        const empLatLng = new google.maps.LatLng(empPos.lat, empPos.lng);
        const offLatLng = new google.maps.LatLng(officePos.lat, officePos.lng);
        
        const distance = google.maps.geometry.spherical.computeDistanceBetween(empLatLng, offLatLng);
        
        if (employeeAccuracy > 1500) {
            updateGeofenceUI(false, "Poor GPS Accuracy (" + Math.round(employeeAccuracy) + "m)");
        } else if (distance <= officeData.radius) {
            updateGeofenceUI(true, "Inside Premises");
        } else {
            updateGeofenceUI(false, "Outside Premises (" + Math.round(distance) + "m away)");
        }
    }
    
    function updateGeofenceUI(isValid, statusText) {
        isInsideGeofence = isValid;
        
        const statusBadge = document.getElementById('geofenceStatus');
        
        if (isValid) {
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700';
            statusBadge.innerHTML = `<i class="fa-solid fa-check-circle"></i> ${statusText}`;
            enablePunchButtons(true, 'bg-emerald-500 hover:bg-emerald-600');
        } else {
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700';
            statusBadge.innerHTML = `<i class="fa-solid fa-xmark-circle"></i> ${statusText}`;
            enablePunchButtons(false, 'bg-slate-400');
        }
    }
    
    function enablePunchButtons(enable, colorClass) {
        const btnIn = document.getElementById('punchInBtn');
        const btnOut = document.getElementById('punchOutBtn');
        
        if (btnIn) {
            btnIn.disabled = !enable;
            btnIn.className = `w-full ${colorClass} text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2 ${enable ? '' : 'opacity-70 cursor-not-allowed'}`;
        }
        if (btnOut) {
            btnOut.disabled = !enable;
            btnOut.className = `w-full ${enable ? 'bg-red-500 hover:bg-red-600' : 'bg-slate-400 opacity-70 cursor-not-allowed'} text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2`;
        }
    }

    const requireSelfiePunch = {{ \App\Models\Setting::get('require_selfie_punch', true) ? 'true' : 'false' }};
    const enableBlinkLiveness = {{ \App\Models\Setting::get('enable_blink_liveness', true) ? 'true' : 'false' }};

    let currentPunchType = '';
    let currentPunchBtn = null;
    let currentPunchBtnText = '';
    let webcamStream = null;
    let capturedPhotoBase64 = null;
    let faceMeshInstance = null;
    let cameraUtilsInstance = null;
    let isLivenessVerified = false;
    let eyeBlinkState = 'OPEN';
    let fallbackTimer = null;

    function handlePunch(type) {
        if (!isInsideGeofence) {
            if(typeof Swal !== 'undefined') {
                Swal.fire('Error', 'You must be inside the office premises to punch ' + (type === 'in' ? 'In' : 'Out') + '.', 'error');
            } else {
                alert('You must be inside the office premises to punch ' + type + '.');
            }
            return;
        }

        currentPunchType = type;
        const btnId = type === 'in' ? 'punchInBtn' : 'punchOutBtn';
        currentPunchBtn = document.getElementById(btnId);
        currentPunchBtnText = currentPunchBtn.innerHTML;

        // If Admin turned OFF selfie capture, bypass webcam modal & punch directly
        if (!requireSelfiePunch) {
            capturedPhotoBase64 = null;
            sendPunchRequest();
            return;
        }
        
        // Show modal
        document.getElementById('webcamType').innerText = type === 'in' ? 'Punch In' : 'Punch Out';
        document.getElementById('webcamTime').innerText = document.getElementById('val-time') ? document.getElementById('val-time').innerText : new Date().toLocaleTimeString();
        document.getElementById('webcamDate').innerText = document.getElementById('val-date') ? document.getElementById('val-date').innerText : new Date().toLocaleDateString();
        document.getElementById('webcamGPS').innerText = 'Valid (' + Math.round(employeeAccuracy) + 'm)';
        
        // Reset UI
        document.getElementById('webcamVideo').classList.remove('hidden');
        document.getElementById('webcamCanvas').classList.add('hidden');
        document.getElementById('retakeBtn').classList.add('hidden');
        document.getElementById('submitPunchBtn').classList.add('hidden');
        document.getElementById('cancelWebcamBtn')?.classList.remove('hidden');
        document.getElementById('manualSnapBtn')?.classList.add('hidden');
        
        openWebcamModal();
    }
    
    function openWebcamModal() {
        const modal = document.getElementById('webcamModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        startWebcam();
    }
    
    function closeWebcamModal() {
        const modal = document.getElementById('webcamModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        stopWebcam();
    }

    function startWebcam() {
        const videoContainer = document.getElementById('videoContainer');
        const video = document.getElementById('webcamVideo');
        videoContainer.classList.remove('border-emerald-500', 'border-red-400', 'border-green-400');
        videoContainer.classList.add('border-slate-100');

        const constraints = {
            video: {
                facingMode: "user",
                width: { ideal: 640 },
                height: { ideal: 640 }
            },
            audio: false
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                webcamStream = stream;
                video.srcObject = stream;
                video.play();
                videoContainer.classList.remove('border-slate-100');
                videoContainer.classList.add('border-emerald-500');
                
                if (enableBlinkLiveness && typeof FaceMesh !== 'undefined') {
                    startLivenessDetection(video);
                } else {
                    capturePhotoFallback();
                }
            })
            .catch(function(err) {
                console.error("Camera error:", err);
                let msg = 'Camera permission is required to punch in/out.';
                if(typeof Swal !== 'undefined') {
                    Swal.fire('Camera Error', msg, 'error');
                } else {
                    alert(msg);
                }
                closeWebcamModal();
            });
    }

    function stopWebcam() {
        if (fallbackTimer) {
            clearInterval(fallbackTimer);
            fallbackTimer = null;
        }
        if (cameraUtilsInstance) {
            try { cameraUtilsInstance.stop(); } catch(e){}
            cameraUtilsInstance = null;
        }
        if (faceMeshInstance) {
            try { faceMeshInstance.close(); } catch(e){}
            faceMeshInstance = null;
        }
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
    }

    function calculateEAR(landmarks, indices) {
        const pUpper = landmarks[indices[0]];
        const pLower = landmarks[indices[1]];
        const pOuter = landmarks[indices[2]];
        const pInner = landmarks[indices[3]];

        const distV = Math.hypot(pUpper.x - pLower.x, pUpper.y - pLower.y);
        const distH = Math.hypot(pOuter.x - pInner.x, pOuter.y - pInner.y);

        return distH > 0 ? (distV / distH) : 0;
    }

    function startLivenessDetection(video) {
        isLivenessVerified = false;
        eyeBlinkState = 'OPEN';

        const statusEl = document.getElementById('livenessStatusText');
        const overlay = document.getElementById('livenessStatusOverlay');
        if (overlay) overlay.classList.remove('hidden');
        if (statusEl) statusEl.innerHTML = '<span class="text-amber-300 font-semibold">Scanning face...</span>';

        try {
            faceMeshInstance = new FaceMesh({
                locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
            });

            faceMeshInstance.setOptions({
                maxNumFaces: 1,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });

            faceMeshInstance.onResults((results) => {
                if (isLivenessVerified) return;

                const videoContainer = document.getElementById('videoContainer');
                if (!results.multiFaceLandmarks || results.multiFaceLandmarks.length === 0) {
                    if (statusEl) statusEl.innerHTML = '<span class="text-red-400 font-bold">❌ No Face Detected</span><br/><span class="text-[11px] text-slate-300">Position face in circle</span>';
                    videoContainer.classList.remove('border-emerald-500', 'border-green-400');
                    videoContainer.classList.add('border-red-400');
                    eyeBlinkState = 'OPEN';
                    return;
                }

                videoContainer.classList.remove('border-red-400');
                videoContainer.classList.add('border-emerald-500');

                const landmarks = results.multiFaceLandmarks[0];
                const leftEAR = calculateEAR(landmarks, [159, 145, 33, 133]);
                const rightEAR = calculateEAR(landmarks, [386, 374, 362, 263]);
                const avgEAR = (leftEAR + rightEAR) / 2;

                if (avgEAR < 0.16) {
                    if (eyeBlinkState === 'OPEN') {
                        eyeBlinkState = 'CLOSING';
                    }
                } else if (avgEAR > 0.22) {
                    if (eyeBlinkState === 'CLOSING') {
                        eyeBlinkState = 'BLINKED';
                        isLivenessVerified = true;
                        
                        if (statusEl) statusEl.innerHTML = '<span class="text-emerald-400 font-bold text-sm">✓ Blink Verified!</span>';
                        videoContainer.classList.remove('border-emerald-500');
                        videoContainer.classList.add('border-green-400');

                        setTimeout(() => {
                            executeSnapshot();
                        }, 250);
                        return;
                    }
                    eyeBlinkState = 'OPEN';
                }

                if (eyeBlinkState === 'OPEN' || eyeBlinkState === 'CLOSING') {
                    if (statusEl) statusEl.innerHTML = '<span class="text-amber-300 font-bold">👁️ Face Detected</span><br/><span class="text-[11px] text-white">BLINK eyes to capture</span>';
                }
            });

            cameraUtilsInstance = new Camera(video, {
                onFrame: async () => {
                    if (webcamStream && !isLivenessVerified && faceMeshInstance) {
                        try {
                            await faceMeshInstance.send({ image: video });
                        } catch(e){}
                    }
                },
                width: 640,
                height: 640
            });
            cameraUtilsInstance.start();

            // Timeout: if no blink after 7s, show manual snap button
            setTimeout(() => {
                if (!isLivenessVerified) {
                    document.getElementById('manualSnapBtn')?.classList.remove('hidden');
                }
            }, 7000);

        } catch(e) {
            console.error("Liveness init error:", e);
            capturePhotoFallback();
        }
    }

    function executeSnapshot() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        const overlay = document.getElementById('livenessStatusOverlay');
        
        if (overlay) overlay.classList.add('hidden');

        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 640;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.9);

        video.classList.add('hidden');
        canvas.classList.remove('hidden');
        document.getElementById('retakeBtn').classList.remove('hidden');
        document.getElementById('submitPunchBtn').classList.remove('hidden');
        document.getElementById('cancelWebcamBtn')?.classList.add('hidden');
        document.getElementById('manualSnapBtn')?.classList.add('hidden');
    }

    function capturePhotoFallback() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        const countdownEl = document.getElementById('countdownOverlay');
        const countdownText = document.getElementById('countdownText');
        const overlay = document.getElementById('livenessStatusOverlay');
        if (overlay) overlay.classList.add('hidden');
        
        let count = 5;
        countdownEl.classList.remove('hidden');
        countdownEl.classList.add('flex');
        countdownText.innerText = count;
        
        fallbackTimer = setInterval(() => {
            count--;
            if (count > 0) {
                countdownText.innerText = count;
            } else if (count === 0) {
                countdownText.innerText = 'Smile!';
            } else {
                clearInterval(fallbackTimer);
                fallbackTimer = null;
                countdownEl.classList.remove('flex');
                countdownEl.classList.add('hidden');
                executeSnapshot();
            }
        }, 1000);
    }

    function retakePhoto() {
        document.getElementById('webcamVideo').classList.remove('hidden');
        document.getElementById('webcamCanvas').classList.add('hidden');
        document.getElementById('retakeBtn').classList.add('hidden');
        document.getElementById('submitPunchBtn').classList.add('hidden');
        document.getElementById('cancelWebcamBtn')?.classList.remove('hidden');
        capturedPhotoBase64 = null;
        
        if (enableBlinkLiveness && typeof FaceMesh !== 'undefined') {
            startLivenessDetection(document.getElementById('webcamVideo'));
        } else {
            capturePhotoFallback();
        }
    }

    function submitPunch() {
        if (requireSelfiePunch && !capturedPhotoBase64) {
            alert('Please capture a photo first.');
            return;
        }
        sendPunchRequest();
    }

    function sendPunchRequest() {
        if (currentPunchBtn) {
            currentPunchBtn.disabled = true;
        }

        const submitBtn = document.getElementById('submitPunchBtn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
        }
        const retakeBtn = document.getElementById('retakeBtn');
        if (retakeBtn) retakeBtn.disabled = true;

        const url = currentPunchType === 'in' ? "{{ route('employee.attendance.punch-in') }}" : "{{ route('employee.attendance.punch-out') }}";

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                latitude: employeeLat, 
                longitude: employeeLng, 
                accuracy: employeeAccuracy, 
                browser: navigator.userAgent,
                photo: capturedPhotoBase64
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    location.reload();
                }
            } else {
                if(typeof Swal !== 'undefined') {
                    Swal.fire('Error', data.message || 'Error occurred.', 'error');
                } else {
                    alert(data.message || 'Error occurred.');
                }
                resetSubmitBtn();
            }
        })
        .catch(err => {
            if(typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Network error. Please try again.', 'error');
            } else {
                alert('Network error. Please try again.');
            }
            resetSubmitBtn();
        });
    }

    function resetSubmitBtn() {
        const submitBtn = document.getElementById('submitPunchBtn');
        if (submitBtn) {
            submitBtn.innerHTML = 'Submit Punch <i class="fa-solid fa-arrow-right ml-1"></i>';
            submitBtn.disabled = false;
        }
        const retakeBtn = document.getElementById('retakeBtn');
        if (retakeBtn) retakeBtn.disabled = false;
        if (currentPunchBtn) currentPunchBtn.disabled = false;
    }
</script>

<script>
    // ── Celebrations Tab Switch ────────────────────────────────────────────
    function epSwitchCeleb(type) {
        const bdayEl  = document.getElementById('epCelebBirthdays');
        const annivEl = document.getElementById('epCelebAnnivs');
        const btnB    = document.getElementById('epBtnBdays');
        const btnA    = document.getElementById('epBtnAnnivs');
        if (type === 'birthdays') {
            bdayEl.style.display  = 'flex';
            annivEl.style.display = 'none';
            btnB.classList.add('active');
            btnA.classList.remove('active');
        } else {
            bdayEl.style.display  = 'none';
            annivEl.style.display = 'flex';
            btnB.classList.remove('active');
            btnA.classList.add('active');
        }
    }

    // ── Events Calendar ────────────────────────────────────────────────────
    const epEmployees = @json($calendarEmployees);
    const epHolidays  = @json($companyHolidays);

    let epCurrentDate = new Date();

    document.addEventListener('DOMContentLoaded', function () {
        epRenderCalendar();
    });

    function epRenderCalendar() {
        const year  = epCurrentDate.getFullYear();
        const month = epCurrentDate.getMonth();
        const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];

        document.getElementById('epCalMonthYear').innerText = `${monthNames[month]} ${year}`;

        const grid = document.getElementById('epCalGrid');
        grid.innerHTML = '';

        const firstDayIndex = new Date(year, month, 1).getDay();
        const lastDay       = new Date(year, month + 1, 0).getDate();
        const prevLastDay   = new Date(year, month, 0).getDate();
        const totalCells    = 42;

        let dayCounter = 1, nextMonthCounter = 1;

        for (let i = 0; i < totalCells; i++) {
            const cell = document.createElement('div');

            if (i < firstDayIndex) {
                cell.className = 'ep-calendar-day-cell other-month';
                cell.innerText = prevLastDay - firstDayIndex + i + 1;
            } else if (dayCounter <= lastDay) {
                const currentDay = dayCounter;
                cell.className = 'ep-calendar-day-cell';
                cell.innerText = currentDay;

                // Mark today
                const today = new Date();
                if (today.getDate() === currentDay && today.getMonth() === month && today.getFullYear() === year) {
                    cell.classList.add('today');
                }

                // Gather events
                const dayEvents = epGetDayEvents(year, month, currentDay);
                if (dayEvents.length > 0) {
                    const hasHoliday     = dayEvents.some(e => e.type === 'holiday');
                    const hasBirthday    = dayEvents.some(e => e.type === 'birthday');
                    const hasAnniversary = dayEvents.some(e => e.type === 'anniversary');

                    if (hasHoliday)          cell.classList.add('has-holiday');
                    else if (hasBirthday)    cell.classList.add('has-birthday');
                    else if (hasAnniversary) cell.classList.add('has-anniversary');

                    cell.addEventListener('click', (event) => {
                        event.stopPropagation();
                        epOpenPopover(year, month, currentDay, dayEvents, cell);
                    });
                }
                dayCounter++;
            } else {
                cell.className = 'ep-calendar-day-cell other-month';
                cell.innerText = nextMonthCounter++;
            }
            grid.appendChild(cell);
        }
    }

    function epPrevMonth() {
        epClosePopover();
        epCurrentDate.setMonth(epCurrentDate.getMonth() - 1);
        epRenderCalendar();
    }

    function epNextMonth() {
        epClosePopover();
        epCurrentDate.setMonth(epCurrentDate.getMonth() + 1);
        epRenderCalendar();
    }

    function epGetDayEvents(year, month, day) {
        const events = [];
        const dateObj   = new Date(year, month, day);
        const dayOfWeek = dateObj.getDay();

        // Sundays are holidays
        if (dayOfWeek === 0) {
            events.push({ type:'holiday', title:'Sunday Holiday', subtitle:'Weekly Off-Day',
                icon:'fa-umbrella-beach', iconBg:'#fff5f5', iconColor:'#e03131', dateLabel:'Holiday' });
        }

        // Birthdays
        epEmployees.forEach(emp => {
            if (emp.dob) {
                const parts = emp.dob.split('-');
                if (parseInt(parts[1],10) - 1 === month && parseInt(parts[2],10) === day) {
                    events.push({ type:'birthday', title:`${emp.name} (Birthday)`, subtitle:`${emp.department} Dept`,
                        icon:'fa-cake-candles', iconBg:'#fff9db', iconColor:'#f08c00', dateLabel:'Birthday' });
                }
            }
        });

        // Anniversaries
        epEmployees.forEach(emp => {
            if (emp.joining_date) {
                const parts    = emp.joining_date.split('-');
                const joinYear = parseInt(parts[0],10);
                if (parseInt(parts[1],10) - 1 === month && parseInt(parts[2],10) === day) {
                    const yrs = year - joinYear;
                    events.push({ type:'anniversary', title:`${emp.name} (Anniversary)`, subtitle:`Joined ${joinYear} • ${emp.department}`,
                        icon:'fa-star', iconBg:'#ebfbee', iconColor:'#2b8a3e', dateLabel: yrs > 0 ? `${yrs} Yr${yrs>1?'s':''}` : 'Joined' });
                }
            }
        });

        // Company Holidays
        epHolidays.forEach(holiday => {
            const parts = holiday.date.split('-');
            const holYear = parseInt(parts[0], 10);
            const holMonth = parseInt(parts[1], 10) - 1;
            const holDay = parseInt(parts[2], 10);
            if (holYear === year && holMonth === month && holDay === day) {
                if (!events.some(e => e.title === holiday.name)) {
                    events.push({ type:'holiday', title:holiday.name, subtitle:'Company Holiday',
                        icon:'fa-umbrella-beach', iconBg:'#fff5f5', iconColor:'#e03131', dateLabel:'Holiday' });
                }
            }
        });

        return events;
    }

    function epOpenPopover(year, month, day, dayEvents, cellElement) {
        const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        document.getElementById('epPopoverTitle').innerText = `Events on ${monthNames[month]} ${day}, ${year}`;

        const body = document.getElementById('epPopoverBody');
        body.innerHTML = '';

        dayEvents.forEach(evt => {
            let badgeStyle = '';
            if (evt.type === 'birthday')    badgeStyle = 'background:#fff9db;color:#f08c00;';
            else if (evt.type === 'anniversary') badgeStyle = 'background:#ebfbee;color:#2b8a3e;';
            else if (evt.type === 'holiday')     badgeStyle = 'background:#fff5f5;color:#e03131;';

            const item = document.createElement('div');
            item.className = 'ep-event-item';
            item.innerHTML = `
                <div class="ep-event-left">
                    <div class="ep-event-icon" style="background:${evt.iconBg};color:${evt.iconColor};">
                        <i class="fa ${evt.icon}"></i>
                    </div>
                    <div class="ep-event-info">
                        <span class="ep-event-title">${evt.title}</span>
                        <span class="ep-event-sub">${evt.subtitle}</span>
                    </div>
                </div>
                <span class="ep-event-badge" style="${badgeStyle}">${evt.dateLabel}</span>
            `;
            body.appendChild(item);
        });

        const popover = document.getElementById('epCalPopover');
        popover.style.display = 'block';
        popover.style.top  = '0px';
        popover.style.left = '0px';

        const card = cellElement.closest('.bg-white');
        if (card) {
            const cardRect    = card.getBoundingClientRect();
            const cellRect    = cellElement.getBoundingClientRect();
            const popoverRect = popover.getBoundingClientRect();

            let top  = cellRect.top - cardRect.top - popoverRect.height - 10;
            let left = cellRect.left - cardRect.left + (cellRect.width / 2) - (popoverRect.width / 2);

            if (left < 10) left = 10;
            if (left + popoverRect.width > cardRect.width - 10) left = cardRect.width - popoverRect.width - 10;
            if (top < 10) top = cellRect.bottom - cardRect.top + 10;

            popover.style.top  = top  + 'px';
            popover.style.left = left + 'px';
        }
    }

    function epClosePopover() {
        document.getElementById('epCalPopover').style.display = 'none';
    }

    document.addEventListener('click', function (event) {
        const popover = document.getElementById('epCalPopover');
        if (!popover) return;
        const isInside = popover.contains(event.target);
        const isCell   = event.target.closest('.ep-calendar-day-cell');
        if (!isInside && !isCell && popover.style.display === 'block') epClosePopover();
    });
</script>
<!-- Stylish Tailwind Webcam Modal -->
<div id="webcamModal" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
        <!-- Header -->
        <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h5 class="text-lg font-bold text-slate-800 flex items-center">
                <i class="fa-solid fa-camera text-emerald-500 mr-2"></i>
                Live Verification: <span id="webcamType" class="text-emerald-600 ml-1"></span>
            </h5>
            <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" onclick="closeWebcamModal()">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div class="p-6 text-center">
            <div class="flex justify-between items-center mb-6 bg-slate-50 rounded-2xl p-3 border border-slate-100 shadow-sm">
                <div class="text-left">
                    <div class="text-sm font-bold text-slate-800">{{ Auth::guard('employee')->user()->employee->first_name }} {{ Auth::guard('employee')->user()->employee->last_name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::guard('employee')->user()->employee_id }}</div>
                </div>
                <div class="text-right text-xs text-slate-600">
                    <div id="webcamDate" class="font-semibold"></div>
                    <div id="webcamTime" class="font-bold text-emerald-600"></div>
                    <div class="mt-1"><i class="fa-solid fa-location-dot text-slate-400"></i> <span id="webcamGPS"></span></div>
                </div>
            </div>

            <!-- Stylish Circular Webcam Container -->
            <div id="videoContainer" class="relative mx-auto bg-slate-900 rounded-full overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.12)] mb-4 border-4 border-slate-100 transition-all duration-300" style="width: 230px; height: 230px;">
                <video id="webcamVideo" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                <canvas id="webcamCanvas" class="hidden w-full h-full object-cover transform scale-x-[-1]"></canvas>
                
                <div id="livenessStatusOverlay" class="absolute inset-0 flex flex-col justify-end items-center pb-3 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent z-10 pointer-events-none transition-all">
                    <div id="livenessStatusText" class="text-xs font-semibold text-white text-center px-2 drop-shadow leading-tight">
                        Scanning face...
                    </div>
                </div>

                <div id="countdownOverlay" class="absolute inset-0 hidden flex-col justify-center items-center bg-slate-900/40 z-10">
                    <span class="text-white font-bold text-6xl drop-shadow-xl" id="countdownText">5</span>
                </div>
            </div>

            <p class="text-xs font-medium text-slate-500">Please align your face inside the circle frame.</p>
        </div>
        
        <!-- Footer -->
        <div class="bg-slate-50 px-6 py-4 flex flex-wrap justify-center gap-2">
            <button type="button" id="cancelWebcamBtn" class="px-4 py-2 rounded-full text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm" onclick="closeWebcamModal()">Cancel</button>
            
            <button type="button" class="hidden px-4 py-2 rounded-full text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm" id="manualSnapBtn" onclick="executeSnapshot()">
                <i class="fa-solid fa-camera mr-1"></i> Capture Snapshot
            </button>

            <button type="button" class="hidden px-4 py-2 rounded-full text-xs font-bold text-white bg-yellow-500 hover:bg-yellow-600 transition-colors shadow-sm" id="retakeBtn" onclick="retakePhoto()">
                <i class="fa-solid fa-rotate-left mr-1"></i> Retake
            </button>
            <button type="button" class="hidden px-4 py-2 rounded-full text-xs font-bold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors shadow-sm shadow-emerald-500/30" id="submitPunchBtn" onclick="submitPunch()">
                Submit Punch <i class="fa-solid fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>
</div>

@endpush
@endsection

