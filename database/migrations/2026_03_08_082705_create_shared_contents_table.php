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
        Schema::create('shared_contents', function (Blueprint $table) {

    $table->id();

    $table->foreignId('content_id')
          ->constrained('download_contents')
          ->cascadeOnDelete();

    $table->string('shared_with_role');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_contents');
    }
};
