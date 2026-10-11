@extends('employee.portal.layout')

@section('title', 'Team Daily Reports')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Team Daily Reports</h2>
            <p class="text-slate-500 text-sm mt-1">View the daily tasks submitted by your department.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6">
            <form method="GET" action="{{ route('employee.team.daily-reports.index') }}" class="flex items-center gap-4 mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Select Date</label>
                    <input type="date" name="date" value="{{ $date }}" class="lv-form-input w-48" onchange="this.form.submit()">
                </div>
            </form>

            @if($reports->isEmpty())
                <div class="text-center py-12 text-slate-500">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300"></i>
                    <p>No team members have submitted reports for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($reports as $report)
                    <div class="border border-slate-200 rounded-lg overflow-hidden">
                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                {{ substr($report->employee->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-sm">{{ $report->employee->name }}</div>
                            </div>
                        </div>
                        
                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="bg-slate-100/50 text-slate-600 border-b border-slate-200">
                                        <th class="py-2 px-4 font-semibold w-12">#</th>
                                        <th class="py-2 px-4 font-semibold">Task Name / Description</th>
                                        <th class="py-2 px-4 font-semibold w-24">Est. Hrs</th>
                                        <th class="py-2 px-4 font-semibold w-24">Act. Hrs</th>
                                        <th class="py-2 px-4 font-semibold w-40">Status</th>
                                        <th class="py-2 px-4 font-semibold">Remarks</th>
                                        <th class="py-2 px-4 font-semibold w-32">Carry Fwd Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($report->tasks ?? [] as $index => $task)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-2 px-4 text-slate-400">{{ $index + 1 }}</td>
                                            <td class="py-2 px-4 text-slate-800">{{ $task[0] ?? '' }}</td>
                                            <td class="py-2 px-4 text-slate-600">{{ $task[1] ?? '0' }}</td>
                                            <td class="py-2 px-4 text-slate-600">{{ $task[2] ?? '0' }}</td>
                                            <td class="py-2 px-4">
                                                @php $status = $task[3] ?? ($task[2] ?? 'Pending'); @endphp
                                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold tracking-wide 
                                                    {{ $status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : 
                                                      ($status === 'In Progress' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 text-slate-600">{{ $task[4] ?? ($task[3] ?? '') }}</td>
                                            <td class="py-2 px-4 text-slate-600 text-xs">{{ $task[5] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
