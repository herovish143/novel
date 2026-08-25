<?php

namespace Database\Factories;

use Domain\DocumentImport\Enums\DocumentImportStatus;
use Domain\DocumentImport\Enums\ExtractionMethod;
use Domain\DocumentImport\Models\DocumentImport;
use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentImport>
 */
class DocumentImportFactory extends Factory
{
    protected $model = DocumentImport::class;

    public function definition(): array
    {
        return [
            'novel_id' => Novel::factory(),
            'original_filename' => 'shadow_slave_vol_1.pdf',
            'storage_disk' => 'public',
            'storage_path' => 'novels/1/imports/sample.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'sha256' => hash('sha256', (string) rand()),
            'page_count' => 100,
            'status' => DocumentImportStatus::REVIEW_REQUIRED,
            'extraction_method' => ExtractionMethod::NATIVE,
            'detected_chapters_count' => 10,
            'approved_chapters_count' => 5,
            'imported_chapters_count' => 0,
            'skipped_chapters_count' => 0,
            'average_confidence' => 90,
        ];
    }
}
