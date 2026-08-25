<?php

namespace Domain\StoryMemory\Data;

use Domain\StoryMemory\Models\ChapterSummary;
use Spatie\LaravelData\Data;

class ChapterSummaryData extends Data
{
    /**
     * @param  list<string>|null  $important_reveals
     * @param  list<string>|null  $unresolved_questions
     */
    public function __construct(
        public int $id,
        public int $chapter_id,
        public string $summary,
        public ?array $important_reveals = null,
        public ?array $unresolved_questions = null,
        public ?string $continuity_notes = null,
        public ?string $ai_model = null,
    ) {}

    public static function fromModel(ChapterSummary $summary): self
    {
        return new self(
            id: $summary->id,
            chapter_id: $summary->chapter_id,
            summary: $summary->summary,
            important_reveals: $summary->important_reveals,
            unresolved_questions: $summary->unresolved_questions,
            continuity_notes: $summary->continuity_notes,
            ai_model: $summary->ai_model,
        );
    }
}
