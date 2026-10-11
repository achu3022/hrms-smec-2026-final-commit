import re

filepath = 'resources/views/employee/portal/dashboard.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Add Google Maps API to styles section if not exists
if 'https://maps.googleapis.com/maps/api/js?key=' not in content:
    content = content.replace("@push('scripts')", "@push('scripts')\n<script src=\"https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry&callback=initMap\" async defer></script>\n")

# Replace Attendance Widget HTML
# The attendance widget is between `<!-- Attendance Widget -->` and `<!-- Column 2 -->`
# We'll use regex to replace that chunk.
attendance_widget_regex = re.compile(r'<!-- Attendance Widget -->.*?</div>\s*</div>\s*<!-- Column 2 -->', re.DOTALL)

new_attendance_widget = r"""<!-- Attendance Widget -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-rose-500 text-lg"></i>
                    <div>
                        <h3 class="font-bold text-slate-800 leading-tight">Attendance</h3>
                        <p class="text-xs text-slate-500">Punch in/out for today</p>
                    </div>
                </div>
                <div id="geofenceStatus" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                    <i class="fa-solid fa-spinner fa-spin"></i> Locating...
                </div>
            </div>
            
            <div class="p-5">
                
                <!-- Map Container -->
                <div id="mapContainer" class="mb-4 w-full bg-slate-100 rounded-lg overflow-hidden relative" style="height: 160px; border: 1px solid #e2e8f0;">
                    <div id="punchMap" style="width: 100%; height: 100%;"></div>
                    <div id="mapOverlay" class="absolute inset-0 bg-white/80 flex items-center justify-center flex-col z-10">
                        <i class="fa-solid fa-location-dot text-3xl text-indigo-500 mb-2 animate-bounce"></i>
                        <p class="text-sm font-semibold text-slate-700">Fetching Location...</p>
                        <p class="text-xs text-slate-500 text-center px-4 mt-1">Please allow browser location access</p>
                    </div>
                </div>
                
                @if($attendanceState === 'unpunched')
                    <!-- Unpunched State -->
                    <div class="text-center py-2" id="unpunched-view">
                        <p class="text-xs font-semibold text-slate-700 mb-1">Punch Machine Time</p>
                        <div class="border border-slate-200 rounded-lg py-2 px-4 mb-4 flex justify-between items-center bg-white shadow-inner">
                            <span class="font-mono text-lg tracking-wider font-semibold text-slate-800" id="live-clock">--:--:--</span>
                            <i class="fa-regular fa-clock text-slate-400"></i>
                        </div>
                        
                        <button onclick="handlePunch('in')" id="punchInBtn" disabled class="w-full bg-slate-400 text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2 opacity-70 cursor-not-allowed">
                            <i class="fa-solid fa-right-to-bracket"></i> Punch In
                        </button>
                    </div>
                @endif
                
                <div id="punched-view" class="{{ $attendanceState === 'unpunched' ? 'hidden' : '' }}">
                    <!-- Status Cards -->
                    <div class="space-y-3 mb-4">
                        <!-- Punched In Card -->
                        <div class="border border-emerald-200 bg-emerald-50 rounded-lg p-3 flex justify-between items-center" id="card-punched-in" style="display: {{ $attendanceState !== 'unpunched' ? 'flex' : 'none' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-800 leading-tight">Punched In</p>
                                    <p class="text-xs text-emerald-600" id="txt-punch-in">{{ $punchInTime ?? '--:--' }}</p>
                                </div>
                            </div>
                            <span class="bg-emerald-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">Active</span>
                        </div>

                        <!-- Half Day Warning (if applicable) -->
                        @if($isHalfDay)
                        <div class="border border-orange-200 bg-orange-50 rounded-lg p-3">
                            <div class="flex items-start gap-2">
                                <i class="fa-solid fa-triangle-exclamation text-orange-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold text-orange-800 leading-tight">Marked as Half Day / Late</p>
                                    <p class="text-xs text-orange-700 mt-1">Punch time after allowed grace period.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Punched Out Card -->
                        <div class="border border-red-200 bg-red-50 rounded-lg p-3 flex justify-between items-center" id="card-punched-out" style="display: {{ $attendanceState === 'punched_out' ? 'flex' : 'none' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-red-800 leading-tight">Punched Out</p>
                                    <p class="text-xs text-red-600" id="txt-punch-out">{{ $punchOutTime ?? '--:--' }}</p>
                                </div>
                            </div>
                            <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">Completed</span>
                        </div>

                        <!-- Working Time / Total Hours -->
                        <div class="border border-blue-200 bg-blue-50 rounded-lg p-3 text-center" id="card-working-time">
                            <p class="text-xs font-bold text-blue-800 mb-1" id="lbl-working-time">
                                {{ $attendanceState === 'punched_out' ? 'Total Work Hours' : 'Working Time' }}
                            </p>
                            <p class="text-2xl font-bold text-blue-600 font-mono tracking-wider" id="val-working-time">
                                {{ $attendanceState === 'punched_out' ? $totalWorkHoursStr : '00:00:00' }}
                            </p>
                        </div>
                    </div>

                    @if($attendanceState !== 'punched_out')
                        <button onclick="handlePunch('out')" id="punchOutBtn" disabled class="w-full bg-slate-400 text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2 opacity-70 cursor-not-allowed">
                            <i class="fa-solid fa-right-from-bracket"></i> Punch Out
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <!-- Column 2 -->"""

content = re.sub(attendance_widget_regex, new_attendance_widget, content)

# Modify script to include initMap and distance calculation
# We'll replace the handlePunch block.

handle_punch_regex = re.compile(r'function handlePunch\(type\) \{.*?function submitPunch\(type, lat, lng, btn, originalText\) \{', re.DOTALL)
new_scripts = r"""let employeeLat = null;
    let employeeLng = null;
    let employeeAccuracy = null;
    let map = null;
    let empMarker = null;
    let isInsideGeofence = false;

    const officeData = {
        lat: {{ \\App\\Models\\OfficeLocation::first()->latitude ?? 9.99419969778416 }},
        lng: {{ \\App\\Models\\OfficeLocation::first()->longitude ?? 76.2922585260837 }},
        radius: {{ \\App\\Models\\OfficeLocation::first()->radius ?? 100 }}
    };

    function initMap() {
        if (!navigator.geolocation) {
            document.getElementById('mapOverlay').innerHTML = '<p class="text-sm font-bold text-red-500">Geolocation not supported</p>';
            return;
        }

        const officePos = { lat: officeData.lat, lng: officeData.lng };
        
        map = new google.maps.Map(document.getElementById("punchMap"), {
            zoom: 18,
            center: officePos,
            mapTypeId: 'satellite',
            disableDefaultUI: true,
            zoomControl: true
        });

        const officeMarker = new google.maps.Marker({
            position: officePos,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
            title: 'Office'
        });

        const circle = new google.maps.Circle({
            map: map,
            radius: officeData.radius,
            fillColor: '#00AA00',
            fillOpacity: 0.1,
            strokeColor: '#00AA00',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            center: officePos
        });

        // Watch position continuously
        navigator.geolocation.watchPosition(
            (position) => {
                employeeLat = position.coords.latitude;
                employeeLng = position.coords.longitude;
                employeeAccuracy = position.coords.accuracy;
                
                const empPos = { lat: employeeLat, lng: employeeLng };
                
                if (!empMarker) {
                    empMarker = new google.maps.Marker({
                        position: empPos,
                        map: map,
                        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        title: 'You are here'
                    });
                    document.getElementById('mapOverlay').style.display = 'none';
                } else {
                    empMarker.setPosition(empPos);
                }

                checkGeofenceStatus(empPos, officePos);
            },
            (error) => {
                let msg = "Location Access Denied";
                if(error.code === 2) msg = "Location Unavailable";
                if(error.code === 3) msg = "Location Timeout";
                
                document.getElementById('mapOverlay').innerHTML = `<i class="fa-solid fa-triangle-exclamation text-3xl text-red-500 mb-2"></i><p class="text-sm font-bold text-slate-700">${msg}</p><p class="text-xs text-slate-500 mt-1 px-4 text-center">Check device location settings and permissions.</p>`;
                
                updateGeofenceUI(false, "Locating Error");
            },
            { enableHighAccuracy: true, maximumAge: 10000, timeout: 30000 }
        );
    }
    
    function checkGeofenceStatus(empPos, officePos) {
        if (!google.maps.geometry) return;
        
        const empLatLng = new google.maps.LatLng(empPos.lat, empPos.lng);
        const offLatLng = new google.maps.LatLng(officePos.lat, officePos.lng);
        
        const distance = google.maps.geometry.spherical.computeDistanceBetween(empLatLng, offLatLng);
        
        if (employeeAccuracy > 50) {
            updateGeofenceUI(false, "Poor GPS Accuracy");
        } else if (distance <= officeData.radius) {
            updateGeofenceUI(true, "Inside Premises");
        } else {
            updateGeofenceUI(false, "Outside Premises (" + Math.round(distance) + "m away)");
        }
    }
    
    function updateGeofenceUI(isValid, statusText) {
        isInsideGeofence = isValid;
        
        const statusBadge = document.getElementById('geofenceStatus');
        
        if (isValid) {
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700';
            statusBadge.innerHTML = `<i class="fa-solid fa-check-circle"></i> ${statusText}`;
            enablePunchButtons(true, 'bg-emerald-500 hover:bg-emerald-600');
        } else {
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700';
            statusBadge.innerHTML = `<i class="fa-solid fa-xmark-circle"></i> ${statusText}`;
            enablePunchButtons(false, 'bg-slate-400');
        }
    }
    
    function enablePunchButtons(enable, colorClass) {
        const btnIn = document.getElementById('punchInBtn');
        const btnOut = document.getElementById('punchOutBtn');
        
        if (btnIn) {
            btnIn.disabled = !enable;
            btnIn.className = `w-full ${colorClass} text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2 ${enable ? '' : 'opacity-70 cursor-not-allowed'}`;
        }
        if (btnOut) {
            btnOut.disabled = !enable;
            btnOut.className = `w-full ${enable ? 'bg-red-500 hover:bg-red-600' : 'bg-slate-400 opacity-70 cursor-not-allowed'} text-white font-bold py-3 px-4 rounded-lg shadow-md transition flex justify-center items-center gap-2`;
        }
    }

    function handlePunch(type) {
        if (!isInsideGeofence) {
            alert('You must be inside the office premises to punch ' + type + '.');
            return;
        }

        const btnId = type === 'in' ? 'punchInBtn' : 'punchOutBtn';
        const btn = document.getElementById(btnId);
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
        btn.disabled = true;

        submitPunch(type, employeeLat, employeeLng, employeeAccuracy, btn, originalText);
    }

    function submitPunch(type, lat, lng, acc, btn, originalText) {"""

content = re.sub(handle_punch_regex, new_scripts, content)

# update the fetch body inside submitPunch
submit_fetch_regex = re.compile(r"body: JSON\.stringify\(\{ latitude: lat, longitude: lng \}\)")
content = re.sub(submit_fetch_regex, r"body: JSON.stringify({ latitude: lat, longitude: lng, accuracy: acc, browser: navigator.userAgent })", content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
