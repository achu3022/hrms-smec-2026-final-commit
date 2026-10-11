@extends('employee.portal.layout')

@push('styles')
<style>
    .att-filter-bar {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 14px; padding: 12px 16px; margin-bottom: 20px;
    }
    .att-filter-label { font-size: 0.75rem; font-weight: 700; color: #64748b; white-space: nowrap; }
    .att-filter-select {
        border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 6px 10px; font-size: 0.82rem; font-weight: 600;
        color: #1e293b; background: #f8fafc; outline: none; cursor: pointer; transition: border-color 0.2s;
    }
    .att-filter-select:focus { border-color: #6366f1; }
    .att-filter-btn {
        background: #2b3a67; color: #fff; border: none;
        border-radius: 8px; padding: 6px 16px;
        font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
    }
    .att-filter-btn:hover { background: #1e293b; }
    .att-period-label { margin-left: auto; font-size: 0.8rem; font-weight: 700; color: #6366f1; }

    .att-stat-grid { display: grid; grid-template-columns: repeat(5,1fr); gap: 10px; margin-bottom: 20px; }
    @media(max-width:640px) { .att-stat-grid { grid-template-columns: repeat(2,1fr); } }
    .att-stat-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 12px 10px; display: flex; flex-direction: column;
        align-items: center; text-align: center; gap: 6px; transition: all 0.2s;
    }
    .att-stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); transform: translateY(-1px); }
    .att-stat-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .att-stat-value { font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1; }
    .att-stat-label { font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.4px; }

    .att-cal-wrap { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; }
    .att-cal-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .att-cal-nav {
        width: 28px; height: 28px; border-radius: 7px; background: #f1f5f9;
        border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: #475569; font-size: 0.75rem; transition: all 0.15s;
    }
    .att-cal-nav:hover { background: #e2e8f0; }
    .att-wday-row {
        display: grid; grid-template-columns: repeat(7,1fr);
        text-align: center; font-size: 0.65rem; font-weight: 700;
        color: #94a3b8; letter-spacing: 0.3px; margin-bottom: 4px;
    }
    .att-day-grid { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; }
    .att-day-cell {
        aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
        border-radius: 7px; font-size: 0.72rem; font-weight: 600;
        cursor: default; position: relative; transition: all 0.12s;
        color: #475569; border: 1px solid transparent;
    }
    .att-day-cell.other-month { color: #e2e8f0; }
    .att-day-cell.today       { outline: 2px solid #2b3a67; outline-offset: 1px; }
    .att-day-cell.future      { color: #cbd5e1; background: #f8fafc; }
    .att-day-cell.present     { background: #dcfce7; color: #16a34a; border-color: #bbf7d0; }
    .att-day-cell.absent      { background: #f5ebe0; color: #5c4033; border-color: #e3d5ca; }
    .att-day-cell.half-day    { background: #fef9c3; color: #ca8a04; border-color: #fef08a; }
    .att-day-cell.late        { background: #ffedd5; color: #ea580c; border-color: #fed7aa; }
    .att-day-cell.leave       { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }
    .att-day-cell.sunday      { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
    .att-day-cell.holiday     { background: #ffccbc; color: #bf360c; border-color: #ffab91; }
    .att-day-cell[data-tip]:hover::after {
        content: attr(data-tip); position: absolute;
        bottom: calc(100% + 5px); left: 50%; transform: translateX(-50%);
        background: #1e293b; color: #fff; font-size: 0.6rem; font-weight: 600;
        padding: 3px 7px; border-radius: 5px; white-space: nowrap; z-index: 50;
    }
    .att-legend {
        display: flex; flex-wrap: wrap; gap: 8px;
        font-size: 0.65rem; font-weight: 600; color: #64748b;
        margin-top: 10px; padding-top: 10px; border-top: 1px solid #f1f5f9;
    }
    .att-legend-dot { width: 8px; height: 8px; border-radius: 2px; display: inline-block; }

    .att-table { width: 100%; border-collapse: collapse; }
    .att-table th {
        background: #f8fafc; color: #64748b; font-size: 0.7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.4px;
        padding: 9px 12px; text-align: left; border-bottom: 1px solid #e2e8f0;
    }
    .att-table td { padding: 10px 12px; font-size: 0.82rem; border-bottom: 1px solid #f1f5f9; color: #374151; }
    .att-table tr:last-child td { border-bottom: none; }
    .att-table tr:hover td { background: #f8fafc; }
    .att-pill {
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 0.68rem; font-weight: 700; padding: 2px 9px; border-radius: 5px;
    }
    .pill-present  { background:#dcfce7; color:#16a34a; }
    .pill-absent   { background:#f5ebe0; color:#5c4033; }
    .pill-half-day { background:#fef9c3; color:#ca8a04; }
    .pill-late     { background:#ffedd5; color:#ea580c; }
    .pill-leave    { background:#dbeafe; color:#1d4ed8; }
    #attTableBody tr.hidden-row { display: none; }
    #noRecordsMsg { display: none; }
</style>
@endpush

@section('content')

<div class="mb-5">
    <h2 class="text-xl font-bold text-slate-800">My Attendance</h2>
    <p class="text-sm text-slate-400 mt-0.5">Track your attendance history</p>
</div>

{{-- Filter Bar --}}
<div class="att-filter-bar">
    <span class="att-filter-label"><i class="fa-solid fa-filter mr-1 text-indigo-500"></i>Filter by</span>
    <select id="filterMonth" class="att-filter-select">
        @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
            </option>
        @endforeach
    </select>
    <select id="filterYear" class="att-filter-select">
        @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
    </select>
    <button class="att-filter-btn" onclick="applyFilter()"><i class="fa-solid fa-search mr-1"></i>Apply</button>
    <span id="periodLabel" class="att-period-label"></span>
</div>

{{-- Stats Bar --}}
<div class="att-stat-grid">
    <div class="att-stat-card">
        <div class="att-stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-circle-check"></i></div>
        <div class="att-stat-value" id="statPresent">0</div>
        <div class="att-stat-label">Present</div>
    </div>
    <div class="att-stat-card">
        <div class="att-stat-icon" style="background:#f5ebe0;color:#5c4033;"><i class="fa-solid fa-circle-xmark"></i></div>
        <div class="att-stat-value" id="statAbsent">0</div>
        <div class="att-stat-label">Absent</div>
    </div>
    <div class="att-stat-card">
        <div class="att-stat-icon" style="background:#fef9c3;color:#ca8a04;"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <div class="att-stat-value" id="statHalf">0</div>
        <div class="att-stat-label">Half Day</div>
    </div>
    <div class="att-stat-card">
        <div class="att-stat-icon" style="background:#ffedd5;color:#ea580c;"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="att-stat-value" id="statLate">0</div>
        <div class="att-stat-label">Late</div>
    </div>
    <div class="att-stat-card">
        <div class="att-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fa-solid fa-umbrella-beach"></i></div>
        <div class="att-stat-value" id="statLeave">0</div>
        <div class="att-stat-label">On Leave</div>
    </div>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Compact Calendar --}}
    <div class="lg:col-span-2 att-cal-wrap">
        <div class="att-cal-head">
            <button id="calPrev" class="att-cal-nav"><i class="fa-solid fa-chevron-left"></i></button>
            <span id="calMonthYear" class="font-bold text-slate-800 text-sm text-center"></span>
            <button id="calNext" class="att-cal-nav"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="att-wday-row">
            <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
        </div>
        <div id="attCalGrid" class="att-day-grid"></div>
        <div class="att-legend">
            <span class="flex items-center gap-1"><span class="att-legend-dot" style="background:#16a34a;"></span>Present</span>
            <span class="flex items-center gap-1"><span class="att-legend-dot" style="background:#5c4033;"></span>Absent</span>
            <span class="flex items-center gap-1"><span class="att-legend-dot" style="background:#ca8a04;"></span>Half Day</span>
            <span class="flex items-center gap-1"><span class="att-legend-dot" style="background:#ea580c;"></span>Late</span>
            <span class="flex items-center gap-1"><span class="att-legend-dot" style="background:#2563eb;"></span>Leave</span>
            <span class="flex items-center gap-1"><span class="att-legend-dot" style="background:#dc2626;"></span>Sunday</span>
            <span class="flex items-center gap-1"><span class="att-legend-dot" style="background:#bf360c;"></span>Holiday</span>
        </div>
    </div>

    {{-- Today's Punch + Mini Summary --}}
    <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-4 flex flex-col gap-3">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide border-b border-slate-100 pb-2">Today's Punch</p>
        @if($todayAttendance)
            <div class="flex justify-between items-center bg-emerald-50 rounded-lg px-3 py-2 border border-emerald-100">
                <span class="text-xs font-semibold text-emerald-800"><i class="fa-solid fa-right-to-bracket mr-1"></i>In</span>
                <span class="text-xs font-bold text-emerald-700">{{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : '—' }}</span>
            </div>
            @if($todayAttendance->check_out)
            <div class="flex justify-between items-center bg-red-50 rounded-lg px-3 py-2 border border-red-100">
                <span class="text-xs font-semibold text-red-800"><i class="fa-solid fa-right-from-bracket mr-1"></i>Out</span>
                <span class="text-xs font-bold text-red-700">{{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</span>
            </div>
            @endif
            <div class="flex justify-between items-center bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                <span class="text-xs font-semibold text-slate-600">Status</span>
                <span class="att-pill @if($todayAttendance->status==='Present') pill-present @elseif($todayAttendance->status==='Absent') pill-absent @elseif($todayAttendance->status==='Half-Day') pill-half-day @elseif($todayAttendance->status==='Late') pill-late @else pill-leave @endif">{{ $todayAttendance->status }}</span>
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-3">No punch today.</p>
        @endif
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide border-t border-slate-100 pt-3 mt-1">Month Overview</p>
        <div id="miniSummary" class="flex flex-col gap-2"></div>
    </div>
</div>

{{-- Records Table --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex justify-between items-center px-5 py-3 border-b border-slate-100">
        <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-table-list text-indigo-500 mr-1"></i> Records</h3>
        <span id="recordCount" class="text-xs text-slate-400"></span>
    </div>
    <div class="overflow-x-auto">
        <table class="att-table">
            <thead>
                <tr><th>Date</th><th>Day</th><th>Punch In</th><th>Punch Out</th><th>Hours</th><th>Status</th></tr>
            </thead>
            <tbody id="attTableBody">
                @foreach($allRecords->sortByDesc('date') as $att)
                    @php
                        $wh = '—';
                        if ($att->check_in && $att->check_out) {
                            $diff = \Carbon\Carbon::parse($att->check_in)->diffInMinutes(\Carbon\Carbon::parse($att->check_out));
                            $wh = floor($diff/60).'h '.($diff%60).'m';
                        }
                        $attDate = \Carbon\Carbon::parse($att->date);
                    @endphp
                    <tr data-month="{{ $attDate->month }}" data-year="{{ $attDate->year }}">
                        <td class="font-semibold">{{ $attDate->format('d-m-Y') }}</td>
                        <td class="text-slate-500">{{ $attDate->format('D') }}</td>
                        <td>{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '—' }}</td>
                        <td class="font-semibold text-slate-700">{{ $wh }}</td>
                        <td>
                            <span class="att-pill @if($att->status==='Present') pill-present @elseif($att->status==='Absent') pill-absent @elseif($att->status==='Half-Day') pill-half-day @elseif($att->status==='Late') pill-late @else pill-leave @endif">
                                @if($att->status==='Present')<i class="fa-solid fa-circle-check"></i>
                                @elseif($att->status==='Absent')<i class="fa-solid fa-circle-xmark"></i>
                                @elseif($att->status==='Half-Day')<i class="fa-solid fa-clock-rotate-left"></i>
                                @elseif($att->status==='Late')<i class="fa-solid fa-hourglass-half"></i>
                                @else<i class="fa-solid fa-umbrella-beach"></i>@endif
                                {{ $att->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div id="noRecordsMsg" class="text-center py-10 text-slate-400">
            <i class="fa-regular fa-calendar-xmark text-3xl mb-2 block"></i>
            <p class="text-sm font-semibold">No records for this period.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const attData = @json($calendarData);
    const holidayData = @json($holidays ?? []);
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const statusMeta = {
        'Present':  { cls:'present',  tip:'Present'  },
        'Absent':   { cls:'absent',   tip:'Absent'   },
        'Half-Day': { cls:'half-day', tip:'Half Day' },
        'Late':     { cls:'late',     tip:'Late'     },
        'Leave':    { cls:'leave',    tip:'On Leave' },
    };
    function pad(n){ return String(n).padStart(2,'0'); }

    let activeMonth = new Date().getMonth() + 1;
    let activeYear  = new Date().getFullYear();

    function applyFilter() {
        activeMonth = parseInt(document.getElementById('filterMonth').value);
        activeYear  = parseInt(document.getElementById('filterYear').value);
        renderCalendar(activeYear, activeMonth - 1);
        filterTable(activeMonth, activeYear);
        document.getElementById('periodLabel').innerText = 'Showing: ' + monthNames[activeMonth-1] + ' ' + activeYear;
    }

    function renderCalendar(year, month) {
        document.getElementById('calMonthYear').innerText = monthNames[month] + ' ' + year;
        const grid = document.getElementById('attCalGrid');
        grid.innerHTML = '';
        const firstIdx = new Date(year, month, 1).getDay();
        const lastDay  = new Date(year, month + 1, 0).getDate();
        const prevLast = new Date(year, month, 0).getDate();
        const today    = new Date();
        let dayCtr = 1, nextCtr = 1;
        let mP=0,mA=0,mH=0,mL=0,mLv=0;
        for (let i = 0; i < 42; i++) {
            const cell = document.createElement('div');
            cell.classList.add('att-day-cell');
            if (i < firstIdx) {
                cell.innerText = prevLast - firstIdx + i + 1;
                cell.classList.add('other-month');
            } else if (dayCtr <= lastDay) {
                const d = dayCtr;
                cell.innerText = d;
                const dateKey  = year+'-'+pad(month+1)+'-'+pad(d);
                const cellDate = new Date(year, month, d);
                const isSunday = cellDate.getDay() === 0;
                const isToday  = today.getFullYear()===year && today.getMonth()===month && today.getDate()===d;
                const isFuture = cellDate > today && !isToday;
                if (isToday) cell.classList.add('today');
                if (isSunday) {
                    cell.classList.add('sunday');
                    cell.setAttribute('data-tip','Sunday');
                } else if (holidayData[dateKey]) {
                    cell.classList.add('holiday');
                    cell.setAttribute('data-tip', holidayData[dateKey]);
                } else if (isFuture) {
                    cell.classList.add('future');
                } else if (attData[dateKey]) {
                    const rec  = attData[dateKey];
                    const meta = statusMeta[rec.status] || { cls:'present', tip:rec.status };
                    cell.classList.add(meta.cls);
                    let tip = meta.tip;
                    if (rec.check_in)  tip += ' · ' + rec.check_in;
                    if (rec.check_out) tip += ' → ' + rec.check_out;
                    cell.setAttribute('data-tip', tip);
                    if(rec.status==='Present') mP++;
                    if(rec.status==='Absent')  mA++;
                    if(rec.status==='Half-Day')mH++;
                    if(rec.status==='Late')    mL++;
                    if(rec.status==='Leave')   mLv++;
                }
                dayCtr++;
            } else {
                cell.innerText = nextCtr++;
                cell.classList.add('other-month');
            }
            grid.appendChild(cell);
        }
        document.getElementById('statPresent').innerText = mP;
        document.getElementById('statAbsent').innerText  = mA;
        document.getElementById('statHalf').innerText    = mH;
        document.getElementById('statLate').innerText    = mL;
        document.getElementById('statLeave').innerText   = mLv;
        const items = [
            {label:'Present', val:mP,  color:'#16a34a', bg:'#dcfce7'},
            {label:'Absent',  val:mA,  color:'#5c4033', bg:'#f5ebe0'},
            {label:'Half Day',val:mH,  color:'#ca8a04', bg:'#fef9c3'},
            {label:'Late',    val:mL,  color:'#ea580c', bg:'#ffedd5'},
            {label:'Leave',   val:mLv, color:'#1d4ed8', bg:'#dbeafe'},
        ];
        document.getElementById('miniSummary').innerHTML = items.map(i =>
            '<div style="display:flex;justify-content:space-between;align-items:center;padding:6px 10px;background:'+i.bg+';border-radius:8px;">'+
            '<span style="font-size:0.75rem;font-weight:700;color:'+i.color+';">'+i.label+'</span>'+
            '<span style="font-size:1rem;font-weight:800;color:'+i.color+';">'+i.val+'</span></div>'
        ).join('');
    }

    document.getElementById('calPrev').addEventListener('click', () => {
        let m = parseInt(document.getElementById('filterMonth').value);
        let y = parseInt(document.getElementById('filterYear').value);
        m--; if (m < 1) { m = 12; y--; }
        document.getElementById('filterMonth').value = m;
        document.getElementById('filterYear').value  = y;
        applyFilter();
    });
    document.getElementById('calNext').addEventListener('click', () => {
        let m = parseInt(document.getElementById('filterMonth').value);
        let y = parseInt(document.getElementById('filterYear').value);
        m++; if (m > 12) { m = 1; y++; }
        document.getElementById('filterMonth').value = m;
        document.getElementById('filterYear').value  = y;
        applyFilter();
    });

    function filterTable(month, year) {
        const rows = document.querySelectorAll('#attTableBody tr');
        let visible = 0;
        rows.forEach(row => {
            if (parseInt(row.dataset.month)===month && parseInt(row.dataset.year)===year) {
                row.classList.remove('hidden-row'); visible++;
            } else {
                row.classList.add('hidden-row');
            }
        });
        document.getElementById('recordCount').innerText = visible + ' record' + (visible!==1?'s':'') + ' found';
        document.getElementById('noRecordsMsg').style.display = visible===0 ? 'block':'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCalendar(activeYear, activeMonth - 1);
        filterTable(activeMonth, activeYear);
        document.getElementById('periodLabel').innerText = 'Showing: ' + monthNames[activeMonth-1] + ' ' + activeYear;
    });
</script>
@endpush

@endsection
