<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarSubMenu extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::saved(fn () => SidebarMenu::clearMenuCache());
        static::deleted(fn () => SidebarMenu::clearMenuCache());
        static::restored(fn () => SidebarMenu::clearMenuCache());
    }

    protected $table = 'sidebar_sub_menus';

    protected $fillable = [
        'sidebar_menu_id',
        'title',
        'path',
        'permission_id',
        'permission',
        'order',
        'is_active',
    ];

    protected $casts = [
        'sidebar_menu_id' => 'integer',
        'permission_id'   => 'integer',
        'order'           => 'integer',
        'is_active'       => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(SidebarMenu::class, 'sidebar_menu_id');
    }

    public function permissionItem()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Permission::class, 'permission_id');
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
