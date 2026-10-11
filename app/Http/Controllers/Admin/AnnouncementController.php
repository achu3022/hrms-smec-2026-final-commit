<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['department', 'employees'])->latest()->get();
        $departments = Department::orderBy('name')->get();
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();

        return view('admin.announcements.index', compact('announcements', 'departments', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,urgent',
            'target_type' => 'required|in:all,department,individual',
            'department_id' => 'required_if:target_type,department|nullable|exists:departments,id',
            'employee_ids' => 'required_if:target_type,individual|nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $announcementData = collect($validated)->except('employee_ids')->toArray();
        $announcementData['is_active'] = true;
        $announcementData['created_by'] = auth()->id();

        $announcement = Announcement::create($announcementData);

        if ($request->target_type === 'individual' && $request->filled('employee_ids')) {
            $announcement->employees()->sync($request->employee_ids);
        }

        return redirect()->back()->with('success', 'Broadcast announcement sent successfully!');
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        return redirect()->back()->with('success', 'Announcement status updated!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully!');
    }
}
