@extends('layouts.admin')

@section('content')
<div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Early Going Requests</h2>
        <p class="text-sm text-slate-400 mt-0.5">Manage all early going requests</p>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Reason</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">TL Approval</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($requests as $req)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="font-bold text-slate-800">{{ $req->employee->name }}</div>
                        <div class="text-xs text-slate-500">{{ $req->employee->employee_id }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($req->time)->format('h:i A') }}</div>
                        <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($req->date)->format('d-m-Y') }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-sm text-slate-600 max-w-[200px] truncate" title="{{ $req->reason }}">
                            {{ $req->reason }}
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @if($req->tl_approved_at)
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                                <i class="fa-solid fa-check mr-1"></i> Approved
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1">{{ \Carbon\Carbon::parse($req->tl_approved_at)->format('d M, h:i A') }}</div>
                        @else
                            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-md">
                                <i class="fa-regular fa-clock mr-1"></i> Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold
                            @if($req->status === 'Approved') bg-emerald-50 text-emerald-600
                            @elseif($req->status === 'Rejected') bg-red-50 text-red-600
                            @elseif($req->status === 'TL Approved') bg-indigo-50 text-indigo-600
                            @else bg-amber-50 text-amber-600 @endif">
                            {{ str_replace('_', ' ', $req->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-end">
                        @if($req->status === 'TL Approved')
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.early-going.update', $req->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Approved">
                                    <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded text-xs font-bold transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.early-going.update', $req->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Rejected">
                                    <button class="bg-red-50 border border-red-200 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded text-xs font-bold transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @elseif($req->status === 'Pending')
                            <span class="text-xs text-amber-500 font-semibold"><i class="fa-solid fa-hourglass-half mr-1"></i> Waiting for TL</span>
                        @else
                            <span class="text-xs text-slate-400 font-medium">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-inbox text-4xl text-slate-200 mb-3 block"></i>
                        <p class="text-slate-500 font-medium">No early going requests found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
