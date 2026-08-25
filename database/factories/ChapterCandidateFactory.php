<?php

namespace Database\Factories;

use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Models\ChapterCandidate;
use Domain\DocumentImport\Models\DocumentImport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChapterCandidate>
 */
class ChapterCandidateFactory extends Factory
{
    protected $model = ChapterCandidate::class;

    public function definition(): array
    {
        return [
            'document_import_id' => DocumentImport::factory(),
            'sequence' => 1,
            'detected_number' => 1,
            'resolved_chapter_number' => 1,
            'detected_title' => 'Chapter 1: The Awakening',
            'title' => 'Chapter 1: The Awakening',
            'start_page' => 1,
            'end_page' => 5,
            'word_count' => 1500,
            'confidence_score' => 95,
            'confidence_level' => 'HIGH',
            'status' => ChapterCandidateStatus::APPROVED,
            'source_text' => 'Sample chapter text body.',
            'content_hash' => hash('sha256', 'Sample chapter text body.'),
        ];
    }
}
