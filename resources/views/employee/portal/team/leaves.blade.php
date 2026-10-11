@extends('employee.portal.layout')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Team Leaves</h2>
        <p class="text-slate-500 text-sm">Manage leave requests from your department team.</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-100 flex items-center gap-3">
    <i class="fa-solid fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Employee</th>
                    <th class="px-6 py-4">Dates</th>
                    <th class="px-6 py-4">Days</th>
                    <th class="px-6 py-4">Reason</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($requests as $req)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $req->employee->name }}</div>
                        <div class="text-xs text-slate-500">{{ $req->employee->employee_id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($req->start_date)->format('M d, Y') }} - 
                        {{ \Carbon\Carbon::parse($req->end_date)->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 font-medium">{{ $req->total_days }}</td>
                    <td class="px-6 py-4 max-w-xs truncate" title="{{ $req->reason }}">
                        {{ $req->reason ?: 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($req->status === 'Pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                Pending
                            </span>
                        @elseif($req->status === 'TL Approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                TL Approved
                            </span>
                        @elseif($req->status === 'Approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Approved
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $req->status }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($req->status === 'Pending')
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('employee.team.leaves.update', $req->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="TL Approved">
                                <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Approve & Send to HR">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('employee.team.leaves.update', $req->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reject">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-xs text-slate-400">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                            <i class="fa-solid fa-inbox text-xl"></i>
                        </div>
                        <p>No team leave requests found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($requests->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
