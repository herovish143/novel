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
        Schema::create('chapters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->integer('chapter_number');
            $table->string('title');
            $table->string('source_url')->nullable();
            $table->longText('source_text');
            $table->string('source_hash', 64);
            $table->foreignId('previous_chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->string('status')->default('IMPORTED');
            $table->timestamp('imported_at')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamp('scripted_at')->nullable();
            $table->timestamps();

            $table->unique(['novel_id', 'chapter_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
