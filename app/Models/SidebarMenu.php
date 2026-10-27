<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarMenu extends Model
{
    use HasFactory;

    protected $table = 'sidebar_menus';

    protected $fillable = [
        'section',
        'title',
        'icon',
        'path',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order'     => 'integer',
        'is_active' => 'boolean',
    ];

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
