<?php

namespace Domain\DocumentImport\Actions;

use App\Models\User;
use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Models\ChapterCandidate;
use Domain\DocumentImport\Models\DocumentImport;

test('import approved chapters action creates real chapters and updates statuses', function () {
    $user = User::factory()->create();
    $import = DocumentImport::factory()->create();

    $candidate = ChapterCandidate::factory()->create([
        'document_import_id' => $import->id,
        'status' => ChapterCandidateStatus::APPROVED,
        'resolved_chapter_number' => 1,
        'title' => 'Chapter 1: The First Flight',
        'source_text' => 'Full narrative text for chapter 1.',
    ]);

    $response = $this->actingAs($user)->post(route('document-imports.import', $import->id));

    expect($candidate->fresh()->status)->toBe(ChapterCandidateStatus::IMPORTED);
    expect($import->fresh()->status->value)->toBe('COMPLETED');

    $this->assertDatabaseHas('chapters', [
        'novel_id' => $import->novel_id,
        'chapter_number' => 1,
        'title' => 'Chapter 1: The First Flight',
    ]);
});
