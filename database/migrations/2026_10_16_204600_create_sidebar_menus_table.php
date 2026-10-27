<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sidebar_menus')) {
            Schema::create('sidebar_menus', function (Blueprint $table) {
                $table->id();
                $table->string('section', 100);
                $table->string('title', 150);
                $table->string('icon', 255)->nullable();
                $table->string('path', 255)->nullable();
                $table->json('sub_menus')->nullable();
                $table->string('permission', 100)->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('section');
                $table->index('order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_menus');
    }
};
