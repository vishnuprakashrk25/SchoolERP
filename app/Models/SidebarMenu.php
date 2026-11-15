<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SidebarMenu extends Model
{
    use HasFactory, SoftDeletes;

    public const CACHE_KEYS = [
        'sidebar_menus_all',
        'sidebar_menus_active',
        'sidebar_menus_items',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => self::clearMenuCache());
        static::deleted(fn () => self::clearMenuCache());
        static::restored(fn () => self::clearMenuCache());
    }

    public static function clearMenuCache(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    protected $table = 'sidebar_menus';

    protected $fillable = [
        'section',
        'title',
        'icon',
        'path',
        'permission_id',
        'permission',
        'order',
        'is_active',
    ];

    protected $casts = [
        'permission_id' => 'integer',
        'order'         => 'integer',
        'is_active'     => 'boolean',
    ];

    public function permissionItem()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Permission::class, 'permission_id');
    }

    public function subMenus()
    {
        return $this->hasMany(SidebarSubMenu::class, 'sidebar_menu_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }
}
