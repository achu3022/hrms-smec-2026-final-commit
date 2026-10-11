<?php

namespace App\Services;

use App\Models\OfficeLocation;

class GeofenceService
{
    /**
     * Calculate the great-circle distance between two points on Earth using the Haversine formula.
     * 
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in meters
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius of Earth in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Get the nearest active office location within its geofence radius.
     *
     * @param float $lat Employee's latitude
     * @param float $lon Employee's longitude
     * @return array|null Returns array with ['office' => OfficeLocation, 'distance' => float] or null if none
     */
    public static function getNearestValidOffice($lat, $lon)
    {
        $offices = OfficeLocation::where('status', true)->get();
        
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($offices as $office) {
            $distance = self::calculateDistance($lat, $lon, $office->latitude, $office->longitude);
            
            if ($distance <= $office->radius && $distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $office;
            }
        }

        if ($nearest) {
            return [
                'office' => $nearest,
                'distance' => $minDistance
            ];
        }

        return null;
    }

    /**
     * Calculate distance to all active offices and return the absolute closest one, 
     * even if outside the radius (useful for auditing/logging).
     */
    public static function getAbsoluteNearestOffice($lat, $lon)
    {
        $offices = OfficeLocation::where('status', true)->get();
        if ($offices->isEmpty()) {
            return null;
        }

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($offices as $office) {
            $distance = self::calculateDistance($lat, $lon, $office->latitude, $office->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $office;
            }
        }

        return [
            'office' => $nearest,
            'distance' => $minDistance
        ];
    }
}
