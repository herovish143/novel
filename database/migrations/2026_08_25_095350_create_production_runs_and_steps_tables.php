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
        Schema::create('production_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->string('status')->default('IMPORTED');
            $table->string('current_stage')->default('IMPORTED');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('estimated_cost', 8, 4)->default(0);
            $table->decimal('actual_cost', 8, 4)->default(0);
            $table->text('error')->nullable();
            $table->timestamps();
        });

        Schema::create('production_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_run_id')->constrained('production_runs')->cascadeOnDelete();
            $table->string('stage');
            $table->string('status')->default('PENDING');
            $table->integer('attempts')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_steps');
        Schema::dropIfExists('production_runs');
    }
};
