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
        Schema::create('artist_track', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('track_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique([
                'artist_id',
                'track_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_track');
    }
};
