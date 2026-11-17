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
        'label',
        'type',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, ?string $value, ?string $label = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'label' => $label ?? ucwords(str_replace('_', ' ', $key)),
            ]
        );

        Cache::forget("setting_{$key}");
    }
}
