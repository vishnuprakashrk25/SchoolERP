<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures all front-office tables (postal_receives, postal_dispatches, phone_call_logs)
     * have all expected columns and nullable date defaults to avoid 422 DB errors.
     */
    public function up(): void
    {
        // 1. postal_receives
        Schema::table('postal_receives', function (Blueprint $table) {
            if (!Schema::hasColumn('postal_receives', 'to_title')) {
                $table->string('to_title')->nullable()->after('from_title');
            }
            if (!Schema::hasColumn('postal_receives', 'note')) {
                $table->text('note')->nullable()->after('address');
            }
            if (!Schema::hasColumn('postal_receives', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 2. postal_dispatches
        Schema::table('postal_dispatches', function (Blueprint $table) {
            if (!Schema::hasColumn('postal_dispatches', 'from_title')) {
                $table->string('from_title')->nullable()->after('to_title');
            }
            if (!Schema::hasColumn('postal_dispatches', 'note')) {
                $table->text('note')->nullable()->after('address');
            }
            if (!Schema::hasColumn('postal_dispatches', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 3. phone_call_logs
        Schema::table('phone_call_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('phone_call_logs', 'description')) {
                $table->text('description')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('phone_call_logs', 'call_duration')) {
                $table->string('call_duration')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('phone_call_logs', 'next_follow_up_date')) {
                $table->date('next_follow_up_date')->nullable()->after('date');
            }
            if (!Schema::hasColumn('phone_call_logs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postal_receives', function (Blueprint $table) {
            if (Schema::hasColumn('postal_receives', 'to_title')) {
                $table->dropColumn('to_title');
            }
            if (Schema::hasColumn('postal_receives', 'note')) {
                $table->dropColumn('note');
            }
        });

        Schema::table('postal_dispatches', function (Blueprint $table) {
            if (Schema::hasColumn('postal_dispatches', 'from_title')) {
                $table->dropColumn('from_title');
            }
            if (Schema::hasColumn('postal_dispatches', 'note')) {
                $table->dropColumn('note');
            }
        });

        Schema::table('phone_call_logs', function (Blueprint $table) {
            if (Schema::hasColumn('phone_call_logs', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('phone_call_logs', 'call_duration')) {
                $table->dropColumn('call_duration');
            }
            if (Schema::hasColumn('phone_call_logs', 'next_follow_up_date')) {
                $table->dropColumn('next_follow_up_date');
            }
        });
    }
};
