<?php

namespace Domain\DocumentImport\Actions;

use App\Models\User;
use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Models\ChapterCandidate;

test('approve chapter candidate action updates status to APPROVED', function () {
    $user = User::factory()->create();
    $candidate = ChapterCandidate::factory()->create([
        'status' => ChapterCandidateStatus::REVIEW_REQUIRED,
    ]);

    $response = $this->actingAs($user)->post(route('chapter-candidates.approve', $candidate->id));

    expect($candidate->fresh()->status)->toBe(ChapterCandidateStatus::APPROVED);
});
