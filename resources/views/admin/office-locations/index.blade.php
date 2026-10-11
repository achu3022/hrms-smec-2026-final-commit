@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-0">Office Locations Settings</h4>
            <p class="text-muted">Manage the geofence parameters for employee punch-in validation.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Location Settings</h5>
                </div>
                <div class="card-body">
                    @if($locations->count() > 0)
                    <div class="mb-4">
                        <label class="form-label text-muted">Select Branch</label>
                        <select class="form-select" onchange="window.location.href='?id='+this.value">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $location && $location->id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    @if($location)
                    <form action="{{ route('admin.office-locations.update', $location->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Office Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $location->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="latitudeInput" name="latitude" class="form-control" value="{{ old('latitude', $location->latitude) }}" required readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="longitudeInput" name="longitude" class="form-control" value="{{ old('longitude', $location->longitude) }}" required readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Geofence Radius (meters)</label>
                            <input type="number" id="radiusInput" name="radius" class="form-control" value="{{ old('radius', $location->radius) }}" min="10" required>
                            <small class="text-muted">Employees must be within this radius to punch in.</small>
                        </div>
                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ $location->status ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusSwitch">Geofence Active</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                    </form>
                    @else
                    <p class="text-danger">No office location found in database. Please run the seeder.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Location Preview</h5>
                    <small class="text-muted">Drag the marker to update coordinates</small>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; width: 100%; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=initMap" async defer></script>
<script>
    let map, marker, circle;
    
    function initMap() {
        @if($location)
        const lat = parseFloat(document.getElementById('latitudeInput').value);
        const lng = parseFloat(document.getElementById('longitudeInput').value);
        const radius = parseFloat(document.getElementById('radiusInput').value);
        
        const position = { lat: lat, lng: lng };

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 18,
            center: position,
            mapTypeId: 'satellite'
        });

        marker = new google.maps.Marker({
            position: position,
            map: map,
            draggable: true,
            title: "Drag to reposition"
        });

        circle = new google.maps.Circle({
            map: map,
            radius: radius,    // 100 in meters
            fillColor: '#AA0000',
            fillOpacity: 0.3,
            strokeColor: '#AA0000',
            strokeOpacity: 0.8,
            strokeWeight: 2
        });
        circle.bindTo('center', marker, 'position');

        // Listen for drag events
        google.maps.event.addListener(marker, 'dragend', function(event) {
            document.getElementById('latitudeInput').value = event.latLng.lat().toFixed(8);
            document.getElementById('longitudeInput').value = event.latLng.lng().toFixed(8);
        });

        // Listen for radius change
        document.getElementById('radiusInput').addEventListener('input', function(e) {
            const val = parseFloat(e.target.value);
            if(val > 0) {
                circle.setRadius(val);
            }
        });
        @endif
    }
</script>
@endpush
