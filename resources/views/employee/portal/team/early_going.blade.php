@extends('employee.portal.layout')

@push('styles')
<style>
    /* Table styling matching leaves */
    .lv-table { width:100%; border-collapse:collapse; }
    .lv-table th { background:#f8fafc; color:#64748b; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:12px 16px; text-align:left; border-bottom:1px solid #e2e8f0; }
    .lv-table td { padding:14px 16px; font-size:.85rem; border-bottom:1px solid #f1f5f9; color:#374151; vertical-align:middle; }
    .lv-table tr:hover td { background:#f8fafc; }
    .lv-pill { display:inline-flex; align-items:center; gap:4px; font-size:.7rem; font-weight:700; padding:4px 10px; border-radius:20px; }
    .pill-pending  { background:#fef3c7; color:#b45309; }
    .pill-approved { background:#dcfce7; color:#15803d; }
    .pill-rejected { background:#fee2e2; color:#b91c1c; }
    .pill-tl-approved { background: #e0e7ff; color: #4338ca; }
    
    .btn-action { padding:5px 12px; font-size:.75rem; font-weight:600; border-radius:6px; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:5px; border:none; }
    .btn-approve { background:#22c55e; color:#fff; }
    .btn-approve:hover { background:#16a34a; }
    .btn-reject { background:#ef4444; color:#fff; }
    .btn-reject:hover { background:#dc2626; }
</style>
@endpush

@section('content')
<div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Team Early Going Requests</h2>
        <p class="text-sm text-slate-400 mt-0.5">Manage early going requests for your department</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold rounded-xl px-5 py-4 mb-6 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="lv-table min-w-[800px]">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date & Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>
                        <div class="font-bold text-slate-800">{{ $req->employee->name }}</div>
                        <div class="text-xs text-slate-500">{{ $req->employee->employee_id }} • {{ $req->employee->designation }}</div>
                    </td>
                    <td>
                        <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($req->time)->format('h:i A') }}</div>
                        <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($req->date)->format('d-m-Y') }}</div>
                    </td>
                    <td>
                        <div class="text-slate-600 max-w-[250px] truncate" title="{{ $req->reason }}">
                            {{ $req->reason }}
                        </div>
                    </td>
                    <td>
                        <span class="lv-pill
                            @if($req->status === 'Approved') pill-approved
                            @elseif($req->status === 'Rejected') pill-rejected
                            @elseif($req->status === 'TL Approved') pill-tl-approved
                            @else pill-pending @endif">
                            {{ str_replace('_', ' ', $req->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        @if($req->status === 'Pending')
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('employee.team.early-going.update', $req->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="TL Approved">
                                    <button class="btn-action btn-approve"><i class="fa-solid fa-check"></i> Approve</button>
                                </form>
                                <form action="{{ route('employee.team.early-going.update', $req->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Rejected">
                                    <button class="btn-action btn-reject"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-slate-400">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12">
                        <i class="fa-solid fa-inbox text-4xl text-slate-200 mb-3 block"></i>
                        <p class="text-slate-500 font-medium">No early going requests from your team.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
