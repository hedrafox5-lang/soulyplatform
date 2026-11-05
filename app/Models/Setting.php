<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Cache key prefix.
     */
    protected const CACHE_PREFIX = 'souly_setting_';

    /**
     * Get a setting by key, checking Cache first with fallback to DB or default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever(self::CACHE_PREFIX . $key, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return config($key, $default);
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set/update a setting in DB and invalidate its cache entry.
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?string $type = null): self
    {
        $setting = static::where('key', $key)->first();

        $resolvedType = $type ?: ($setting ? $setting->type : 'string');

        // Store serialized string
        $stringValue = is_bool($value) ? ($value ? '1' : '0') : (is_array($value) ? json_encode($value) : (string) $value);

        if ($setting) {
            $setting->update([
                'value' => $stringValue,
                'group' => $group,
                'type' => $resolvedType,
            ]);
        } else {
            $setting = static::create([
                'key' => $key,
                'value' => $stringValue,
                'group' => $group,
                'type' => $resolvedType,
            ]);
        }

        Cache::forget(self::CACHE_PREFIX . $key);

        return $setting;
    }

    /**
     * Retrieve all settings grouped by their category.
     */
    public static function getAllGrouped(): array
    {
        $settings = static::orderBy('group')->orderBy('id')->get();
        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting->group][] = [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => self::castValue($setting->value, $setting->type),
                'raw_value' => $setting->value,
                'group' => $setting->group,
                'type' => $setting->type,
                'description' => $setting->description,
            ];
        }

        return $grouped;
    }

    /**
     * Cast string database value into native PHP type.
     */
    protected static function castValue(?string $value, ?string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean', 'bool' => in_array($value, ['1', 'true', 'on', 'yes'], true),
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'json', 'array' => json_decode($value, true) ?: [],
            default => $value,
        };
    }

    /**
     * Flush all setting caches.
     */
    public static function flushAllCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }
}
