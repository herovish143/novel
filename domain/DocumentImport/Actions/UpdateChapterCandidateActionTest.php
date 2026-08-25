<?php

namespace Domain\DocumentImport\Actions;

use App\Models\User;
use Domain\DocumentImport\Models\ChapterCandidate;

test('update chapter candidate action updates title and number', function () {
    $user = User::factory()->create();
    $candidate = ChapterCandidate::factory()->create([
        'title' => 'Old Title',
        'resolved_chapter_number' => 1,
    ]);

    $response = $this->actingAs($user)->patch(route('chapter-candidates.update', $candidate->id), [
        'title' => 'Renamed Title',
        'resolved_chapter_number' => 10,
    ]);

    expect($candidate->fresh()->title)->toBe('Renamed Title');
    expect($candidate->fresh()->resolved_chapter_number)->toBe(10);
});
