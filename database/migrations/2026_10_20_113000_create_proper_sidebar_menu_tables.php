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
        // Drop existing sidebar_sub_menus if exists
        Schema::dropIfExists('sidebar_sub_menus');

        // Drop existing sidebar_menus table to recreate with proper normalized relational structure
        Schema::dropIfExists('sidebar_menus');

        Schema::create('sidebar_menus', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100)->index();
            $table->string('title', 150);
            $table->string('icon', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->integer('order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('sidebar_sub_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sidebar_menu_id')->index();
            $table->string('title', 150);
            $table->string('path', 255)->nullable();
            $table->integer('order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->foreign('sidebar_menu_id')
                  ->references('id')
                  ->on('sidebar_menus')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_sub_menus');
        Schema::dropIfExists('sidebar_menus');
    }
};
