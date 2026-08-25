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
        Schema::create('characters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->string('name');
            $table->string('canonical_name');
            $table->string('gender')->nullable();
            $table->string('age_description')->nullable();
            $table->text('physical_description')->nullable();
            $table->text('personality')->nullable();
            $table->text('background')->nullable();
            $table->text('visual_description')->nullable();
            $table->string('importance')->default('SECONDARY');
            $table->foreignId('first_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->foreignId('last_seen_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('character_aliases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('alias')->index();
            $table->timestamps();
        });

        Schema::create('character_relationships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('related_character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('relationship_type');
            $table->text('description')->nullable();
            $table->foreignId('first_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->foreignId('last_updated_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('visual_description')->nullable();
            $table->foreignId('first_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->foreignId('last_seen_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('abilities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->foreignId('character_id')->nullable()->constrained('characters')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('first_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->foreignId('last_updated_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('story_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('owner_character_id')->nullable()->constrained('characters')->nullOnDelete();
            $table->foreignId('first_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->foreignId('last_seen_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('story_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->integer('sequence')->default(1);
            $table->string('event_type')->default('PLOT');
            $table->text('description');
            $table->integer('importance_score')->default(5);
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('chapter_summaries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->unique()->constrained('chapters')->cascadeOnDelete();
            $table->text('summary');
            $table->json('important_reveals')->nullable();
            $table->json('unresolved_questions')->nullable();
            $table->text('continuity_notes')->nullable();
            $table->string('ai_model')->nullable();
            $table->string('prompt_version')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_summaries');
        Schema::dropIfExists('story_events');
        Schema::dropIfExists('story_items');
        Schema::dropIfExists('abilities');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('character_relationships');
        Schema::dropIfExists('character_aliases');
        Schema::dropIfExists('characters');
    }
};
