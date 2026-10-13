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
        Schema::create('albums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('artist_id')->constrained('artists')->cascadeOnDelete();
            $table->foreignId('genres_id')->constrained('genres');

            $table->string('title');
            $table->string('cover_url')->nullable(); 
            $table->date('release_date')->nullable();
            $table->enum('album_type', ['album', 'single', 'ep'])->default('album');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
