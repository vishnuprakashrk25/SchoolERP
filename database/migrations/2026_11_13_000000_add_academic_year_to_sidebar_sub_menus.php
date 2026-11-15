<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SidebarMenu;
use App\Models\SidebarSubMenu;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $menus = SidebarMenu::where(function ($q) {
            $q->where('title', 'Settings Section')
              ->orWhere('title', 'General Settings')
              ->orWhere('section', 'Settings Section');
        })->get();

        foreach ($menus as $menu) {
            $exists = SidebarSubMenu::where('sidebar_menu_id', $menu->id)
                ->where('title', 'Academic Year')
                ->exists();

            if (!$exists) {
                $generalSettingSub = SidebarSubMenu::where('sidebar_menu_id', $menu->id)
                    ->where('title', 'General Settings')
                    ->first();

                $order = $generalSettingSub ? $generalSettingSub->order + 1 : 2;

                SidebarSubMenu::where('sidebar_menu_id', $menu->id)
                    ->where('order', '>=', $order)
                    ->increment('order');

                SidebarSubMenu::create([
                    'sidebar_menu_id' => $menu->id,
                    'title'           => 'Academic Year',
                    'path'            => null,
                    'order'           => $order,
                    'is_active'       => true,
                ]);
            }
        }

        if (class_exists(SidebarMenu::class) && method_exists(SidebarMenu::class, 'clearMenuCache')) {
            SidebarMenu::clearMenuCache();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $menuIds = SidebarMenu::where(function ($q) {
            $q->where('title', 'Settings Section')
              ->orWhere('title', 'General Settings')
              ->orWhere('section', 'Settings Section');
        })->pluck('id');

        SidebarSubMenu::whereIn('sidebar_menu_id', $menuIds)
            ->where('title', 'Academic Year')
            ->delete();

        if (class_exists(SidebarMenu::class) && method_exists(SidebarMenu::class, 'clearMenuCache')) {
            SidebarMenu::clearMenuCache();
        }
    }
};

