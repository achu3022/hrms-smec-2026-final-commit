<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\EmployeeLogin;
use App\Models\QrAttendanceSession;
use App\Models\Attendance;
use Carbon\Carbon;

class QrAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function createEmployee()
    {
        $employee = Employee::create([
            'employee_id' => 'EMP100',
            'name' => 'John Doe',
            'status' => 'Active',
            'shift' => '09:00 AM - 06:00 PM',
            'is_profile_completed' => true,
        ]);

        $employeeLogin = EmployeeLogin::create([
            'employee_id' => $employee->id,
            'username' => 'EMP100',
            'password' => bcrypt('password'),
            'is_first_login' => false,
        ]);

        return [$employee, $employeeLogin];
    }

    public function test_kiosk_returns_active_qr_token_and_svg()
    {
        $response = $this->getJson(route('api.qr.kiosk-token'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'active',
            ]);

        $this->assertNotNull($response->json('token'));
        $this->assertNotNull($response->json('qr_svg'));
        $this->assertDatabaseHas('qr_attendance_sessions', [
            'token' => $response->json('token'),
            'status' => 'active',
        ]);
    }

    public function test_scanning_qr_code_marks_token_as_scanned()
    {
        [$employee, $employeeLogin] = $this->createEmployee();

        $session = QrAttendanceSession::createNewSession(30);

        $response = $this->actingAs($employeeLogin, 'employee')
            ->get(route('employee.qr.scan', ['token' => $session->token]));

        $response->assertStatus(200);

        $session->refresh();
        $this->assertEquals('scanned', $session->status);
        $this->assertEquals($employee->id, $session->scanned_by_employee_id);

        // Verify Kiosk now detects this and rotates to new token on next fetch
        $kioskResponse = $this->getJson(route('api.qr.kiosk-token'));
        $kioskResponse->assertStatus(200);
        $this->assertNotEquals($session->token, $kioskResponse->json('token'));
    }

    public function test_employee_can_punch_in_via_qr_with_selfie()
    {
        Carbon::setTestNow(Carbon::today()->setTime(9, 15, 0));

        [$employee, $employeeLogin] = $this->createEmployee();

        $session = QrAttendanceSession::createNewSession(30);

        // Dummy base64 photo data
        $dummyPhoto = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($employeeLogin, 'employee')
            ->postJson(route('employee.attendance.punch-via-qr'), [
                'token' => $session->token,
                'punch_type' => 'check_in',
                'photo' => $dummyPhoto,
                'browser' => 'Test Browser',
                'device' => 'Test Device',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Punched In successfully via Front Office QR Code!',
            ]);

        $session->refresh();
        $this->assertEquals('consumed', $session->status);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => Carbon::today()->toDateString(),
            'location_verified' => 1,
        ]);
    }

    public function test_expired_qr_code_cannot_be_used_for_punch()
    {
        [$employee, $employeeLogin] = $this->createEmployee();

        $session = QrAttendanceSession::create([
            'token' => 'expired_token_123',
            'status' => 'expired',
            'expires_at' => Carbon::now()->subMinutes(5),
        ]);

        $dummyPhoto = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($employeeLogin, 'employee')
            ->postJson(route('employee.attendance.punch-via-qr'), [
                'token' => $session->token,
                'punch_type' => 'check_in',
                'photo' => $dummyPhoto,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_punch_in_after_cutoff_time_is_rejected()
    {
        [$employee, $employeeLogin] = $this->createEmployee();

        $session = QrAttendanceSession::createNewSession(86400);

        // Freeze time at 15:00 (3:00 PM, after 2:00 PM cutoff for 9:00 AM shift)
        Carbon::setTestNow(Carbon::today()->setTime(15, 0, 0));

        $dummyPhoto = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($employeeLogin, 'employee')
            ->postJson(route('employee.attendance.punch-via-qr'), [
                'token' => $session->token,
                'punch_type' => 'check_in',
                'photo' => $dummyPhoto,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertStringContainsString('cannot punch in after', $response->json('message'));
        
        Carbon::setTestNow(null);
    }
}
