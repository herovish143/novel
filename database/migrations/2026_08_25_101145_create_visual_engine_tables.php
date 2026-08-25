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
        Schema::create('scenes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->foreignId('script_id')->constrained('scripts')->cascadeOnDelete();
            $table->integer('sequence')->default(1);
            $table->integer('start_ms')->default(0);
            $table->integer('end_ms')->default(0);
            $table->string('scene_type')->default('IMAGE'); // IMAGE, VIDEO
            $table->text('description');
            $table->text('image_prompt');
            $table->string('camera_motion')->default('slow_zoom'); // slow_zoom, pan_left, pan_right, static
            $table->integer('importance')->default(5);
            $table->string('status')->default('PLANNED'); // PLANNED, GENERATING, COMPLETED, REUSED, FAILED
            $table->timestamps();
        });

        Schema::create('character_visuals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('image_path');
            $table->text('prompt')->nullable();
            $table->string('provider')->default('OpenAI');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('scene_assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('scene_id')->constrained('scenes')->cascadeOnDelete();
            $table->string('asset_type')->default('IMAGE'); // IMAGE, VIDEO, BACKGROUND, OVERLAY
            $table->string('provider')->default('OpenAI');
            $table->text('prompt');
            $table->string('storage_path');
            $table->integer('width')->default(1792);
            $table->integer('height')->default(1024);
            $table->decimal('cost', 8, 4)->default(0);
            $table->string('status')->default('READY');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scene_assets');
        Schema::dropIfExists('character_visuals');
        Schema::dropIfExists('scenes');
    }
};
