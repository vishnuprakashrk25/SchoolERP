<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restructures complaints table: renames old columns → new names,
     * adds missing columns (email, complaint_type, source).
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Rename complaint_by → name (if old column exists)
            if (Schema::hasColumn('complaints', 'complaint_by') && !Schema::hasColumn('complaints', 'name')) {
                $table->renameColumn('complaint_by', 'name');
            }
            // Rename description → message (if old column exists)
            if (Schema::hasColumn('complaints', 'description') && !Schema::hasColumn('complaints', 'message')) {
                $table->renameColumn('description', 'message');
            }
            // Add new columns if missing
            if (!Schema::hasColumn('complaints', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('complaints', 'complaint_type')) {
                $table->string('complaint_type')->nullable()->after('message');
            }
            if (!Schema::hasColumn('complaints', 'source')) {
                $table->string('source')->nullable()->after('complaint_type');
            }
            // Drop old columns no longer needed
            if (Schema::hasColumn('complaints', 'date')) {
                $table->dropColumn('date');
            }
            if (Schema::hasColumn('complaints', 'action_taken')) {
                $table->dropColumn('action_taken');
            }
            // Add soft deletes if missing
            if (!Schema::hasColumn('complaints', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'name') && !Schema::hasColumn('complaints', 'complaint_by')) {
                $table->renameColumn('name', 'complaint_by');
            }
            if (Schema::hasColumn('complaints', 'message') && !Schema::hasColumn('complaints', 'description')) {
                $table->renameColumn('message', 'description');
            }
            if (Schema::hasColumn('complaints', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('complaints', 'complaint_type')) {
                $table->dropColumn('complaint_type');
            }
            if (Schema::hasColumn('complaints', 'source')) {
                $table->dropColumn('source');
            }
            if (!Schema::hasColumn('complaints', 'date')) {
                $table->date('date')->nullable();
            }
            if (!Schema::hasColumn('complaints', 'action_taken')) {
                $table->text('action_taken')->nullable();
            }
        });
    }
};
