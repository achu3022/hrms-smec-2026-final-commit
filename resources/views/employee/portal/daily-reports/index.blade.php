@extends('employee.portal.layout')

@section('title', 'Daily Work Report')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Daily Work Report</h2>
            <p class="text-slate-500 text-sm mt-1">Submit your daily tasks and time spent.</p>
        </div>
        
        <form method="GET" action="{{ route('employee.daily-reports.index') }}" class="flex items-center gap-2">
            <label class="text-sm text-slate-600 font-medium">Date:</label>
            <input type="date" name="date" value="{{ $date }}" class="lv-form-input rounded-md py-1.5" onchange="this.form.submit()">
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-bold text-slate-700">Tasks for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h3>
            <button id="save-report" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Save Report
            </button>
        </div>
        
        <div class="p-4">
            <div id="handsontable-container" class="w-full mb-3"></div>
            
            <div class="flex gap-6 mt-4 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Estimated Time</span>
                    <span id="est-counter" class="text-lg font-bold text-slate-700">0.00 / 9.00 hrs</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Actual Time</span>
                    <span id="act-counter" class="text-lg font-bold text-slate-700">0.00 / 9.00 hrs</span>
                </div>
                <div class="flex flex-col ml-auto justify-center">
                    <span id="counter-status" class="text-sm font-semibold text-rose-600">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> Totals must exactly equal 9 hrs
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Handsontable CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css" />
<style>
    /* Force Handsontable Light Theme & Overrides */
    .handsontable {
        font-family: 'Inter', sans-serif !important;
    }
    .handsontable .htCore {
        color: #334155 !important;
    }
    .handsontable th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 600 !important;
        border-right: 1px solid #e2e8f0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        text-align: left !important;
    }
    .handsontable td {
        background-color: #ffffff !important;
        border-right: 1px solid #e2e8f0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        vertical-align: middle !important;
        color: #1e293b !important;
    }
    .handsontable th:first-child, .handsontable td:first-child {
        border-left: 1px solid #e2e8f0 !important;
    }
    .handsontable tr:first-child th {
        border-top: 1px solid #e2e8f0 !important;
    }
    .handsontable .currentRow, .handsontable .currentCol {
        background-color: #f1f5f9 !important;
    }
    .handsontable .wtSpreader {
        background-color: transparent !important;
    }
    .handsontableInput {
        background-color: #ffffff !important;
        color: #334155 !important;
        box-shadow: 0 0 0 2px #4f46e5 inset !important;
    }
</style>

<!-- Handsontable JS -->
<script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('handsontable-container');
        
        let savedData = @json(optional($report)->tasks ?? []);
        if (savedData.length > 0) {
            savedData = savedData.map(row => {
                // If it's a 4-col array (Task, Hours, Status, Remarks)
                if (row.length === 4) {
                    return [row[0], row[1], row[1], row[2], row[3], ''];
                }
                // If it's a 5-col array (Task, Est, Act, Status, Remarks)
                if (row.length === 5) {
                    return [...row, ''];
                }
                return row;
            });
        }
        
        const defaultData = savedData.length > 0 ? savedData : [
            ['', '', '', '', '', ''],
            ['', '', '', '', '', ''],
            ['', '', '', '', '', ''],
            ['', '', '', '', '', ''],
            ['', '', '', '', '', '']
        ];

        let hot;
        hot = new Handsontable(container, {
            data: defaultData,
            colHeaders: ['Task Name / Description', 'Estimated Time (Hrs)', 'Actual Time (Hrs)', 'Status', 'Remarks', 'Carry Forward Date'],
            columns: [
                { type: 'text' },
                { type: 'numeric' },
                { type: 'numeric' },
                { type: 'dropdown', source: ['Pending', 'In Progress', 'Completed'] },
                { type: 'text' },
                { type: 'date', dateFormat: 'YYYY-MM-DD', correctFormat: true }
            ],
            rowHeaders: true,
            minSpareRows: 1,
            contextMenu: true,
            stretchH: 'all',
            height: 'auto',
            licenseKey: 'non-commercial-and-evaluation',
            afterChange: function (changes, source) {
                updateCounters();
            },
            afterRemoveRow: function() {
                updateCounters();
            }
        });

        function updateCounters() {
            if (!hot) return;
            const rawData = hot.getData();
            const cleanData = rawData.filter(row => row.some(cell => cell !== null && cell !== ''));
            
            let totalEst = 0;
            let totalAct = 0;
            cleanData.forEach(row => {
                totalEst += parseFloat(row[1]) || 0;
                totalAct += parseFloat(row[2]) || 0;
            });

            document.getElementById('est-counter').textContent = totalEst.toFixed(2) + ' / 9.00 hrs';
            document.getElementById('act-counter').textContent = totalAct.toFixed(2) + ' / 9.00 hrs';

            const statusEl = document.getElementById('counter-status');
            if (totalEst === 9 && totalAct === 9) {
                statusEl.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Valid (Ready to Save)';
                statusEl.className = 'text-sm font-semibold text-emerald-600';
            } else {
                statusEl.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i> Totals must exactly equal 9 hrs';
                statusEl.className = 'text-sm font-semibold text-rose-600';
            }
        }
        
        // Initial call
        updateCounters();

        // Save functionality
        document.getElementById('save-report').addEventListener('click', function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;

            // Filter out completely empty rows
            const rawData = hot.getData();
            const cleanData = rawData.filter(row => row.some(cell => cell !== null && cell !== ''));

            let totalEst = 0;
            let totalAct = 0;
            let missingCarryForward = false;
            
            cleanData.forEach(row => {
                totalEst += parseFloat(row[1]) || 0;
                totalAct += parseFloat(row[2]) || 0;
                
                let status = row[3];
                let carryFwdDate = row[5];
                if (status === 'Pending' && !carryFwdDate) {
                    missingCarryForward = true;
                }
            });

            if (missingCarryForward) {
                alert('Error: All tasks marked as "Pending" strictly require a Carry Forward Date.');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                return;
            }

            if (totalEst !== 9 || totalAct !== 9) {
                alert(`Error: Both Estimated Time and Actual Time must total exactly 9 hours.\n\nCurrent Estimated: ${totalEst} hrs\nCurrent Actual: ${totalAct} hrs`);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                return;
            }

            fetch("{{ route('employee.daily-reports.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    report_date: "{{ $date }}",
                    tasks: cleanData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Report saved successfully!');
                } else {
                    alert(data.message || 'Error saving report.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving.');
            })
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        });
    });
</script>
@endsection
