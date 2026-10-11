<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendancePunchAttempt;
use App\Services\GeofenceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\WebcamImageService;
use App\Models\Setting;

class AttendanceController extends Controller
{
    public function punchIn(Request $request)
    {
        $requireSelfie = Setting::get('require_selfie_punch', true);

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'browser' => 'nullable|string',
            'device' => 'nullable|string',
            'photo' => $requireSelfie ? 'required|string' : 'nullable|string',
        ]);

        $user = Auth::guard('employee')->user();
        $employeeId = $user->employee_id;
        $shift = $user->employee->shift ?? '9:00 AM - 6:00 PM';
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $time = $now->format('H:i');
        
        $lat = $request->latitude;
        $lon = $request->longitude;
        $accuracy = $request->accuracy ?? 0;
        
        // Find nearest office
        $officeData = GeofenceService::getAbsoluteNearestOffice($lat, $lon);
        $distance = $officeData ? $officeData['distance'] : null;
        $isInside = false;

        // GPS Accuracy Check
        if ($accuracy > 50) {
            AttendancePunchAttempt::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'time' => $now->toTimeString(),
                'latitude' => $lat,
                'longitude' => $lon,
                'distance' => $distance,
                'accuracy' => $accuracy,
                'reason' => 'Poor GPS Accuracy (>50m)',
            ]);
            return response()->json(['success' => false, 'message' => 'Location accuracy is poor. Please move outdoors and try again.'], 400);
        }

        // Distance Check
        $validOffice = GeofenceService::getNearestValidOffice($lat, $lon);
        if (!$validOffice) {
            AttendancePunchAttempt::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'time' => $now->toTimeString(),
                'latitude' => $lat,
                'longitude' => $lon,
                'distance' => $distance,
                'accuracy' => $accuracy,
                'reason' => 'Outside Geofence (' . ($distance ? round($distance) . 'm away' : 'No office found') . ')',
            ]);
            return response()->json(['success' => false, 'message' => 'You are outside the company premises. Punch In is not allowed.'], 400);
        }

        $distance = $validOffice['distance'];
        $isInside = true;

        $shiftDetails = $user->employee ? $user->employee->getShiftDetails() : [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'start_carbon' => Carbon::parse('09:00'),
            'end_carbon' => Carbon::parse('18:00'),
            'label' => '9:00 AM - 6:00 PM',
        ];

        $lateGraceMinutes = (int)\App\Models\Setting::get('late_grace_period_minutes', 5);
        $halfDayThresholdMinutes = (int)\App\Models\Setting::get('half_day_late_threshold_minutes', 15);

        $shiftStart = $shiftDetails['start_carbon'];
        $lateThreshold = $shiftStart->copy()->addMinutes($lateGraceMinutes)->format('H:i');
        $halfDayThreshold = $shiftStart->copy()->addMinutes($halfDayThresholdMinutes)->format('H:i');
        
        $cutoffMinutes = max($halfDayThresholdMinutes + 60, 300);
        $cutoffTime = $shiftStart->copy()->addMinutes($cutoffMinutes)->format('H:i');

        if ($time > $cutoffTime) {
            $formattedCutoff = $shiftStart->copy()->addMinutes($cutoffMinutes)->format('g:i A');
            return response()->json(['success' => false, 'message' => "You cannot punch in after {$formattedCutoff} for the {$shiftDetails['label']} shift."], 400);
        }

        $status = 'Present';
        if ($time > $halfDayThreshold) {
            $status = 'Half-Day';
        } elseif ($time > $lateThreshold) {
            $status = 'Late';
        }

        $photoPath = WebcamImageService::processAndSaveImage($request->photo, $employeeId, 'checkin');

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employeeId, 'date' => $today],
            [
                'check_in' => $now->toTimeString(),
                'check_in_latitude' => $lat,
                'check_in_longitude' => $lon,
                'checkin_accuracy' => $accuracy,
                'checkin_distance' => $distance,
                'checkin_ip' => $request->ip(),
                'checkin_browser' => $request->browser,
                'checkin_device' => $request->device,
                'location_verified' => $isInside,
                'status' => $status,
                'checkin_photo' => $photoPath,
            ]
        );

        if (!$attendance->wasRecentlyCreated && !$attendance->check_in) {
            $attendance->update([
                'check_in' => $now->toTimeString(),
                'check_in_latitude' => $lat,
                'check_in_longitude' => $lon,
                'checkin_accuracy' => $accuracy,
                'checkin_distance' => $distance,
                'checkin_ip' => $request->ip(),
                'checkin_browser' => $request->browser,
                'checkin_device' => $request->device,
                'location_verified' => $isInside,
                'status' => $status,
                'checkin_photo' => $photoPath,
            ]);
        } else if (!$attendance->wasRecentlyCreated && $attendance->check_in) {
             return response()->json(['success' => false, 'message' => 'Already punched in today.'], 400);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Punched in successfully!',
            'check_in' => Carbon::parse($attendance->check_in)->format('h:i A')
        ]);
    }

    public function punchOut(Request $request)
    {
        $requireSelfie = Setting::get('require_selfie_punch', true);

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'browser' => 'nullable|string',
            'device' => 'nullable|string',
            'photo' => $requireSelfie ? 'required|string' : 'nullable|string',
        ]);

        $employeeId = Auth::guard('employee')->user()->employee_id;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::where('employee_id', $employeeId)->where('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json(['success' => false, 'message' => 'You must punch in first before you can punch out.'], 400);
        }

        if ($attendance->check_out) {
            return response()->json(['success' => false, 'message' => 'Already punched out.'], 400);
        }

        $lat = $request->latitude;
        $lon = $request->longitude;
        $accuracy = $request->accuracy ?? 0;
        
        $officeData = GeofenceService::getAbsoluteNearestOffice($lat, $lon);
        $distance = $officeData ? $officeData['distance'] : null;

        // GPS Accuracy Check
        if ($accuracy > 50) {
            AttendancePunchAttempt::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'time' => $now->toTimeString(),
                'latitude' => $lat,
                'longitude' => $lon,
                'distance' => $distance,
                'accuracy' => $accuracy,
                'reason' => 'Punch Out Failed: Poor GPS Accuracy (>50m)',
            ]);
            return response()->json(['success' => false, 'message' => 'Location accuracy is poor. Please move outdoors and try again.'], 400);
        }

        // Distance Check
        $validOffice = GeofenceService::getNearestValidOffice($lat, $lon);
        if (!$validOffice) {
            AttendancePunchAttempt::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'time' => $now->toTimeString(),
                'latitude' => $lat,
                'longitude' => $lon,
                'distance' => $distance,
                'accuracy' => $accuracy,
                'reason' => 'Punch Out Failed: Outside Geofence (' . ($distance ? round($distance) . 'm away' : 'No office found') . ')',
            ]);
            return response()->json(['success' => false, 'message' => 'You are outside the company premises. Punch Out is not allowed.'], 400);
        }

        $distance = $validOffice['distance'];

        $photoPath = WebcamImageService::processAndSaveImage($request->photo, $employeeId, 'checkout');

        $attendance->update([
            'check_out' => $now->toTimeString(),
            'check_out_latitude' => $lat,
            'check_out_longitude' => $lon,
            'checkout_accuracy' => $accuracy,
            'checkout_distance' => $distance,
            'checkout_ip' => $request->ip(),
            'checkout_browser' => $request->browser,
            'checkout_device' => $request->device,
            'checkout_photo' => $photoPath,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Punched out successfully!',
            'check_out' => Carbon::parse($attendance->check_out)->format('h:i A')
        ]);
    }
}
