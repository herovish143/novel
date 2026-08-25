<?php

namespace Domain\DocumentImport\Actions;

use App\Models\User;
use Domain\DocumentImport\Models\ChapterCandidate;
use Domain\DocumentImport\Models\DocumentImport;

test('merge chapter candidates action combines next candidate into target', function () {
    $user = User::factory()->create();
    $import = DocumentImport::factory()->create();

    $candidate1 = ChapterCandidate::factory()->create([
        'document_import_id' => $import->id,
        'sequence' => 1,
        'start_page' => 1,
        'end_page' => 5,
        'source_text' => 'Part 1',
    ]);

    $candidate2 = ChapterCandidate::factory()->create([
        'document_import_id' => $import->id,
        'sequence' => 2,
        'start_page' => 6,
        'end_page' => 10,
        'source_text' => 'Part 2',
    ]);

    $response = $this->actingAs($user)->post(route('chapter-candidates.merge-next', $candidate1->id));

    expect($candidate1->fresh()->end_page)->toBe(10);
    expect($candidate1->fresh()->source_text)->toContain('Part 1');
    expect($candidate1->fresh()->source_text)->toContain('Part 2');

    $this->assertDatabaseMissing('chapter_candidates', [
        'id' => $candidate2->id,
    ]);
});
