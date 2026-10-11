import re

filepath = 'routes/web.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Add use statement for Admin\OfficeLocationController if not exists
if 'use App\Http\Controllers\Admin\OfficeLocationController;' not in content:
    content = content.replace('use App\Http\Controllers\Admin\HolidayController;', "use App\Http\Controllers\Admin\HolidayController;\nuse App\Http\Controllers\Admin\OfficeLocationController;")

# Add the route for office-locations inside the admin middleware group
if 'admin.office-locations.index' not in content:
    # Find a good place to insert it. E.g. right before "Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');"
    route_string = """
    // Office Locations
    Route::get('office-locations', [OfficeLocationController::class, 'index'])->name('office-locations.index');
    Route::put('office-locations/{id}', [OfficeLocationController::class, 'update'])->name('office-locations.update');
"""
    # Let's just append it to the end of the admin group
    # We can match `// Dashboard` and insert before it
    content = content.replace('// Dashboard', route_string + '\n    // Dashboard')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
