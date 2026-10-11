<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$employee = \App\Models\Employee::find(3);
$request = new \Illuminate\Http\Request();
$request->setMethod('PUT');
$request->server->set('REQUEST_URI', '/admin/employees/3');

$route = new \Illuminate\Routing\Route('PUT', 'admin/employees/{employee}', ['as' => 'admin.employees.update']);
$route->bind($request);
$route->setParameter('employee', '3'); // Assuming string parameter if not model bound

$updateRequest = new \App\Http\Requests\UpdateEmployeeRequest();
$updateRequest->setRouteResolver(function() use ($route) {
    return $route;
});

echo "Employee Param: " . gettype($updateRequest->route('employee')) . " - " . $updateRequest->route('employee') . "\n";

$rules = $updateRequest->rules();
echo "Rules for employee_id: \n";
print_r($rules['employee_id']);
