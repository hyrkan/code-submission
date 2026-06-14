<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    /**
     * In-memory cache for settings within a single request.
     */
    protected static array $cache = [];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, $default = null)
    {
        // Return from in-memory cache if available
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        $setting = static::where('key', $key)->first();

        if (!$setting || $setting->value === null) {
            static::$cache[$key] = $default;
            return $default;
        }

        $value = match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };

        static::$cache[$key] = $value;

        return $value;
    }

    /**
     * Alias for getValue - used by AiService and controllers.
     */
    public static function get(string $key, $default = null)
    {
        return static::getValue($key, $default);
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, string $group = 'general', string $type = 'string')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        // Update in-memory cache
        static::$cache[$key] = $value;

        return $setting;
    }

    /**
     * Cast value based on type (instance method).
     */
    public function castValue()
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            'text' => $this->value,
            default => $this->value,
        };
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => $s->castValue()])
            ->toArray();
    }
}