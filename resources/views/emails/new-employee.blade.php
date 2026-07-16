<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background: #004b87; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { width: 40%; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Employee Onboarding</h2>
        </div>
        <div class="content">
            <p>Hello HR,</p>
            <p>A new employee has just submitted their information through the onboarding portal. Please review their details and activate their profile if everything is correct.</p>
            
            <table>
                <tr>
                    <th>Employee Name</th>
                    <td>{{ $employee->name }}</td>
                </tr>
                <tr>
                    <th>Employee ID</th>
                    <td>{{ $employee->employee_id }}</td>
                </tr>
                <tr>
                    <th>Company Email</th>
                    <td>{{ $employee->company_email }}</td>
                </tr>
                <tr>
                    <th>Designation</th>
                    <td>{{ $employee->designationRel->name ?? $employee->designation }}</td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td>{{ $employee->departmentRel->name ?? $employee->department }}</td>
                </tr>
            </table>

            <p style="margin-top: 20px;">
                <a href="{{ route('admin.employees.show', $employee->id) }}" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">View Profile & Activate</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Employee DataLog System. All rights reserved.
        </div>
    </div>
</body>
</html>
