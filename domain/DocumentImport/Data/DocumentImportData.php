<?php

namespace Domain\DocumentImport\Data;

use Domain\DocumentImport\Models\DocumentImport;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class DocumentImportData extends Data
{
    public function __construct(
        public int $id,
        public int $novelId,
        public string $originalFilename,
        public string $storagePath,
        public int $fileSize,
        public string $sha256,
        public int $pageCount,
        public string $status,
        public string $extractionMethod,
        public int $detectedChaptersCount,
        public int $approvedChaptersCount,
        public int $importedChaptersCount,
        public int $skippedChaptersCount,
        public int $averageConfidence,
        public ?string $createdAt = null,
    ) {}

    public static function fromModel(DocumentImport $import): self
    {
        return new self(
            id: $import->id,
            novelId: $import->novel_id,
            originalFilename: $import->original_filename,
            storagePath: $import->storage_path,
            fileSize: $import->file_size,
            sha256: $import->sha256,
            pageCount: $import->page_count,
            status: $import->status->value ?? (string) $import->status,
            extractionMethod: $import->extraction_method->value ?? (string) $import->extraction_method,
            detectedChaptersCount: $import->detected_chapters_count,
            approvedChaptersCount: $import->approved_chapters_count,
            importedChaptersCount: $import->imported_chapters_count,
            skippedChaptersCount: $import->skipped_chapters_count,
            averageConfidence: $import->average_confidence,
            createdAt: $import->created_at?->toIso8601String(),
        );
    }
}
