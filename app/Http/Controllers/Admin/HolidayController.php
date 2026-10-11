<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $holidays = Holiday::whereYear('date', $year)->orderBy('date')->get();
        return view('admin.holidays.index', compact('holidays', 'year'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date',
        ]);

        Holiday::create($request->only(['name', 'date']));

        return back()->with('success', 'Holiday added successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return back()->with('success', 'Holiday deleted successfully.');
    }
}
