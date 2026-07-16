<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experience Certificate - {{ $employee->name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: #fff;
            margin: 0;
            padding: 40px;
        }
        .certificate-container {
            border: 10px solid #004b87;
            padding: 50px;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header .logo {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #004b87;
            font-size: 36px;
            margin: 10px 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header p {
            font-size: 16px;
            color: #555;
            margin: 5px 0 0;
        }
        .title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 40px;
            text-decoration: underline;
        }
        .content {
            font-size: 18px;
            line-height: 1.8;
            text-align: justify;
        }
        .signature-section {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-bottom: 10px;
        }
        .qr-code {
            position: absolute;
            bottom: 50px;
            right: 50px;
        }
        .date {
            margin-bottom: 30px;
            font-size: 16px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .certificate-container { border: 10px solid #004b87; }
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size:16px; cursor: pointer;">Print Certificate</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size:16px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="certificate-container">
        <div class="header">
            <img src="{{ asset('logo-new.webp') }}" alt="SMEC Labs Logo" class="logo">
            <h1>{{ $companyName }}</h1>
            @if($branch)
                <p>{{ $branch->address ?? $branch->name }}</p>
            @else
                <p>Head Office, Corporate Address</p>
            @endif
        </div>

        <div class="date">
            <strong>Date:</strong> {{ date('d F Y') }}
        </div>

        <div class="title">EXPERIENCE CERTIFICATE</div>

        <div class="content">
            <p>To Whomsoever It May Concern,</p>
            <p>
                This is to certify that <strong>{{ $employee->name }}</strong> was employed with our organization from 
                <strong>{{ \Carbon\Carbon::parse($employee->joining_date)->format('d F Y') }}</strong> 
                to <strong>{{ date('d F Y') }}</strong>. 
                They held the designation of <strong>{{ $employee->designationRel->name ?? $employee->designation }}</strong> 
                in the <strong>{{ $employee->departmentRel->name ?? $employee->department }}</strong> department.
            </p>
            <p>
                During their tenure, we found them to be a dedicated, hardworking, and reliable professional. 
                They have shown excellent commitment to their responsibilities and contributed significantly to the success of our projects.
            </p>
            <p>
                We wish them all the best in their future endeavors.
            </p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>Authorized Signatory</strong><br>
                {{ $companyName }}<br>
                <small>Human Resources Department</small>
            </div>
        </div>

        <div class="qr-code">
            {!! QrCode::size(80)->generate('Experience Certificate - ' . $employee->employee_id) !!}
        </div>
    </div>
</body>
</html>
