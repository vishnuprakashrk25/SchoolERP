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
        Schema::create('phone_call_logs', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone');
    $table->string('call_type')->nullable();
    $table->date('date');
    $table->date('follow_up_date')->nullable();
    $table->text('note')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_call_logs');
    }
};
