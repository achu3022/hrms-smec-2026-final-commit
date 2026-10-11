<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Get setting value by key with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cached = Cache::remember("hrms_setting_{$key}", 3600, function () use ($key) {
            $record = static::where('key', $key)->first();
            if (!$record) {
                return null;
            }
            return [
                'value' => $record->value,
                'type'  => $record->type,
            ];
        });

        // Handle case where cache returns incomplete object from prior serialization or corrupt data
        if (!is_array($cached)) {
            Cache::forget("hrms_setting_{$key}");
            $record = static::where('key', $key)->first();
            if (!$record) {
                return $default;
            }
            $value = $record->value;
            $type = $record->type;
        } else {
            $value = $cached['value'];
            $type = $cached['type'];
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json'    => json_decode($value, true) ?? [],
            default   => $value ?? $default,
        };
    }

    /**
     * Set / Update setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();
        if ($setting) {
            if ($setting->type === 'json' && is_array($value)) {
                $value = json_encode($value);
            } elseif ($setting->type === 'boolean') {
                $value = ($value === '1' || $value === true || $value === 1 || $value === 'true') ? '1' : '0';
            }
            $setting->update(['value' => (string)$value]);
            Cache::forget("hrms_setting_{$key}");
        }
    }

    /**
     * Clear all settings cache.
     */
    public static function flushCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("hrms_setting_{$key}");
        }
    }
}
