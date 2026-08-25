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
        Schema::create('novels', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('original_language')->default('en');
            $table->string('output_language')->default('hi');
            $table->string('source_url')->nullable();
            $table->text('description')->nullable();
            $table->string('default_voice_provider')->nullable();
            $table->string('default_voice_id')->nullable();
            $table->string('visual_style')->default('dark cinematic fantasy');
            $table->string('narration_style')->default('conversational Hindi explanation');
            $table->decimal('max_cost_per_episode', 8, 2)->default(5.00);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novels');
    }
};
