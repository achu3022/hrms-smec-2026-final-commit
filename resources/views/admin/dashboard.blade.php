@extends('layouts.admin')

@section('content')
<!-- Custom Google Fonts for Twisty style dashboard -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* Premium Dashboard Theme - Glassmorphism & Gradients */
    .twisty-theme {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        color: #1e293b;
        background: radial-gradient(circle at top left, #f3e8ff 0%, #f8fafc 40%, #e0e7ff 100%);
        background-attachment: fixed;
        min-height: 100vh;
        padding-top: 15px;
        padding-bottom: 40px;
    }

    /* Tab styles */
    .twisty-nav-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 15px;
    }
    .twisty-tabs {
        display: flex;
        gap: 30px;
    }
    .twisty-tab-link {
        font-size: 1.1rem;
        font-weight: 700;
        color: #64748b;
        background: none;
        border: none;
        padding: 5px 0;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .twisty-tab-link:hover {
        color: #334155;
    }
    .twisty-tab-link.active {
        color: #0f172a;
    }
    .twisty-tab-link.active::after {
        content: '';
        position: absolute;
        bottom: -16px;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #6366f1, #a855f7);
        border-radius: 2px;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.4);
    }

    /* Twisty Card styling (Glassmorphism) */
    .twisty-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(30, 41, 59, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.8);
        padding: 28px;
        margin-bottom: 28px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
    }
    .twisty-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(30, 41, 59, 0.08);
        border: 1px solid rgba(255, 255, 255, 1);
    }
    .twisty-card-title {
        font-size: 1.35rem;
        font-weight: 800;
        background: linear-gradient(90deg, #1e293b, #334155);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 6px;
        letter-spacing: -0.3px;
    }
    .twisty-card-subtitle {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 24px;
        font-weight: 500;
    }

    /* Status Summary Grid */
    .status-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    @media (min-width: 768px) {
        .status-summary-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }
    .status-metric-card {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;
        padding: 24px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .status-metric-card:hover {
        background: #ffffff;
        box-shadow: 0 12px 30px rgba(99, 102, 241, 0.12);
        transform: translateY(-6px) scale(1.02);
        border-color: rgba(99, 102, 241, 0.3);
    }
    .status-metric-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.8), 0 4px 10px rgba(0,0,0,0.05);
    }
    .status-metric-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .status-metric-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .status-metric-value {
        font-size: 1.85rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
    }

    /* Vibrant Gradients for Icons */
    .icon-gradient-blue { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5; }
    .icon-gradient-green { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #16a34a; }
    .icon-gradient-yellow { background: linear-gradient(135deg, #fef9c3, #fef08a); color: #ca8a04; }
    .icon-gradient-red { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
    .icon-gradient-gray { background: linear-gradient(135deg, #f1f5f9, #e2e8f0); color: #475569; }

    /* Celebrations */
    .celebrations-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .celebration-tabs {
        display: flex;
        gap: 8px;
        background: rgba(241, 245, 249, 0.6);
        padding: 6px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .celebration-tab-btn {
        background: none;
        border: none;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        padding: 8px 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .celebration-tab-btn:hover {
        color: #1e293b;
    }
    .celebration-tab-btn.active {
        color: #ffffff;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    .celebration-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 20px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    .celebration-item:hover {
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        transform: translateX(6px);
        border-color: rgba(99, 102, 241, 0.3);
    }
    .celebration-profile {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .celebration-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .celebration-info {
        display: flex;
        flex-direction: column;
    }
    .celebration-name {
        font-size: 1rem;
        font-weight: 800;
        color: #1e293b;
    }
    .celebration-subtitle {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }
    .celebration-badge {
        font-size: 0.8rem;
        font-weight: 800;
        padding: 8px 16px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.2px;
        transition: all 0.3s ease;
    }
    .celebration-badge.birthday {
        background: linear-gradient(135deg, #fff0f6 0%, #ffdeeb 100%);
        color: #e03131;
        border: 1px solid #fcc2d7;
        box-shadow: 0 2px 6px rgba(224, 49, 49, 0.08);
    }
    .celebration-badge.birthday.is-today {
        background: linear-gradient(135deg, #fff9db 0%, #ffe066 100%);
        color: #854d0e;
        border: 1px solid #fab005;
        box-shadow: 0 4px 12px rgba(240, 140, 0, 0.25);
    }
    .celebration-badge.anniversary {
        background: linear-gradient(135deg, #e6fcf5 0%, #c3fae8 100%);
        color: #0ca678;
        border: 1px solid #96f2d7;
    }

    /* Blood Group Search form styling */
    .blood-search-form {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        align-items: center;
        flex-wrap: wrap;
    }
    .blood-select-wrapper {
        flex: 1;
        min-width: 200px;
        position: relative;
    }
    .blood-select {
        width: 100%;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        padding: 14px 20px;
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        appearance: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .blood-select:focus {
        border-color: #6366f1;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }
    .blood-select-wrapper::after {
        content: '078';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
    }
    .blood-btn {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        padding: 14px 32px;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }
    .blood-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
    }

    /* Blood Group list results layout */
    .blood-results-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .blood-drop-icon {
        color: #ef4444;
        font-size: 1.4rem;
        filter: drop-shadow(0 2px 4px rgba(239,68,68,0.3));
    }
    .blood-employee-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    @media (min-width: 768px) {
        .blood-employee-list {
            grid-template-columns: 1fr 1fr;
        }
    }
    .blood-employee-card {
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 20px;
        padding: 22px;
        display: flex;
        gap: 18px;
        align-items: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    .blood-employee-card:hover {
        background: #ffffff;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
        border-color: rgba(99, 102, 241, 0.2);
        transform: translateY(-3px);
    }
    .blood-badge-avatar {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1px solid #fecaca;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.3rem;
        color: #ef4444;
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.8), 0 4px 10px rgba(239,68,68,0.1);
    }
    .blood-emp-details {
        flex: 1;
    }
    .blood-emp-name-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }
    .blood-emp-name {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1e293b;
    }
    .blood-emp-status-badge {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-active {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #16a34a;
        box-shadow: 0 2px 6px rgba(22, 163, 74, 0.15);
    }
    .status-inactive {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.15);
    }
    .blood-emp-info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 6px;
        margin-top: 10px;
    }
    .blood-emp-info-item {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }
    .blood-emp-info-item i {
        width: 16px;
        color: #94a3b8;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #64748b;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        border: 1px dashed rgba(203, 213, 225, 0.8);
    }
    .empty-state-icon {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 20px;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
    }
    .empty-state-text {
        font-size: 1rem;
        font-weight: 600;
    }
    
    /* Loading spinner style */
    .loading-spinner {
        display: none;
        justify-content: center;
        align-items: center;
        padding: 50px;
    }
    
    /* Micro-animation transitions */
    .fade-in {
        animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Calendar day grid cells */
    .calendar-day-cell {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        position: relative;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        color: #334155;
        background: rgba(255,255,255,0.4);
        border: 1px solid transparent;
    }
    .calendar-day-cell:hover {
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        transform: scale(1.1);
        z-index: 10;
    }
    .calendar-day-cell.other-month {
        color: #cbd5e1;
        background: transparent;
        pointer-events: none;
    }
    .calendar-day-cell.today {
        border: 2px solid #6366f1;
        background: #e0e7ff;
        color: #4f46e5;
        box-shadow: 0 4px 12px rgba(99,102,241,0.2);
    }
    .calendar-day-cell.has-holiday {
        background: linear-gradient(135deg, #fee2e2, #fecaca) !important;
        color: #dc2626 !important;
        border: 1px solid #fca5a5;
        box-shadow: 0 2px 8px rgba(220,38,38,0.15);
    }
    .calendar-day-cell.has-holiday:hover {
        transform: scale(1.15);
    }
    .calendar-day-cell.has-birthday {
        background: linear-gradient(135deg, #fef9c3, #fef08a) !important;
        color: #ca8a04 !important;
        border: 1px solid #fde047;
        box-shadow: 0 2px 8px rgba(202,138,4,0.15);
    }
    .calendar-day-cell.has-anniversary {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0) !important;
        color: #16a34a !important;
        border: 1px solid #86efac;
        box-shadow: 0 2px 8px rgba(22,163,74,0.15);
    }
    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .event-item-right {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
    }

    /* Custom Popover Container */
    .calendar-event-popover {
        position: absolute;
        width: 300px;
        height: auto;
        max-height: 280px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border-radius: 20px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.15);
        border: 1px solid rgba(255, 255, 255, 1);
        z-index: 100;
        padding: 20px;
        display: none;
        box-sizing: border-box;
    }
    .popover-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding-bottom: 12px;
        margin-bottom: 15px;
    }
    .popover-close-btn {
        background: rgba(241, 245, 249, 0.8);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .popover-close-btn:hover {
        color: #ffffff;
        background: #ef4444;
        transform: rotate(90deg);
    }
    .popover-body {
        max-height: 200px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .popover-body::-webkit-scrollbar {
        width: 6px;
    }
    .popover-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .popover-fade-in {
        animation: popoverFadeIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes popoverFadeIn {
        from { opacity: 0; transform: scale(0.9) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* Calendar Event items inside Popover */
    .calendar-event-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px;
        border-radius: 16px;
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(255,255,255,1);
        margin-bottom: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .calendar-event-item:hover {
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.05);
    }
    .event-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .event-item-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .event-item-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .event-item-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .event-item-subtitle {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="twisty-theme">
    <!-- Redesigned Top Header Menu -->
    <div class="twisty-nav-container">
        <div class="twisty-tabs">
            <button class="twisty-tab-link active" onclick="switchTab(event, 'overview-tab')">Overview</button>
            <button class="twisty-tab-link" onclick="switchTab(event, 'blood-group-tab')">Blood Group</button>
        </div>
        <div class="text-muted small fw-semibold">
            <i class="fa fa-calendar me-1 text-primary"></i> {{ date('d-m-Y') }}
        </div>
    </div>

    <!-- OVERVIEW TAB CONTENT -->
    <div id="overview-tab" class="tab-content fade-in">
        <div class="row">
            <!-- Left Side Column (Wider Widgets) -->
            <div class="col-lg-8">
                <!-- Employee Status Card -->
                <div class="twisty-card">
                    <div class="twisty-card-title">Employee Status</div>
                    <div class="twisty-card-subtitle">Overview of team structure and employment states</div>
                    
                    <div class="status-summary-grid">
                        <div class="status-metric-card">
                            <div class="status-metric-icon icon-gradient-blue">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="status-metric-info">
                                <span class="status-metric-label">Total Employees</span>
                                <span class="status-metric-value">{{ $totalEmployees }}</span>
                            </div>
                        </div>
                        <div class="status-metric-card">
                            <div class="status-metric-icon icon-gradient-green">
                                <i class="fa fa-user-check"></i>
                            </div>
                            <div class="status-metric-info">
                                <span class="status-metric-label">Active</span>
                                <span class="status-metric-value">{{ $activeEmployees }}</span>
                            </div>
                        </div>
                        <div class="status-metric-card">
                            <div class="status-metric-icon icon-gradient-yellow">
                                <i class="fa fa-user-minus"></i>
                            </div>
                            <div class="status-metric-info">
                                <span class="status-metric-label">Inactive</span>
                                <span class="status-metric-value">{{ $inactiveEmployees }}</span>
                            </div>
                        </div>
                        <div class="status-metric-card">
                            <div class="status-metric-icon icon-gradient-red">
                                <i class="fa fa-user-xmark"></i>
                            </div>
                            <div class="status-metric-info">
                                <span class="status-metric-label">Terminated</span>
                                <span class="status-metric-value">{{ $terminatedEmployees }}</span>
                            </div>
                        </div>
                        <div class="status-metric-card">
                            <div class="status-metric-icon icon-gradient-gray">
                                <i class="fa fa-user-slash"></i>
                            </div>
                            <div class="status-metric-info">
                                <span class="status-metric-label">Resigned</span>
                                <span class="status-metric-value">{{ $resignedEmployees }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Celebrations Widget -->
                <div class="twisty-card">
                    <div class="celebrations-header">
                        <div>
                            <div class="twisty-card-title">Upcoming Celebrations</div>
                            <div class="twisty-card-subtitle">Staff events happening this month</div>
                        </div>
                        <div class="celebration-tabs">
                            <button id="btnBirthdays" class="celebration-tab-btn active" onclick="switchCelebrationTab('birthdays')">Birthdays</button>
                            <button id="btnAnniversaries" class="celebration-tab-btn" onclick="switchCelebrationTab('anniversaries')">Anniversaries</button>
                        </div>
                    </div>

                    <!-- Birthdays List -->
                    <div id="celebrationBirthdays" class="celebration-list fade-in">
                        @forelse($upcomingBirthdays as $emp)
                            @php
                                $initials = collect(explode(' ', $emp->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                                $dobCarbon = \Carbon\Carbon::parse($emp->dob);
                                $isToday = $dobCarbon->isBirthday();
                                $bdayFormatted = $dobCarbon->format('d M');
                            @endphp
                            <div class="celebration-item">
                                <div class="celebration-profile">
                                    @if($emp->photo)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($emp->photo) }}" alt="{{ $emp->name }}" class="celebration-avatar-img" style="width: 42px; height: 42px; border-radius: 12px; object-fit: cover; border: 1px solid #fcc2d7; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                                    @else
                                        <div class="celebration-avatar" style="background: linear-gradient(135deg, #fff0f6, #ffe3e3); color: #d6336c; border: 1px solid #fcc2d7;">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div class="celebration-info">
                                        <span class="celebration-name">{{ $emp->name }}</span>
                                        <span class="celebration-subtitle">{{ $emp->designationRel->name ?? $emp->designation ?? 'N/A' }} • {{ $emp->departmentRel->name ?? $emp->department ?? 'General' }}</span>
                                    </div>
                                </div>
                                <div class="celebration-badge birthday {{ $isToday ? 'is-today' : '' }}">
                                    <i class="fa-solid fa-cake-candles {{ $isToday ? 'fa-bounce' : '' }}"></i>
                                    <span>{{ $isToday ? 'Today 🎉 ' . $bdayFormatted : $bdayFormatted }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">No upcoming birthdays this month.</div>
                        @endforelse
                    </div>

                    <!-- Anniversaries List -->
                    <div id="celebrationAnniversaries" class="celebration-list fade-in" style="display: none;">
                        @forelse($workAnniversaries as $emp)
                            @php
                                $initials = collect(explode(' ', $emp->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                                $years = $emp->anniversary_years;
                            @endphp
                            <div class="celebration-item">
                                <div class="celebration-profile">
                                    @if($emp->photo)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($emp->photo) }}" alt="{{ $emp->name }}" class="celebration-avatar-img" style="width: 42px; height: 42px; border-radius: 12px; object-fit: cover; border: 1px solid #96f2d7; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                                    @else
                                        <div class="celebration-avatar" style="background-color: #e6fcf5; color: #0ca678;">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div class="celebration-info">
                                        <span class="celebration-name">{{ $emp->name }}</span>
                                        <span class="celebration-subtitle">Joined on {{ \Carbon\Carbon::parse($emp->joining_date)->format('d-m-Y') }} • {{ $emp->departmentRel->name ?? $emp->department ?? 'General' }}</span>
                                    </div>
                                </div>
                                <div class="celebration-badge anniversary">
                                    <i class="fa fa-award"></i> {{ $years > 0 ? $years . ' Yr' . ($years > 1 ? 's' : '') : 'New Joiner' }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">No work anniversaries in the next 2 days.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side Column (Narrower Widgets) -->
            <div class="col-lg-4">
                <!-- Events Calendar Card -->
                <div class="twisty-card" style="position: relative;">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div class="twisty-card-title mb-0"><i class="fa fa-calendar-days text-primary me-2"></i>Events Calendar</div>
                        <div class="d-flex gap-2 align-items-center">
                            <button onclick="prevMonth()" class="btn btn-sm btn-light py-0 px-2" style="border-radius: 8px;"><i class="fa fa-chevron-left"></i></button>
                            <span id="calendarMonthYear" class="fw-bold text-dark" style="font-size: 0.9rem; min-width: 90px; text-align: center;"></span>
                            <button onclick="nextMonth()" class="btn btn-sm btn-light py-0 px-2" style="border-radius: 8px;"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    
                    <!-- Weekdays Header -->
                    <div class="d-grid text-center text-muted fw-bold mb-2" style="grid-template-columns: repeat(7, 1fr); font-size: 0.75rem; letter-spacing: 0.5px;">
                        <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                    </div>

                    <!-- Days Grid -->
                    <div id="calendarDaysGrid" class="d-grid text-center gap-1 mb-3" style="grid-template-columns: repeat(7, 1fr);">
                        <!-- Days will be dynamically injected by JS -->
                    </div>

                    <!-- Calendar Legend -->
                    <div class="d-flex justify-content-center gap-3 pt-2 border-top" style="font-size: 0.7rem; font-weight: 600;">
                        <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background-color: #f08c00;"></span>Birthday</span>
                        <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background-color: #2b8a3e;"></span>Anniversary</span>
                        <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background-color: #e03131;"></span>Holiday</span>
                    </div>

                    <!-- Custom Calendar Event Popover -->
                    <div id="calendarEventPopover" class="calendar-event-popover popover-fade-in" style="display: none;">
                        <div class="popover-header d-flex justify-content-between align-items-center">
                            <span id="popoverTitle" class="fw-bold text-dark" style="font-size: 0.85rem;">Events</span>
                            <button onclick="closePopover()" class="popover-close-btn">&times;</button>
                        </div>
                        <div id="popoverBody" class="popover-body">
                            <!-- Injected by JS -->
                        </div>
                    </div>
                </div>

                <!-- Status distribution removed -->
            </div>
        </div>
    </div>

    <!-- BLOOD GROUP TAB CONTENT -->
    <div id="blood-group-tab" class="tab-content fade-in" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="twisty-card">
                    <div class="twisty-card-title">Blood Group Directory</div>
                    <div class="twisty-card-subtitle">Search for employees by their blood group types</div>

                    <!-- Search Form -->
                    <div class="blood-search-form">
                        <div class="blood-select-wrapper">
                            <select id="bloodGroupSelect" class="blood-select">
                                <option value="" disabled selected>Select blood group...</option>
                                @foreach($bloodGroups as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button onclick="performBloodGroupSearch()" class="blood-btn">
                            <i class="fa fa-magnifying-glass"></i> Search
                        </button>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="searchSpinner" class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="resultsContainer">
                        <!-- Default empty state -->
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fa-solid fa-droplet text-muted opacity-50"></i>
                            </div>
                            <div class="empty-state-text">Select a blood group type from the dropdown and click "Search" to view matching employees.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching function
    function switchTab(evt, tabId) {
        // Get all elements with class="tab-content" and hide them
        const tabContents = document.getElementsByClassName("tab-content");
        for (let i = 0; i < tabContents.length; i++) {
            tabContents[i].style.display = "none";
        }

        // Get all elements with class="twisty-tab-link" and remove the class "active"
        const tabLinks = document.getElementsByClassName("twisty-tab-link");
        for (let i = 0; i < tabLinks.length; i++) {
            tabLinks[i].classList.remove("active");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabId).style.display = "block";
        evt.currentTarget.classList.add("active");
    }

    // Switch celebrations tab
    function switchCelebrationTab(type) {
        const birthdaysEl = document.getElementById('celebrationBirthdays');
        const anniversariesEl = document.getElementById('celebrationAnniversaries');
        const btnBday = document.getElementById('btnBirthdays');
        const btnAnniv = document.getElementById('btnAnniversaries');

        if (type === 'birthdays') {
            birthdaysEl.style.display = 'flex';
            anniversariesEl.style.display = 'none';
            btnBday.classList.add('active');
            btnAnniv.classList.remove('active');
        } else {
            birthdaysEl.style.display = 'none';
            anniversariesEl.style.display = 'flex';
            btnBday.classList.remove('active');
            btnAnniv.classList.add('active');
        }
    }

    // Click trigger for CSV import shortcut
    function triggerImport(e) {
        // Check if there is an import element or modal we can click in the employees index
        // Since we link directly with a URL query ?show_import=1, the employees index page can open the import dialog.
        // Let's proceed with default navigation.
    }

    // AJAX Blood Group Search
    function performBloodGroupSearch() {
        const selectEl = document.getElementById('bloodGroupSelect');
        const bloodGroup = selectEl.value;
        if (!bloodGroup) {
            Swal.fire({
                icon: 'warning',
                title: 'Selection Required',
                text: 'Please select a blood group before searching.',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        const resultsContainer = document.getElementById('resultsContainer');
        const spinner = document.getElementById('searchSpinner');

        // Show spinner, hide results
        resultsContainer.innerHTML = '';
        spinner.style.display = 'flex';

        // Perform GET request
        fetch(`/admin/dashboard/blood-group?blood_group=${encodeURIComponent(bloodGroup)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            spinner.style.display = 'none';

            if (data.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="empty-state fade-in">
                        <div class="empty-state-icon">
                            <i class="fa-solid fa-circle-exclamation text-warning"></i>
                        </div>
                        <div class="empty-state-text">No employees found with blood group <strong>${bloodGroup}</strong>.</div>
                    </div>
                `;
                return;
            }

            // Render matching list
            let listHtml = `
                <div class="blood-results-title fade-in">
                    <i class="fa-solid fa-droplet blood-drop-icon"></i>
                    <span>Matching Employees for Blood Group ${bloodGroup} (${data.length})</span>
                </div>
                <div class="blood-employee-list">
            `;

            data.forEach(emp => {
                const statusClass = emp.status.toLowerCase() === 'active' ? 'status-active' : 'status-inactive';
                listHtml += `
                    <div class="blood-employee-card fade-in">
                        <div class="blood-badge-avatar">
                            <span class="small" style="font-size:0.5rem; text-transform:uppercase; font-weight:700; color: #4f46e5; display:block; margin-bottom: 2px;">Group</span>
                            <span>${emp.blood_group}</span>
                        </div>
                        <div class="blood-emp-details">
                            <div class="blood-emp-name-row">
                                <span class="blood-emp-name">${emp.name}</span>
                                <span class="blood-emp-status-badge ${statusClass}">${emp.status}</span>
                            </div>
                            <div class="blood-emp-info-grid">
                                <div class="blood-emp-info-item">
                                    <i class="fa fa-building"></i>
                                    <span>${emp.department}</span>
                                </div>
                                <div class="blood-emp-info-item">
                                    <i class="fa fa-phone"></i>
                                    <span>${emp.contact}</span>
                                </div>
                                <div class="blood-emp-info-item">
                                    <i class="fa-solid fa-heart-pulse"></i>
                                    <span>Status: <strong>${emp.status}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            listHtml += `</div>`;
            resultsContainer.innerHTML = listHtml;
        })
        .catch(error => {
            console.error('Error fetching blood group data:', error);
            spinner.style.display = 'none';
            resultsContainer.innerHTML = `
                <div class="empty-state text-danger fade-in">
                    <div class="empty-state-icon">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="empty-state-text">Failed to retrieve search results. Please try again.</div>
                </div>
            `;
        });
    }

    // Calendar Data from PHP backend
    const employees = @json($calendarEmployees);
    const holidays = @json($companyHolidays);

    let currentDate = new Date();

    document.addEventListener("DOMContentLoaded", function() {
        renderCalendar();
    });

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Update Header Month/Year
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.getElementById("calendarMonthYear").innerText = `${monthNames[month]} ${year}`;

        // Get grid container
        const grid = document.getElementById("calendarDaysGrid");
        grid.innerHTML = "";

        // First day of the month index (0 = Sun, 1 = Mon...)
        const firstDayIndex = new Date(year, month, 1).getDay();

        // Last day of the current month
        const lastDay = new Date(year, month + 1, 0).getDate();

        // Last day of the previous month
        const prevLastDay = new Date(year, month, 0).getDate();

        // Total calendar cells (6 rows * 7 columns = 42)
        const totalCells = 42;

        let dayCounter = 1;
        let nextMonthCounter = 1;

        for (let i = 0; i < totalCells; i++) {
            const cell = document.createElement("div");
            
            if (i < firstDayIndex) {
                // Previous month day cell
                const prevDay = prevLastDay - firstDayIndex + i + 1;
                cell.className = "calendar-day-cell other-month";
                cell.innerText = prevDay;
            } else if (dayCounter <= lastDay) {
                // Current month day cell
                const currentDay = dayCounter;
                cell.className = "calendar-day-cell";
                cell.innerText = currentDay;

                // Check if it is today
                const today = new Date();
                if (today.getDate() === currentDay && today.getMonth() === month && today.getFullYear() === year) {
                    cell.classList.add("today");
                }

                // Gather events for this day
                const dayEvents = getDayEvents(year, month, currentDay);
                if (dayEvents.length > 0) {
                    // Check types of events to decide cell color
                    const hasHoliday = dayEvents.some(e => e.type === 'holiday');
                    const hasBirthday = dayEvents.some(e => e.type === 'birthday');
                    const hasAnniversary = dayEvents.some(e => e.type === 'anniversary');

                    // Prioritize color classes: holiday (red) > birthday (orange) > anniversary (green)
                    if (hasHoliday) {
                        cell.classList.add("has-holiday");
                    } else if (hasBirthday) {
                        cell.classList.add("has-birthday");
                    } else if (hasAnniversary) {
                        cell.classList.add("has-anniversary");
                    }

                    // Click event triggers popover positioned above the calendar cell
                    cell.addEventListener("click", (event) => {
                        event.stopPropagation();
                        openEventsPopover(year, month, currentDay, dayEvents, cell);
                    });
                }

                dayCounter++;
            } else {
                // Next month day cell
                cell.className = "calendar-day-cell other-month";
                cell.innerText = nextMonthCounter;
                nextMonthCounter++;
            }

            grid.appendChild(cell);
        }
    }

    function prevMonth() {
        closePopover();
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        closePopover();
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    }

    // Get all events on a particular day (Sundays are automatically holidays)
    function getDayEvents(year, month, day) {
        const events = [];

        // Check if the day is a Sunday (0-indexed local day)
        const dateObj = new Date(year, month, day);
        const dayOfWeek = dateObj.getDay(); // 0 = Sunday

        if (dayOfWeek === 0) {
            events.push({
                type: 'holiday',
                title: 'Sunday Holiday',
                subtitle: 'Weekly Off-Day',
                icon: 'fa-umbrella-beach',
                iconBg: '#fff5f5',
                iconColor: '#e03131',
                dateLabel: 'Holiday'
            });
        }

        // 1. Birthdays
        employees.forEach(emp => {
            if (emp.dob) {
                const parts = emp.dob.split('-');
                const dobMonth = parseInt(parts[1], 10) - 1;
                const dobDay = parseInt(parts[2], 10);
                if (dobMonth === month && dobDay === day) {
                    events.push({
                        type: 'birthday',
                        title: `${emp.name} (Birthday)`,
                        subtitle: `${emp.department} Department`,
                        icon: 'fa-cake-candles',
                        iconBg: '#fff9db',
                        iconColor: '#f08c00',
                        dateLabel: 'Birthday'
                    });
                }
            }
        });

        // 2. Anniversaries
        employees.forEach(emp => {
            if (emp.joining_date) {
                const parts = emp.joining_date.split('-');
                const joinYear = parseInt(parts[0], 10);
                const joinMonth = parseInt(parts[1], 10) - 1;
                const joinDay = parseInt(parts[2], 10);
                if (joinMonth === month && joinDay === day) {
                    const yrs = year - joinYear;
                    events.push({
                        type: 'anniversary',
                        title: `${emp.name} (Anniversary)`,
                        subtitle: `Joined ${joinYear} â€¢ ${emp.department}`,
                        icon: 'fa-star',
                        iconBg: '#ebfbee',
                        iconColor: '#2b8a3e',
                        dateLabel: yrs > 0 ? `${yrs} Yr${yrs > 1 ? 's' : ''}` : 'Joined'
                    });
                }
            }
        });

        // 3. Static Holidays (timezone safe via string splitting)
        holidays.forEach(holiday => {
            const parts = holiday.date.split('-');
            const holYear = parseInt(parts[0], 10);
            const holMonth = parseInt(parts[1], 10) - 1;
            const holDay = parseInt(parts[2], 10);
            if (holYear === year && holMonth === month && holDay === day) {
                const exists = events.some(e => e.title === holiday.name);
                if (!exists) {
                    events.push({
                        type: 'holiday',
                        title: holiday.name,
                        subtitle: 'Company Holiday',
                        icon: 'fa-umbrella-beach',
                        iconBg: '#fff5f5',
                        iconColor: '#e03131',
                        dateLabel: 'Holiday'
                    });
                }
            }
        });

        return events;
    }

    // Popover popup positioned above cell
    function openEventsPopover(year, month, day, dayEvents, cellElement) {
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        
        // Update Title
        document.getElementById("popoverTitle").innerText = `Events on ${monthNames[month]} ${day}, ${year}`;

        const body = document.getElementById("popoverBody");
        body.innerHTML = "";

        // Build HTML for each event
        dayEvents.forEach(evt => {
            const item = document.createElement("div");
            item.className = "calendar-event-item fade-in";

            let rightBadgeStyle = '';
            if (evt.type === 'birthday') rightBadgeStyle = 'background-color: #fff9db; color: #f08c00;';
            else if (evt.type === 'anniversary') rightBadgeStyle = 'background-color: #ebfbee; color: #2b8a3e;';
            else if (evt.type === 'holiday') rightBadgeStyle = 'background-color: #fff5f5; color: #e03131;';

            item.innerHTML = `
                <div class="event-item-left">
                    <div class="event-item-icon" style="background-color: ${evt.iconBg}; color: ${evt.iconColor};">
                        <i class="fa ${evt.icon}"></i>
                    </div>
                    <div class="event-item-info">
                        <span class="event-item-title">${evt.title}</span>
                        <span class="event-item-subtitle">${evt.subtitle}</span>
                    </div>
                </div>
                <div class="event-item-right" style="${rightBadgeStyle}">
                    ${evt.dateLabel}
                </div>
            `;
            body.appendChild(item);
        });

        // Toggle popover visible
        const popover = document.getElementById("calendarEventPopover");
        
        // Reset styles to calculate bounds accurately
        popover.style.display = "block";
        popover.style.top = "0px";
        popover.style.left = "0px";
        
        // Calculate position relative to the .twisty-card parent
        const card = cellElement.closest('.twisty-card');
        if (card) {
            const cardRect = card.getBoundingClientRect();
            const cellRect = cellElement.getBoundingClientRect();
            const popoverRect = popover.getBoundingClientRect();
            
            let top = cellRect.top - cardRect.top - popoverRect.height - 10;
            let left = cellRect.left - cardRect.left + (cellRect.width / 2) - (popoverRect.width / 2);
            
            // Ensure it doesn't go outside the card horizontally
            if (left < 10) left = 10;
            if (left + popoverRect.width > cardRect.width - 10) {
                left = cardRect.width - popoverRect.width - 10;
            }
            
            // If there's no space above, show it below the cell
            if (top < 10) {
                top = cellRect.bottom - cardRect.top + 10;
            }
            
            popover.style.top = top + "px";
            popover.style.left = left + "px";
        }
    }

    function closePopover() {
        document.getElementById("calendarEventPopover").style.display = "none";
    }

    // Dismiss on click outside
    document.addEventListener("click", function(event) {
        const popover = document.getElementById("calendarEventPopover");
        const isClickInside = popover.contains(event.target);
        const isCalendarCell = event.target.closest(".calendar-day-cell");
        
        if (!isClickInside && !isCalendarCell && popover.style.display === "block") {
            closePopover();
        }
    });
</script>
@endsection

