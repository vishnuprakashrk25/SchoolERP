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
       Schema::create('postal_receives', function (Blueprint $table) {
    $table->id();
    $table->string('from_title');
    $table->string('reference_no')->nullable();
    $table->text('address')->nullable();
    $table->date('date');
    $table->string('file')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_receives');
    }
};
