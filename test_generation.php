<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\EmployeeLogin;

// Create a dummy employee
$employee = Employee::create([
    'name' => 'John Doe',
    'employee_id' => 'EMP-' . rand(1000, 9999),
    'company_email' => 'john.doe' . rand(100, 999) . '@company.com',
    'personal_phone' => '123456' . rand(1000, 9999),
    'dob' => '1990-05-15',
    'gender' => 'Male',
    'blood_group' => 'O+',
    'emergency_name' => 'Jane Doe',
    'emergency_relation' => 'Wife',
    'emergency_phone' => '0987654321',
    'aadhaar' => '12345678' . rand(1000, 9999),
    'bank_name' => 'Test Bank',
    'account_holder' => 'John Doe',
    'account_number' => '987654321',
    'ifsc' => 'TEST0001234',
    'photo' => 'dummy.jpg',
    'aadhaar_file' => 'dummy.pdf'
]);

echo "Created employee: {$employee->employee_id}\n";

$login = EmployeeLogin::where('employee_id', $employee->id)->first();
if ($login) {
    echo "SUCCESS: Auto-generated login found!\n";
    echo "Username: {$login->username}\n";
    echo "Is First Login: " . ($login->is_first_login ? 'true' : 'false') . "\n";
    
    // Check if the hashed password matches the expected '15051990'
    if (Illuminate\Support\Facades\Hash::check('15051990', $login->password)) {
        echo "SUCCESS: Password formatting (DDMMYYYY) is correct!\n";
    } else {
        echo "ERROR: Password format incorrect.\n";
    }
} else {
    echo "ERROR: Auto-generated login NOT found!\n";
}
