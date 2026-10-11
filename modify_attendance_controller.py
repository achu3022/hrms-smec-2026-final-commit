import re

filepath = 'app/Http/Controllers/Employee/AttendanceController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Add use statements if not exist
if 'use App\Services\GeofenceService;' not in content:
    content = content.replace('use App\Models\Attendance;', "use App\Models\Attendance;\nuse App\Models\AttendancePunchAttempt;\nuse App\Services\GeofenceService;")

# --- Replace punchIn method ---
new_punch_in = """
    public function punchIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'browser' => 'nullable|string',
            'device' => 'nullable|string',
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

        $status = 'Present';
        
        if ($shift === '8:00 AM - 5:00 PM') {
            if ($time > '13:30') {
                return response()->json(['success' => false, 'message' => 'You cannot punch in after 1:30 PM for the 8:00 AM - 5:00 PM shift.'], 400);
            }
            if ($time > '08:15') {
                $status = 'Half-Day';
            } elseif ($time > '08:05') {
                $status = 'Late';
            }
        } else {
            if ($time > '14:00') {
                return response()->json(['success' => false, 'message' => 'You cannot punch in after 2:00 PM for the 9:00 AM - 6:00 PM shift.'], 400);
            }
            if ($time > '09:20') {
                $status = 'Half-Day';
            } elseif ($time > '09:05') {
                $status = 'Late';
            }
        }

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employeeId, 'date' => $today],
            [
                'check_in' => $now->toTimeString(),
                'check_in_latitude' => $lat,
                'check_in_longitude' => $lon,
                'check_in_accuracy' => $accuracy,
                'check_in_distance' => $distance,
                'check_in_ip' => $request->ip(),
                'check_in_browser' => $request->browser,
                'check_in_device' => $request->device,
                'location_verified' => $isInside,
                'status' => $status,
            ]
        );

        if (!$attendance->wasRecentlyCreated && !$attendance->check_in) {
            $attendance->update([
                'check_in' => $now->toTimeString(),
                'check_in_latitude' => $lat,
                'check_in_longitude' => $lon,
                'check_in_accuracy' => $accuracy,
                'check_in_distance' => $distance,
                'check_in_ip' => $request->ip(),
                'check_in_browser' => $request->browser,
                'check_in_device' => $request->device,
                'location_verified' => $isInside,
                'status' => $status,
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
"""

new_punch_out = """
    public function punchOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'browser' => 'nullable|string',
            'device' => 'nullable|string',
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

        $attendance->update([
            'check_out' => $now->toTimeString(),
            'checkout_latitude' => $lat,
            'checkout_longitude' => $lon,
            'checkout_accuracy' => $accuracy,
            'checkout_distance' => $distance,
            'checkout_ip' => $request->ip(),
            'checkout_browser' => $request->browser,
            'checkout_device' => $request->device,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Punched out successfully!',
            'check_out' => Carbon::parse($attendance->check_out)->format('h:i A')
        ]);
    }
"""

content = re.sub(r'public function punchIn.*?public function punchOut', new_punch_in + "\n", content, flags=re.DOTALL)
content = re.sub(r'public function punchOut.*?\n    }\n', new_punch_out + "\n", content, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
