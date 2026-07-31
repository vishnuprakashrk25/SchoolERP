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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_access_token')) {
                $table->text('google_access_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'google_refresh_token')) {
                $table->text('google_refresh_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'google_token_expires_at')) {
                $table->timestamp('google_token_expires_at')->nullable();
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('events', 'google_event_id')) {
                $table->string('google_event_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_access_token',
                'google_refresh_token',
                'google_token_expires_at'
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'google_event_id']);
        });
    }
};
