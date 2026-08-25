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
        Schema::create('chapter_facts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->string('fact_type')->default('PLOT'); // PLOT, REVEAL, CHARACTER, LOCATION, ABILITY, ITEM
            $table->foreignId('subject_entity_id')->nullable()->constrained('characters')->nullOnDelete();
            $table->foreignId('object_entity_id')->nullable()->constrained('characters')->nullOnDelete();
            $table->text('statement');
            $table->decimal('confidence', 4, 2)->default(1.00);
            $table->string('source_reference')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_facts');
    }
};
