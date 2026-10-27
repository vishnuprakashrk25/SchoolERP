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
        if (!Schema::hasTable('behaviour_settings')) {
            Schema::create('behaviour_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->boolean('student_comment')->default(false);
                $table->boolean('parent_comment')->default(true);
                $table->boolean('student_view')->default(true);
                $table->boolean('parent_view')->default(false);
                $table->timestamps();
            });

            // Seed default row
            \Illuminate\Support\Facades\DB::table('behaviour_settings')->insert([
                'branch_id'       => null,
                'student_comment' => false,
                'parent_comment'  => true,
                'student_view'    => true,
                'parent_view'     => false,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('behaviour_settings');
    }
};
