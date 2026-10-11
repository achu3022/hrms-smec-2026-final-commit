<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EarlyGoingRequest;

class EarlyGoingController extends Controller
{
    public function index()
    {
        // Admin needs to see all requests, particularly those that are TL Approved
        $requests = EarlyGoingRequest::with('employee')->orderByDesc('created_at')->get();
        return view('admin.early_going.index', compact('requests'));
    }

    public function update(Request $request, $id)
    {
        $earlyRequest = EarlyGoingRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $earlyRequest->update([
            'status' => $request->status,
            'admin_approved_at' => $request->status === 'Approved' ? now() : null,
        ]);

        $requester = $earlyRequest->employee;
        if ($requester && $requester->employeeLogin) {
            if ($request->status === 'Approved') {
                $requester->employeeLogin->notify(new \App\Notifications\EarlyGoingStatusUpdated($earlyRequest, "Your early going request was Approved by Management."));
            } else {
                $requester->employeeLogin->notify(new \App\Notifications\EarlyGoingStatusUpdated($earlyRequest, "Your early going request was Rejected by Management."));
            }
        }

        return back()->with('success', 'Early going request status updated.');
    }
}
