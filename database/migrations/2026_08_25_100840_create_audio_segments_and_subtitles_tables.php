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
        Schema::create('audio_segments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('script_segment_id')->constrained('script_segments')->cascadeOnDelete();
            $table->string('provider')->default('ElevenLabs');
            $table->string('voice_id')->nullable();
            $table->string('model')->default('eleven_multilingual_v2');
            $table->string('storage_path');
            $table->integer('duration_ms')->default(0);
            $table->integer('character_count')->default(0);
            $table->decimal('cost', 8, 4)->default(0);
            $table->string('status')->default('GENERATED');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('subtitle_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->foreignId('script_id')->constrained('scripts')->cascadeOnDelete();
            $table->string('format')->default('SRT'); // SRT or ASS
            $table->string('storage_path');
            $table->string('language')->default('hi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtitle_files');
        Schema::dropIfExists('audio_segments');
    }
};
