<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {

            if (!Schema::hasColumn('branches', 'code')) {
                $table->string('code')->nullable()->after('name');
            }

            if (!Schema::hasColumn('branches', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (!Schema::hasColumn('branches', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('branches', 'address')) {
                $table->text('address')->nullable();
            }

            if (!Schema::hasColumn('branches', 'status')) {
                $table->boolean('status')->default(1);
            }

        });
    }

    public function down(): void
    {
        //
    }
};