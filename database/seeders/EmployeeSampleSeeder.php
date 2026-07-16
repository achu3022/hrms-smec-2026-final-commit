<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EducationDetail;
use App\Models\ExperienceDetail;
use App\Models\EmployeeCertificate;
use App\Models\Department;
use App\Models\Designation;
use App\Models\CompanyBranch;

class EmployeeSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample employee data
        $employees = [
            [
                'name' => 'Rajesh Kumar',
                'employee_id' => 'SMEC001',
                'department' => 'Engineering',
                'designation' => 'Senior Software Engineer',
                'company_email' => 'rajesh.kumar@smeclabs.com',
                'personal_email' => 'rajesh.kumar@gmail.com',
                'personal_phone' => '+91-9876543210',
                'office_phone' => '+91-080-12345001',
                'dob' => '1990-05-15',
                'gender' => 'Male',
                'blood_group' => 'O+',
                'marital_status' => 'Married',
                'aadhaar' => '123456789001',
                'pan' => 'ABCDE1234F',
                'joining_date' => '2020-01-15',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Priya Sharma',
                'employee_id' => 'SMEC002',
                'department' => 'Human Resources',
                'designation' => 'HR Manager',
                'company_email' => 'priya.sharma@smeclabs.com',
                'personal_email' => 'priya.sharma@gmail.com',
                'personal_phone' => '+91-9876543211',
                'office_phone' => '+91-080-12345002',
                'dob' => '1988-08-22',
                'gender' => 'Female',
                'blood_group' => 'A+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789002',
                'pan' => 'BCDEF2345G',
                'joining_date' => '2019-03-10',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Amit Patel',
                'employee_id' => 'SMEC003',
                'department' => 'Engineering',
                'designation' => 'Backend Developer',
                'company_email' => 'amit.patel@smeclabs.com',
                'personal_email' => 'amit.patel@gmail.com',
                'personal_phone' => '+91-9876543212',
                'office_phone' => '+91-080-12345003',
                'dob' => '1993-03-10',
                'gender' => 'Male',
                'blood_group' => 'B+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789003',
                'pan' => 'CDEFG3456H',
                'joining_date' => '2021-06-01',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Sneha Reddy',
                'employee_id' => 'SMEC004',
                'department' => 'Marketing',
                'designation' => 'Marketing Manager',
                'company_email' => 'sneha.reddy@smeclabs.com',
                'personal_email' => 'sneha.reddy@gmail.com',
                'personal_phone' => '+91-9876543213',
                'office_phone' => '+91-080-12345004',
                'dob' => '1991-11-05',
                'gender' => 'Female',
                'blood_group' => 'AB+',
                'marital_status' => 'Married',
                'aadhaar' => '123456789004',
                'pan' => 'DEFGH4567I',
                'joining_date' => '2020-09-15',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Vikram Singh',
                'employee_id' => 'SMEC005',
                'department' => 'Finance',
                'designation' => 'Financial Analyst',
                'company_email' => 'vikram.singh@smeclabs.com',
                'personal_email' => 'vikram.singh@gmail.com',
                'personal_phone' => '+91-9876543214',
                'office_phone' => '+91-080-12345005',
                'dob' => '1989-07-18',
                'gender' => 'Male',
                'blood_group' => 'O-',
                'marital_status' => 'Married',
                'aadhaar' => '123456789005',
                'pan' => 'EFGHI5678J',
                'joining_date' => '2018-11-20',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Ananya Iyer',
                'employee_id' => 'SMEC006',
                'department' => 'Engineering',
                'designation' => 'Frontend Developer',
                'company_email' => 'ananya.iyer@smeclabs.com',
                'personal_email' => 'ananya.iyer@gmail.com',
                'personal_phone' => '+91-9876543215',
                'office_phone' => '+91-080-12345006',
                'dob' => '1994-02-28',
                'gender' => 'Female',
                'blood_group' => 'A-',
                'marital_status' => 'Single',
                'aadhaar' => '123456789006',
                'pan' => 'FGHIJ6789K',
                'joining_date' => '2021-01-05',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Rohan Gupta',
                'employee_id' => 'SMEC007',
                'department' => 'Sales',
                'designation' => 'Sales Executive',
                'company_email' => 'rohan.gupta@smeclabs.com',
                'personal_email' => 'rohan.gupta@gmail.com',
                'personal_phone' => '+91-9876543216',
                'office_phone' => '+91-080-12345007',
                'dob' => '1992-09-12',
                'gender' => 'Male',
                'blood_group' => 'B-',
                'marital_status' => 'Single',
                'aadhaar' => '123456789007',
                'pan' => 'GHIJK7890L',
                'joining_date' => '2020-04-10',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Kavya Menon',
                'employee_id' => 'SMEC008',
                'department' => 'Engineering',
                'designation' => 'DevOps Engineer',
                'company_email' => 'kavya.menon@smeclabs.com',
                'personal_email' => 'kavya.menon@gmail.com',
                'personal_phone' => '+91-9876543217',
                'office_phone' => '+91-080-12345008',
                'dob' => '1991-06-20',
                'gender' => 'Female',
                'blood_group' => 'AB-',
                'marital_status' => 'Married',
                'aadhaar' => '123456789008',
                'pan' => 'HIJKL8901M',
                'joining_date' => '2019-08-22',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Arjun Nair',
                'employee_id' => 'SMEC009',
                'department' => 'Engineering',
                'designation' => 'QA Engineer',
                'company_email' => 'arjun.nair@smeclabs.com',
                'personal_email' => 'arjun.nair@gmail.com',
                'personal_phone' => '+91-9876543218',
                'office_phone' => '+91-080-12345009',
                'dob' => '1993-12-03',
                'gender' => 'Male',
                'blood_group' => 'O+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789009',
                'pan' => 'IJKLM9012N',
                'joining_date' => '2021-03-18',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Divya Krishnan',
                'employee_id' => 'SMEC010',
                'department' => 'Design',
                'designation' => 'UI/UX Designer',
                'company_email' => 'divya.krishnan@smeclabs.com',
                'personal_email' => 'divya.krishnan@gmail.com',
                'personal_phone' => '+91-9876543219',
                'office_phone' => '+91-080-12345010',
                'dob' => '1995-04-15',
                'gender' => 'Female',
                'blood_group' => 'A+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789010',
                'pan' => 'JKLMN0123O',
                'joining_date' => '2022-01-10',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Karthik Rao',
                'employee_id' => 'SMEC011',
                'department' => 'Engineering',
                'designation' => 'Tech Lead',
                'company_email' => 'karthik.rao@smeclabs.com',
                'personal_email' => 'karthik.rao@gmail.com',
                'personal_phone' => '+91-9876543220',
                'office_phone' => '+91-080-12345011',
                'dob' => '1987-01-25',
                'gender' => 'Male',
                'blood_group' => 'B+',
                'marital_status' => 'Married',
                'aadhaar' => '123456789011',
                'pan' => 'KLMNO1234P',
                'joining_date' => '2017-05-15',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Meera Joshi',
                'employee_id' => 'SMEC012',
                'department' => 'Human Resources',
                'designation' => 'HR Executive',
                'company_email' => 'meera.joshi@smeclabs.com',
                'personal_email' => 'meera.joshi@gmail.com',
                'personal_phone' => '+91-9876543221',
                'office_phone' => '+91-080-12345012',
                'dob' => '1992-10-08',
                'gender' => 'Female',
                'blood_group' => 'AB+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789012',
                'pan' => 'LMNOP2345Q',
                'joining_date' => '2020-07-20',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Sanjay Verma',
                'employee_id' => 'SMEC013',
                'department' => 'Operations',
                'designation' => 'Operations Manager',
                'company_email' => 'sanjay.verma@smeclabs.com',
                'personal_email' => 'sanjay.verma@gmail.com',
                'personal_phone' => '+91-9876543222',
                'office_phone' => '+91-080-12345013',
                'dob' => '1986-03-30',
                'gender' => 'Male',
                'blood_group' => 'O-',
                'marital_status' => 'Married',
                'aadhaar' => '123456789013',
                'pan' => 'MNOPQ3456R',
                'joining_date' => '2016-09-01',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Pooja Desai',
                'employee_id' => 'SMEC014',
                'department' => 'Marketing',
                'designation' => 'Content Writer',
                'company_email' => 'pooja.desai@smeclabs.com',
                'personal_email' => 'pooja.desai@gmail.com',
                'personal_phone' => '+91-9876543223',
                'office_phone' => '+91-080-12345014',
                'dob' => '1994-07-12',
                'gender' => 'Female',
                'blood_group' => 'A-',
                'marital_status' => 'Single',
                'aadhaar' => '123456789014',
                'pan' => 'NOPQR4567S',
                'joining_date' => '2021-11-05',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Rahul Mehta',
                'employee_id' => 'SMEC015',
                'department' => 'Engineering',
                'designation' => 'Mobile Developer',
                'company_email' => 'rahul.mehta@smeclabs.com',
                'personal_email' => 'rahul.mehta@gmail.com',
                'personal_phone' => '+91-9876543224',
                'office_phone' => '+91-080-12345015',
                'dob' => '1993-05-18',
                'gender' => 'Male',
                'blood_group' => 'B-',
                'marital_status' => 'Single',
                'aadhaar' => '123456789015',
                'pan' => 'OPQRS5678T',
                'joining_date' => '2020-12-10',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Nisha Kapoor',
                'employee_id' => 'SMEC016',
                'department' => 'Finance',
                'designation' => 'Accountant',
                'company_email' => 'nisha.kapoor@smeclabs.com',
                'personal_email' => 'nisha.kapoor@gmail.com',
                'personal_phone' => '+91-9876543225',
                'office_phone' => '+91-080-12345016',
                'dob' => '1990-09-25',
                'gender' => 'Female',
                'blood_group' => 'AB-',
                'marital_status' => 'Married',
                'aadhaar' => '123456789016',
                'pan' => 'PQRST6789U',
                'joining_date' => '2019-02-14',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Aditya Bhat',
                'employee_id' => 'SMEC017',
                'department' => 'Engineering',
                'designation' => 'Data Scientist',
                'company_email' => 'aditya.bhat@smeclabs.com',
                'personal_email' => 'aditya.bhat@gmail.com',
                'personal_phone' => '+91-9876543226',
                'office_phone' => '+91-080-12345017',
                'dob' => '1992-11-30',
                'gender' => 'Male',
                'blood_group' => 'O+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789017',
                'pan' => 'QRSTU7890V',
                'joining_date' => '2021-04-20',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Riya Chatterjee',
                'employee_id' => 'SMEC018',
                'department' => 'Sales',
                'designation' => 'Business Development Manager',
                'company_email' => 'riya.chatterjee@smeclabs.com',
                'personal_email' => 'riya.chatterjee@gmail.com',
                'personal_phone' => '+91-9876543227',
                'office_phone' => '+91-080-12345018',
                'dob' => '1989-04-08',
                'gender' => 'Female',
                'blood_group' => 'A+',
                'marital_status' => 'Married',
                'aadhaar' => '123456789018',
                'pan' => 'RSTUV8901W',
                'joining_date' => '2018-06-18',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Varun Pillai',
                'employee_id' => 'SMEC019',
                'department' => 'Engineering',
                'designation' => 'System Administrator',
                'company_email' => 'varun.pillai@smeclabs.com',
                'personal_email' => 'varun.pillai@gmail.com',
                'personal_phone' => '+91-9876543228',
                'office_phone' => '+91-080-12345019',
                'dob' => '1991-08-14',
                'gender' => 'Male',
                'blood_group' => 'B+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789019',
                'pan' => 'STUVW9012X',
                'joining_date' => '2020-02-28',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
            [
                'name' => 'Tanvi Agarwal',
                'employee_id' => 'SMEC020',
                'department' => 'Design',
                'designation' => 'Graphic Designer',
                'company_email' => 'tanvi.agarwal@smeclabs.com',
                'personal_email' => 'tanvi.agarwal@gmail.com',
                'personal_phone' => '+91-9876543229',
                'office_phone' => '+91-080-12345020',
                'dob' => '1996-01-22',
                'gender' => 'Female',
                'blood_group' => 'AB+',
                'marital_status' => 'Single',
                'aadhaar' => '123456789020',
                'pan' => 'TUVWX0123Y',
                'joining_date' => '2022-05-16',
                'employment_type' => 'Full-time',
                'status' => 'Active',
            ],
        ];

        // Get or create departments
        $departments = [];
        foreach (array_unique(array_column($employees, 'department')) as $deptName) {
            $departments[$deptName] = Department::firstOrCreate(
                ['name' => $deptName],
                ['is_active' => true]
            );
        }

        // Get or create designations
        $designations = [];
        foreach (array_unique(array_column($employees, 'designation')) as $desigName) {
            $designations[$desigName] = Designation::firstOrCreate(
                ['name' => $desigName],
                ['is_active' => true]
            );
        }

        // Get or create a default branch
        $branch = CompanyBranch::firstOrCreate(
            ['name' => 'Bangalore Head Office'],
            [
                'address' => 'MG Road, Bangalore, Karnataka 560001',
                'is_active' => true
            ]
        );

        // Create employees with details
        foreach ($employees as $index => $empData) {
            $employee = Employee::create([
                'employee_id' => $empData['employee_id'],
                'name' => $empData['name'],
                'department_id' => $departments[$empData['department']]->id,
                'designation_id' => $designations[$empData['designation']]->id,
                'branch_id' => $branch->id,
                'company_email' => $empData['company_email'],
                'personal_email' => $empData['personal_email'],
                'personal_phone' => $empData['personal_phone'],
                'office_phone' => $empData['office_phone'],
                'dob' => $empData['dob'],
                'gender' => $empData['gender'],
                'blood_group' => $empData['blood_group'],
                'marital_status' => $empData['marital_status'],
                'present_address' => $this->generateAddress(),
                'permanent_address' => $this->generateAddress(),
                'emergency_name' => $this->generateEmergencyContact(),
                'emergency_relation' => 'Father',
                'emergency_phone' => '+91-98765432' . str_pad($index + 30, 2, '0', STR_PAD_LEFT),
                'aadhaar' => $empData['aadhaar'],
                'pan' => $empData['pan'],
                'bank_name' => $this->getRandomBank(),
                'account_holder' => $empData['name'],
                'account_number' => rand(1000000000, 9999999999),
                'ifsc' => $this->getRandomIFSC(),
                'branch' => 'Main Branch',
                'joining_date' => $empData['joining_date'],
                'employment_type' => $empData['employment_type'],
                'reporting_manager' => 'John Smith',
                'work_location' => 'Bangalore',
                'status' => $empData['status'],
                'provided_assets' => [
                    'laptop' => 'Yes',
                    'phone' => 'Yes',
                ],
            ]);

            // Add Education Details
            $this->addEducationDetails($employee);

            // Add Experience Details (for employees with 2+ years)
            if (rand(0, 1)) {
                $this->addExperienceDetails($employee);
            }

            // Add Certificates (50% chance)
            if (rand(0, 1)) {
                $this->addCertificates($employee);
            }
        }

        $this->command->info('Successfully created 20 sample employees with complete details!');
    }

    private function generateAddress()
    {
        $addresses = [
            '#123, 4th Cross, Koramangala, Bangalore 560034',
            'Flat 456, Green Park Apartments, Indiranagar, Bangalore 560038',
            'House No. 789, Jayanagar 4th Block, Bangalore 560041',
            '#234, Brigade Road, MG Road, Bangalore 560001',
            'Villa 567, Whitefield, Bangalore 560066',
        ];
        return $addresses[array_rand($addresses)];
    }

    private function generateEmergencyContact()
    {
        $names = ['Ramesh Kumar', 'Suresh Patel', 'Mahesh Singh', 'Dinesh Sharma', 'Ganesh Reddy'];
        return $names[array_rand($names)];
    }

    private function getRandomBank()
    {
        $banks = ['HDFC Bank', 'ICICI Bank', 'State Bank of India', 'Axis Bank', 'Kotak Mahindra Bank'];
        return $banks[array_rand($banks)];
    }

    private function getRandomIFSC()
    {
        $codes = ['HDFC0001234', 'ICIC0001234', 'SBIN0001234', 'UTIB0001234', 'KKBK0001234'];
        return $codes[array_rand($codes)];
    }

    private function addEducationDetails($employee)
    {
        $qualifications = [
            ['qualification' => 'B.Tech', 'course' => 'Computer Science', 'institution' => 'IIT Delhi', 'university' => 'IIT Delhi', 'year' => '2015', 'percentage' => '85%'],
            ['qualification' => 'MBA', 'course' => 'Human Resources', 'institution' => 'IIM Bangalore', 'university' => 'IIM Bangalore', 'year' => '2017', 'percentage' => '78%'],
            ['qualification' => 'B.Com', 'course' => 'Commerce', 'institution' => 'Delhi University', 'university' => 'Delhi University', 'year' => '2016', 'percentage' => '72%'],
            ['qualification' => 'BCA', 'course' => 'Computer Applications', 'institution' => 'Christ University', 'university' => 'Bangalore University', 'year' => '2014', 'percentage' => '80%'],
            ['qualification' => 'B.Des', 'course' => 'Design', 'institution' => 'NID Ahmedabad', 'university' => 'NID', 'year' => '2018', 'percentage' => '88%'],
        ];

        $qual = $qualifications[array_rand($qualifications)];
        EducationDetail::create([
            'employee_id' => $employee->id,
            'qualification' => $qual['qualification'],
            'course' => $qual['course'],
            'institution' => $qual['institution'],
            'university' => $qual['university'],
            'year' => $qual['year'],
            'percentage' => $qual['percentage'],
        ]);
    }

    private function addExperienceDetails($employee)
    {
        $experiences = [
            ['company' => 'TCS', 'designation' => 'Software Engineer', 'from_date' => '2015-06-01', 'to_date' => '2018-05-31', 'years' => '3'],
            ['company' => 'Infosys', 'designation' => 'Senior Developer', 'from_date' => '2016-07-01', 'to_date' => '2019-06-30', 'years' => '3'],
            ['company' => 'Wipro', 'designation' => 'Tech Lead', 'from_date' => '2014-08-01', 'to_date' => '2017-07-31', 'years' => '3'],
            ['company' => 'Accenture', 'designation' => 'Consultant', 'from_date' => '2015-09-01', 'to_date' => '2018-08-31', 'years' => '3'],
        ];

        $exp = $experiences[array_rand($experiences)];
        ExperienceDetail::create([
            'employee_id' => $employee->id,
            'company' => $exp['company'],
            'designation' => $exp['designation'],
            'from_date' => $exp['from_date'],
            'to_date' => $exp['to_date'],
            'years' => $exp['years'],
        ]);
    }

    private function addCertificates($employee)
    {
        $certificates = [
            ['certificate_name' => 'AWS Certified Solutions Architect', 'issued_by' => 'Amazon Web Services', 'issued_date' => '2021-03-15', 'expiry_date' => '2024-03-15'],
            ['certificate_name' => 'Certified ScrumMaster', 'issued_by' => 'Scrum Alliance', 'issued_date' => '2020-06-20', 'expiry_date' => '2023-06-20'],
            ['certificate_name' => 'Google Cloud Certified', 'issued_by' => 'Google', 'issued_date' => '2022-01-10', 'expiry_date' => '2025-01-10'],
            ['certificate_name' => 'PMP Certification', 'issued_by' => 'PMI', 'issued_date' => '2019-09-05', 'expiry_date' => '2022-09-05'],
        ];

        $cert = $certificates[array_rand($certificates)];
        EmployeeCertificate::create([
            'employee_id' => $employee->id,
            'certificate_name' => $cert['certificate_name'],
            'issued_by' => $cert['issued_by'],
            'issued_date' => $cert['issued_date'],
            'expiry_date' => $cert['expiry_date'],
        ]);
    }
}
