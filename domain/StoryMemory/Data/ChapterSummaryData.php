<?php

namespace Domain\StoryMemory\Data;

use Domain\StoryMemory\Models\ChapterSummary;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class ChapterSummaryData extends Data
{
    /**
     * @param  list<string>|null  $importantReveals
     * @param  list<string>|null  $unresolvedQuestions
     */
    public function __construct(
        public int $id,
        public int $chapterId,
        public string $summary,
        public ?array $importantReveals = null,
        public ?array $unresolvedQuestions = null,
        public ?string $continuityNotes = null,
        public ?string $aiModel = null,
    ) {}

    public static function fromModel(ChapterSummary $summary): self
    {
        return new self(
            id: $summary->id,
            chapterId: $summary->chapter_id,
            summary: $summary->summary,
            importantReveals: $summary->important_reveals,
            unresolvedQuestions: $summary->unresolved_questions,
            continuityNotes: $summary->continuity_notes,
            aiModel: $summary->ai_model,
        );
    }
}
