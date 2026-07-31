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
        Schema::table('students', function (Blueprint $table) {
            $table->string('apaar_id')->nullable()->after('birth_certificate_no');
            $table->string('aadharshila_no')->nullable()->after('apaar_id');
            $table->string('pen_no')->nullable()->after('aadharshila_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['apaar_id', 'aadharshila_no', 'pen_no']);
        });
    }
};
