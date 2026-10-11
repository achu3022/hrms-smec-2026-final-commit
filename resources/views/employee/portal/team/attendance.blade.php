@extends('employee.portal.layout')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Team Attendance</h2>
        <p class="text-slate-500 text-sm">Manage attendance for your department team.</p>
    </div>
    
    <form method="GET" action="{{ route('employee.team.attendance') }}" class="flex items-center gap-2">
        <label for="date" class="text-sm font-medium text-slate-600">Select Date:</label>
        <input type="date" name="date" id="date" value="{{ $date }}" class="form-input rounded-md border-slate-300 shadow-sm text-sm" onchange="this.form.submit()">
    </form>
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
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Check In</th>
                    <th class="px-6 py-4">Check Out</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($teamMembers as $member)
                    @php
                        $attendance = $attendances->get($member->id);
                        $status = $attendance ? $attendance->status : 'Absent'; // Default if not marked
                    @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $member->name }}</div>
                        <div class="text-xs text-slate-500">{{ $member->employee_id }}</div>
                    </td>
                    <form method="POST" action="{{ route('employee.team.attendance.update') }}">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $member->id }}">
                        <input type="hidden" name="date" value="{{ $date }}">
                        
                        <td class="px-6 py-2">
                            <select name="status" class="form-select rounded-md border-slate-300 shadow-sm text-sm" required>
                                <option value="Present" {{ $status == 'Present' ? 'selected' : '' }}>Present</option>
                                <option value="Absent" {{ $status == 'Absent' ? 'selected' : '' }}>Absent</option>
                                <option value="Half-Day" {{ $status == 'Half-Day' ? 'selected' : '' }}>Half-Day</option>
                                <option value="Leave" {{ $status == 'Leave' ? 'selected' : '' }}>Leave</option>
                                <option value="Late" {{ $status == 'Late' ? 'selected' : '' }}>Late</option>
                            </select>
                        </td>
                        <td class="px-6 py-2">
                            <input type="time" name="check_in" value="{{ $attendance && $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '' }}" class="form-input rounded-md border-slate-300 shadow-sm text-sm">
                        </td>
                        <td class="px-6 py-2">
                            <input type="time" name="check_out" value="{{ $attendance && $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '' }}" class="form-input rounded-md border-slate-300 shadow-sm text-sm">
                        </td>
                        <td class="px-6 py-2 text-right">
                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                Save
                            </button>
                        </td>
                    </form>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                        <p>No team members found in your department.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
