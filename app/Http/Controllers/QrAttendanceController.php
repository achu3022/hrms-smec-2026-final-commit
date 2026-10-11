<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrAttendanceSession;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\WebcamImageService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QrAttendanceController extends Controller
{
    /**
     * Display the Front-Office Kiosk page.
     */
    public function showKiosk()
    {
        return view('admin.qr_kiosk');
    }

    /**
     * API for Front-Office Kiosk polling & token rotation.
     */
    public function getKioskToken(Request $request)
    {
        $session = QrAttendanceSession::getActiveSession(86400);

        // If force refresh requested or current session was scanned/consumed/expired, create new token
        if ($request->boolean('force_refresh') || in_array($session->status, ['scanned', 'consumed', 'expired']) || $session->isExpired()) {
            $session = QrAttendanceSession::createNewSession(86400);
        }

        $scanUrl = route('employee.qr.scan', ['token' => $session->token]);

        // Generate high resolution SVG string
        $qrSvg = (string) QrCode::size(320)->margin(1)->generate($scanUrl);

        return response()->json([
            'success' => true,
            'token' => $session->token,
            'status' => $session->status,
            'qr_svg' => $qrSvg,
            'scan_url' => $scanUrl,
            'scanned_by' => $session->scannedByEmployee ? $session->scannedByEmployee->name : null,
        ]);
    }

    /**
     * Render Employee QR Scanner or Punch page.
     */
    public function showScanPage(Request $request)
    {
        $token = $request->query('token');
        $user = Auth::guard('employee')->user();
        $employee = $user->employee;

        if (!$token) {
            // Show camera scanner view
            return view('employee.portal.qr_scan', [
                'token' => null,
                'session' => null,
                'employee' => $employee,
            ]);
        }

        $session = QrAttendanceSession::where('token', $token)->first();

        if (!$session || $session->isExpired() || $session->status === 'consumed') {
            return view('employee.portal.qr_scan', [
                'error' => 'This QR Code is invalid, expired, or has already been used. Please scan the active QR code displayed on the Front Office screen.',
                'token' => null,
                'session' => null,
                'employee' => $employee,
            ]);
        }

        // If active, claim it for this employee so kiosk rotates
        if ($session->status === 'active') {
            $session->markAsScanned($employee->id);
        } elseif ($session->status === 'scanned' && $session->scanned_by_employee_id !== $employee->id) {
            return view('employee.portal.qr_scan', [
                'error' => 'This QR code was already scanned by another employee. Please scan the current QR code on the screen.',
                'token' => null,
                'session' => null,
                'employee' => $employee,
            ]);
        }

        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::where('employee_id', $employee->id)->where('date', $today)->first();

        return view('employee.portal.qr_scan', [
            'token' => $token,
            'session' => $session,
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
        ]);
    }

    /**
     * Submit attendance punch via QR verification + Selfie.
     */
    public function punchViaQr(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'punch_type' => 'required|in:check_in,check_out',
            'photo' => 'required|string',
            'browser' => 'nullable|string',
            'device' => 'nullable|string',
        ]);

        $user = Auth::guard('employee')->user();
        $employee = $user->employee;
        $employeeId = $employee->id;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $time = $now->format('H:i');

        // Validate QR Token
        $session = QrAttendanceSession::where('token', $request->token)->first();

        if (!$session || $session->isExpired() || $session->status === 'consumed') {
            return response()->json([
                'success' => false,
                'message' => 'The QR token is invalid, expired, or already used. Please scan the fresh QR code on the kiosk screen.'
            ], 400);
        }

        if ($session->scanned_by_employee_id && $session->scanned_by_employee_id !== $employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'This QR code was claimed by another employee.'
            ], 400);
        }

        $photoPath = WebcamImageService::processAndSaveImage($request->photo, $employeeId, $request->punch_type);

        if ($request->punch_type === 'check_in') {
            $shiftDetails = $employee ? $employee->getShiftDetails() : [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'start_carbon' => Carbon::parse('09:00'),
                'end_carbon' => Carbon::parse('18:00'),
                'label' => '9:00 AM - 6:00 PM',
            ];

            $lateGraceMinutes = (int) Setting::get('late_grace_period_minutes', 5);
            $halfDayThresholdMinutes = (int) Setting::get('half_day_late_threshold_minutes', 15);

            $shiftStart = $shiftDetails['start_carbon'];
            $lateThreshold = $shiftStart->copy()->addMinutes($lateGraceMinutes)->format('H:i');
            $halfDayThreshold = $shiftStart->copy()->addMinutes($halfDayThresholdMinutes)->format('H:i');

            $cutoffMinutes = max($halfDayThresholdMinutes + 60, 300);
            $cutoffTime = $shiftStart->copy()->addMinutes($cutoffMinutes)->format('H:i');

            if ($time > $cutoffTime) {
                $formattedCutoff = $shiftStart->copy()->addMinutes($cutoffMinutes)->format('g:i A');
                return response()->json([
                    'success' => false,
                    'message' => "You cannot punch in after {$formattedCutoff} for the {$shiftDetails['label']} shift."
                ], 400);
            }

            $status = 'Present';
            if ($time > $halfDayThreshold) {
                $status = 'Half-Day';
            } elseif ($time > $lateThreshold) {
                $status = 'Late';
            }

            $attendance = Attendance::firstOrCreate(
                ['employee_id' => $employeeId, 'date' => $today],
                [
                    'check_in' => $now->toTimeString(),
                    'checkin_ip' => $request->ip(),
                    'checkin_browser' => $request->browser ?? 'Front Office QR Scan',
                    'checkin_device' => $request->device ?? 'Mobile Scanner',
                    'location_verified' => true,
                    'status' => $status,
                    'checkin_photo' => $photoPath,
                ]
            );

            if (!$attendance->wasRecentlyCreated && !$attendance->check_in) {
                $attendance->update([
                    'check_in' => $now->toTimeString(),
                    'checkin_ip' => $request->ip(),
                    'checkin_browser' => $request->browser ?? 'Front Office QR Scan',
                    'checkin_device' => $request->device ?? 'Mobile Scanner',
                    'location_verified' => true,
                    'status' => $status,
                    'checkin_photo' => $photoPath,
                ]);
            } else if (!$attendance->wasRecentlyCreated && $attendance->check_in) {
                return response()->json(['success' => false, 'message' => 'Already punched in today.'], 400);
            }

            // Consume Token
            $session->markAsConsumed();

            return response()->json([
                'success' => true,
                'message' => 'Punched In successfully via Front Office QR Code!',
                'check_in' => Carbon::parse($attendance->check_in)->format('h:i A'),
            ]);

        } else {
            // Punch Out
            $attendance = Attendance::where('employee_id', $employeeId)->where('date', $today)->first();

            if (!$attendance || !$attendance->check_in) {
                return response()->json(['success' => false, 'message' => 'You must punch in first before punching out.'], 400);
            }

            if ($attendance->check_out) {
                return response()->json(['success' => false, 'message' => 'Already punched out today.'], 400);
            }

            $attendance->update([
                'check_out' => $now->toTimeString(),
                'checkout_ip' => $request->ip(),
                'checkout_browser' => $request->browser ?? 'Front Office QR Scan',
                'checkout_device' => $request->device ?? 'Mobile Scanner',
                'checkout_photo' => $photoPath,
            ]);

            // Consume Token
            $session->markAsConsumed();

            return response()->json([
                'success' => true,
                'message' => 'Punched Out successfully via Front Office QR Code!',
                'check_out' => Carbon::parse($attendance->check_out)->format('h:i A'),
            ]);
        }
    }
}
