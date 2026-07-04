<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = ['key', 'value', 'label', 'group', 'type'];

    /**
     * Ambil nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Simpan nilai setting berdasarkan key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Ambil banyak setting sekaligus, return array key => value.
     */
    public static function getMany(array $keys): array
    {
        return static::whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Ambil semua setting dikelompokkan berdasarkan group.
     */
    public static function getAllGrouped(): array
    {
        return static::all()
            ->groupBy('group')
            ->toArray();
    }

    /**
     * Ambil semua setting sebagai key => value.
     */
    public static function getAllFlat(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
