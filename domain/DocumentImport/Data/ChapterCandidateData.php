<?php

namespace Domain\DocumentImport\Data;

use Domain\DocumentImport\Models\ChapterCandidate;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class ChapterCandidateData extends Data
{
    public function __construct(
        public int $id,
        public int $documentImportId,
        public int $sequence,
        public ?int $detectedNumber,
        public ?int $resolvedChapterNumber,
        public ?string $detectedTitle,
        public ?string $title,
        public int $startPage,
        public int $endPage,
        public int $wordCount,
        public int $confidenceScore,
        public string $confidenceLevel,
        public string $status,
        public ?string $sourceText,
        public ?int $chapterId = null,
        public ?string $approvedAt = null,
        public ?string $importedAt = null,
    ) {}

    public static function fromModel(ChapterCandidate $candidate): self
    {
        return new self(
            id: $candidate->id,
            documentImportId: $candidate->document_import_id,
            sequence: $candidate->sequence,
            detectedNumber: $candidate->detected_number,
            resolvedChapterNumber: $candidate->resolved_chapter_number,
            detectedTitle: $candidate->detected_title,
            title: $candidate->title,
            startPage: $candidate->start_page,
            endPage: $candidate->end_page,
            wordCount: $candidate->word_count,
            confidenceScore: $candidate->confidence_score,
            confidenceLevel: $candidate->confidence_level,
            status: $candidate->status->value ?? (string) $candidate->status,
            sourceText: $candidate->source_text,
            chapterId: $candidate->chapter_id,
            approvedAt: $candidate->approved_at?->toIso8601String(),
            importedAt: $candidate->imported_at?->toIso8601String(),
        );
    }
}
