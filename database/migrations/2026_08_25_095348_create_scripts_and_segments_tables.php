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
        Schema::create('scripts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->integer('version')->default(1);
            $table->string('language')->default('hi');
            $table->string('status')->default('GENERATED');
            $table->text('hook')->nullable();
            $table->text('previous_recap')->nullable();
            $table->text('main_narration')->nullable();
            $table->text('analysis')->nullable();
            $table->text('ending_hook')->nullable();
            $table->longText('full_script');
            $table->integer('word_count')->default(0);
            $table->integer('character_count')->default(0);
            $table->string('ai_model')->nullable();
            $table->string('prompt_version')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('script_segments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('script_id')->constrained('scripts')->cascadeOnDelete();
            $table->integer('sequence')->default(1);
            $table->string('type')->default('STORY');
            $table->text('text');
            $table->decimal('estimated_duration', 8, 2)->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_segments');
        Schema::dropIfExists('scripts');
    }
};
