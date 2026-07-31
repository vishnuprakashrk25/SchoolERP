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
        Schema::create('admission_queries', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone');
    $table->string('email')->nullable();
    $table->text('message')->nullable();
    $table->date('follow_up_date')->nullable();
    $table->string('source')->nullable();
    $table->string('status')->default('pending');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_queries');
    }
};
