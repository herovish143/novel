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
        Schema::create('ai_usage', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_run_id')->nullable()->constrained('production_runs')->nullOnDelete();
            $table->string('provider');
            $table->string('service');
            $table->string('model')->nullable();
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->integer('characters')->default(0);
            $table->integer('images')->default(0);
            $table->decimal('seconds', 8, 2)->default(0);
            $table->decimal('estimated_cost', 8, 4)->default(0);
            $table->decimal('actual_cost', 8, 4)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('pronunciations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->string('term');
            $table->string('pronunciation');
            $table->string('language')->default('hi');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pronunciations');
        Schema::dropIfExists('ai_usage');
    }
};
