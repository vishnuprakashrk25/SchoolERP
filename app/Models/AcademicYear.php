<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class AcademicYear extends Model
{
    use SoftDeletes;

    public const CACHE_KEY_CURRENT_ID = 'app_current_academic_year_id';
    public const CACHE_KEY_LIST = 'app_academic_years_list';

    protected static function booted(): void
    {
        $clear = function (): void {
            Cache::forget(self::CACHE_KEY_CURRENT_ID);
            Cache::forget(self::CACHE_KEY_LIST);
        };

        static::saved($clear);
        static::deleted($clear);
        static::restored($clear);
    }

    /**
     * Retrieve the cached active academic year ID.
     */
    public static function cachedCurrentId(): ?int
    {
        $id = Cache::remember(self::CACHE_KEY_CURRENT_ID, 3600, function () {
            return static::where('is_current', true)->value('id') ?? 0;
        });

        return $id > 0 ? (int) $id : null;
    }

    /**
     * Retrieve cached academic years ordered latest first.
     */
    public static function cachedList()
    {
        return Cache::remember(self::CACHE_KEY_LIST, 3600, fn () => static::latest()->get());
    }

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current'
    ];
}