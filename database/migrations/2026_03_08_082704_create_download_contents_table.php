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
        Schema::create('download_contents', function (Blueprint $table) {
    $table->id();

    $table->foreignId('type_id')
          ->constrained('download_content_types')
          ->cascadeOnDelete();

    $table->string('title');

    $table->text('description')->nullable();

    $table->string('file')->nullable();

    $table->foreignId('uploaded_by')
          ->constrained('users');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_contents');
    }
};
