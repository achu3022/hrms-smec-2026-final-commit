@extends('employee.portal.layout')

@push('styles')
<style>
    /* Apply Form */
    .lv-form-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-bottom:24px; }
    .lv-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    @media(max-width:640px){ .lv-form-grid{ grid-template-columns:1fr; } }
    .lv-form-group { display:flex; flex-direction:column; gap:5px; }
    .lv-form-label { font-size:.75rem; font-weight:700; color:#475569; }
    .lv-form-input, .lv-form-textarea {
        border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px;
        font-size:.85rem; color:#1e293b; background:#f8fafc; outline:none;
        transition:border-color .2s; font-family:inherit;
    }
    .lv-form-input:focus, .lv-form-textarea:focus { border-color:#6366f1; background:#fff; }
    .lv-form-textarea { resize:vertical; min-height:90px; }
    .lv-submit-btn {
        background:#2b3a67; color:#fff; border:none; border-radius:8px;
        padding:9px 22px; font-size:.85rem; font-weight:700; cursor:pointer; transition:all .2s;
        display:inline-flex; align-items:center; gap:6px;
    }
    .lv-submit-btn:hover { background:#1e293b; }

    /* Stats */
    .lv-stat-row { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
    @media(max-width:480px){ .lv-stat-row{ grid-template-columns:1fr 1fr; } }
    .lv-stat-card {
        background:#fff; border:1px solid #e2e8f0; border-radius:12px;
        padding:14px; display:flex; flex-direction:column; gap:4px; text-align:center; transition:all .2s;
    }
    .lv-stat-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.05); transform:translateY(-1px); }
    .lv-stat-val  { font-size:1.6rem; font-weight:800; color:#1e293b; line-height:1; }
    .lv-stat-lbl  { font-size:.65rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; }

    /* History Table */
    .lv-table { width:100%; border-collapse:collapse; }
    .lv-table th { background:#f8fafc; color:#64748b; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:9px 14px; text-align:left; border-bottom:1px solid #e2e8f0; }
    .lv-table td { padding:11px 14px; font-size:.82rem; border-bottom:1px solid #f1f5f9; color:#374151; }
    .lv-table tr:last-child td { border-bottom:none; }
    .lv-table tr:hover td { background:#f8fafc; }
    .lv-pill { display:inline-flex; align-items:center; gap:3px; font-size:.68rem; font-weight:700; padding:2px 9px; border-radius:5px; }
    .pill-pending  { background:#fef9c3; color:#ca8a04; }
    .pill-approved { background:#dcfce7; color:#16a34a; }
    .pill-rejected { background:#fee2e2; color:#dc2626; }
    .pill-tl-approved { background: #e0e7ff; color: #4338ca; }
</style>
@endpush

@section('content')
<div class="mb-5">
    <h2 class="text-xl font-bold text-slate-800">Early Going</h2>
    <p class="text-sm text-slate-400 mt-0.5">Apply to leave work early</p>
</div>

{{-- Summary Stats --}}
<div class="lv-stat-row">
    <div class="lv-stat-card">
        <div class="lv-stat-val" style="color:#ca8a04;">{{ $requests->whereIn('status',['Pending', 'TL Approved'])->count() }}</div>
        <div class="lv-stat-lbl">Pending</div>
    </div>
    <div class="lv-stat-card">
        <div class="lv-stat-val" style="color:#16a34a;">{{ $requests->where('status','Approved')->count() }}</div>
        <div class="lv-stat-lbl">Approved</div>
    </div>
    <div class="lv-stat-card">
        <div class="lv-stat-val" style="color:#dc2626;">{{ $requests->where('status','Rejected')->count() }}</div>
        <div class="lv-stat-lbl">Rejected</div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Apply Form --}}
    <div class="lv-form-card">
        <h3 class="font-bold text-slate-800 text-sm mb-4 border-b border-slate-100 pb-3">
            <i class="fa-solid fa-clock-rotate-left text-indigo-500 mr-1"></i> Apply for Early Going
        </h3>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>{{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-lg px-4 py-3 mb-4">
                @foreach($errors->all() as $e)<div class="flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('employee.early-going.store') }}">
            @csrf
            <div class="flex flex-col gap-4">
                
                <div class="lv-form-group">
                    <label class="lv-form-label">Date *</label>
                    <input type="text" name="date" class="lv-form-input datepicker" required autocomplete="off">
                </div>

                <div class="lv-form-group">
                    <label class="lv-form-label">Time to Leave *</label>
                    <input type="time" name="time" class="lv-form-input" required>
                </div>

                <div class="lv-form-group">
                    <label class="lv-form-label">Reason *</label>
                    <textarea name="reason" class="lv-form-textarea" placeholder="Why do you need to leave early?" required></textarea>
                </div>

                <div class="mt-2">
                    <button type="submit" class="lv-submit-btn w-full justify-center">
                        Submit Request <i class="fa-solid fa-arrow-right text-[10px] opacity-70"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- History --}}
    <div class="xl:col-span-2">
        @if($requests->count() > 0)
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="lv-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Applied On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    <tr>
                        <td class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($req->date)->format('d-m-Y') }}</td>
                        <td class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($req->time)->format('h:i A') }}</td>
                        <td class="text-slate-500 max-w-[200px] truncate" title="{{ $req->reason }}">{{ $req->reason }}</td>
                        <td class="text-slate-400">{{ $req->created_at->format('d-m-Y') }}</td>
                        <td>
                            <span class="lv-pill
                                @if($req->status === 'Approved') pill-approved
                                @elseif($req->status === 'Rejected') pill-rejected
                                @elseif($req->status === 'TL Approved') pill-tl-approved
                                @else pill-pending @endif">
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="text-center py-14 text-slate-400">
                <i class="fa-regular fa-clock text-4xl mb-3 block"></i>
                <p class="text-sm">No early going requests found</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: 0
        });
    });
</script>
@endpush
@endsection
