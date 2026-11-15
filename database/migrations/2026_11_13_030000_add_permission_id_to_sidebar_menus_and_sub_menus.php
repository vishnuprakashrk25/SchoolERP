<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add permission_id to sidebar_menus if missing
        if (Schema::hasTable('sidebar_menus')) {
            Schema::table('sidebar_menus', function (Blueprint $table) {
                if (!Schema::hasColumn('sidebar_menus', 'permission_id')) {
                    $table->unsignedBigInteger('permission_id')->nullable()->after('path');
                }
            });
        }

        // 2. Add permission_id to sidebar_sub_menus if missing
        if (Schema::hasTable('sidebar_sub_menus')) {
            Schema::table('sidebar_sub_menus', function (Blueprint $table) {
                if (!Schema::hasColumn('sidebar_sub_menus', 'permission_id')) {
                    $table->unsignedBigInteger('permission_id')->nullable()->after('path');
                }
            });
        }

        // 3. Populate permission_id in sidebar_menus from matching permissions table
        if (Schema::hasTable('sidebar_menus') && Schema::hasTable('permissions')) {
            DB::statement("
                UPDATE `sidebar_menus` sm
                JOIN `permissions` p ON p.name = sm.permission
                SET sm.permission_id = p.id
                WHERE sm.permission IS NOT NULL AND sm.permission_id IS NULL
            ");
        }

        // 4. Populate permission_id in sidebar_sub_menus from matching permissions table
        if (Schema::hasTable('sidebar_sub_menus') && Schema::hasTable('permissions')) {
            DB::statement("
                UPDATE `sidebar_sub_menus` ssm
                JOIN `permissions` p ON p.name = ssm.permission
                SET ssm.permission_id = p.id
                WHERE ssm.permission IS NOT NULL AND ssm.permission_id IS NULL
            ");
        }

        // 5. Clear menu caches
        if (class_exists(\App\Models\SidebarMenu::class) && method_exists(\App\Models\SidebarMenu::class, 'clearMenuCache')) {
            \App\Models\SidebarMenu::clearMenuCache();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sidebar_sub_menus') && Schema::hasColumn('sidebar_sub_menus', 'permission_id')) {
            Schema::table('sidebar_sub_menus', function (Blueprint $table) {
                $table->dropColumn('permission_id');
            });
        }

        if (Schema::hasTable('sidebar_menus') && Schema::hasColumn('sidebar_menus', 'permission_id')) {
            Schema::table('sidebar_menus', function (Blueprint $table) {
                $table->dropColumn('permission_id');
            });
        }

        if (class_exists(\App\Models\SidebarMenu::class) && method_exists(\App\Models\SidebarMenu::class, 'clearMenuCache')) {
            \App\Models\SidebarMenu::clearMenuCache();
        }
    }
};

