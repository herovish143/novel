<?php

namespace Domain\DocumentImport\Actions;

use App\Models\User;
use Domain\DocumentImport\Models\ChapterCandidate;

test('split chapter candidate action creates new candidate at split page', function () {
    $user = User::factory()->create();
    $candidate = ChapterCandidate::factory()->create([
        'sequence' => 1,
        'start_page' => 1,
        'end_page' => 10,
        'source_text' => 'Part 1 story content. Part 2 story content.',
    ]);

    $response = $this->actingAs($user)->post(route('chapter-candidates.split', $candidate->id), [
        'split_page' => 6,
    ]);

    expect($candidate->fresh()->end_page)->toBe(5);

    $this->assertDatabaseHas('chapter_candidates', [
        'document_import_id' => $candidate->document_import_id,
        'sequence' => 2,
        'start_page' => 6,
    ]);
});
