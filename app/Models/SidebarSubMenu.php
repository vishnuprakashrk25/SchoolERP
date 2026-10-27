<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarSubMenu extends Model
{
    use HasFactory;

    protected $table = 'sidebar_sub_menus';

    protected $fillable = [
        'sidebar_menu_id',
        'title',
        'path',
        'order',
        'is_active',
    ];

    protected $casts = [
        'sidebar_menu_id' => 'integer',
        'order'           => 'integer',
        'is_active'       => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(SidebarMenu::class, 'sidebar_menu_id');
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
