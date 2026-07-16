<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background: #dc3545; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ Document Expiry Reminders</h2>
        </div>
        <div class="content">
            <p>Hello HR,</p>
            <p>The following employee documents are expiring in exactly 30 days:</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Document</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringCerts as $cert)
                        <tr>
                            <td>
                                <strong>{{ $cert->employee->name ?? 'Unknown' }}</strong><br>
                                <small>{{ $cert->employee->employee_id ?? '' }}</small>
                            </td>
                            <td>{{ $cert->certificate_name }}</td>
                            <td style="color: #dc3545;">{{ \Carbon\Carbon::parse($cert->expiry_date)->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <p>Please remind the respective employees to submit updated documents.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Employee DataLog System.
        </div>
    </div>
</body>
</html>
