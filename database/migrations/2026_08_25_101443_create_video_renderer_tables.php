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
        Schema::create('video_projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->foreignId('script_id')->constrained('scripts')->cascadeOnDelete();
            $table->string('resolution')->default('1920x1080');
            $table->integer('fps')->default(30);
            $table->string('status')->default('PENDING'); // PENDING, RENDERING, COMPLETED, FAILED
            $table->integer('duration_ms')->default(0);
            $table->string('output_path')->nullable();
            $table->timestamp('render_started_at')->nullable();
            $table->timestamp('render_completed_at')->nullable();
            $table->decimal('cost', 8, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('video_timeline_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('video_project_id')->constrained('video_projects')->cascadeOnDelete();
            $table->integer('sequence')->default(1);
            $table->string('type')->default('IMAGE'); // IMAGE, VIDEO, AUDIO, SUBTITLE
            $table->integer('start_ms')->default(0);
            $table->integer('end_ms')->default(0);
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->string('transition')->default('crossfade');
            $table->string('animation')->default('slow_zoom');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_timeline_items');
        Schema::dropIfExists('video_projects');
    }
};
