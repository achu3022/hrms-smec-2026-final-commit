<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
use App\Models\Holiday;

class AttendanceExport implements FromView, WithColumnWidths, WithStyles
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    public function view(): View
    {
        // Get all unique dates from the collection and sort them
        $dates = $this->attendances->pluck('date')->unique()->sort()->values()->toArray();
        
        // Fetch all holidays within the date range first
        $holidays = [];
        if (!empty($dates)) {
            $minDate = reset($dates);
            $maxDate = end($dates);
            $holidays = Holiday::whereBetween('date', [$minDate, $maxDate])->pluck('date')->toArray();
        }

        // Structure the data grouped by employee
        $employees = [];
        foreach ($this->attendances as $att) {
            $empId = $att->employee_id;
            if (!isset($employees[$empId])) {
                $employees[$empId] = [
                    'name' => $att->employee->name ?? 'Unknown',
                    'attendance' => [],
                    'present' => 0,
                    'absent' => 0,
                    'leave' => 0,
                ];
            }
            
            $status = $att->status;
            $isWeekend = Carbon::parse($att->date)->isSunday();
            $isHoliday = in_array($att->date, $holidays);
            
            $code = '';
            if ($status === 'Present') {
                $code = 'P';
                $employees[$empId]['present']++;
            } elseif (in_array($status, ['Absent', 'Not Punched In'])) {
                if ($isWeekend || $isHoliday) {
                    $code = 'H';
                } else {
                    $code = 'A';
                    $employees[$empId]['absent']++;
                }
            } elseif ($status === 'Late') {
                $code = 'L';
                $employees[$empId]['present']++; // Often counted as present
            } elseif ($status === 'Half-Day') {
                $code = 'H';
                $employees[$empId]['present'] += 0.5;
            } elseif ($status === 'Leave') {
                $code = 'LV';
                $employees[$empId]['leave']++;
            }
            
            $employees[$empId]['attendance'][$att->date] = $code;
        }

        return view('exports.attendance', compact('dates', 'employees', 'holidays'));
    }

    public function columnWidths(): array
    {
        $dates = $this->attendances->pluck('date')->unique()->toArray();
        $dateCount = count($dates);
        
        $widths = [
            'A' => 20, // Employee Name
        ];
        
        // Date columns
        for ($i = 0; $i < $dateCount; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i + 2); // 1-based index, A is 1
            $widths[$colLetter] = 4;
        }
        
        // Total columns
        $totalsStart = $dateCount + 2;
        $widths[Coordinate::stringFromColumnIndex($totalsStart)] = 15;
        $widths[Coordinate::stringFromColumnIndex($totalsStart + 1)] = 15;
        $widths[Coordinate::stringFromColumnIndex($totalsStart + 2)] = 15;

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
