<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card - {{ $employee->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .id-card {
            width: 2.125in;
            height: 3.375in;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
            border: 2px solid #e9ecef;
            backdrop-filter: blur(10px);
        }
        .id-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(135deg, #2f3092 0%, #0070bc 50%, #00aa9e 100%);
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            z-index: 0;
        }
        .header {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 12px 10px 8px;
            color: white;
        }
        .company-logo {
            width: 50px;
            height: auto;
            margin-bottom: 5px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        .company-name {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            margin: 0;
        }
        .photo-container {
            text-align: center;
            margin-top: 5px;
            position: relative;
            z-index: 1;
        }
        .photo-wrapper {
            display: inline-block;
            padding: 4px;
            background: linear-gradient(135deg, #00aa9e, #0070bc);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .photo {
            width: 90px;
            height: 90px;
            border-radius: 8px;
            border: 3px solid white;
            object-fit: cover;
            display: block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .photo-placeholder {
            width: 90px;
            height: 90px;
            border-radius: 8px;
            border: 3px solid white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            color: #adb5bd;
            font-size: 32px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .details {
            text-align: center;
            margin-top: 12px;
            padding: 0 15px;
        }
        .name {
            font-size: 15px;
            font-weight: 800;
            color: #2f3092;
            margin-bottom: 3px;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .designation {
            font-size: 10px;
            color: #0070bc;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-grid {
            background: linear-gradient(135deg, rgba(47,48,146,0.05), rgba(0,170,158,0.05));
            border-radius: 10px;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid rgba(0,170,158,0.2);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-size: 9px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            color: #6c757d;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .info-label i {
            color: #00aa9e;
            font-size: 10px;
        }
        .info-value {
            color: #212529;
            font-weight: 700;
        }
        .qr-code {
            text-align: center;
            margin-top: 12px;
        }
        .qr-code svg {
            width: 45px;
            height: 45px;
            padding: 4px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: linear-gradient(135deg, #2f3092, #0070bc);
            text-align: center;
            font-size: 7px;
            padding: 6px 0;
            color: white;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        @media print {
            body { 
                background: white;
                padding: 0;
            }
            .id-card { 
                box-shadow: none;
                margin: 0 auto;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div style="position: absolute; top: 20px; left: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-size:16px;">Print ID Card</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; font-size:16px; margin-left: 10px;">Close</button>
    </div>

    <div class="id-card">
        <div class="header">
            <img src="{{ asset('logo-new.webp') }}" alt="SMEC Labs Logo" class="company-logo">
            <div class="company-name">{{ $companyName }}</div>
        </div>
        <div class="photo-container">
            <div class="photo-wrapper">
                @if($employee->photo)
                    <img src="{{ Storage::url($employee->photo) }}" class="photo" alt="Employee Photo">
                @else
                    <div class="photo-placeholder">
                        <i class="fa fa-user"></i>
                    </div>
                @endif
            </div>
        </div>
        <div class="details">
            <div class="name">{{ $employee->name }}</div>
            <div class="designation">{{ $employee->designationRel->name ?? $employee->designation }}</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-id-badge"></i> ID</span>
                    <span class="info-value">{{ $employee->employee_id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-tint"></i> Blood</span>
                    <span class="info-value">{{ $employee->blood_group }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-phone"></i> Contact</span>
                    <span class="info-value">{{ substr($employee->personal_phone, -4) }}</span>
                </div>
            </div>
        </div>
        <div class="qr-code">
            {!! QrCode::size(45)->generate($employee->employee_id) !!}
        </div>
        <div class="footer">
            IF FOUND, PLEASE RETURN TO {{ strtoupper($companyName) }}
        </div>
    </div>
</body>
</html>
