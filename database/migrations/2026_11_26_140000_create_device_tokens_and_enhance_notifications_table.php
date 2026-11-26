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
        // 1. Create device_tokens table for push notifications (FCM / APNs / Web)
        if (!Schema::hasTable('device_tokens')) {
            Schema::create('device_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->text('token');
                $table->string('device_type', 50)->default('android'); // android, ios, web
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            });
        }

        // 2. Enhance existing notifications table with type, data, and sender_id if missing
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'type')) {
                    $table->string('type', 100)->default('general')->after('message');
                }
                if (!Schema::hasColumn('notifications', 'data')) {
                    $table->json('data')->nullable()->after('type');
                }
                if (!Schema::hasColumn('notifications', 'sender_id')) {
                    $table->unsignedBigInteger('sender_id')->nullable()->after('user_id');
                    $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'sender_id')) {
                    $table->dropForeign(['sender_id']);
                    $table->dropColumn('sender_id');
                }
                if (Schema::hasColumn('notifications', 'data')) {
                    $table->dropColumn('data');
                }
                if (Schema::hasColumn('notifications', 'type')) {
                    $table->dropColumn('type');
                }
            });
        }
    }
};
