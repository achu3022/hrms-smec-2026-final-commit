import re

filepath = 'app/Models/Attendance.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

new_fields = """
        'checkin_accuracy',
        'checkin_distance',
        'checkout_accuracy',
        'checkout_distance',
        'checkin_ip',
        'checkout_ip',
        'checkin_browser',
        'checkout_browser',
        'checkin_device',
        'checkout_device',
        'location_verified',
"""

# Insert into $fillable array
content = re.sub(r'protected \$fillable = \[', 'protected $fillable = [' + new_fields, content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
