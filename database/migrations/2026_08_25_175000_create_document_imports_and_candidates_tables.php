<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('novel_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('storage_disk')->default('public');
            $table->string('storage_path');
            $table->string('mime_type')->default('application/pdf');
            $table->unsignedBigInteger('file_size');
            $table->string('sha256', 64);
            $table->unsignedInteger('page_count')->default(0);
            $table->string('status')->default('UPLOADED'); // UPLOADED, EXTRACTING, DETECTING, REVIEW_REQUIRED, IMPORTING, COMPLETED, FAILED
            $table->string('extraction_method')->default('NATIVE'); // NATIVE, OCR, NATIVE_WITH_OCR_FALLBACK
            $table->unsignedInteger('detected_chapters_count')->default(0);
            $table->unsignedInteger('approved_chapters_count')->default(0);
            $table->unsignedInteger('imported_chapters_count')->default(0);
            $table->unsignedInteger('skipped_chapters_count')->default(0);
            $table->unsignedInteger('average_confidence')->default(0);
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['novel_id', 'status']);
            $table->index('sha256');
        });

        Schema::create('chapter_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_import_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->unsignedInteger('detected_number')->nullable();
            $table->unsignedInteger('resolved_chapter_number')->nullable();
            $table->string('detected_title')->nullable();
            $table->string('title')->nullable();
            $table->unsignedInteger('start_page');
            $table->unsignedInteger('end_page');
            $table->unsignedInteger('word_count')->default(0);
            $table->unsignedInteger('confidence_score')->default(50); // 0 to 100
            $table->string('confidence_level')->default('MEDIUM'); // HIGH, MEDIUM, LOW
            $table->string('status')->default('DETECTED'); // DETECTED, REVIEW_REQUIRED, APPROVED, SKIPPED, IMPORTED, DUPLICATE, FAILED
            $table->string('content_path')->nullable();
            $table->string('content_hash', 64)->nullable();
            $table->text('source_text')->nullable();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->unique(['document_import_id', 'sequence']);
            $table->index(['document_import_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_candidates');
        Schema::dropIfExists('document_imports');
    }
};
