<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeApiController extends Controller
{
    public function index()
    {
        return response()->json(Employee::where('status', 'Active')->get(['id', 'employee_id', 'name', 'company_email']));
    }
}
