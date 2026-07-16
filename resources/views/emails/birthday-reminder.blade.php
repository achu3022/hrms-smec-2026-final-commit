<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background: #ffc107; color: #333; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; }
        ul { list-style-type: none; padding: 0; }
        li { padding: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎂 Today's Birthdays!</h2>
        </div>
        <div class="content">
            <p>Hello HR,</p>
            <p>The following employees are celebrating their birthday today:</p>
            
            <ul>
                @foreach($employees as $emp)
                    <li>
                        <strong>{{ $emp->name }}</strong> ({{ $emp->employee_id }})<br>
                        <small>{{ $emp->departmentRel->name ?? $emp->department }}</small>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Employee DataLog System.
        </div>
    </div>
</body>
</html>
