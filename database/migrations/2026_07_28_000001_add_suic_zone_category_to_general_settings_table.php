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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('suic_code')->nullable()->after('school_code');
            $table->string('zone')->nullable()->after('suic_code');
            $table->string('category_of_institution')->nullable()->after('zone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['suic_code', 'zone', 'category_of_institution']);
        });
    }
};
