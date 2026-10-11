<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class WebcamImageService
{
    /**
     * Process and save a base64 webcam image.
     *
     * @param string $base64Image
     * @param string $employeeId
     * @param string $type 'checkin' or 'checkout'
     * @return string|null Path relative to storage/app/public/
     */
    public static function processAndSaveImage(?string $base64Image, string $employeeId, string $type = 'checkin'): ?string
    {
        if (empty($base64Image)) {
            return null;
        }

        try {
            // Remove the data URL prefix if present (e.g., "data:image/jpeg;base64,")
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $typeMatch)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $typeMatch = strtolower($typeMatch[1]); // jpeg, png, etc.
            }

            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                throw new \Exception('Base64 decode failed');
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->decode($imageData);

            // Resize max 640x640, keeping aspect ratio
            $image->scaleDown(width: 640, height: 640);
            
            // Auto orient based on EXIF (Intervention Image v4 handles this mostly automatically, 
            // but reading EXIF data might depend on the driver. In v4 read() handles basic EXIF orientation)

            $year = date('Y');
            $month = date('m');
            $timestamp = date('Ymd_His');
            
            // Filename Format: employeeid_YYYYMMDD_HHMMSS.jpg
            $filename = "{$employeeId}_{$timestamp}_{$type}.jpg";
            $relativePath = "attendance/{$year}/{$month}/{$employeeId}/{$filename}";

            // Encode as JPEG and save via Laravel Storage (public disk)
            $jpegData = (string) $image->encodeUsingFileExtension('jpeg');
            \Illuminate\Support\Facades\Storage::disk('public')->put($relativePath, $jpegData);

            return $relativePath;

        } catch (\Throwable $e) {
            \Log::error("WebcamImageService Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            return null;
        }
    }
}
