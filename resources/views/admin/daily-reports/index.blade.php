@extends('layouts.admin')

@section('content')
<div class="container-fluid mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--brand-dark);">Daily Work Reports</h2>
            <p class="text-muted mb-0">Manage employee daily task submissions and requirements.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <!-- Tabs Header -->
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-fill border-0" style="margin-bottom: -1px;">
                <li class="nav-item">
                    <a href="{{ route('admin.daily-reports.index', ['tab' => 'reports', 'date' => $date]) }}" 
                       class="nav-link {{ $tab === 'reports' ? 'active fw-bold' : 'text-muted' }} py-3 border-0 border-bottom {{ $tab === 'reports' ? 'border-primary border-3 text-primary' : '' }}">
                        <i class="fa-solid fa-list-check me-2"></i> View Reports
                    </a>
                </li>
                @role('Super Admin')
                <li class="nav-item">
                    <a href="{{ route('admin.daily-reports.index', ['tab' => 'settings']) }}" 
                       class="nav-link {{ $tab === 'settings' ? 'active fw-bold' : 'text-muted' }} py-3 border-0 border-bottom {{ $tab === 'settings' ? 'border-primary border-3 text-primary' : '' }}">
                        <i class="fa-solid fa-gear me-2"></i> Report Settings
                    </a>
                </li>
                @endrole
            </ul>
        </div>

        <div class="card-body p-4">
            @if($tab === 'reports')
                
                <form method="GET" action="{{ route('admin.daily-reports.index') }}" class="mb-4 bg-light p-3 rounded border">
                    <input type="hidden" name="tab" value="reports">
                    <div class="row align-items-center g-3">
                        <div class="col-auto">
                            <label class="form-label text-uppercase text-muted fw-bold mb-0" style="font-size: 0.8rem;">Filter Employee</label>
                        </div>
                        <div class="col-md-3">
                            <select name="employee_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ isset($employeeId) && $employeeId == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label text-uppercase text-muted fw-bold mb-0" style="font-size: 0.8rem;">Date</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>

                @if($reports->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-folder-open display-4 mb-3" style="opacity: 0.3;"></i>
                        <h5>No Reports Found</h5>
                        <p>No daily reports were submitted for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($reports as $report)
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                            {{ substr($report->employee->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold mb-0" style="color: var(--brand-dark);">{{ $report->employee->name }}</div>
                                            <div class="small text-muted">{{ $report->employee->departmentRel->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    
                                    @role('Super Admin')
                                    <button type="button" onclick="editReport({{ $report->id }}, {{ json_encode($report->tasks) }})" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Report
                                    </button>
                                    @endrole
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Task Name / Description</th>
                                                <th style="width: 100px;">Est. Hrs</th>
                                                <th style="width: 100px;">Act. Hrs</th>
                                                <th style="width: 130px;">Status</th>
                                                <th>Remarks</th>
                                                <th style="width: 120px;">Carry Fwd Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($report->tasks ?? [] as $index => $task)
                                                <tr>
                                                    <td class="text-muted">{{ $index + 1 }}</td>
                                                    <td class="fw-medium">{{ $task[0] ?? '' }}</td>
                                                    <td>{{ $task[1] ?? '0' }}</td>
                                                    <td>{{ $task[2] ?? '0' }}</td>
                                                    <td>
                                                        @php $status = $task[3] ?? ($task[2] ?? 'Pending'); @endphp
                                                        @if($status === 'Completed')
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">{{ $status }}</span>
                                                        @elseif($status === 'In Progress')
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">{{ $status }}</span>
                                                        @else
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">{{ $status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted">{{ $task[4] ?? ($task[3] ?? '') }}</td>
                                                    <td class="text-muted small">{{ $task[5] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
                
            @endif

            @if($tab === 'settings')
                <div class="table-responsive mt-2">
                    <table class="table table-hover align-middle border">
                        <thead class="table-light">
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th class="text-center" style="width: 200px;">Requires Daily Report</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td class="text-muted fw-medium">{{ $employee->employee_id }}</td>
                                    <td class="fw-bold" style="color: var(--brand-dark);">{{ $employee->name }}</td>
                                    <td>{{ $employee->departmentRel->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('admin.daily-reports.toggle', $employee->id) }}" class="m-0">
                                            @csrf
                                            <div class="form-check form-switch d-flex justify-content-center mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" {{ $employee->requires_daily_report ? 'checked' : '' }} style="width: 40px; height: 20px; cursor: pointer;">
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Modal (Bootstrap) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold">Edit Daily Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
         <div id="handsontable-container" class="w-100 p-3" style="min-height: 300px;"></div>
         <div class="px-4 pb-3">
             <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                 <div class="d-flex gap-4">
                     <div>
                         <span class="d-block text-muted small fw-bold text-uppercase">Total Estimated</span>
                         <span id="est-counter" class="fs-5 fw-bold text-dark">0.00 / 9.00 hrs</span>
                     </div>
                     <div>
                         <span class="d-block text-muted small fw-bold text-uppercase">Total Actual</span>
                         <span id="act-counter" class="fs-5 fw-bold text-dark">0.00 / 9.00 hrs</span>
                     </div>
                 </div>
                 <div>
                     <span id="counter-status" class="fw-bold text-danger">
                         <i class="fa-solid fa-circle-exclamation me-1"></i> Totals must exactly equal 9 hrs
                     </span>
                 </div>
             </div>
         </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="save-edit-btn">
            <i class="fa-solid fa-save me-1"></i> Save Changes
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Handsontable -->
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
<script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>

<script>
    let hot = null;
    let currentEditId = null;
    let editModal = null;

    document.addEventListener('DOMContentLoaded', function() {
        editModal = new bootstrap.Modal(document.getElementById('editModal'));
    });

    function editReport(id, tasks) {
        currentEditId = id;
        
        const container = document.getElementById('handsontable-container');
        
        // Convert 4-col data to 5-col, or 5-col to 6-col if needed
        let formattedTasks = Array.isArray(tasks) ? tasks.map(row => {
            if (row.length === 4) {
                return [row[0], row[1], row[1], row[2], row[3], ''];
            }
            if (row.length === 5) {
                return [...row, ''];
            }
            return row;
        }) : [];

        let data = formattedTasks;
        while (data.length < 5) {
            data.push(['', '', '', '', '', '']);
        }

        if (hot) {
            hot.destroy();
        }

        hot = new Handsontable(container, {
            data: data,
            colHeaders: ['Task Name / Description', 'Est. Time (Hrs)', 'Act. Time (Hrs)', 'Status', 'Remarks', 'Carry Forward Date'],
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
            width: '100%',
            height: 'auto',
            licenseKey: 'non-commercial-and-evaluation',
            afterChange: function (changes, source) {
                updateAdminCounters();
            },
            afterRemoveRow: function() {
                updateAdminCounters();
            }
        });
        
        editModal.show();
        
        setTimeout(() => {
            hot.render();
            updateAdminCounters();
        }, 200);
    }

    function updateAdminCounters() {
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
            statusEl.className = 'fw-bold text-success';
        } else {
            statusEl.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i> Totals must exactly equal 9 hrs';
            statusEl.className = 'fw-bold text-danger';
        }
    }

    document.getElementById('save-edit-btn').addEventListener('click', function() {
        if (!currentEditId) return;
        
        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;

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

        fetch("{{ url('admin/daily-reports') }}/" + currentEditId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tasks: cleanData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error updating report.');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    });
</script>
@endsection
