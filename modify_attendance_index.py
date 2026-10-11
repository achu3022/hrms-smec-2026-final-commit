import re

filepath = 'resources/views/admin/attendance/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Add Google Maps API script
if 'https://maps.googleapis.com/maps/api/js?key=' not in content:
    content = content.replace("@push('scripts')", "@push('scripts')\n<script src=\"https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap\" async defer></script>\n")

# Table Headers
headers_old = """                            <th>Check In</th>
                            <th>Check Out</th>
                            <th class="text-end">Actions</th>"""
headers_new = """                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Location</th>
                            <th class="text-end">Actions</th>"""
content = content.replace(headers_old, headers_new)

# Table Rows
rows_regex = re.compile(r'(<td class="fw-medium text-dark">\{\{ \$att->check_out \? \\Carbon\\Carbon::parse\(\$att->check_out\)->format\(\'h:i A\'\) : \'—\' \}\}</td>\s*)<td class="text-end">')
rows_new = r"""\1
                                <td>
                                    @if($att->location_verified)
                                        <span class="badge bg-success"><i class="fa-solid fa-location-dot"></i> Verified</span>
                                    @else
                                        <span class="badge bg-secondary">Unverified</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="bg-light text-primary border rounded me-1" style="padding: 4px 10px; transition: 0.2s;" title="View Location" onclick="viewLocation({{ $att->id }}, {{ $att->checkin_latitude ?? 'null' }}, {{ $att->checkin_longitude ?? 'null' }}, '{{ $att->checkin_distance ? round($att->checkin_distance) . 'm' : 'N/A' }}', '{{ $att->checkin_accuracy ? round($att->checkin_accuracy) . 'm' : 'N/A' }}')"><i class="fa-solid fa-map-location-dot"></i></button>
                                    """
content = re.sub(rows_regex, rows_new, content)

# Add Modal
modal_html = """
<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-map-location-dot text-primary me-2"></i>Punch In Location Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Distance from Office:</strong> <span id="modalDistance"></span>
                    </div>
                    <div class="col-6">
                        <strong>GPS Accuracy:</strong> <span id="modalAccuracy"></span>
                    </div>
                </div>
                <div id="mapContainer" style="height: 400px; width: 100%; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                    <div id="map" style="height: 100%; width: 100%; border-radius: 8px; display: none;"></div>
                    <span id="noLocationMsg" class="text-muted">No location data recorded for this punch.</span>
                </div>
            </div>
        </div>
    </div>
</div>
"""

content = content.replace('<!-- Add Modal -->', modal_html + '\n<!-- Add Modal -->')

# Add JS logic
js_logic = """
    let detailMap, empMarker, officeMarker, circle, polyline;
    // Define the default office location from DB or default
    const officeLat = {{ \App\Models\OfficeLocation::first()->latitude ?? 9.99419969778416 }};
    const officeLng = {{ \App\Models\OfficeLocation::first()->longitude ?? 76.2922585260837 }};
    const officeRadius = {{ \App\Models\OfficeLocation::first()->radius ?? 100 }};

    function initMap() {
        // Will initialize on demand
    }

    function viewLocation(id, lat, lng, dist, acc) {
        document.getElementById('modalDistance').innerText = dist;
        document.getElementById('modalAccuracy').innerText = acc;
        
        const myModal = new bootstrap.Modal(document.getElementById('locationModal'));
        myModal.show();
        
        if (!lat || !lng) {
            document.getElementById('map').style.display = 'none';
            document.getElementById('noLocationMsg').style.display = 'block';
            return;
        }
        
        document.getElementById('map').style.display = 'block';
        document.getElementById('noLocationMsg').style.display = 'none';
        
        setTimeout(() => {
            const empPos = { lat: parseFloat(lat), lng: parseFloat(lng) };
            const officePos = { lat: parseFloat(officeLat), lng: parseFloat(officeLng) };
            
            if (!detailMap) {
                detailMap = new google.maps.Map(document.getElementById("map"), {
                    zoom: 17,
                    center: officePos,
                    mapTypeId: 'satellite'
                });
            } else {
                detailMap.setCenter(officePos);
            }
            
            if (empMarker) empMarker.setMap(null);
            if (officeMarker) officeMarker.setMap(null);
            if (circle) circle.setMap(null);
            if (polyline) polyline.setMap(null);
            
            officeMarker = new google.maps.Marker({
                position: officePos,
                map: detailMap,
                title: "Office Location",
                icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
            });
            
            empMarker = new google.maps.Marker({
                position: empPos,
                map: detailMap,
                title: "Employee Location",
                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
            });
            
            circle = new google.maps.Circle({
                map: detailMap,
                radius: parseFloat(officeRadius),
                fillColor: '#00AA00',
                fillOpacity: 0.1,
                strokeColor: '#00AA00',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                center: officePos
            });
            
            polyline = new google.maps.Polyline({
                path: [officePos, empPos],
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2,
                map: detailMap
            });
            
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(officePos);
            bounds.extend(empPos);
            detailMap.fitBounds(bounds);
        }, 300); // wait for modal transition
    }
"""

content = content.replace('function toggleDateInputs() {', js_logic + '\n    function toggleDateInputs() {')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
