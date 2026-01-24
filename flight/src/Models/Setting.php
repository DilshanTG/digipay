<?php

namespace App\Models;

class Setting extends Model
{
    protected string $table = 'settings';
    public bool $timestamps = false;

    protected array $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key);
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        $existing = self::where('key', $key);

        if ($existing) {
            $existing->value = $value;
            $existing->update();
            return $existing;
        }

        return self::create(['key' => $key, 'value' => $value]);
    }
}
