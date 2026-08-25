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
        if (! Schema::hasTable('character_relationships')) {
            Schema::create('character_relationships', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
                $table->foreignId('source_entity_id')->constrained('characters')->cascadeOnDelete();
                $table->foreignId('target_entity_id')->constrained('characters')->cascadeOnDelete();
                $table->string('relationship_type')->default('UNKNOWN');
                $table->text('description')->nullable();
                $table->unsignedInteger('valid_from_chapter_id')->nullable();
                $table->unsignedInteger('valid_to_chapter_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Table created in base migration
    }
};
