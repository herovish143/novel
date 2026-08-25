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
        Schema::create('youtube_publications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->foreignId('video_project_id')->nullable()->constrained('video_projects')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->json('tags')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('visibility')->default('UNLISTED'); // UNLISTED, PRIVATE, PUBLIC
            $table->string('youtube_video_id')->nullable();
            $table->string('publish_status')->default('DRAFT'); // DRAFT, UPLOADED, PUBLISHED, FAILED
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_publications');
    }
};
